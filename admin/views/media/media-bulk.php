<?php
/**
 * View: Media Bulk page
 *
 * @package Ilove_Pdf_WP\views\media
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Ilove_Pdf_WP\Media\Files_List_Table;
use Ilove_Pdf_WP\Media\Views\Media_Overview;

$ilove_pdf_list_table = new Files_List_Table();
$ilove_pdf_list_table->prepare_items();

?>

<main class="ilovepdf-base ilovepdf-media__main">
    <?php require_once plugin_dir_path( __DIR__ ) . 'components/logo.php'; ?>

    <section class="ilovepdf-media__overview">
        <?php Media_Overview::render(); ?>
    </section>

    <section class="ilovepdf-media__table">
        <form id="posts-filter" method="post">
            <?php $ilove_pdf_list_table->display(); ?>
        </form>
    </section>
</main>