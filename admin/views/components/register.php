<?php
/**
 * View: Account page register
 *
 * @package Ilove_Pdf_WP\views\components
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Ilove_Pdf_WP\Account\Views\Form_Register;

?>

<div class="ilovepdf-settings__main__account-register-wrap ilovepdf-settings__main__account-common">
    <?php Form_Register::render(); ?>
</div>