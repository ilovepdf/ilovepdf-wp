<?php
/**
 * Component: Banner logout.
 *
 * @package Ilove_Pdf_WP\views\account\components
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Ilove_Pdf_WP\Helpers\File_System;

?>
<figure class="ilovepdf-banner-account">
    <img src="<?php echo esc_url( File_System::get_assets_url( 'img/ilovepdf_banner_login.png' ) ); ?>" alt="account banner ilovepdf" />
</figure>
