<?php

namespace Ilove_Pdf_WP;

use Ilove_Pdf_WP\Helpers\DB_Handler;
use Ilove_Pdf_WP\Helpers\Media_Handler;

/**
 * Handles file restoration, backup clearing, and AJAX processes for the iLovePDF plugin.
 *
 * @package Ilove_Pdf_WP
 * @since 3.0.0
 */
class Tool_Process {
    /**
     * Post meta key used to store individual attachment backups.
     *
     * @var string
     * @since 3.0.0
     */
    public $db_key_file_backup = '_ipdf_attachment_backup';


    /**
     * Option key used to store all attachments that have a backup available.
     *
     * @var string
     * @since 3.0.0
     */
    public $db_key_all_files_backup = 'ilovepdf_files_to_restore';

    /**
     * Initializes AJAX actions related to file processing.
     *
     * @since 3.0.0
     * @return void
     */
    public function init() {
        add_action( 'wp_ajax_ilovepdf_restore_file', array( $this, 'restore_file' ) );
        add_action( 'wp_ajax_ilovepdf_restore_all_files', array( $this, 'restore_all' ) );
        add_action( 'wp_ajax_ilovepdf_clear_backup', array( $this, 'clear_backup' ) );

        add_filter( 'delete_attachment', array( $this, 'handle_delete_file' ) );
    }

