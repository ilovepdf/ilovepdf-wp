<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package    Ilove_Pdf_WP
 * @since      1.0.0
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';

use Ilove_Pdf_WP\Ilove_Pdf_Plugin;

if ( is_multisite() ) {
	$ilove_pdf_blogs = get_sites( array( 'fields' => 'ids' ) );

	foreach ( $ilove_pdf_blogs as $ilove_pdf_site_id ) {
		switch_to_blog( $ilove_pdf_site_id );
		Ilove_Pdf_Plugin::uninstall_plugin();
		restore_current_blog();
	}
	return;
}

Ilove_Pdf_Plugin::uninstall_plugin();
