<?php
/**
 * View: Account page statistics
 *
 * @package Ilove_Pdf_WP\views\account
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Ilove_Pdf_WP\Account\User_Account;

?>

<article class="ilovepdf__account-credits-section ilovepdf-base__layout-flex ilovepdf-base__layout-flex-wrap ilovepdf-base__layout-flex-1">
    <div class="ilovepdf__account-statistics-wrapper ilovepdf-base__layout-flex-1">
        <div class="ilovepdf__account-info-inner ilovepdf__account-free">
            <h5 class="ipdf-subtitle">
                <?php echo esc_html_x( 'Free.', 'subtitle package section', 'ilove-pdf' ); ?>
            </h5>
            <div class="ipdf-bar-progress">
                <div style="width: <?php echo User_Account::get_percent_credits_used( 'free' ); ?>%;"></div>
            </div>
            <p>
                <?php echo User_Account::get_readable_credits( 'free' ); ?>
            </p>
        </div>

        <?php if ( User_Account::user_has( 'package' ) ) : ?>
        <div class="ilovepdf__account-info-inner ilovepdf__account-prepaid">
            <h5 class="ipdf-subtitle">
                <?php echo esc_html_x( 'Packages.', 'subtitle package section', 'ilove-pdf' ); ?>
            </h5>
            <div class="ipdf-bar-progress">
                <div style="width: <?php echo User_Account::get_percent_credits_used( 'package' ); ?>%;"></div>
            </div>
            <p>
                <?php echo User_Account::get_readable_credits( 'package' ); ?>
            </p>
        </div>
        <?php endif; ?>

        <?php if ( User_Account::user_has( 'suscription' ) ) : ?>
        <div class="ilovepdf__account-info-inner ilovepdf__account-suscription">
            <h5 class="ipdf-subtitle">
                <?php echo User_Account::get_readable_suscription_type(); ?>
            </h5>
            <div class="ipdf-bar-progress">
                <div style="width: <?php echo User_Account::get_percent_credits_used( 'suscription' ); ?>%;"></div>
            </div>
            <p>
                <?php echo User_Account::get_readable_credits( 'suscription' ); ?>
            </p>
        </div>
        <?php endif; ?>
    </div>

    <div class="ilovepdf__account-details-wrapper ilovepdf-base__layout-flex-1">
        <p>
            <?php esc_html_e( 'You get 2,500 free credits every month to process your files.', 'ilove-pdf' ); ?>
        </p>
        <p>
            <?php
            printf(
                wp_kses_post(
                    // translators: %1$s and %2$s are HTML link tags.
                    __( 'Need more credits? %1$s Upgrade your plan %2$s or %1$s buy a credit package. %2$s', 'ilove-pdf' )
                ),
                '<a class="ipdf-btn--inline-secondary" href="https://iloveapi.com/pricing" target="_blank">',
                '</a>'
            );
            ?>
        </p>
        <a class="ipdf-btn ipdf-btn--secondary" href="https://iloveapi.com/pricing" target="_blank">
            <?php echo esc_html_x( 'Buy more credits', 'button link', 'ilove-pdf' ); ?>
        </a>
    </div>
</article>