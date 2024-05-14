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

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ilove_Pdf_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ilove_Pdf_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $pagenow;

		if ( ( 'upload.php' === $pagenow || 'options-general.php' === $pagenow || 'media-new.php' === $pagenow || 'post.php' === $pagenow ) && get_current_screen()->post_type !== 'product' ) {
			wp_enqueue_style( $this->plugin_name, plugins_url( '/assets/css/app.min.css', __DIR__ ), array(), $this->version, 'all' );
		}

		wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ilove_Pdf_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ilove_Pdf_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Add the color picker css file
		wp_enqueue_style( 'wp-color-picker' );

		global $pagenow;

		if ( ( 'upload.php' === $pagenow || 'options-general.php' === $pagenow || 'media-new.php' === $pagenow || 'post.php' === $pagenow ) && get_current_screen()->post_type !== 'product' ) {
			wp_enqueue_script( 'ilove-pdf-admin', plugins_url( '/assets/js/main.min.js', __DIR__ ), array( 'wp-color-picker', 'sweetalert-js-ilovepdf' ), '1.0.0', true );
			wp_enqueue_script( 'sweetalert-js-ilovepdf', plugins_url( '/assets/js/sweetalert2.all.min.js', __DIR__ ), array(), '11.11.0', true );
		}
	}

	/**
	 * Add Link to page settings from Plugins List Page.
	 *
	 * @since    2.1.0
	 *
	 * @param    array $actions    An array of plugin action links.
	 */
	public function add_action_links( $actions ) {
		$custom_links = array(
			'<a href="' . admin_url( 'options-general.php?page=ilove-pdf-content-setting' ) . '">Settings</a>',
		);
		$actions      = array_merge( $actions, $custom_links );

		return $actions;
	}
}
