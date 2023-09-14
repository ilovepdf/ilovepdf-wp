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
	 * Short Description. (use period)
	 *
	 * Long Description.
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
		if ( get_option( 'ilovepdf_wordpress_id' ) == null ) {
			add_option( 'ilovepdf_wordpress_id', md5( get_option( 'siteurl' ) . get_option( 'admin_email' ) ) );
        }
	}
}
