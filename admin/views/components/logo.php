<?php
/**
 * Component: Plugin logo
 *
 * @package Ilove_Pdf_WP\views\components
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Ilove_Pdf_WP\File_System;

?>
<figure class="ipdf-logo">
    <img src="<?php echo esc_url( File_System::get_assets_url( 'img/logo_ilovepdf.svg' ) ); ?>" alt="logo ilovepdf" />
</figure>