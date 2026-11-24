<?php

namespace Ilove_Pdf_WP\Tools\Watermark;

use Exception;
use Ilovepdf\WatermarkTask;
use Ilove_Pdf_WP\Tools\Backup;
use Ilove_Pdf_WP\Account\User_Auth;
use Ilove_Pdf_WP\Account\User_Data;
use Ilove_Pdf_WP\Helpers\DB_Handler;
use Ilove_Pdf_WP\Helpers\File_System;
use Ilove_Pdf_WP\Helpers\Admin_Notice;
use Ilovepdf\Exceptions\AuthException;
use Ilove_Pdf_WP\Helpers\Media_Handler;
use Ilove_Pdf_WP\Tools\Base\Status_Process;
use Ilove_Pdf_WP\Tools\Compress\Tool_Compress;
use Ilove_Pdf_WP\Tools\Watermark\Settings as Watermark_Settings;
use Ilove_Pdf_WP\Tools\Compress\Statistics as Compress_Statistics;
use Ilove_Pdf_WP\Tools\Watermark\Statistics as Watermark_Statistics;

/**
 * Manages the watermark process.
 *
 * @package Ilove_Pdf_WP\Tools\Watermark
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
        add_action( 'wp_ajax_ilovepdf_status_watermark', array( $this, 'handler_status_watermark' ) );
        add_filter( 'bulk_actions-upload', array( $this, 'add_bulk_action' ) );
        add_filter( 'handle_bulk_actions-upload', array( $this, 'handle_bulk_action' ), 10, 3 );
        add_action( 'add_attachment', array( $this, 'handle_auto_watermark' ) );
        add_action( 'load-upload.php', array( $this, 'reset_stale_rows' ) );
        add_action( 'load-toplevel_page_ilovepdf-admin-page', array( $this, 'reset_stale_rows' ) );
        add_action( 'load-ilovepdf_page_ipdf-compress-admin-page', array( $this, 'reset_stale_rows' ) );
        add_action( 'load-ilovepdf_page_ipdf-watermark-admin-page', array( $this, 'reset_stale_rows' ) );
        // Submenu pages under upload.php fire load-{page_hook}, not load-upload.php.
        add_action( 'load-media_page_ipdf-media-optimization', array( $this, 'reset_stale_rows' ) );
        // CHANGE-A: invalidate cached statistics whenever a new attachment is uploaded.
        Watermark_Statistics::register_hooks();
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
        $post_id = $this->verify_request_or_die( 'ilovepdf_action_watermark' );

        try {
            $watermark_process = $this->watermark_process( $post_id );

            wp_send_json_success(
                $watermark_process,
                200
            );

        } catch ( Exception $e ) {
            wp_send_json_error(
                sprintf(
                    /* translators: %1$s Additional process error  */
                    _x( 'Could not apply watermark: %1$s', 'Watermark PDF: Error message.', 'ilove-pdf' ),
                    $e->getMessage()
                ),
                500
            );
        }
    }

    /**
     * Handle the read-only AJAX status check for a watermark job.
     *
     * Performs the same nonce, capability and ID checks as handler_action_watermark.
     * Calls resolve_status() — a per-attachment stale check — and responds:
     *   • HTTP 202 (in_process)   → job still in flight; client should keep polling.
     *   • wp_send_json_success    → terminal success; data reconstructed from stored meta.
     *   • wp_send_json_error      → terminal failure or never-processed.
     *
     * This handler MUST NOT call watermark_process() or any pipeline step.
     *
     * @since 3.0.0
     * @return void
     */
    public function handler_status_watermark() {

        $post_id = $this->verify_request_or_die( 'ilovepdf_action_watermark' );

        $status = $this->resolve_status( $post_id, $this->db_key_status );

        if ( 'in_process' === $status ) {
            // HTTP 202: job is still in flight — client must keep polling.
            // We use wp_send_json (not http_response_code + wp_die) because WP's
            // _ajax_wp_die_handler silently rewrites a bare 500 to a 200 empty body.
            wp_send_json( array( 'status' => 'in_process' ), 202 );
        }

        if ( 'ready' === $status ) {
            $original_size   = 0;
            $compressed_size = 0;

            if ( Tool_Compress::is_file_compressed( $post_id ) ) {
                $compress_metadata = get_post_meta( $post_id, Tool_Compress::get_db_key_process(), true );
                if ( is_array( $compress_metadata ) && isset( $compress_metadata['original_size'] ) && isset( $compress_metadata['compressed_size'] ) ) {
                    $original_size   = (int) $compress_metadata['original_size'];
                    $compressed_size = (int) $compress_metadata['compressed_size'];
                }
            }

            $file_name = basename( (string) get_attached_file( $post_id ) );
            $message   = sprintf(
                /* translators: %1$s The file name */
                _x( 'Watermark applied successfully: %1$s', 'Watermark PDF: Success message.', 'ilove-pdf' ),
                $file_name,
            );

            wp_send_json_success(
                array(
                    'error'       => false,
                    'type_notice' => 'success',
                    'message'     => $message,
                    'data'        => array(
                        'files_protected'     => Watermark_Statistics::get_protected_files(),
                        'resume'              => Watermark_Statistics::get_resume(),
                        'backup'              => true,
                        'file_is_compressed'  => Tool_Compress::is_file_compressed( $post_id ),
                        'compress_statistics' => array(
                            'percentage'        => Tool_Compress::get_compressed_reabable_percentage( $original_size, $compressed_size ),
                            'files_processed'   => Compress_Statistics::get_files_processed(),
                            'average_reduction' => Compress_Statistics::get_average_reduction(),
                            'space_saved'       => Compress_Statistics::get_space_saved(),
                            'total_resume'      => Compress_Statistics::get_resume(),
                            'original_size'     => $original_size,
                            'compressed_size'   => $compressed_size,
                        ),
                    ),
                )
            );
        }

        // Status is 'error', '' (never-processed), or any unexpected value — terminal failure.
        wp_send_json_error(
            array(
                'status'  => 'error',
                'message' => __( 'The watermark could not be applied. Please try again.', 'ilove-pdf' ),
            )
        );
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
     * @throws AuthException If authentication fails.
     * @since 3.0.0
     */
    public function watermark_process( $post_id ) {
        // Guarantee the job always runs to a terminal status even if the client disconnects
        // mid-request (e.g. a proxy timeout). Covers both the AJAX path and the add_attachment
        // auto-process hook since this is the shared choke point for both.
        ignore_user_abort( true );

        $options   = Watermark_Settings::get_settings();
        $file_name = basename( get_attached_file( $post_id ) );

        try {

            if ( $this->is_file_watermarked( $post_id ) ) {
                $message = sprintf(
                    /* translators: %1$s The file name */
                    _x( 'File already watermarked: %1$s', 'Watermark PDF: Info message.', 'ilove-pdf' ),
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
                throw new Exception( _x( 'Watermark is not enabled. Please check your settings.', 'Watermark PDF: Error message.', 'ilove-pdf' ) );
            }

            if ( get_post_mime_type( $post_id ) !== 'application/pdf' ) {
                $message = sprintf(
                    /* translators: %1$s The file name */
                    _x( 'This is not a PDF file: %1$s', 'Error message.', 'ilove-pdf' ),
                    $file_name,
                );

                throw new Exception( $message );
            }

            /** Filesystem @var \WP_Filesystem_Base $wp_filesystem */
            global $wp_filesystem;

            if ( ! WP_Filesystem() ) {
                throw new Exception(
                    esc_html_x( 'Could not connect to the server.', 'Error message: Unable to connect to the core WordPress function.', 'ilove-pdf' )
                );
            }

            $public_key  = User_Data::get_settings( User_Data::get_db_user_publickey_key(), '' );
            $private_key = User_Data::get_settings( User_Data::get_db_user_privatekey_key(), '' );

            if ( empty( $public_key ) || empty( $private_key ) ) {
                throw new AuthException(
                    _x( 'API key is missing. Please check your settings.', 'Auth: Error message.', 'ilove-pdf' ),
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

                        $image_url     = $options[ Watermark_Settings::get_field_image_mode() ];
                        $attachment_id = attachment_url_to_postid( $image_url );

                        if ( $attachment_id ) {
                            $image = $main_task->addFile( get_attached_file( $attachment_id ) );
                        } else {
                            $image = $main_task->addFileFromUrl( $image_url );
                        }

                        $main_task->setImage( $image->getServerFilename() );
                    }

                    break;

                case Watermark_Settings::get_mode_values( 'text' ):
                    $main_task->setMode( 'text' );

                    $main_task->setText( $options[ Watermark_Settings::get_field_text_mode() ] );
                    $main_task->setFontFamily( $options[ Watermark_Settings::get_field_font_family() ] );
                    $main_task->setFontSize( $options[ Watermark_Settings::get_field_font_size() ] );

                    if ( isset( $options[ Watermark_Settings::get_field_font_style() ] ) ) {
                        $main_task->setFontStyle( (string) $options[ Watermark_Settings::get_field_font_style() ] );
                    }

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
                File_System::create_ilovepdf_directories();
            }

            // and finally download file. If no path is set, it will be downloaded on current folder.
            $main_task->download( $tmp_folder );

            $watermarked_file = $tmp_folder . $file_name;

            if ( ! $wp_filesystem->exists( $watermarked_file ) ) {
                $message = sprintf(
                    /* translators: %1$s The file name */
                    _x( 'Temporary file not found: %1$s', 'Process Error', 'ilove-pdf' ),
                    $file_name,
                );

                throw new Exception( $message );
            }

            $wp_filesystem->move( $watermarked_file, $attachment_file, true );

            $original_size   = 0;
            $compressed_size = 0;

            // When applying a watermark, the file may end up increasing in size than the original.
            // If the file was compressed, we preserve the original compression metadata.
            if ( Tool_Compress::is_file_compressed( $post_id ) ) {
                // Get the existing compression metadata to preserve it.
                $compress_metadata = get_post_meta( $post_id, Tool_Compress::get_db_key_process(), true );
                if ( ! empty( $compress_metadata ) && isset( $compress_metadata['original_size'] ) && isset( $compress_metadata['compressed_size'] ) ) {
                    $original_size   = (int) $compress_metadata['original_size'];
                    $compressed_size = (int) $compress_metadata['compressed_size'];
                }
            } else {
                Compress_Statistics::reset_statistics();
            }

            Media_Handler::regenerate_attachment_data( $post_id );

            $this->set_status_ready( $post_id, $this->db_key_status );

            Watermark_Statistics::reset_statistics();
            DB_Handler::delete_transient( User_Data::get_transient_key() );

            $message = sprintf(
                /* translators: %1$s The file name */
                _x( 'Watermark applied successfully: %1$s', 'Watermark PDF: Success message.', 'ilove-pdf' ),
                $file_name,
            );

            return array(
                'error'       => false,
                'type_notice' => 'success',
                'message'     => $message,
                'data'        => array(
                    'files_protected'     => Watermark_Statistics::get_protected_files(),
                    'resume'              => Watermark_Statistics::get_resume(),
                    'backup'              => true,
                    'file_is_compressed'  => Tool_Compress::is_file_compressed( $post_id ),
                    'compress_statistics' => array(
                        'percentage'        => Tool_Compress::get_compressed_reabable_percentage( $original_size, $compressed_size ),
                        'files_processed'   => Compress_Statistics::get_files_processed(),
                        'average_reduction' => Compress_Statistics::get_average_reduction(),
                        'space_saved'       => Compress_Statistics::get_space_saved(),
                        'total_resume'      => Compress_Statistics::get_resume(),
                        'original_size'     => $original_size,
                        'compressed_size'   => $compressed_size,
                    ),
                ),
            );

        } catch ( Exception $e ) {
            $this->set_status_error( $post_id, $this->db_key_status );
            throw new Exception( esc_html( $e->getMessage() ) );
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

        if ( 'ready' !== $status ) {
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

            if ( ! empty( $status ) && 1 === (int) $status ) {
                $instance->set_status_ready( $attachment_id, self::get_db_key_status() );
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
     * Processing is now browser-driven via sequential per-file AJAX requests.
     * This server-side handler is reached only when JavaScript did not intercept
     * the form submit (e.g. JS disabled). It informs the user and returns without
     * processing, ensuring the old memory-exhausting synchronous foreach never runs.
     *
     * @since 3.0.0
     * @param string $redirect_to The URL to redirect to after processing.
     * @param string $doaction The action being performed.
     * @param array  $post_ids The IDs of the selected posts/files.
     * @return string Redirect URL.
     */
    public function handle_bulk_action( $redirect_to, $doaction, $post_ids ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

        if ( 'ilovepdf_watermark' !== $doaction ) {
            return $redirect_to;
        }

        // Processing is browser-driven (sequential per-file AJAX). This server path is reached
        // only when JS did not intercept the submit (e.g. JS disabled) — never run the old
        // all-at-once foreach, which is what exhausted memory. Inform the user instead.
        Admin_Notice::add_notice(
            __( 'Bulk PDF processing requires JavaScript. Please enable it and try again.', 'ilove-pdf' ),
            'warning'
        );

        return $redirect_to;
    }

    /**
     * Reset any orphaned 'in_process' watermark rows on eligible admin page loads.
     *
     * Delegates to Status_Process::reset_stale_in_process() with the watermark status key.
     * Hooked on load-upload.php, the three plugin admin page-load actions and the media
     * optimization page so the sweep runs only on screens where the watermark column appears,
     * never on every admin request.
     *
     * @since 3.0.0
     */
    public function reset_stale_rows() {
        $this->reset_stale_in_process( $this->db_key_status );
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

        if ( ! User_Auth::is_user_logged_in() ) {
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
