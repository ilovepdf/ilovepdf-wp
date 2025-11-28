<?php
/**
 * View: Watermark settings page
 *
 * @package Ilove_Pdf_WP\views\settings
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Ilove_Pdf_WP\Tools\Watermark\Views\Form_Options;

?>
<div class="ilovepdf-settings__main__view--watermark ilovepdf-settings__main__view">
    <?php Form_Options::render(); ?>
</div>
