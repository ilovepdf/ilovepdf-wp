<?php
/**
 * Component: Account banner.
 *
 * @package Ilove_Pdf_WP\views\components
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Ilove_Pdf_WP\File_System;

?>
<figure class="ilovepdf-banner-account">
    <img src="<?php echo esc_url( File_System::get_assets_url( 'img/ilovepdf_banner_login.png' ) ); ?>" alt="account banner ilovepdf" />
</figure>