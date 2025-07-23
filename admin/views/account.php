<?php
/**
 * View: Account page
 *
 * @package Ilove_Pdf_WP/views
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
                require_once 'components/banner-account.php';

                if ( isset( $_GET['section'] ) && 'register' === $_GET['section'] ) {
                    require_once 'components/register.php';
                } else {
                    require_once 'components/login.php';
                }

                ?>
            </div>
        </div>

        <?php require_once 'components/banner-info.php'; ?>

    <?php else : ?>
        <div class="ilovepdf-settings__main__account-inner ilovepdf-settings__main__account-user-loggedin">
            <div class="ilovepdf-base__layout-flex ilovepdf-base__layout-flex-wrap ilovepdf-base__layout-gap--normal">
                <?php
                require_once 'components/statistics.php';
                require_once 'components/user.php';
                ?>
            </div>
        </div>
    <?php endif; ?>
</section>
