<?php

namespace Ilove_Pdf_WP\Account\Views\Base;

use Ilove_Pdf_WP\Account\User_Account;

/**
 * Base class for forms in account sections.
 *
 * Provides common functionality for creating forms.
 *
 * @package Ilove_Pdf_WP\Account\Views\Base
 * @since 3.0.0
 */
class Form {
    /**
     * Creates the input field for the user's email.
     *
     * @since 3.0.0
     * @return string HTML markup for the email input field.
     */
    protected static function create_field_email() {
        return sprintf(
            '<input class="ipdf-input ipdf-input--email" type="email" name="%1$s" id="%1$s" placeholder="%2$s" value="%3$s" required />',
            User_Account::get_field_email(),
            esc_html_x( 'Email', 'input placeholder', 'ilove-pdf' ),
            '',
        );
    }

    /**
     * Creates the input field for the user's password.
     *
     * @since 3.0.0
     * @return string HTML markup for the password input field.
     */
    protected static function create_field_password() {
        return sprintf(
            '<input class="ipdf-input ipdf-input--password" type="password" name="%1$s" id="%1$s" placeholder="%2$s" value="" autocomplete="true" required />',
            User_Account::get_field_password(),
            esc_html_x( 'Password', 'input placeholder', 'ilove-pdf' ),
        );
    }
}
