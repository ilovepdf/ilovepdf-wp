<?php
/**
 * View: Account page user info
 *
 * @package Ilove_Pdf_WP\views\account
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Ilove_Pdf_WP\Account\Views\Form_Logout;
use Ilove_Pdf_WP\Account\User_Data;

?>

<article class="ilovepdf-settings__main__account-user-details ilovepdf-settings__main__account-common">
    <h4 class="ilovepdf-title">
        <?php echo esc_html_x( 'Account', 'title section', 'ilove-pdf' ); ?>
    </h4>
    <p class="ilovepdf__account-logged-user-name">
        <?php echo esc_html( User_Data::get_settings( User_Data::get_db_user_name_key() ) ); ?>
    </p>
    <div class="ilovepdf__account-logged-content">
        <p class="ilovepdf__account-logged-user-email">
            <?php echo esc_html( User_Data::get_settings( User_Data::get_db_user_email_key() ) ); ?>
        </p>
    </div>

    <?php Form_Logout::render(); ?>

    <hr class="ipdf-divisor" />

    <div class="ilovepdf__account-project-wrapper">
        <?php require_once 'form-projects.php'; ?>
    </div>
</article>