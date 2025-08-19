<?php

namespace Ilove_Pdf_WP\Account\Views;

use Ilove_Pdf_WP\Account\User_Auth;
use Ilove_Pdf_WP\Account\Views\Base\Form;

/**
 * Handles the rendering of the registration form.
 *
 * @package Ilove_Pdf_WP\Account\Views
 * @since 3.0.0
 */
class Form_Register extends Form {
    /**
     * Renders the account register form.
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
                    %7$s
                </div>
                %8$s
                %9$s
            </form>',
            esc_html( admin_url( 'admin-post.php' ) ),
            User_Auth::get_action_register_key(),
            wp_nonce_field( -1, '_wpnonce_register', true, false ),
            esc_html_x( 'Create your iLovePDF developer account', 'form title', 'ilove-pdf' ),
            self::create_field_name(),
            self::create_field_email(),
            self::create_field_password(),
            self::create_submit_button(),
            self::create_btn_goto_login(),
        );
    }

    /**
     * Creates the input field for the user's name.
     *
     * @since 3.0.0
     * @return string HTML markup for the name input field.
     */
    private static function create_field_name() {
        return sprintf(
            '<input class="ipdf-input ipdf-input--name" type="text" name="%1$s" id="%1$s" placeholder="%2$s" value="" class="ilovepdf_field_name" required />',
            User_Auth::get_field_name(),
            esc_html_x( 'Full Name', 'input placeholder', 'ilove-pdf' ),
        );
    }

    /**
     * Creates a button that redirects the user to the login page.
     *
     * @since 3.0.0
     * @return string HTML markup for the button.
     */
    private static function create_btn_goto_login() {
        if ( isset( $_GET['_wpnonce'] ) && ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'ilove-pdf-goto-register' ) ) {
            return '';
        }

        $nonce = wp_create_nonce( 'ilove-pdf-goto-login' );

        $url = add_query_arg(
            array(
				'page'     => isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '',
                '_wpnonce' => $nonce,
            ),
            admin_url( 'admin.php' )
        );

        return sprintf(
            '<a class="ipdf-btn--inline-secondary" href="%1$s">%2$s</a>',
            esc_url( $url ),
            esc_html_x( 'Log in to your account', 'button link', 'ilove-pdf' ),
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
                <button type="submit" name="submit-register" id="submit-register" class="ipdf-btn ipdf-btn--primary">
                    %1$s
                </button>
            </div>',
            esc_html_x( 'Create account', 'button link', 'ilove-pdf' ),
        );
    }
}
