<?php
/**
 * Component: Tab Menu
 *
 * @var string $active_tab
 * @var string $nonce_settings
 *
 * @package Ilove_Pdf
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<h2 class="nav-tab-wrapper">
    <a href="?page=ilovepdf-admin-page&tab=setting_options&nonce_ilove_pdf_settings_tab=<?php echo sanitize_key( $nonce_settings ); ?>" class="nav-tab <?php echo 'setting_options' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php echo esc_html( __( 'General', 'ilove-pdf' ) ); ?></a>
    <a href="?page=ipdf-compress-admin-page&tab=compress_options&nonce_ilove_pdf_settings_tab=<?php echo sanitize_key( $nonce_settings ); ?>" class="nav-tab <?php echo 'compress_options' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php echo esc_html( __( 'Compress PDF', 'ilove-pdf' ) ); ?></a>
    <a href="?page=ipdf-watermark-admin-page&tab=watermark_options&nonce_ilove_pdf_settings_tab=<?php echo sanitize_key( $nonce_settings ); ?>" class="nav-tab <?php echo 'watermark_options' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php echo esc_html( __( 'Watermark', 'ilove-pdf' ) ); ?></a>
</h2>