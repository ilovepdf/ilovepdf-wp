<?php

namespace Ilove_Pdf_Admin;

/**
 * Managing the iLovePDF plugin's submenu and pages.
 *
 * Responsible for adding a submenu to the menu in the WordPress admin area and rendering the plugin's settings and content pages. It initializes the submenu and adds individual pages for compress settings, watermark settings, and media optimization.
 *
 * @since 3.0.0
 * @package Ilove_Pdf/admin
 */
class Submenu_Page {

	/**
	 * Parent slug for the submenu page.
	 *
	 * @var string
	 * @access protected
	 * @since 3.0.0
	 */
	protected $parent_slug = 'ilovepdf-admin-page';

	/**
	 * Slug for the compress settings page.
	 *
	 * @var string
	 * @access protected
	 * @since 3.0.0
	 */
	protected $compress_slug = 'ipdf-compress-admin-page';

	/**
	 * Slug for the watermark settings page.
	 *
	 * @var string
	 * @access protected
	 * @since 3.0.0
	 */
	protected $watermark_slug = 'ipdf-watermark-admin-page';

    /**
     * Initializing the class and adding the page menu to WordPress dashboard.
	 *
	 * @since 3.0.0
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_page_settings' ) );
        add_filter( 'plugin_action_links_' . ILOVE_PDF_PLUGIN_NAME, array( $this, 'add_action_links' ) );
    }

    /**
     * Adding page menu to WordPress dashboard.
	 *
	 * @since 3.0.0
     */
    public function add_page_settings() {

        // Add page menu to WordPress dashboard.
        add_menu_page(
            _x( 'General Settings', 'submenu link', 'ilove-pdf' ),
			'iLovePDF',
			'manage_options',
			$this->parent_slug,
			array(
                $this,
                'render_page_general_settings',
            ),
			plugins_url( 'assets/img/ilovepdf-icon-16x16.png', __DIR__ )
		);

		// Add general settings.
        add_submenu_page(
			$this->parent_slug,
			_x( 'General Settings', 'submenu link', 'ilove-pdf' ),
			_x( 'General Settings', 'submenu link', 'ilove-pdf' ),
			'manage_options',
			$this->parent_slug,
			array(
				$this,
				'render_page_general_settings',
			)
		);

		// Add compress settings.
        add_submenu_page(
			$this->parent_slug,
			_x( 'Compress settings', 'submenu link', 'ilove-pdf' ),
			_x( 'Compress settings', 'submenu link', 'ilove-pdf' ),
			'manage_options',
			$this->compress_slug,
			array(
				$this,
				'render_page_compress_settings',
			)
		);

		// Add watermark settings.
		add_submenu_page(
			$this->parent_slug,
			_x( 'Watermark settings', 'submenu link', 'ilove-pdf' ),
			_x( 'Watermark settings', 'submenu link', 'ilove-pdf' ),
			'manage_options',
			$this->watermark_slug,
			array(
				$this,
				'render_page_watermark_settings',
			)
		);
    }

    /**
     * Showing general settings page
	 *
	 * @since 3.0.0
     */
    public function render_page_general_settings() {

        $logo_svg = ILOVE_PDF_ASSETS_PLUGIN_PATH . 'assets/img/logo_ilovepdf.svg';
        $options  = get_option( 'ilove_pdf_display_settings_watermark' );

        require_once plugin_dir_path( __DIR__ ) . 'admin/views/general-settings.php';
    }

    /**
     * Showing compress settings page
	 *
	 * @since 3.0.0
     */
    public function render_page_compress_settings() {

        $logo_svg = ILOVE_PDF_ASSETS_PLUGIN_PATH . 'assets/img/logo_ilovepdf.svg';
        $options  = get_option( 'ilove_pdf_display_settings_watermark' );

        require_once plugin_dir_path( __DIR__ ) . 'admin/views/compress-settings.php';
    }

    /**
     * Showing watermark settings page
	 *
	 * @since 3.0.0
     */
    public function render_page_watermark_settings() {

        $logo_svg = ILOVE_PDF_ASSETS_PLUGIN_PATH . 'assets/img/logo_ilovepdf.svg';
        $options  = get_option( 'ilove_pdf_display_settings_watermark' );

        require_once plugin_dir_path( __DIR__ ) . 'admin/views/watermark-settings.php';
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
			esc_url( add_query_arg( 'page', $this->parent_slug, get_admin_url() . 'admin.php' ) ),
			esc_html_x( 'General Settings', 'Link item', 'ilove-pdf' )
		);

		$compress_settings[] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( add_query_arg( 'page', $this->compress_slug, get_admin_url() . 'admin.php' ) ),
			esc_html_x( 'Compress Settings', 'Link item', 'ilove-pdf' )
		);

		$watermark_settings[] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( add_query_arg( 'page', $this->watermark_slug, get_admin_url() . 'admin.php' ) ),
			esc_html_x( 'Watermark Settings', 'Link item', 'ilove-pdf' )
		);

		$actions = array_merge( $actions, $general_settings, $compress_settings, $watermark_settings );

		return $actions;
	}
}
