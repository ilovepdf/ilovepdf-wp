<?php

namespace Ilove_Pdf_WP\Tools\Base;

use Ilove_Pdf_WP\Account\User_Auth;

/**
 * Base class for forms in the iLovePDF WordPress plugin.
 *
 * Provides common functionality for creating forms, such as submit buttons.
 *
 * @package Ilove_Pdf_WP\Tools\Base
 * @since 3.0.0
 */
class Form {
    /**
     * Creates the submit button for the form.
     *
     * The button is disabled if the user is not logged in.
     *
     * @since 3.0.0
     * @return string HTML markup for the submit button.
     */
    protected static function create_submit_button() {
        return sprintf(
            '<div class="ilovepdf-form-elements ilovepdf ilovepdf-base__layout-flex ilovepdf-base__layout-justify--end">
                <button %1$s type="submit" name="submit" class="ipdf-btn ipdf-btn--secondary ipdf-tooltip ipdf-input-submit">
                    %2$s
                    <span class="ipdf-tooltip-text">
                        %3$s
                    </span>
                </button>
            </div>',
            ! User_Auth::is_user_logged_in() ? 'disabled' : '',
            esc_html_x( 'Save', 'button link', 'ilove-pdf' ),
            esc_html_x( 'Register and login with us to save settings changes', 'tooltip: Appears when the user is not logged in.', 'ilove-pdf' )
        );
    }
}
