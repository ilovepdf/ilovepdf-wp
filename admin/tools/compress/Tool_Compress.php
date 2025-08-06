<?php

namespace Ilove_Pdf_WP\Tools\Compress;

use Exception;
use Ilovepdf\CompressTask;
use Ilove_Pdf_WP\Helpers\File_System;
use Ilove_Pdf_WP\Tools\Backup;
use Ilove_Pdf_WP\Account\User_Account;
use Ilove_Pdf_WP\Tools\Base\Status_Process;
use Ilovepdf\Exceptions\AuthException;
use Ilove_Pdf_WP\Tools\Compress\Settings as Compress_Settings;

/**
 * Manages the compression process.
 *
 * @package Ilove_Pdf_WP
 * @since 3.0.0
 */
class Tool_Compress {
    use Status_Process;

    /**
     * Post meta key for tracking the compression status of an attachment.
     *
     * @var string
     * @since 3.0.0
     */
    private $db_key_status = '_ipdf_attachment_compress_status';

    /**
     * Post meta key for storing the compression process ID or reference.
     *
     * @var string
     * @since 3.0.0
     */
    private $db_key_process = '_ipdf_attachment_compress_process';

    /**
     * Legacy database keys for original file sizes.
     *
     * @var string
     */
    private $legacy_db_key_original_size = '_wp_attached_original_size';

    /**
     * Legacy database keys for compressed file sizes.
     *
     * @var string
     */
    private $legacy_db_key_compressed_size = '_wp_attached_compress_size';

    /**
     * Legacy database key for process status.
     *
     * @var string
     */
    private $legacy_db_key_process = '_compressed_file';

    /**
     * Constructor to initialize the compression tool.
     *
     * @since 3.0.0
     */
    public function __construct() {
        add_action( 'wp_ajax_ilovepdf_action_compress', array( $this, 'handler_compress_action' ) );
        add_filter( 'bulk_actions-upload', array( $this, 'add_bulk_action' ) );
        add_filter( 'handle_bulk_actions-upload', array( $this, 'handle_bulk_action' ), 10, 3 );
    }

    /**
     * Get the database key for the compression status.
     *
     * @return string
     * @since 3.0.0
     */
    public static function get_db_key_status() {
        return ( new self() )->db_key_status;
    }

    /**
     * Get the database key for the compression process.
     *
     * @return string
     * @since 3.0.0
     */
    public static function get_db_key_process() {
        return ( new self() )->db_key_process;
    }

