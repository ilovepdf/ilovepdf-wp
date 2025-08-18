<?php
/**
 * IlovePdf WordPress Plugin
 *
 * @link              https://ilovepdf.com/
 * @since             1.0.0
 * @package           Ilove_Pdf_WP
 *
 * @wordpress-plugin
 * Plugin Name:       iLovePDF
 * Plugin URI:        https://iloveapi.com/
 * Description:       Compress your PDF files and Stamp Images or text into PDF files. This is the Official iLovePDF plugin for WordPress. You can optimize all your PDF and stamp them automatically as you do in ilovepdf.com.
 * Version:           3.0.0
 * Requires at least: 5.3
 * Requires PHP:      7.4
 * Author:            ILovePDF
 * Author URI:        https://ilovepdf.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ilove-pdf
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ini_get( 'max_execution_time' ) < 300 ) {
    set_time_limit( 300 );
}

require_once plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';

use Ilove_Pdf_WP\Activator;
use Ilove_Pdf_WP\Deactivator;
use Ilove_Pdf_WP\Ilove_Pdf_Plugin;

register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );

register_deactivation_hook( __FILE__, array( Deactivator::class, 'deactivate' ) );

new Ilove_Pdf_Plugin( '3.0.0', plugin_basename( __FILE__ ) );
