<?php

namespace Ilove_Pdf_WP\Account\Views;

use Ilove_Pdf_WP\Account\User_Account;
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
            User_Account::get_action_register_key(),
            wp_nonce_field( -1, '_wpnonce_register', true, false ),
            esc_html_x( 'Register as iLoveAPI developer', 'form title', 'ilove-pdf' ),
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
     * TODO: implementar: si el registro falla, mostrar el valor que el usuario ingresó en el campo de nombre.
     */
    private static function create_field_name() {
        return sprintf(
            '<input class="ipdf-input ipdf-input--name" type="text" name="%1$s" id="%1$s" placeholder="%2$s" value="%3$s" class="ilovepdf_field_name" required />',
            User_Account::get_field_name(),
            esc_html_x( 'Name', 'input placeholder', 'ilove-pdf' ),
            '',
        );
    }

    /**
     * Creates a button that redirects the user to the login page.
     *
     * @since 3.0.0
     * @return string HTML markup for the button.
     */
    private static function create_btn_goto_login() {
        $url = add_query_arg(
            array(
				'page' => $_GET['page'],
            ),
            admin_url( 'admin.php' )
        );

        return sprintf(
            '<a class="ipdf-btn--inline-secondary" href="%1$s">%2$s</a>',
            esc_url( $url ),
            esc_html_x( 'Login to your account', 'button link', 'ilove-pdf' ),
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
            esc_html_x( 'Register', 'button link', 'ilove-pdf' ),
        );
    }
}
