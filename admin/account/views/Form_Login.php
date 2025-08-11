<?php

namespace Ilove_Pdf_WP\Account\Views;

use Ilove_Pdf_WP\Account\User_Account;
use Ilove_Pdf_WP\Account\Views\Base\Form;

/**
 * Handles the rendering of the login form.
 *
 * @package Ilove_Pdf_WP\Account\Views
 * @since 3.0.0
 */
class Form_Login extends Form {
    /**
     * Renders the account login form.
     *
     * @since 3.0.0
     *
     * phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
     */
    public static function render() {
        printf(
            '<form method="post" action="%1$s" class="ipdf-form form-%2$s">
                <input type="hidden" name="action" value="%2$s" />
                %3$s
                <h2>%4$s</h2>
                <div class="ilovepdf-settings__main__account-fields-wrapper ipdf-spacer">
                    %5$s
                    %6$s
                </div>
                %7$s
                %8$s
                %9$s
            </form>',
            esc_html( admin_url( 'admin-post.php' ) ),
            User_Account::get_action_login_key(),
            wp_nonce_field( -1, '_wpnonce_login', true, false ),
            esc_html_x( 'Login to your account', 'form title', 'ilove-pdf' ),
            self::create_field_email(),
            self::create_field_password(),
            self::create_btn_forget_password(),
            self::create_submit_button(),
            self::create_btn_goto_register(),
        );
    }

    /**
     * Creates the button to navigate to the password reset page.
     *
     * @since 3.0.0
     * @return string HTML markup for the password reset button.
     */
    private static function create_btn_forget_password() {
        return sprintf(
            '<a class="ipdf-btn--inline-primary ipdf-spacer" href="%1$s" target="_blank">%2$s</a>',
            esc_url( 'https://iloveapi.com/login/reset' ),
            esc_html_x( 'Forgot your password?', 'button link', 'ilove-pdf' ),
        );
    }

    /**
     * Creates the button to navigate to the registration form.
     *
     * @since 3.0.0
     * @return string HTML markup for the registration button.
     */
    private static function create_btn_goto_register() {
        $url = add_query_arg(
            array(
				'page'    => $_GET['page'],
				'section' => 'register',
            ),
            admin_url( 'admin.php' )
        );

        return sprintf(
            '<a class="ipdf-btn--inline-secondary" href="%1$s">%2$s</a>',
            esc_url( $url ),
            esc_html_x( 'Register as iLoveAPI developer', 'button link', 'ilove-pdf' ),
        );
    }

    /**
     * Creates the submit button for the form.
     *
     * The button is disabled if the user is not logged in.
     *
     * @since 3.0.0
     * @return string HTML markup for the submit button.
     */
    private static function create_submit_button() {
        return sprintf(
            '<div class="ilovepdf-form-elements ipdf-spacer">
                <button type="submit" name="submit-login" id="submit-login" class="ipdf-btn ipdf-btn--primary">
                    %1$s
                </button>
            </div>',
            esc_html_x( 'Login', 'button link', 'ilove-pdf' ),
        );
    }
}
