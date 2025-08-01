<?php
/**
 * View: Media Bulk page
 *
 * @package Ilove_Pdf_WP/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Ilove_Pdf_WP\Helpers\Admin_Notice;
use Ilove_Pdf_WP\Media\Files_List_Table;
use Ilove_Pdf_WP\Media\Media_Overview;

if ( isset( $_GET['ilovepdf_notice'] ) ) {
    Admin_Notice::render( $_GET['ilovepdf_notice']['message'], $_GET['ilovepdf_notice']['type'] );
}

$ilove_pdf_list_table = new Files_List_Table();
$ilove_pdf_list_table->prepare_items();

?>

<main class="ilovepdf-base ilovepdf-media__main">
    <?php require_once 'components/logo.php'; ?>

    <section class="ilovepdf-media__overview">
        <?php Media_Overview::render(); ?>
    </section>

    <section class="ilovepdf-media__table">
        <?php $ilove_pdf_list_table->display(); ?>
    </section>
</main>