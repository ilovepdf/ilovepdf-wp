<?php

namespace Ilove_Pdf_WP\Tools\Watermark;

use Exception;
use Ilovepdf\WatermarkTask;
use Ilovepdf\Exceptions\AuthException;
use Ilove_Pdf_WP\Account\User_Account;
use Ilove_Pdf_WP\Helpers\Admin_Notice;
use Ilove_Pdf_WP\Helpers\File_System;
use Ilove_Pdf_WP\Helpers\Media_Handler;
use Ilove_Pdf_WP\Tools\Backup;
use Ilove_Pdf_WP\Tools\Base\Status_Process;
use Ilove_Pdf_WP\Tools\Watermark\Settings as Watermark_Settings;

/**
 * Manages the watermark process.
 *
 * @package Ilove_Pdf_WP
 * @since 3.0.0
 */
class Tool_Watermark {
    use Status_Process;

    /**
     * Post meta key for tracking the watermark status of an attachment.
     *
     * @var string
     * @since 3.0.0
     */
    private $db_key_status = '_ipdf_attachment_watermark_status';

    /**
     * Legacy post meta key for tracking the watermark status of an attachment.
     *
     * @var string
     * @since 3.0.0
     */
    private $legacy_db_key_status = '_watermarked_file';

    /**
     * Constructor to initialize the watermark tool.
     *
     * This method sets up the AJAX handler for the watermark action.
     *
     * @since 3.0.0
     */
    public function __construct() {
        add_action( 'wp_ajax_ilovepdf_action_watermark', array( $this, 'handler_action_watermark' ) );
        add_filter( 'bulk_actions-upload', array( $this, 'add_bulk_action' ) );
        add_filter( 'handle_bulk_actions-upload', array( $this, 'handle_bulk_action' ), 10, 3 );
        add_action( 'add_attachment', array( $this, 'handle_auto_watermark' ) );
    }

    /**
     * Get the database key for the watermark status.
     *
     * @return string
     * @since 3.0.0
     */
    public static function get_db_key_status() {
        return ( new self() )->db_key_status;
    }

