<?php

namespace Ilove_Pdf_WP\Tools;

use Exception;
use Ilove_Pdf_WP\Helpers\File_System;
use Ilove_Pdf_WP\Helpers\DB_Handler;
use Ilove_Pdf_WP\Helpers\Media_Handler;
use Ilove_Pdf_WP\Tools\General\Settings as General_Settings;
use Ilove_Pdf_WP\Tools\Compress\Tool_Compress;

/**
 * Handles backup and restoration of PDF files in the WordPress media library.
 *
 * @package Ilove_Pdf_WP\Tools
 * @since 3.0.0
 */
class Backup {
    /**
     * Post meta key used to store individual attachment backups.
     *
     * @var string
     * @since 3.0.0
     */
    private $db_key_file_backup = '_ipdf_attachment_backup';

    /**
     * Option key used to store all attachments that have a backup available.
     *
     * @var string
     * @since 3.0.0
     */
    private $db_key_all_files_backup = 'ilovepdf_files_to_restore';

    /**
     * Legacy post meta key used to store individual attachment backups.
     *
     * This is used for migration purposes to ensure compatibility with older versions.
     *
     * @var string
     * @since 3.0.0
     */
    private $legacy_db_key_file_backup = '_wp_attached_file_backup';

    /**
     * Initializes AJAX actions related to file processing.
     *
     * @since 3.0.0
     * @return void
     */
    public function __construct() {
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
     * TODO: revisar si la metadata del archivo restaurado se actualiza correctamente.
     *
     * @since 3.0.0
     * @return void
     */
    public function restore_file() {
        /** @var \WP_Filesystem_Base $wp_filesystem */
        global $wp_filesystem;

        try {
            if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'ilovepdf_restore_file' ) ) {
				wp_send_json_error( __( 'Error processing your request. Invalid Nonce code', 'ilove-pdf' ), 401 );
			}

            if ( ! WP_Filesystem() ) {
                throw new Exception(
                    esc_html__( 'Unable to connect to the filesystem', 'ilove-pdf' )
                );
			}

			if ( ! isset( $_POST['post_id'] ) ) {
				wp_send_json_error( __( 'Error processing your request. The file ID must be sent', 'ilove-pdf' ), 400 );
			}

			$attachment_id = intval( $_POST['post_id'] );
			$files_restore = get_option( $this->db_key_all_files_backup, array() );
			$key_founded   = array_search( $attachment_id, $files_restore, true );

			if ( ! in_array( $attachment_id, $files_restore, true ) ) {
				wp_send_json_error( __( 'Sorry. There is no backup for this file', 'ilove-pdf' ), 404 );
			}

			$attached_file    = get_attached_file( $attachment_id );
			$file_name        = basename( $attached_file );
			$file_backup_path = File_System::get_full_path_backup_folder() . $file_name;

			$wp_filesystem->copy( $file_backup_path, $attached_file, true );

			Media_Handler::regenerate_attachment_data( $attachment_id );

			delete_post_meta( $attachment_id, '_ipdf_attachment_watermark_status' );
			delete_post_meta( $attachment_id, Tool_Compress::get_db_key_status() );
			delete_post_meta( $attachment_id, Tool_Compress::get_db_key_process() );
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
        } catch ( Exception $e ) {
            wp_send_json_error(
                sprintf(
                    __( 'Error restoring file: %s', 'ilove-pdf' ),
                    $e->getMessage(),
                ),
                $e->getCode(),
            );
        }
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

