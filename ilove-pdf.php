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
use Ilove_Pdf_WP\Helpers\Admin_Notice;
use Ilove_Pdf_WP\Tools\General\Settings as General_Settings;
use Ilove_Pdf_WP\Tools\Compress\Settings as Compress_Settings;
use Ilove_Pdf_WP\Tools\Watermark\Settings as Watermark_Settings;

register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );

register_deactivation_hook( __FILE__, array( Deactivator::class, 'deactivate' ) );

/**
 * Plugin update.
 *
 * Fires when the upgrader process is complete.
 *
 * @since    2.1.2
 *
 * @param object $upgrader_object Reference to the plugin upgrader object.
 * @param array  $options {
 *     Array of plugin update options.
 *
 *     @type string $action Type of action. Default 'update'.
 *     @type string $type Type of plugin being updated. Default 'plugin'.
 *     @type string $slug Slug of the plugin being updated. Default ''.
 * }
 */
function ilove_pdf_upgrade_plugin( $upgrader_object, $options ) {
	if ( 'update' === $options['action'] && 'plugin' === $options['type'] ) {
		foreach ( $options['plugins'] as $each_plugin ) {
			if ( Ilove_Pdf_Plugin::get_plugin_basename() === $each_plugin ) {

				try {
					General_Settings::migrate_general_settings();
					Compress_Settings::migrate_compress_settings();
					Watermark_Settings::migrate_watermark_settings();
				} catch ( \Error $e ) {
					if ( ! empty( $e->getMessage() ) ) {
						Admin_Notice::render( $e->getMessage(), 'error' );
					}
				}
			}
		}
	}
}
add_action( 'upgrader_process_complete', 'ilove_pdf_upgrade_plugin', 10, 2 );
new Ilove_Pdf_Plugin( '3.0.0', plugin_basename( __FILE__ ) );
