<?php

namespace Ilove_Pdf_WP;

use Ilove_Pdf_WP\I18n;
use Ilove_Pdf_WP\Submenu_Page;
use Ilove_Pdf_WP\Tools\Backup;
use Ilove_Pdf_WP\Media\Library;
use Ilove_Pdf_WP\Account\User_Auth;
use Ilove_Pdf_WP\Account\User_Data;
use Ilove_Pdf_WP\Helpers\File_System;
use Ilove_Pdf_WP\Helpers\Admin_Notice;
use Ilove_Pdf_WP\Helpers\DB_Handler;
use Ilove_Pdf_WP\Media\Edit_File_Page;
use Ilove_Pdf_WP\Tools\Compress\Tool_Compress;
use Ilove_Pdf_WP\Tools\Watermark\Tool_Watermark;
use Ilove_Pdf_WP\Tools\General\Settings as General_Settings;
use Ilove_Pdf_WP\Tools\Compress\Settings as Compress_Settings;
use Ilove_Pdf_WP\Tools\Watermark\Settings as Watermark_Settings;

/**
 * The main functionality of the plugin.
 *
 * @package Ilove_Pdf_WP
 * @author  ILovePDF <info@ilovepdf.com>
 */
class Ilove_Pdf_Plugin {

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @var   string $plugin_name The ID of this plugin.
	 */
	private $plugin_name = 'ilove-pdf';

	/**
	 * The plugin file basename.
	 *
	 * @since 3.0.0
	 * @var string $plugin_file_basename The plugin file basename e.g plugin_dir/main-file.php.
	 */
	protected static $plugin_file_basename;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @var   string $version The current version of this plugin.
	 */
	public $version;

	/**
	 * The database key for user migration.
	 *
	 * @since 3.0.0
	 * @var   string
	 */
	private static $db_key_user_migration = 'ilovepdf_user_migration';

	/**
	 * Init Plugin
	 *
	 * Fires the corresponding hooks.
	 * Sets the plugin version.
	 *
	 * @since 3.0.0
	 * @param string $version The current version of the plugin.
	 * @param string $file_basename The plugin file basename.
	 */
	public function __construct( $version, $file_basename ) {
		$this->version              = $version;
		self::$plugin_file_basename = $file_basename;

		new Submenu_Page();

		add_action( 'plugins_loaded', array( I18n::class, 'load_textdomain' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since 3.0.0
	 */
    public function admin_init() {

		new Admin_Notice();

		new Backup();
		new User_Auth();
		new Library();

		new General_Settings();
		new Compress_Settings();
		new Watermark_Settings();
		new Tool_Compress();
		new Tool_Watermark();
		new Edit_File_Page();

		$this->migrate_settings();

        // Enqueue scripts for the admin area.
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_resources' ) );
		add_action( 'admin_footer', array( User_Data::class, 'popup_buymore_action' ) );
    }

	/**
	 * Get Plugin Basename.
	 *
	 * @since 3.0.0
	 * @return string Plugin basename.
	 */
	public static function get_plugin_basename() {
		return self::$plugin_file_basename;
	}

	/**
	 * Register the stylesheets/scripts for the admin area.
	 *
	 * @since 3.0.0
	 */
	public function admin_enqueue_resources() {

		global $pagenow, $hook_suffix;

		$asset_file   = include plugin_dir_path( __DIR__ ) . 'assets/build/main.min.asset.php';
		$css_key_name = $this->plugin_name . '-css';
		$js_key_name  = $this->plugin_name . '-js';
		$logo_url     = File_System::get_assets_url( 'img/logo_ilovepdf.svg' );

		wp_enqueue_style( $css_key_name, plugins_url( '/assets/build/main.css', __DIR__ ), array(), $asset_file['version'], 'all' );
		wp_style_add_data( $css_key_name, 'rtl', true ); // RTL stylesheet is available.

		if ( (
			'upload.php' === $pagenow ||
			'toplevel_page_ilovepdf-admin-page' === $hook_suffix ||
			'ilovepdf_page_ipdf-compress-admin-page' === $hook_suffix ||
			'ilovepdf_page_ipdf-watermark-admin-page' === $hook_suffix ||
			'media-new.php' === $pagenow ||
			'post.php' === $pagenow
			) && get_current_screen()->post_type !== 'product' ) {

			wp_enqueue_media();
			wp_enqueue_script( $js_key_name, plugins_url( '/assets/build/main.min.js', __DIR__ ), array_merge( $asset_file['dependencies'], array() ), $asset_file['version'], true );
			wp_set_script_translations( $js_key_name, 'ilove-pdf', plugin_dir_path( __DIR__ ) . 'languages' );
			wp_add_inline_script(
				$js_key_name,
				sprintf(
					'const IlovePdfData = {
						logoUrl: "%s",
						userIsLoggued: %s,
						userHasCredits: %s,
					};',
					esc_url( $logo_url ),
					wp_json_encode( User_Auth::is_user_logged_in() ),
					wp_json_encode( User_Data::has_credits() ),
				)
			);
		}
	}

	/**
	 * Migrate settings from older versions.
	 *
	 * @since 3.0.0
	 */
	public function migrate_settings() {
		if ( DB_Handler::get_option( self::$db_key_user_migration, false ) ) {
			return;
		}

		if ( ! is_multisite() ) {
			$this->perform_migration();
			DB_Handler::update_option( self::$db_key_user_migration, true );
			return;
		}

		switch_to_blog( get_current_blog_id() );
		$this->perform_migration();
		update_option( self::$db_key_user_migration, true );
		restore_current_blog();
	}

	/**
	 * Performs the migration of settings and data.
	 *
	 * @since 3.0.0
	 */
	private function perform_migration() {
		File_System::migrate_legacy_directories();
		Backup::migrate_file_backup();

		User_Data::migrate_account_settings();

		General_Settings::migrate();
		Compress_Settings::migrate();
		Watermark_Settings::migrate();

		Tool_Compress::migrate_metadata();
		Tool_Watermark::migrate_watermark_status();
	}

	/**
	 * Uninstall the plugin.
	 *
	 * Deletes all options and data related to the plugin.
	 *
	 * @since 3.0.0
	 */
	public static function uninstall_plugin() {
		DB_Handler::delete_option( self::$db_key_user_migration );
		DB_Handler::delete_option( User_Data::get_db_key_account() );
		DB_Handler::delete_option( Backup::get_db_key_all_files_backup() );
		DB_Handler::delete_option( General_Settings::get_db_key_settings() );
		DB_Handler::delete_option( Compress_Settings::get_db_key_settings() );
		DB_Handler::delete_option( Watermark_Settings::get_db_key_settings() );
	}
}