    /**
     * Handle the AJAX request for applying a watermark to a PDF file.
     *
     * This method verifies the nonce, checks for the post ID, and processes the watermarking.
     * It returns a JSON response indicating success or failure.
     *
     * @since 3.0.0
     * @return void
     */
    public function handler_action_watermark() {
        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'ilovepdf_action_watermark' ) ) {
            wp_send_json_error( __( 'Error processing your request. Invalid Nonce code', 'ilove-pdf' ), 401 );
        }

        if ( ! isset( $_POST['post_id'] ) ) {
            wp_send_json_error( __( 'Error processing your request. The file ID must be sent', 'ilove-pdf' ), 400 );
        }

        try {
            $watermark_process = $this->watermark_process( (int) $_POST['post_id'] );

            wp_send_json_success(
                $watermark_process,
                200
            );

        } catch ( Exception $e ) {
            wp_send_json_error(
                sprintf(
                    /* translators: %s Additional process error  */
                    _x( 'Watermark PDF error: %s', 'Watermark PDF: Error message.', 'ilove-pdf' ),
                    $e->getMessage()
                ),
                500
            );
        }
    }

    /**
     * Process the watermarking of a PDF file.
     *
     * This method applies the watermark to the specified PDF file, handling various settings and exceptions.
     * It returns an array with the result of the watermarking process.
     *
     * @param int $post_id The ID of the post (attachment) to be watermarked.
     * @return array
     * @throws Exception If an error occurs during the watermarking process.
     * @since 3.0.0
     */
    public function watermark_process( $post_id ) {
        $options   = Watermark_Settings::get_settings();
        $file_name = basename( get_attached_file( $post_id ) );

        try {

            if ( $this->is_file_watermarked( $post_id ) ) {
                $message = sprintf(
                    /* translators: %1$s The file name */
                    _x( 'The file %1$s already has a watermark applied.', 'Watermark PDF: Info message.', 'ilove-pdf' ),
                    $file_name,
                );

                return array(
                    'error'       => false,
                    'type_notice' => 'info',
                    'message'     => $message,
                );
            }

            $this->set_status_in_process( $post_id, $this->db_key_status );

            if ( ! isset( $options[ Watermark_Settings::get_field_watermark_active() ] ) ) {
                throw new Exception( _x( 'The watermark tool is not activated.', 'Watermark PDF: Error message.', 'ilove-pdf' ) );
            }

            if ( get_post_mime_type( $post_id ) !== 'application/pdf' ) {
                $message = sprintf(
                    /* translators: %1$s The file name */
                    _x( 'The file %1$s is not a PDF.', 'Watermark PDF: Error message.', 'ilove-pdf' ),
                    $file_name,
                );

                throw new Exception( $message );
            }

            /** @var \WP_Filesystem_Base $wp_filesystem */
            global $wp_filesystem;

            if ( ! WP_Filesystem() ) {
                throw new Exception(
                    esc_html_x( 'Unable to connect to the filesystem', '', 'ilove-pdf' )
                );
            }

            $public_key  = User_Account::get_settings( User_Account::get_db_user_publickey_key(), '' );
            $private_key = User_Account::get_settings( User_Account::get_db_user_privatekey_key(), '' );

            if ( empty( $public_key ) || empty( $private_key ) ) {
                throw new AuthException(
                    _x( 'The API Keys are not set. Please check your settings.', 'Auth: Error message.', 'ilove-pdf' ),
                );
            }

            $attachment_file = get_attached_file( $post_id );

            Backup::add_file( $post_id, $attachment_file );

            $main_task = new WatermarkTask( $public_key, $private_key );

            $main_task->addFile( $attachment_file );

            switch ( $options[ Watermark_Settings::get_field_mode() ] ) {
                case Watermark_Settings::get_mode_values( 'image' ):
                    $main_task->setMode( 'image' );

                    if ( isset( $options[ Watermark_Settings::get_field_image_mode() ] ) ) {
                        $image = $main_task->addFile( $options[ Watermark_Settings::get_field_image_mode() ] );
                        $main_task->setImage( $image->getServerFilename() );
                    }

                    break;

                case Watermark_Settings::get_mode_values( 'text' ):
                    $main_task->setMode( 'text' );

                    $main_task->setText( $options[ Watermark_Settings::get_field_text_mode() ] );
                    $main_task->setFontFamily( $options[ Watermark_Settings::get_field_font_family() ] );
                    $main_task->setFontSize( $options[ Watermark_Settings::get_field_font_size() ] );
                    $main_task->setFontStyle( $options[ Watermark_Settings::get_field_font_style() ] );
                    $main_task->setFontColor( $options[ Watermark_Settings::get_field_font_color() ] );

                    break;
            }

            $position = explode( ' ', $options[ Watermark_Settings::get_field_position() ] );
            $main_task->setHorizontalPosition( $position[0] );
            $main_task->setVerticalPosition( $position[1] );

            $main_task->setTransparency( $options[ Watermark_Settings::get_field_transparency() ] );
            $main_task->setRotation( $options[ Watermark_Settings::get_field_rotation() ] );
            $main_task->setLayer( $options[ Watermark_Settings::get_field_layer() ] );

            if ( isset( $options[ Watermark_Settings::get_field_mosaic() ] ) && Watermark_Settings::get_field_mosaic() === 'on' ) {
                $main_task->setMosaic( true );
            }

            $main_task->execute();

            $tmp_folder = File_System::get_full_path_tmp_watermark_folder();

            if ( ! $wp_filesystem->exists( $tmp_folder ) ) {
                File_System::create_dir( $tmp_folder );
            }

            // and finally download file. If no path is set, it will be downloaded on current folder
            $main_task->download( $tmp_folder );

            $watermarked_file = $tmp_folder . $file_name;

            if ( ! $wp_filesystem->exists( $watermarked_file ) ) {
                $message = sprintf(
                    /* translators: %1$s The file name */
                    _x( 'The %1$s file could not be found inside the temporary download folder.', 'Watermark PDF: Error message.', 'ilove-pdf' ),
                    $file_name,
                );

                throw new Exception( $message );
            }

            $wp_filesystem->move( $watermarked_file, $attachment_file, true );

            Media_Handler::regenerate_attachment_data( $post_id );

            $this->set_status_ready( $post_id, $this->db_key_status );

            $message = sprintf(
                /* translators: %1$s The file name */
                _x( 'The watermark was applied successfully to %1$s.', 'Watermark PDF: Success message.', 'ilove-pdf' ),
                $file_name,
            );

            return array(
                'error'       => false,
                'type_notice' => 'success',
                'message'     => $message,
                'data'        => array(),
            );

        } catch ( Exception $e ) {
            $this->set_status_error( $post_id, $this->db_key_status );
            throw new Exception( $e->getMessage() );
        }
    }

    /**
     * Check if a file is already watermarked.
     *
     * This method checks the post meta for the watermark status of a file.
     *
     * @param int $file_id The ID of the file to check.
     * @return bool True if the file is watermarked, false otherwise.
     * @since 3.0.0
     */
    public static function is_file_watermarked( $file_id ) {

        $status = get_post_meta( $file_id, self::get_db_key_status(), true );

        if ( empty( $status ) ) {
            return false;
        }

        if ( $status === 'error' ) {
            return false;
        }

        if ( $status === 'in_progress' ) {
            return false;
        }

        return true;
    }

    /**
     * Migrate the legacy watermark status to the new system.
     *
     * This method checks for attachments with the legacy watermark status and updates them to the new status.
     *
     * @since 3.0.0
     */
    public static function migrate_watermark_status() {
        $instance = new self();
        $args     = array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'application/pdf',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        );

        $attachments = get_posts( $args );

        foreach ( $attachments as $attachment_id ) {
            $status = get_post_meta( $attachment_id, $instance->legacy_db_key_status, true );

            if ( ! empty( $status ) && (int) $status === 1 ) {
                $instance->set_status_in_process( $attachment_id, self::get_db_key_status() );
                delete_post_meta( $attachment_id, $instance->legacy_db_key_status );
            }
        }
    }

    /**
     * Add a bulk action for applying watermarks to PDF files.
     *
     * This method adds a custom bulk action to the media library for applying watermarks to selected PDF files.
     *
     * @since 3.0.0
     * @param array $actions Existing bulk actions.
     * @return array Modified bulk actions with the new 'ilovepdf_watermark' action.
     */
    public function add_bulk_action( $actions ) {
        $actions['ilovepdf_watermark'] = _x( 'Apply Watermark', 'Bulk action button', 'ilove-pdf' );
        return $actions;
    }

    /**
     * Handle the bulk action for applying watermarks to selected files.
     *
     * This method processes the selected files when the 'ilovepdf_watermark' action is triggered.
     *
     * @since 3.0.0
     * @param string $redirect_to The URL to redirect to after processing.
     * @param string $doaction The action being performed.
     * @param array  $post_ids The IDs of the selected posts/files.
     * @return string Redirect URL.
     */
    public function handle_bulk_action( $redirect_to, $doaction, $post_ids ) {

        if ( 'ilovepdf_watermark' !== $doaction ) {
            return $redirect_to;
        }

        if ( empty( $post_ids ) ) {
            return $redirect_to;
        }

		foreach ( $post_ids as $id ) {
			$process = $this->watermark_process( $id );

			if ( ! empty( $process['error'] ) ) {
                Admin_Notice::add_notice(
                    $process['message'],
                    'error',
                );

			} else {
                Admin_Notice::add_notice(
                    $process['message'],
                    $process['type_notice'] ?? 'success',
                );
			}
		}

        wp_safe_redirect( $redirect_to );
        exit;
    }

    /**
     * Handle automatic watermarking when a new attachment is added.
     *
     * This method checks the settings and user account status, then processes the watermarking.
     * It sets a transient with success or error messages for the watermark process.
     *
     * @since 3.0.0
     * @param int $post_id The ID of the newly added attachment.
     */
    public function handle_auto_watermark( $post_id ) {
        $options = Watermark_Settings::get_settings();

        if ( ! User_Account::is_user_logged_in() ) {
            return;
        }

        if ( ! isset( $options[ Watermark_Settings::get_field_watermark_active() ] ) ) {
            return;
        }

        if ( ! isset( $options[ Watermark_Settings::get_field_auto_watermark() ] ) ) {
            return;
        }

        if ( get_post_mime_type( $post_id ) !== 'application/pdf' ) {
            return;
        }

        try {
            $process = $this->watermark_process( $post_id );

            Admin_Notice::add_notice(
                $process['message'],
                $process['error'] ? 'error' : 'success',
            );

        } catch ( Exception $e ) {
            Admin_Notice::add_notice(
                $e->getMessage(),
                'error',
            );
        }
    }
}
