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
		add_option( 'ilove_pdf_display_settings_watermark', array( 'ilove_pdf_watermark_backup' => 1 ) );
		if ( get_option( 'ilovepdf_wordpress_id' ) === null ) {
			add_option( 'ilovepdf_wordpress_id', md5( get_option( 'siteurl' ) . get_option( 'admin_email' ) ) );
        }

		self::set_default_values_watermark_settings();
	}

	/**
	 * Watermark Settings Value.
	 *
	 * Set default values.
	 *
	 * @since    1.2.4
	 */
	public static function set_default_values_watermark_settings() {

		$get_options = get_option( 'ilove_pdf_display_settings_format_watermark' );

		if ( ! is_array( $get_options ) ) {
			$get_options = array();
		}

		if ( ! isset( $get_options['ilove_pdf_format_watermark_vertical'] ) ) {
			$get_options['ilove_pdf_format_watermark_vertical'] = '2';
        }

		if ( ! isset( $get_options['ilove_pdf_format_watermark_horizontal'] ) ) {
			$get_options['ilove_pdf_format_watermark_horizontal'] = '2';
        }

		if ( ! isset( $get_options['ilove_pdf_format_watermark_text'] ) ) {
			$get_options['ilove_pdf_format_watermark_text'] = get_bloginfo();
        }

		if ( ! isset( $get_options['ilove_pdf_format_watermark_text_size'] ) ) {
			$get_options['ilove_pdf_format_watermark_text_size'] = '22';
		}

		if ( ! isset( $get_options['ilove_pdf_format_watermark_font_family'] ) ) {
			$get_options['ilove_pdf_format_watermark_font_family'] = 'Verdana';
		}

		if ( ! isset( $get_options['ilove_pdf_format_watermark_text_color'] ) ) {
			$get_options['ilove_pdf_format_watermark_text_color'] = '#dd3333';
		}

		update_option( 'ilove_pdf_display_settings_format_watermark', $get_options );
	}
}
