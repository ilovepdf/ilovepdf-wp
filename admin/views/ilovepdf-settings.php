<?php
/**
 * View: Main settings page
 *
 * @package Ilove_Pdf_WP\views
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Ilove_Pdf_WP\Submenu_Page;

?>
<main class="ilovepdf-base ilovepdf-settings__main">
    <?php
    require_once plugin_dir_path( __DIR__ ) . 'views/components/logo.php';

    require_once plugin_dir_path( __DIR__ ) . 'views/account/account.php';
    ?>

    <section class="ilovepdf-base__layout-flex ilovepdf-base__layout-flex-wrap ilovepdf-settings__main__section">
        <?php
        require_once plugin_dir_path( __DIR__ ) . 'views/components/tab-menu.php';

        switch ( get_current_screen()->base ) {
            case 'toplevel_page_' . Submenu_Page::$parent_slug:
                require_once plugin_dir_path( __DIR__ ) . 'views/settings/general.php';
                break;
            case 'ilovepdf_page_' . Submenu_Page::$compress_slug:
                require_once plugin_dir_path( __DIR__ ) . 'views/settings/compress.php';
                break;
            case 'ilovepdf_page_' . Submenu_Page::$watermark_slug:
                require_once plugin_dir_path( __DIR__ ) . 'views/settings/watermark.php';
                break;
        }

        ?>
    </section>
</main>