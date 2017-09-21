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
 * Description:       Compress your PDF files and Stamp Images or text into PDF files. This is the Official iLovePDF plugin for Wordpress. You can optimize all your PDF and stamp them automatically as you do in ilovepdf.com.
 * Version:           1.0.0
 * Author:            ILovePDF
 * Author URI:        https://ilovepdf.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ilove-pdf
 * Domain Path:       /languages
 */

require dirname( __FILE__ ) . '/admin/ilove-pdf-admin.php';

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ilove-pdf-activator.php
 */
function activate_ilove_pdf() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ilove-pdf-activator.php';
	Ilove_Pdf_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ilove-pdf-deactivator.php
 */
function deactivate_ilove_pdf() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ilove-pdf-deactivator.php';
	Ilove_Pdf_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ilove_pdf' );
register_deactivation_hook( __FILE__, 'deactivate_ilove_pdf' );

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


define('ILOVEPDF_REGISTER_URL', 'https://api.ilovepdf.com/v1/user');
define('ILOVEPDF_LOGIN_URL', 'https://api.ilovepdf.com/v1/user/login');
define('ILOVEPDF_USER_URL', 'https://api.ilovepdf.com/v1/user');

$plugin = new Ilove_Pdf();
$plugin->run();



add_action( 'ilove_pdf_before_delete_post', 'ilove_pdf_before_delete_media' );
function ilove_pdf_before_delete_media( $postid ){

	// We check if the global post type isn't ours and just return
    global $post_type;   
    if ( $post_type == 'attachment' ) {
    	//update_option('ilovepdf_initial_pdf_files_size', 0);
    }

    // My custom stuff for deleting my custom post type here
}
