<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://ilovepdf.com/
 * @since      1.0.0
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/includes
 * @author     ILovePDF <info@ilovepdf.com>
 */
class Ilove_Pdf_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		/*
		delete_option('ilovepdf_initial_pdf_files_size');
		delete_option('ilovepdf_compressed_files');
		delete_option('ilovepdf_watermarked_files');*/
		update_option( 'ilovepdf_initial_pdf_files_size', 0 );
		update_option( 'ilovepdf_compressed_files', 0 );
		update_option( 'ilovepdf_watermarked_files', 0 );
	}
}
