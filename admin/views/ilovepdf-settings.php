<?php
/**
 * View: Main settings page
 *
 * @package Ilove_Pdf_WP/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! current_user_can( 'manage_options' ) ) {
	return;
}

use Ilove_Pdf_WP\Helpers\Admin_Notice;
use Ilove_Pdf_WP\Submenu_Page;

if ( isset( $_GET['ilovepdf_notice'] ) ) {
    Admin_Notice::render( $_GET['ilovepdf_notice']['message'], $_GET['ilovepdf_notice']['type'] );
}

?>
<main class="ilovepdf-base ilovepdf-settings__main">
    <?php
    require_once 'components/logo.php';

    require_once 'account.php';
    ?>

    <section class="ilovepdf-base__layout-flex ilovepdf-base__layout-flex-wrap ilovepdf-settings__main__section">
        <?php
        require_once 'components/tab-menu.php';

        switch ( get_current_screen()->base ) {
            case 'toplevel_page_' . Submenu_Page::$parent_slug:
                require_once 'general.php';
                break;
            case 'ilovepdf_page_' . Submenu_Page::$compress_slug:
                require_once 'compress.php';
                break;
            case 'ilovepdf_page_' . Submenu_Page::$watermark_slug:
                require_once 'watermark.php';
                break;
        }

        ?>
    </section>
</main>