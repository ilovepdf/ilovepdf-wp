<?php

namespace Ilove_Pdf_WP\Helpers;

/**
 * File system management.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP
 */
class File_System {
	/**
	 * Folder backup.
	 *
	 * @since 3.0.0
	 * @var string $folder_backup Path to backup folder.
	 */
	public static $folder_backup = '/ilovepdf/backup';

	/**
	 * Temp folder for compress.
	 *
	 * @since 3.0.0
	 * @var string $folder_tmp_compress Path to temporary compress folder
	 */
	public static $folder_tmp_compress = '/ilovepdf/tmp_compress';

	/**
	 * Temp folder for watermark.
	 *
	 * @since 3.0.0
	 * @var string $folder_tmp_watermark Path to temporary watermark folder
	 */
	public static $folder_tmp_watermark = '/ilovepdf/tmp_watermark';

	/**
	 * Legacy directories.
	 *
	 * @since 3.0.0 The pdf/backup, /compress, and /watermark directories have been deprecated.
	 * @since 1.0.0
	 * @var array $legacy_directories
	 */
	public static $legacy_directories = array( '/pdf/backup', '/pdf/compress', '/pdf/watermark' );

	/**
	 * Get the full path to the backup folder.
	 *
	 * @since  3.0.0
	 * @return string
	 */
	public static function get_full_path_backup_folder() {
		$wp_upload_dir = wp_upload_dir();
		return $wp_upload_dir['basedir'] . self::$folder_backup . '/';
	}

	/**
	 * Get the full path to the temporary compress folder.
	 *
	 * @since  3.0.0
	 * @return string
	 */
	public static function get_full_path_tmp_compress_folder() {
		$wp_upload_dir = wp_upload_dir();
		return $wp_upload_dir['basedir'] . self::$folder_tmp_compress . '/';
	}

	/**
	 * Get the full path to the temporary watermark folder.
	 *
	 * @since  3.0.0
	 * @return string
	 */
	public static function get_full_path_tmp_watermark_folder() {
		$wp_upload_dir = wp_upload_dir();
		return $wp_upload_dir['basedir'] . self::$folder_tmp_watermark . '/';
	}

    /**
	 * Create directories, works with multisite if enabled
	 *
	 * @since  2.1.5
	 * @param  array|string $directories  The directories to create.
	 */
	public static function create_dir( $directories ) {

		if ( ! is_array( $directories ) ) {
			$directories = array( $directories );
		}

		if ( ! is_multisite() ) {
			foreach ( $directories as $directory ) {
				$upload_dir = wp_upload_dir();
				$directory  = $upload_dir['basedir'] . $directory;

				if ( ! file_exists( $directory ) ) {
					wp_mkdir_p( $directory );
				}
			}
			return;
		}

		$sites = get_sites();
        foreach ( $sites as $site ) {
            switch_to_blog( (int) $site->blog_id );

			foreach ( $directories as $directory ) {
				$upload_dir = wp_upload_dir();
				$directory  = $upload_dir['basedir'] . $directory;

				if ( ! file_exists( $directory ) ) {
					wp_mkdir_p( $directory );
				}
			}

            restore_current_blog();
        }
	}

	/**
	 * Create iLovePDF directories.
	 *
	 * Creates backup and temporary directories used by the tools.
	 *
	 * @since 3.0.0
	 */
	public static function create_ilovepdf_directories() {
		self::create_dir(
            array(
				self::$folder_backup,
				self::$folder_tmp_compress,
				self::$folder_tmp_watermark,
            )
        );
	}

	/**
	 * Get the URL to the plugin assets.
	 *
	 * @since  3.0.0
	 * @param  string $path  The path to append to the assets directory.
	 *
	 * @return string
	 */
	public static function get_assets_url( $path = '' ) {
		$plugin_dir_name = basename( dirname( __DIR__, 2 ) );
		return plugins_url( $plugin_dir_name . '/assets/' . $path );
	}

	/**
     * Get the size of the backup folder.
     *
     * @since 3.0.0
     * @return string|int Number string on success, 0 on failure.
     */
    public static function get_size_backup() {
		if ( ! file_exists( self::get_full_path_backup_folder() ) ) {
			return 0;
		}

		if ( get_transient( 'dirsize_cache' ) ) {
			delete_transient( 'dirsize_cache' );
		}

        $size_bytes_folder = get_dirsize( self::get_full_path_backup_folder() );

		return $size_bytes_folder > 0 ? size_format( $size_bytes_folder, 2 ) : 0;
    }

	/**
     * Migrates files from legacy directories to the new backup folder.
     *
     * This method checks for deprecated directories (`pdf/backup`, `pdf/compress`, `pdf/watermark`),
     * moves their contents to the new backup directory (`/ilovepdf/backup`), and removes the old directories.
     * Also removes the parent `/pdf` directory if it becomes empty.
     *
     * @since 3.0.0
     * @return void
     */
	public static function migrate_legacy_directories() {
		/** Filesystem @var \WP_Filesystem_Base $wp_filesystem */
		global $wp_filesystem;

		if ( ! WP_Filesystem() ) {

            Admin_Notice::add_notice(
                esc_html_x( 'Unable to connect to the filesystem', '', 'ilove-pdf' ),
                'error',
            );

            return;
        }

		$upload_dir = wp_upload_dir();

		foreach ( self::$legacy_directories as $directory ) {
			$directory = $upload_dir['basedir'] . $directory;

			if ( file_exists( $directory ) ) {
				$files = glob( $directory . '/*' );

				foreach ( $files as $file ) {
					$filename    = basename( $file );
					$destination = $upload_dir['basedir'] . '/' . self::$folder_backup . '/' . $filename;
					$wp_filesystem->move( $file, $destination );
				}

				$wp_filesystem->rmdir( $directory );
			}
		}

		if ( file_exists( $upload_dir['basedir'] . '/pdf' ) ) {
			$wp_filesystem->rmdir( $upload_dir['basedir'] . '/pdf' );
		}
	}
}
