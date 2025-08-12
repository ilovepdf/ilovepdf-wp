<?php
/**
 * View: Account page
 *
 * @package Ilove_Pdf_WP\views\account
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Ilove_Pdf_WP\Account\User_Account;

?>

<section class="ilovepdf-settings__main__account">
    <?php if ( ! User_Account::is_user_logged_in() ) : ?>
        <div class="ilovepdf-settings__main__account-inner ilovepdf-settings__main__account-user-logout">
            <div class="ilovepdf-base__layout-flex ilovepdf-base__layout-flex-wrap ilovepdf-base__layout-gap--normal">
                <?php
                require_once 'components/banner-logout.php';

                if ( isset( $_GET['section'] ) && 'register' === $_GET['section'] ) {//phpcs:ignore
                    require_once 'form-register.php';
                } else {
                    require_once 'form-login.php';
                }

                ?>
            </div>
        </div>

        <?php require_once 'components/banner-info.php'; ?>

    <?php else : ?>
        <div class="ilovepdf-settings__main__account-inner ilovepdf-settings__main__account-user-loggedin">
            <div class="ilovepdf-base__layout-flex ilovepdf-base__layout-flex-wrap ilovepdf-base__layout-gap--normal">
                <?php
                require_once 'statistics.php';
                require_once 'user.php';
                ?>
            </div>
        </div>
    <?php endif; ?>
</section>
