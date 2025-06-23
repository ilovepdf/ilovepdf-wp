<?php
/**
 * Main Settings Admin
 *
 * @link       https://ilovepdf.com/
 * @since      1.0.0
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/admin
 */

require __DIR__ . '/general-settings.php';
require __DIR__ . '/compress-settings.php';
require __DIR__ . '/watermark-settings.php';
require __DIR__ . '/general-statistics.php';
require __DIR__ . '/partials/ilove-pdf-statistics-display.php';

require __DIR__ . '/functions-processed-files.php';
require __DIR__ . '/functions-compress.php';
require __DIR__ . '/functions-watermark.php';
require __DIR__ . '/functions-statistics.php';

/**
 * Add Menu Page to Dashboard.
 *
 * @since    1.0.0
 */
function ilove_pdf_menu() {

    add_submenu_page(
        'upload.php',
        'iLovePDF Statistics',
        'iLovePDF',
        'manage_options',
        'ilove-pdf-content-statistics',
        'ilove_pdf_content_page_statistics'
    );
}
add_action( 'admin_menu', 'ilove_pdf_menu' );