    /**
     * Handle the AJAX request for compressing a PDF file.
     *
     * Validates nonce and required POST data, then processes the compression.
     * Returns JSON response with success or error message.
     *
     * @since 3.0.0
     */
	public function handler_compress_action() {

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'ilovepdf_action_compress' ) ) {
            wp_send_json_error( __( 'Error processing your request. Invalid Nonce code', 'ilove-pdf' ), 401 );
        }

        if ( ! isset( $_POST['post_id'] ) ) {
            wp_send_json_error( __( 'Error processing your request. The file ID must be sent', 'ilove-pdf' ), 400 );
        }

        try {
            $compress_process = $this->compress_process( (int) $_POST['post_id'] );

            wp_send_json_success(
                $compress_process,
                200
            );

        } catch ( Exception $e ) {
            wp_send_json_error(
                sprintf(
                    /* translators: %s Additional process error  */
                    _x( 'Compress PDF error: %s', 'Compress PDF: Error message.', 'ilove-pdf' ),
                    $e->getMessage()
                ),
                500
            );
        }
	}

    /**
     * Compress PDF File.
     *
     * @since    1.0.0
     * @param    int $post_id    File ID.
     * @return   array   Response with success or error message.
     * @throws   Exception If the compression fails or if the file is not a PDF.
     * @throws   AuthException If the API keys are not set.
     */
	public function compress_process( $post_id ) {
        // TODO: en la respuesta enviar un true en caso de que el archivo tenga un backup y se pueda restaurar.
        $options = Compress_Settings::get_compress_settings();

        try {

            if ( $this->is_file_compressed( $post_id ) ) {
                return array(
                    'error'   => false,
                    'message' => __( 'The file is already compressed.', 'ilove-pdf' ),
                );
            }

            $this->set_status_in_process( $post_id, $this->db_key_status );

            if ( ! isset( $options[ Compress_Settings::get_field_compress_active() ] ) ) {
                throw new Exception( _x( 'The compress tool is not activated.', 'Compress PDF: Error message.', 'ilove-pdf' ) );
            }

            if ( get_post_mime_type( $post_id ) !== 'application/pdf' ) {
                throw new Exception( _x( 'The file is not a PDF.', 'Compress PDF: Error message.', 'ilove-pdf' ) );
            }

            /** File System. @var \WP_Filesystem_Base $wp_filesystem */
            global $wp_filesystem;

            if ( ! WP_Filesystem() ) {
                throw new Exception(
                    esc_html__( 'Unable to connect to the filesystem', 'ilove-pdf' )
                );
            }

            $public_key  = User_Account::get_settings( User_Account::get_db_user_publickey_key(), '' );
            $private_key = User_Account::get_settings( User_Account::get_db_user_privatekey_key(), '' );

            if ( empty( $public_key ) || empty( $private_key ) ) {
                throw new AuthException(
                    _x( 'The API Keys are not set. Please check your settings.', 'Auth: Error message.', 'ilove-pdf' ),
                );
            }

            $attachment_file   = get_attached_file( $post_id );
            $compression_level = 'recommended';

            Backup::add_file( $post_id, $attachment_file );

            $main_task = new CompressTask( $public_key, $private_key );

            $main_task->addFile( $attachment_file );

            if ( isset( $options[ Compress_Settings::get_field_compression_level() ] ) ) {
                $compression_level = $options[ Compress_Settings::get_field_compression_level() ];
            }

            $main_task->setCompressionLevel( $compression_level );
            $main_task->execute();

            $tmp_folder = File_System::get_full_path_tmp_compress_folder();

            if ( ! $wp_filesystem->exists( $tmp_folder ) ) {
                File_System::create_dir( $tmp_folder );
            }

            // and finally download file. If no path is set, it will be downloaded on current folder.
            $main_task->download( $tmp_folder );

            $compressed_file = $tmp_folder . basename( $attachment_file );

            if ( ! $wp_filesystem->exists( $compressed_file ) ) {
                throw new Exception( __( 'The compressed file was not found.', 'ilove-pdf' ) );
            }

            $compressed_size = filesize( $compressed_file );
            $original_size   = filesize( $attachment_file );

            $compress_process_data = array(
                'original_size'   => $original_size,
                'compressed_size' => $compressed_size,
            );

            update_post_meta( $post_id, $this->db_key_process, $compress_process_data );

            $original_metadata             = wp_get_attachment_metadata( $post_id );
            $original_metadata['filesize'] = $compressed_size;
            wp_update_attachment_metadata( $post_id, $original_metadata );

            $wp_filesystem->move( $compressed_file, $attachment_file, true );

            $this->set_status_ready( $post_id, $this->db_key_status );

            return array(
                'error'   => false,
                'message' => __( 'The file was compressed successfully.', 'ilove-pdf' ),
                'data'    => array(
                    'percentage' => self::get_compressed_reabable_percentage( $original_size, $compressed_size ),
                ),
            );

        } catch ( Exception $e ) {
            $this->set_status_error( $post_id, $this->db_key_status );
            throw new Exception( $e->getMessage() );
        }
	}

    /**
     * Check if the file is already compressed.
     *
     * @since 3.0.0 now uses post meta to track compression status.
     * @since 1.0.0
     * @param int $file_id The ID of the file to check.
     * @return bool True if the file is compressed, false otherwise.
     */
    public static function is_file_compressed( $file_id ) {

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
     * Get the readable compressed percentage.
     *
     * @since 3.0.0
     * @param int $original The original file size.
     * @param int $compressed The compressed file size.
     * @return string Readable percentage of compression.
     */
    public static function get_compressed_reabable_percentage( $original, $compressed ) {
        if ( $original === $compressed ) {
            return sprintf(
                _x( 'Compressed (%d%%)', 'Compress PDF: Compressed percentage.', 'ilove-pdf' ),
                0,
            );
        }

        if ( 0 === $original ) {
            return '';
        }

        $percentage = ( $original - $compressed ) / $original * 100;
        $percentage = ( $percentage > 100 ) ? 100 : number_format( $percentage, 2 );

        return sprintf(
            _x( 'Compressed (-%1$s%%)', 'Compress PDF: Compressed percentage.', 'ilove-pdf' ),
            $percentage,
        );
    }

    /**
     * Migrate metadata from legacy keys to the new format.
     *
     * This method checks for existing metadata using the legacy keys and updates them to the new format.
     * It also deletes the old metadata keys after migration.
     *
     * @since 3.0.0
     */
    public static function migrate_metadata() {
        $instance = new self();

        $query_args  = array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'post_mime_type' => 'application/pdf',
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => $instance->legacy_db_key_original_size,
                    'compare' => 'EXISTS',
                ),
                array(
                    'key'     => $instance->legacy_db_key_compressed_size,
                    'compare' => 'EXISTS',
                ),
            ),
            'posts_per_page' => -1,
            'fields'         => 'ids',
        );
        $query_files = get_posts( $query_args );

        if ( empty( $query_files ) ) {
            return;
        }

        foreach ( $query_files as $file_id ) {
            $original_size   = get_post_meta( $file_id, $instance->legacy_db_key_original_size, true );
            $compressed_size = get_post_meta( $file_id, $instance->legacy_db_key_compressed_size, true );

            if ( ! empty( $original_size ) && ! empty( $compressed_size ) ) {
                $instance->set_status_ready( $file_id, $instance->db_key_status );
                update_post_meta(
                    $file_id,
                    self::get_db_key_process(),
                    array(
						'original_size'   => $original_size,
						'compressed_size' => $compressed_size,
                    )
                );

                delete_post_meta( $file_id, $instance->legacy_db_key_process );
                delete_post_meta( $file_id, $instance->legacy_db_key_original_size );
                delete_post_meta( $file_id, $instance->legacy_db_key_compressed_size );
            }
        }
    }

    /**
     * Add a bulk action for compressing PDF files.
     *
     * This method adds a custom bulk action to the media library for compressing selected PDF files.
     *
     * @since 3.0.0
     * @param array $actions Existing bulk actions.
     * @return array Modified bulk actions with the new 'ilovepdf_compress' action.
     */
    public function add_bulk_action( $actions ) {
        $actions['ilovepdf_compress'] = _x( 'Compress PDF', 'Bulk action button', 'ilove-pdf' );
        return $actions;
    }

    /**
     * Handle the bulk action for compressing PDF files.
     *
     * This method processes the selected files for compression and redirects to the media library.
     * It sets a transient with success and error messages for the bulk action.
     *
     * @since 3.0.0
     * @param string $redirect_to The URL to redirect to after processing.
     * @param string $doaction The action being performed.
     * @param array  $post_ids The IDs of the selected posts/files.
     * @return string Redirect URL.
     */
    public function handle_bulk_action( $redirect_to, $doaction, $post_ids ) {

        if ( 'ilovepdf_compress' !== $doaction ) {
            return $redirect_to;
        }

        if ( empty( $post_ids ) ) {
            return $redirect_to;
        }

		$success_items = array();
		$error_items   = array();

		foreach ( $post_ids as $id ) {
			$process = $this->compress_process( $id );

			if ( ! empty( $process['error'] ) ) {
				$error_items[] = array(
					'id'      => $id,
					'message' => $process['message'],
				);
			} else {
				$success_items[] = $process['message'];
			}
		}

		set_transient(
            'ilovepdf_bulk',
            array(
				'success' => $success_items,
				'errors'  => $error_items,
            ),
            600
        );

        wp_safe_redirect( $redirect_to );
        exit;
    }
}
