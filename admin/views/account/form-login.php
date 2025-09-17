<?php
/**
 * View: Account page login
 *
 * @package Ilove_Pdf_WP\views\account
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Ilove_Pdf_WP\Account\Views\Form_Login;

?>

<div class="ilovepdf-settings__main__account-login-wrap ilovepdf-settings__main__account-common">
    <?php Form_Login::render(); ?>
</div>