    /**
     * Restores a single file from the backup directory.
     *
     * Validates nonce and required POST data, then restores the file,
     * updates media metadata, and cleans related post meta and backup references.
     *
     * @since 3.0.0
     * @return void
     */
    public function restore_file() {
        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) ) ) {
            wp_send_json_error( __( 'Error processing your request. Invalid Nonce code', 'ilove-pdf' ), 401 );
        }

        if ( ! isset( $_POST['id'] ) ) {
            wp_send_json_error( __( 'Error processing your request. The file ID must be sent', 'ilove-pdf' ), 400 );
        }

        $attachment_id = intval( $_POST['id'] );
        $files_restore = get_option( $this->db_key_all_files_backup, array() );
        $key_founded   = array_search( $attachment_id, $files_restore, true );

        if ( ! in_array( $attachment_id, $files_restore, true ) ) {
            wp_send_json_error( __( 'Sorry. There is no backup for this file', 'ilove-pdf' ), 404 );
        }

        $attached_file    = get_attached_file( $attachment_id );
        $file_name        = basename( $attached_file );
        $file_backup_path = File_System::get_full_path_backup_folder() . $file_name;

        copy( $file_backup_path, $attached_file );

        Media_Handler::regenerate_attachment_data( $attachment_id );

        delete_post_meta( $attachment_id, '_ipdf_attachment_watermark_status' );
        delete_post_meta( $attachment_id, '_ipdf_attachment_compress_status' );
        delete_post_meta( $attachment_id, '_ipdf_attachment_compress_process' );
        delete_post_meta( $attachment_id, $this->db_key_file_backup );

        if ( false !== $key_founded ) {
            unset( $files_restore[ $key_founded ] );
            wp_delete_file( $file_backup_path );
            DB_Handler::update_option( $this->db_key_all_files_backup, $files_restore );
        }

        wp_send_json_success(
            sprintf(
                __( 'The %1$s file was restored successfully', 'ilove-pdf' ),
                $file_name
            ),
            200
        );
    }

    /**
     * Restores all files listed in the backup directory.
     *
     * Iterates through all saved file IDs, restores each if possible,
     * and returns a success or error JSON response.
     *
     * @since 3.0.0
     * @throws \Error If unable to connect to the filesystem.
     */
    public function restore_all() {

        /** @var \WP_Filesystem_Base $wp_filesystem */
        global $wp_filesystem;

        if ( ! WP_Filesystem() ) {
            return new \WP_Error(
                'Unable Filesystem',
                esc_html__( 'Unable to connect to the filesystem', 'ilove-pdf' )
            );
        }

        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) ) ) {
            wp_send_json_error( __( 'Error processing your request. Invalid Nonce code', 'ilove-pdf' ), 401 );
        }

        if ( ! $wp_filesystem->exists( File_System::get_full_path_backup_folder() ) ) {
            wp_send_json_error( __( 'Sorry. No backup folder found.', 'ilove-pdf' ), 404 );
        }

        $files_restore = get_option( $this->db_key_all_files_backup, array() );

        if ( empty( $files_restore ) ) {
            wp_send_json_error( __( 'Sorry. No files found to restore.', 'ilove-pdf' ), 404 );
        }

        $files_restored = array();
        $files_errors   = array();

        foreach ( $files_restore as $key => $value ) {

            $attached_file = get_attached_file( $value );

            if ( ! $attached_file ) {
                $files_errors[] = array(
                    'id'      => $value,
                    'message' => sprintf(
                        __( 'The original file ID %1$s was not found', 'ilove-pdf' ),
                        $value
                    ),
                );

                continue;
            }

            $file_name        = basename( $attached_file );
            $file_backup_path = File_System::get_full_path_backup_folder() . $file_name;

            if ( ! file_exists( $file_backup_path ) ) {
                $files_errors[] = array(
                    'id'      => $value,
                    'message' => sprintf(
                        __( 'The backup file ID %1$s was not found', 'ilove-pdf' ),
                        $value
                    ),
                );

                continue;
            }

            copy( $file_backup_path, $attached_file );

            Media_Handler::regenerate_attachment_data( $value );

            delete_post_meta( $value, '_ipdf_attachment_watermark_status' );
            delete_post_meta( $value, '_ipdf_attachment_compress_status' );
            delete_post_meta( $value, '_ipdf_attachment_compress_process' );
            delete_post_meta( $value, $this->db_key_file_backup );

            wp_delete_file( $file_backup_path );
            unset( $files_restore[ $key ] );
            DB_Handler::update_option( $this->db_key_all_files_backup, $files_restore );

            $files_restored[] = $file_name;
        }

        if ( empty( $files_restored ) && ! empty( $files_errors ) ) {
            wp_send_json_error(
                array(
					'data'   => array(
						'files_errors' => $files_errors,
					),
					'succes' => false,
                ),
                404
            );
        }

        wp_send_json(
            array(
                'data'    => array(
                    'files_restored' => sprintf(
                        __( 'The %1$s file was restored successfully', 'ilove-pdf' ),
                        implode( ', ', $files_restored )
                    ),
                    'files_errors'   => count( $files_errors ) ? $files_errors : false,
                ),
                'success' => true,
            ),
            200
        );
    }

    /**
     * Clears the entire backup directory and its corresponding database entries.
     *
     * @since 3.0.0
     * @throws \Error If unable to connect to the filesystem.
     */
    public function clear_backup() {

        try {
            /** @var \WP_Filesystem_Base $wp_filesystem */
            global $wp_filesystem;

            if ( ! WP_Filesystem() ) {
                return new \WP_Error(
                    'Unable Filesystem',
                    esc_html__( 'Unable to connect to the filesystem', 'ilove-pdf' )
                );
            }

            if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) ) ) {
                wp_send_json_error( __( 'Error processing your request. Invalid Nonce code', 'ilove-pdf' ), 401 );
            }

            if ( ! $wp_filesystem->exists( File_System::get_full_path_backup_folder() ) ) {
                wp_send_json_error( __( 'Sorry. No backup folder found.', 'ilove-pdf' ), 404 );
            }

            $wp_filesystem->rmdir( File_System::get_full_path_backup_folder(), true );
            delete_option( $this->db_key_all_files_backup );

            wp_send_json_success( __( 'Backup folder deleted successfully', 'ilove-pdf' ), 200 );

        } catch ( \Error $e ) {
            wp_send_json_error( $e->getMessage(), $e->getCode() );
        }
    }

    /**
     * Handles cleanup when a PDF attachment is deleted from the Media Library.
     *
     * If a matching backup file exists, it is removed from the backup folder.
     * Also clears related post meta and updates the backup list if needed.
     *
     * @since 1.0.0
     * @param int $attachment_id The ID of the attachment being deleted.
     * @throws \Error If unable to connect to the filesystem.
     */
    public function handle_delete_file( $attachment_id ) {
        if ( get_post_mime_type( $attachment_id ) === 'application/pdf' ) {

            /** @var \WP_Filesystem_Base $wp_filesystem */
            global $wp_filesystem;

            if ( ! WP_Filesystem() ) {
                return new \WP_Error(
                    'Unable Filesystem',
                    esc_html__( 'Unable to connect to the filesystem', 'ilove-pdf' )
                );
            }

            $file_name     = basename( get_attached_file( $attachment_id ) );
            $files_restore = get_option( $this->db_key_all_files_backup, array() );
            $key_founded   = array_search( $attachment_id, $files_restore, true );

            delete_option( '_ipdf_attachment_compress_status' );
            delete_option( '_ipdf_attachment_compress_process' );
            delete_option( '_ipdf_attachment_watermark_status' );

            if ( $wp_filesystem->exists( File_System::get_full_path_backup_folder() . $file_name ) ) {
                wp_delete_file( File_System::get_full_path_backup_folder() . $file_name );

                if ( $key_founded ) {
                    unset( $files_restore[ $key_founded ] );
                    DB_Handler::update_option( $this->db_key_all_files_backup, $files_restore );
                }
            }
        }
    }
}
