<?php

namespace Ilove_Pdf_WP;

use Ilove_Pdf_WP\Tool_Process;
use Ilove_Pdf_WP\Account\User_Account;
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

		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since 3.0.0
	 */
    public function admin_init() {

		$tool_process = new Tool_Process();
		$tool_process->init();

		$user_account = new User_Account();
		$user_account->init_hooks();

		new General_Settings();
		new Compress_Settings();
		new Watermark_Settings();

        // Enqueue scripts for the admin area.
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_resources' ) );
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

		if ( (
			'upload.php' === $pagenow ||
			'toplevel_page_ilovepdf-admin-page' === $hook_suffix ||
			'ilovepdf_page_ipdf-compress-admin-page' === $hook_suffix ||
			'ilovepdf_page_ipdf-watermark-admin-page' === $hook_suffix ||
			'media-new.php' === $pagenow ||
			'post.php' === $pagenow
			) && get_current_screen()->post_type !== 'product' ) {

			wp_enqueue_style( $css_key_name, plugins_url( '/assets/build/main.css', __DIR__ ), array(), $asset_file['version'], 'all' );
			wp_style_add_data( $css_key_name, 'rtl', true ); // RTL stylesheet is available.

			wp_enqueue_media();
			wp_enqueue_script( $js_key_name, plugins_url( '/assets/build/main.min.js', __DIR__ ), array_merge( $asset_file['dependencies'], array() ), $asset_file['version'], true );
			wp_set_script_translations( $js_key_name, 'ilove-pdf', plugin_dir_path( __DIR__ ) . 'languages' );
		}
	}
}
