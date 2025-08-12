<?php

namespace Ilove_Pdf_WP\Account\Views;

use Ilove_Pdf_WP\Account\User_Account;

/**
 * Handles the rendering of the project selection form.
 *
 * @package Ilove_Pdf_WP\Account\Views
 * @since 3.0.0
 */
class Form_Project {
    /**
     * Renders the account projects form.
     *
     * @since 3.0.0
     *
     * phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
     */
    public static function render() {
        printf(
            '<form method="post" action="%1$s" class="ipdf-form form-%2$s ilovepdf-base__layout-flex ">
                <input type="hidden" name="action" value="%2$s" />
                %3$s
                %4$s
                %5$s
            </form>',
            esc_html( admin_url( 'admin-post.php' ) ),
            User_Account::get_action_change_project_key(),
            wp_nonce_field( -1, '_wpnonce_project', true, false ),
            self::create_field_projects(),
            self::create_submit_button(),
        );
    }

    /**
     * Creates the select field for choosing a project.
     *
     * @since 3.0.0
     * @return string HTML markup for the project select field.
     */
    private static function create_field_projects() {
        $options             = '';
        $db_projects_value   = User_Account::get_settings( User_Account::get_db_user_projects_key() );
        $db_public_key_value = User_Account::get_settings( User_Account::get_db_user_publickey_key() );

        foreach ( $db_projects_value as $project ) {
            $options .= sprintf(
                '<option value="%1$s" %3$s>%2$s</option>',
                $project['id'],
                $project['name'],
                selected( $db_public_key_value, $project['public_key'], false )
            );
        }

        return sprintf(
            '<div class="ilovepdf__account-project-select">
            <label for="%2$s">%1$s</label>
            <select class="ipdf-select" name="%2$s" id="%2$s">
                %3$s
            </select></div>',
            _x( 'Select your working proyect', 'Input Project Select label', 'ilove-pdf' ),
            User_Account::get_db_user_projects_key(),
            $options,
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
            esc_html_x( 'Change', 'Form change project: submit button', 'ilove-pdf' ),
        );
    }
}
