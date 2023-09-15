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
require __DIR__ . '/partials/ilove-pdf-settings-display.php';
require __DIR__ . '/partials/ilove-pdf-statistics-display.php';

require __DIR__ . '/functions_processed_files.php';
require __DIR__ . '/functions_compress.php';
require __DIR__ . '/functions_watermark.php';
require __DIR__ . '/functions_statistics.php';

require_once plugin_dir_path( __DIR__ ) . 'lib/ilovepdf-php-master/init.php';

/**
 * Add Menu Page to Dashboard.
 *
 * @since    1.0.0
 */
function ilove_pdf_menu() {
	// Añade una página de menú a WordPress

    add_submenu_page(
        'options-general.php',              // Register this submenu with the menu defined above
        'iLovePDF Settings',                // The text to the display in the browser when this menu item is active
        'iLovePDF',                         // The text for this menu item
        'administrator',                    // Which type of users can see this menu
        'ilove-pdf-content-setting',        // The unique ID - the slug - for this menu item
        'ilove_pdf_content_page_setting'    // The function used to render the menu for this page to the screen
    );

    add_submenu_page(
        'upload.php',
        'iLovePDF Statistics',
        'iLovePDF',
        'administrator',
        'ilove-pdf-content-statistics',
        'ilove_pdf_content_page_statistics'
    );
}
add_action( 'admin_menu', 'ilove_pdf_menu' );
