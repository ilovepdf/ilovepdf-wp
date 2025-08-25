<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://ilovepdf.com/
 * @since      1.0.0
 *
 * @package    Ilove_Pdf_WP
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';

use Ilove_Pdf_WP\Ilove_Pdf_Plugin;

Ilove_Pdf_Plugin::uninstall_plugin();
