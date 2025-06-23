<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ilovepdf.com/
 * @since      1.0.0
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/admin
 */

namespace Ilove_Pdf_Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/admin
 * @author     ILovePDF <info@ilovepdf.com>
 */
class Ilove_Pdf_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		global $pagenow, $hook_suffix;

		if ( (
			'upload.php' === $pagenow ||
			'toplevel_page_ilovepdf-admin-page' === $hook_suffix ||
			'ilovepdf_page_ipdf-compress-admin-page' === $hook_suffix ||
			'ilovepdf_page_ipdf-watermark-admin-page' === $hook_suffix ||
			'media-new.php' === $pagenow ||
			'post.php' === $pagenow
			) && get_current_screen()->post_type !== 'product' ) {
			wp_enqueue_style( $this->plugin_name, plugins_url( '/assets/css/app.min.css', __DIR__ ), array(), $this->version, 'all' );
			wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		global $pagenow, $hook_suffix;

		// Add the color picker css file.
		wp_enqueue_style( 'wp-color-picker' );

		if ( (
			'upload.php' === $pagenow ||
			'toplevel_page_ilovepdf-admin-page' === $hook_suffix ||
			'ilovepdf_page_ipdf-compress-admin-page' === $hook_suffix ||
			'ilovepdf_page_ipdf-watermark-admin-page' === $hook_suffix ||
			'media-new.php' === $pagenow ||
			'post.php' === $pagenow
			) && get_current_screen()->post_type !== 'product' ) {
			wp_enqueue_script( 'ilove-pdf-admin', plugins_url( '/assets/js/main.min.js', __DIR__ ), array( 'wp-color-picker' ), '1.0.0', true );
		}
	}
}
