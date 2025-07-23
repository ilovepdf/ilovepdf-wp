<?php
/**
 * View: Compress settings page
 *
 * @package Ilove_Pdf_WP/views
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Ilove_Pdf_WP\Tools\Compress\Views\Form_Options;

?>
<div class="ilovepdf-settings__main__view--compress ilovepdf-settings__main__view">
    <?php Form_Options::render(); ?>
</div>