        try {
			if ( ! WP_Filesystem() ) {
                throw new Exception(
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
				delete_post_meta( $value, Tool_Compress::get_db_key_status() );
				delete_post_meta( $value, Tool_Compress::get_db_key_process() );
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

        } catch ( Exception $e ) {
            wp_send_json_error( $e->getMessage(), $e->getCode() );
        }
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
                throw new Exception(
                    esc_html__( 'Unable to connect to the filesystem', 'ilove-pdf' )
                );
            }

            if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) ) ) {
                wp_send_json_error( __( 'Error processing your request. Invalid Nonce code', 'ilove-pdf' ), 401 );
            }

            if ( ! $wp_filesystem->exists( File_System::get_full_path_backup_folder() ) ) {
                wp_send_json_error( __( 'Sorry. No backup folder found.', 'ilove-pdf' ), 404 );
            }

            $files_backup = get_option( $this->db_key_all_files_backup, array() );

            if ( ! empty( $files_backup ) ) {
                foreach ( $files_backup as $file_id ) {
                    $attached_file = get_attached_file( $file_id );

                    if ( $attached_file ) {
                        delete_post_meta( $file_id, $this->db_key_file_backup );
                    }
                }
            }

            $wp_filesystem->rmdir( File_System::get_full_path_backup_folder(), true );
            delete_option( $this->db_key_all_files_backup );

            wp_send_json_success( __( 'Backup folder deleted successfully', 'ilove-pdf' ), 200 );

        } catch ( Exception $e ) {
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
                set_transient(
                    'ilovepdf_notices',
                    array(
                        'errors' => array(
                            array(
                                'message' => esc_html__( 'Unable to connect to the filesystem', 'ilove-pdf' ),
                            ),
                        ),
                    )
                );

                return;
            }

            $file_name     = basename( get_attached_file( $attachment_id ) );
            $files_restore = get_option( $this->db_key_all_files_backup, array() );
            $key_founded   = array_search( $attachment_id, $files_restore, true );

            delete_post_meta( $attachment_id, $this->db_key_file_backup );
            delete_post_meta( $attachment_id, Tool_Compress::get_db_key_status() );
            delete_post_meta( $attachment_id, Tool_Compress::get_db_key_process() );
            delete_post_meta( $attachment_id, '_ipdf_attachment_watermark_status' );

            if ( $wp_filesystem->exists( File_System::get_full_path_backup_folder() . $file_name ) ) {
                wp_delete_file( File_System::get_full_path_backup_folder() . $file_name );

                if ( $key_founded ) {
                    unset( $files_restore[ $key_founded ] );
                    DB_Handler::update_option( $this->db_key_all_files_backup, $files_restore );
                }
            }
        }
    }

    /**
     * Adds a file to the backup list and creates a backup copy if the backup feature is enabled.
     *
     * @since 3.0.0
     * @param int    $file_id The ID of the file to be backed up.
     * @param string $file_path The path to the file to be backed up.
     * @throws \Error If unable to connect to the filesystem or if backup creation fails.
     */
    public static function add_file( $file_id, $file_path ) {
        /** @var \WP_Filesystem_Base $wp_filesystem */
        global $wp_filesystem;

        $is_backup_activated = General_Settings::get_general_settings( General_Settings::get_field_backup() );

        if ( $is_backup_activated && ! self::is_file_backup( $file_id ) ) {
            if ( ! WP_Filesystem() ) {
                throw new Exception(
                    esc_html__( 'Unable to connect to the filesystem', 'ilove-pdf' )
                );
            }

            $instance      = new self();
            $files_restore = get_option( $instance->db_key_all_files_backup, array() );
            $backup_folder = File_System::get_full_path_backup_folder();

            if ( ! $wp_filesystem->exists( $backup_folder ) ) {
                File_System::create_dir( $backup_folder );
            }

            $backup_file = $backup_folder . basename( $file_path );

            if ( ! $wp_filesystem->copy( $file_path, $backup_file ) ) {
                throw new Exception( __( 'Failed to create a backup of the file.', 'ilove-pdf' ) );
            }

            if ( ! in_array( $file_id, $files_restore, true ) ) {
                $files_restore[] = $file_id;
                DB_Handler::update_option( $instance->db_key_all_files_backup, $files_restore );
            }

            update_post_meta(
                $file_id,
                $instance->db_key_file_backup,
                $file_path,
            );
        }
    }

    /**
     * Checks if a file has a backup available.
     *
     * @since 3.0.0
     * @param int $file_id The ID of the file to check.
     * @return bool True if the file has a backup, false otherwise.
     */
    public static function is_file_backup( $file_id ) {
        /** @var \WP_Filesystem_Base $wp_filesystem */
        global $wp_filesystem;

        if ( ! WP_Filesystem() ) {
            throw new Exception(
                esc_html__( 'Unable to connect to the filesystem', 'ilove-pdf' )
            );
        }

        $file_path = get_post_meta( $file_id, '_ipdf_attachment_backup', true );
        if ( empty( $file_path ) ) {
            return false;
        }

        $backup_folder = File_System::get_full_path_backup_folder();
        $backup_file   = $backup_folder . basename( $file_path );

        return $wp_filesystem->exists( $backup_file );
    }

    /**
     * Migrates existing file backups from the legacy post meta key to the new option.
     *
     * This method checks for files with the legacy backup key and updates them to the new format.
     * It also ensures that the backup folder exists before proceeding with the migration.
     *
     * @since 3.0.0
     * @throws \Exception If unable to connect to the filesystem.
     */
    public static function migrate_file_backup() {
        /** @var \WP_Filesystem_Base $wp_filesystem */
        global $wp_filesystem;

        if ( ! WP_Filesystem() ) {

            set_transient(
                'ilovepdf_notices',
                array(
                    'errors' => array(
                        array(
                            'message' => esc_html__( 'Unable to connect to the filesystem', 'ilove-pdf' ),
                        ),
                    ),
                )
            );

            return;
        }

        $instance      = new self();
        $files_restore = get_option( $instance->db_key_all_files_backup, array() );

        $query_args = array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'application/pdf',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => $instance->legacy_db_key_file_backup,
                    'compare' => 'EXISTS',
                ),
            ),
            'fields'         => 'ids',
        );

        $attachments = get_posts( $query_args );
        if ( empty( $attachments ) ) {
            return;
        }

        if ( ! $wp_filesystem->exists( File_System::get_full_path_backup_folder() ) ) {
            File_System::create_dir( File_System::get_full_path_backup_folder() );
        }

        // Migrate each attachment's backup file.
        foreach ( $attachments as $post_id ) {
            $file_path = get_post_meta( $post_id, $instance->legacy_db_key_file_backup, true );

            if ( empty( $file_path ) ) {
                continue;
            }

            $file_name   = basename( $file_path );
            $backup_file = File_System::get_full_path_backup_folder() . $file_name;
            if ( ! $wp_filesystem->exists( $backup_file ) ) {
                continue;
            }

            if ( ! in_array( $post_id, $files_restore, true ) ) {
                $files_restore[] = $post_id;
                DB_Handler::update_option( $instance->db_key_all_files_backup, $files_restore );
            }

            update_post_meta( $post_id, $instance->db_key_file_backup, $file_path ); // Update the post meta to use the new key.
            delete_post_meta( $post_id, $instance->legacy_db_key_file_backup ); // Remove the old post meta.
        }
    }
}
