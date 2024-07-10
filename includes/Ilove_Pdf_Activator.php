<?php
/**
 * Fired during plugin activation
 *
 * @link       https://ilovepdf.com/
 * @since      1.0.0
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/includes
 */

namespace Ilove_Pdf_Includes;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/includes
 * @author     ILovePDF <info@ilovepdf.com>
 */
class Ilove_Pdf_Activator {
	/**
	 * Activate plugin
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$upload_dir  = wp_upload_dir();
		$pdf_dirname = $upload_dir['basedir'] . '/pdf';
		if ( ! file_exists( $pdf_dirname ) ) {
		    wp_mkdir_p( $pdf_dirname );
		}

		$pdf_dirname = $upload_dir['basedir'] . '/pdf/compress';
		if ( ! file_exists( $pdf_dirname ) ) {
		    wp_mkdir_p( $pdf_dirname );
		}

		$pdf_dirname = $upload_dir['basedir'] . '/pdf/watermark';
		if ( ! file_exists( $pdf_dirname ) ) {
		    wp_mkdir_p( $pdf_dirname );
		}

		$pdf_dirname = $upload_dir['basedir'] . '/pdf/backup';
		if ( ! file_exists( $pdf_dirname ) ) {
		    wp_mkdir_p( $pdf_dirname );
		}

		$initial_pdf_size = ilove_pdf_get_all_pdf_current_size();
		add_option( 'ilovepdf_initial_pdf_files_size', $initial_pdf_size );

		if ( get_option( 'ilovepdf_wordpress_id' ) === false ) {
			add_option( 'ilovepdf_wordpress_id', md5( get_option( 'siteurl' ) . get_option( 'admin_email' ) ) );
        }

		self::set_default_values_watermark_settings();
		self::set_default_values_compress_settings();
		self::set_default_values_general_settings();
	}

	/**
	 * Watermark Settings Value.
	 *
	 * Set default values.
	 *
	 * @since    1.2.4
	 */
	public static function set_default_values_watermark_settings() {

		$get_format_options = get_option( 'ilove_pdf_display_settings_format_watermark' );
		$get_gral_options   = get_option( 'ilove_pdf_display_settings_watermark' );

		if ( ! is_array( $get_format_options ) ) {
			$get_format_options = array();
		}

		if ( ! is_array( $get_gral_options ) ) {
			$get_gral_options = array();
		}

		if ( ! isset( $get_gral_options['ilove_pdf_watermark_active'] ) ) {
			$get_gral_options['ilove_pdf_watermark_active'] = 1;
        }

		if ( ! isset( $get_format_options['ilove_pdf_format_watermark_mode'] ) ) {
			$get_format_options['ilove_pdf_format_watermark_mode'] = 0;
        }

		if ( ! isset( $get_format_options['ilove_pdf_format_watermark_vertical'] ) ) {
			$get_format_options['ilove_pdf_format_watermark_vertical'] = '2';
        }

		if ( ! isset( $get_format_options['ilove_pdf_format_watermark_horizontal'] ) ) {
			$get_format_options['ilove_pdf_format_watermark_horizontal'] = '2';
        }

		if ( ! isset( $get_format_options['ilove_pdf_format_watermark_text'] ) ) {
			$get_format_options['ilove_pdf_format_watermark_text'] = get_bloginfo();
        }

		if ( ! isset( $get_format_options['ilove_pdf_format_watermark_text_size'] ) ) {
			$get_format_options['ilove_pdf_format_watermark_text_size'] = '22';
		}

		if ( ! isset( $get_format_options['ilove_pdf_format_watermark_font_family'] ) ) {
			$get_format_options['ilove_pdf_format_watermark_font_family'] = 'Verdana';
		}

		if ( ! isset( $get_format_options['ilove_pdf_format_watermark_text_color'] ) ) {
			$get_format_options['ilove_pdf_format_watermark_text_color'] = '#dd3333';
		}

		update_option( 'ilove_pdf_display_settings_format_watermark', $get_format_options );
		update_option( 'ilove_pdf_display_settings_watermark', $get_gral_options );
	}

	/**
	 * General Settings Value.
	 *
	 * Set default values.
	 *
	 * @since    2.1.0
	 */
	public static function set_default_values_general_settings() {

		$get_options = get_option( 'ilove_pdf_display_general_settings', array() );

		if ( ! isset( $get_options['ilove_pdf_general_backup'] ) ) {
			$get_options['ilove_pdf_general_backup'] = 1;
		}

		update_option( 'ilove_pdf_display_general_settings', $get_options );
	}

	/**
	 * Compress Settings Value.
	 *
	 * Set default values.
	 *
	 * @since    2.1.1
	 */
	public static function set_default_values_compress_settings() {

		$get_options = get_option( 'ilove_pdf_display_settings_compress' );

		if ( ! is_array( $get_options ) ) {
			$get_options = array();
		}

		if ( ! isset( $get_options['ilove_pdf_compress_active'] ) ) {
			$get_options['ilove_pdf_compress_active'] = 1;
		}

		if ( ! isset( $get_options['ilove_pdf_compress_quality'] ) ) {
			$get_options['ilove_pdf_compress_quality'] = 1;
		}

		update_option( 'ilove_pdf_display_settings_compress', $get_options );
	}
}
