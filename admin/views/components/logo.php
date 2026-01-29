<?php
/**
 * Component: Plugin logo
 *
 * @package Ilove_Pdf_WP\views\components
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Ilove_Pdf_WP\Helpers\File_System;

?>
<figure class="ipdf-logo">
    <img src="<?php echo esc_url( File_System::get_assets_url( 'img/logo_ilovepdf.svg' ) ); ?>" alt="logo ilovepdf" />
</figure>
