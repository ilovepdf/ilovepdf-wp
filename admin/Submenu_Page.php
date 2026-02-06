<?php

namespace Ilove_Pdf_WP;

use Exception;
use Ilove_Pdf_WP\Media\Files_List_Table;

/**
 * Managing the iLovePDF plugin's submenu and pages.
 *
 * Responsible for adding a submenu to the menu in the WordPress admin area and rendering the plugin's settings and content pages. It initializes the submenu and adds individual pages for compress settings, watermark settings, and media optimization.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP
 */
class Submenu_Page {

	/**
	 * Parent slug for the submenu page.
	 *
	 * @var string
	 * @since 3.0.0
	 */
	public static $parent_slug = 'ilovepdf-admin-page';

	/**
	 * Slug for the compress settings page.
	 *
	 * @var string
	 * @since 3.0.0
	 */
	public static $compress_slug = 'ipdf-compress-admin-page';

	/**
	 * Slug for the watermark settings page.
	 *
	 * @var string
	 * @since 3.0.0
	 */
	public static $watermark_slug = 'ipdf-watermark-admin-page';

	/**
	 * Slug for the media optimization page.
	 *
	 * @var string
	 * @since 3.0.0
	 */
	private static $media_slug = 'ipdf-media-optimization';

    /**
     * Initializing the class and adding the page menu to WordPress dashboard.
	 *
	 * @since 3.0.0
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_page_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_media_page' ) );
        add_filter( 'plugin_action_links_' . Ilove_Pdf_Plugin::get_plugin_basename(), array( $this, 'add_action_links' ) );
    }

    /**
     * Adding page menu to WordPress dashboard.
	 *
	 * @since 3.0.0
     */
    public function add_page_settings() {

        // Add page menu to WordPress dashboard.
        add_menu_page(
            _x( 'General Settings', 'Link element: This appears in the WordPress side menu.', 'ilove-pdf' ),
			'iLovePDF',
			'manage_options',
			self::$parent_slug,
			array(
                $this,
                'render_page_general_settings',
            ),
			plugins_url( 'assets/img/ilovepdf-icon-16x16.png', __DIR__ )
		);

		// Add general settings.
        add_submenu_page(
			self::$parent_slug,
			_x( 'General Settings', 'Link element: This appears in the WordPress side menu.', 'ilove-pdf' ),
			_x( 'General Settings', 'Link element: This appears in the WordPress side menu.', 'ilove-pdf' ),
			'manage_options',
			self::$parent_slug,
			array(
				$this,
				'render_page_general_settings',
			)
		);

		// Add compress settings.
        add_submenu_page(
			self::$parent_slug,
			_x( 'Compress settings', 'Link element: This appears in the WordPress side menu.', 'ilove-pdf' ),
			_x( 'Compress settings', 'Link element: This appears in the WordPress side menu.', 'ilove-pdf' ),
			'manage_options',
			self::$compress_slug,
			array(
				$this,
				'render_page_general_settings',
			)
		);

		// Add watermark settings.
		add_submenu_page(
			self::$parent_slug,
			_x( 'Watermark settings', 'Link element: This appears in the WordPress side menu.', 'ilove-pdf' ),
			_x( 'Watermark settings', 'Link element: This appears in the WordPress side menu.', 'ilove-pdf' ),
			'manage_options',
			self::$watermark_slug,
			array(
				$this,
				'render_page_general_settings',
			)
		);
    }

	/**
	 * Adding media optimization page.
	 *
	 * @since 3.0.0
	 */
	public function add_media_page() {
		$hook = add_media_page(
			'iLovePDF',
			'iLovePDF',
			'upload_files',
			self::$media_slug,
			array(
				$this,
				'render_media_page',
			)
		);

		// Process bulk actions early (before WP sends headers) to avoid "headers already sent" issues.
		add_action( 'load-' . $hook, array( $this, 'maybe_process_media_bulk_actions' ) );
	}

	/**
	 * Processes bulk actions from the media optimization page before any output is sent.
	 *
	 * This prevents header warnings when a redirect is performed after CSS (@font-face) or other outputs have been printed.
	 *
	 * @since 3.0.0
	 */
	public static function maybe_process_media_bulk_actions() {
		// In multisite, explicitly verify the user can upload files on current site
		if ( ! current_user_can( 'upload_files' ) ) {
			return;
		}

		$maybe_action  = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';// phpcs:ignore WordPress.Security.NonceVerification
		$maybe_action2 = isset( $_REQUEST['action2'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action2'] ) ) : '';// phpcs:ignore WordPress.Security.NonceVerification

		if ( empty( $maybe_action ) && empty( $maybe_action2 ) ) {
			return;
		}

		try {
			$list_table = new Files_List_Table();
			$list_table->get_bulk_actions();
			$list_table->process_bulk_action();
		} catch ( Exception $e ) {
			error_log( 'iLovePDF bulk early process error: ' . print_r( var_export( $e->getMessage(), true ), true ) );// phpcs:ignore
		}
	}

    /**
     * Showing general settings page
	 *
	 * @since 3.0.0
     */
    public function render_page_general_settings() {
        require_once plugin_dir_path( __DIR__ ) . 'admin/views/ilovepdf-settings.php';
    }

	/**
	 * Render the media optimization page.
	 *
	 * @since 3.0.0
	 */
	public function render_media_page() {
		require_once plugin_dir_path( __DIR__ ) . 'admin/views/media/media-bulk.php';
	}

    /**
	 * Add Link to page settings from Plugins List Page.
	 *
	 * @since 2.1.0
	 *
	 * @param array $actions An array of plugin action links.
     * @return array plugin action links.
	 */
	public function add_action_links( $actions ) {

		$general_settings[] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( add_query_arg( 'page', self::$parent_slug, get_admin_url() . 'admin.php' ) ),
			esc_html_x( 'General Settings', 'Link item: appears in the plugins list.', 'ilove-pdf' )
		);

		$compress_settings[] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( add_query_arg( 'page', self::$compress_slug, get_admin_url() . 'admin.php' ) ),
			esc_html_x( 'Compress Settings', 'Link item: appears in the plugins list.', 'ilove-pdf' )
		);

		$watermark_settings[] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( add_query_arg( 'page', self::$watermark_slug, get_admin_url() . 'admin.php' ) ),
			esc_html_x( 'Watermark Settings', 'Link item: appears in the plugins list.', 'ilove-pdf' )
		);

		$media_list[] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( add_query_arg( 'page', self::$media_slug, get_admin_url() . 'upload.php' ) ),
			esc_html_x( 'Media Optimization', 'Link item: appears in the plugins list.', 'ilove-pdf' )
		);

		$actions = array_merge( $actions, $general_settings, $compress_settings, $watermark_settings, $media_list );

		return $actions;
	}
}
