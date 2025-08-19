<?php

namespace Ilove_Pdf_WP;

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
		delete_option( 'ilovepdf_initial_pdf_files_size' );
		delete_option( 'ilovepdf_compressed_files' );
		delete_option( 'ilovepdf_watermarked_files' );
	}
}
