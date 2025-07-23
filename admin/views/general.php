<?php
/**
 * View: General settings page
 *
 * @package Ilove_Pdf/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Ilove_Pdf_WP\Tools\General\Views\Form_Options;

?>

<div class="ilovepdf-settings__main__view--general ilovepdf-settings__main__view">
    <?php Form_Options::render(); ?>
</div>
