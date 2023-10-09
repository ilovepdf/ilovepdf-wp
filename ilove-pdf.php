<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ilovepdf.com/
 * @since             1.0.0
 * @package           Ilove_Pdf
 *
 * @wordpress-plugin
 * Plugin Name:       iLovePDF
 * Plugin URI:        https://developer.ilovepdf.com/
 * Description:       Compress your PDF files and Stamp Images or text into PDF files. This is the Official iLovePDF plugin for WordPress. You can optimize all your PDF and stamp them automatically as you do in ilovepdf.com.
 * Version:           1.2.4
 * Requires at least: 4.7
 * Requires PHP:      7.1
 * Author:            ILovePDF
 * Author URI:        https://ilovepdf.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ilove-pdf
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ILOVE_PDF_ASSETS_PLUGIN_PATH', plugin_dir_url( __FILE__ ) );

require __DIR__ . '/includes/utility-functions.php';
require __DIR__ . '/admin/ilove-pdf-admin.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ilove-pdf-activator.php
 */
function ilove_pdf_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ilove-pdf-activator.php';
	Ilove_Pdf_Activator::activate();
}
register_activation_hook( __FILE__, 'ilove_pdf_activate' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ilove-pdf-deactivator.php
 */
function ilove_pdf_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ilove-pdf-deactivator.php';
	Ilove_Pdf_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'ilove_pdf_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ilove-pdf.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
require_once plugin_dir_path( __FILE__ ) . 'lib/ilovepdf-php-master/init.php';

define( 'ILOVE_PDF_REGISTER_URL', 'https://api.ilovepdf.com/v1/user' );
define( 'ILOVE_PDF_LOGIN_URL', 'https://api.ilovepdf.com/v1/user/login' );
define( 'ILOVE_PDF_USER_URL', 'https://api.ilovepdf.com/v1/user' );

$ilove_pdf_plugin = new Ilove_Pdf();
$ilove_pdf_plugin->run();
