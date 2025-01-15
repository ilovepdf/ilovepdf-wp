<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ilovepdf.com/
 * @since      1.0.0
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/includes
 */

namespace Ilove_Pdf_Includes;

use Ilove_Pdf_Admin\Ilove_Pdf_Admin;
use Ilove_Pdf_Includes\Ilove_Pdf_Loader;
use Ilove_Pdf_Includes\Ilove_Pdf_I18n;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/includes
 * @author     ILovePDF <info@ilovepdf.com>
 */
class Ilove_Pdf {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ilove_Pdf_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'ilove-pdf';
		$this->version     = 'wp.2.1.7';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ilove_Pdf_Loader. Orchestrates the hooks of the plugin.
	 * - Ilove_Pdf_i18n. Defines internationalization functionality.
	 * - Ilove_Pdf_Admin. Defines all hooks for the admin area.
	 * - Ilove_Pdf_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */

		$this->loader = new Ilove_Pdf_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ilove_Pdf_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ilove_Pdf_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ilove_Pdf_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'plugin_action_links_' . ILOVE_PDF_PLUGIN_NAME, $plugin_admin, 'add_action_links' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ilove_Pdf_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Update option, works with multisite if enabled
	 *
	 * @since  2.1.5
	 * @param  string    $option Name of the option to update. Expected to not be SQL-escaped.
	 * @param  mixed     $value Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
	 * @param  bool      $update_all_sites Optional. Whether to update all sites in the network.
	 * @param  bool|null $autoload Optional. Whether to load the option when WordPress starts up. Accepts a boolean, or null.
	 */
	public static function update_option( $option, $value, $update_all_sites = false, $autoload = null ) {

		if ( ! is_multisite() ) {
			update_option( $option, $value, $autoload );
			return;
		}

        if ( ! $update_all_sites ) {
            self::switch_update_blog( get_current_blog_id(), $option, $value, $autoload );
            return;
        }

        $sites = get_sites();
        foreach ( $sites as $site ) {
            self::switch_update_blog( (int) $site->blog_id, $option, $value, $autoload );
        }
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
     * Switch to blog and update option
     *
     * @since  2.1.6
     * @param  int       $blog_id ID of the blog to switch to.
     * @param  string    $option Name of the option to update.
     * @param  mixed     $value Option value.
     * @param  bool|null $autoload Whether to load the option when WordPress starts up.
     */
    private static function switch_update_blog( $blog_id, $option, $value, $autoload ) {
        switch_to_blog( $blog_id );
        update_option( $option, $value, $autoload );
        restore_current_blog();
    }
}
