<?php

namespace Ilove_Pdf_WP;

use Ilove_Pdf_WP\Helpers\DB_Handler;

/**
 * Fired during plugin deactivation.
 *
 * @since   1.0.0
 * @package Ilove_Pdf_WP
 */
class Deactivator {

	/**
	 * Deactivates options related to PDF files when deactivating the plugin.
	 *
	 * @since 3.0.0 The Key (ilovepdf_initial_pdf_files_size, ilovepdf_compressed_files, ilovepdf_watermarked_files) is deleted from database
	 * @since 1.0.0
	 */
	public static function deactivate() {
		DB_Handler::delete_option( 'ilovepdf_initial_pdf_files_size' );
		DB_Handler::delete_option( 'ilovepdf_compressed_files' );
		DB_Handler::delete_option( 'ilovepdf_watermarked_files' );
	}
}
