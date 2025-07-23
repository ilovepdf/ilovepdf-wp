<?php

namespace Ilove_Pdf_WP\Account\Views;

use Ilove_Pdf_WP\Account\User_Account;

/**
 * Handles the rendering of the logout form.
 *
 * @package Ilove_Pdf_WP\Account\Views
 * @since 3.0.0
 */
class Form_Logout {
    /**
     * Renders the account logout form.
     *
     * @since 3.0.0
     */
    public static function render() {
        printf(
            '<form method="post" action="%1$s" class="ipdf-form form-%2$s">
                <input type="hidden" name="action" value="%2$s" />
                %3$s
                %4$s
            </form>',
            esc_html( admin_url( 'admin-post.php' ) ),
            User_Account::get_action_logout_key(),
            wp_nonce_field( -1, '_wpnonce_logout', true, false ),
            self::create_submit_button(),
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
            '<div class="ilovepdf-form-elements">
                <button type="submit" name="submit-login" id="submit-logout" class="ipdf-btn ipdf-btn--primary">
                    %1$s
                </button>
            </div>',
            esc_html_x( 'Logout', 'button link', 'ilove-pdf' ),
        );
    }
}
