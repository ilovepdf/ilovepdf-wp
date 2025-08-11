<?php

namespace Ilove_Pdf_WP\Tools\General\Views;

use Ilove_Pdf_WP\Helpers\File_System;
use Ilove_Pdf_WP\Tools\Base\Form;
use Ilove_Pdf_WP\Tools\General\Settings;

/**
 * Handles rendering of the general options.
 *
 * Contains methods for creating form fields, buttons and sections related to backup/restore functionality.
 *
 * @package Ilove_Pdf_WP\Tools\General\Views
 * @since 3.0.0
 */
class Form_Options extends Form {
    /**
     * Renders the general options form.
     *
     * This method generates the HTML for the general options form, including form fields,
     * buttons, and sections for backup/restore functionality.
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
                <h2>%5$s</h2>
                <hr class="ipdf-divisor" />
                <div class="ilovepdf-base__layout-flex ilovepdf-base__layout-flex-wrap ilovepdf-settings__main__form_fields">
                    <div class="ilovepdf-settings__main__form_fields_inner_field">
                        %6$s
                    </div>
                    <div class="ilovepdf-settings__main__form_fields_inner_field">
                        %7$s
                    </div>
                </div>
                %4$s
            </form>',
            esc_html( admin_url( 'admin-post.php' ) ),
            Settings::get_action_key(),
            wp_nonce_field( -1, '_wpnonce', true, false ),
            self::create_submit_button(),
            esc_html_x( 'General Options', 'form title', 'ilove-pdf' ),
            self::create_field_backup(),
            self::create_section_restore_files()
        );
    }

    /**
     * Creates the HTML for the backup toggle field in the options form.
     *
     * This field allows the user to enable or disable backups of original files
     * before they are compressed or watermarked.
     *
     * @since 3.0.0
     * @return string HTML markup for the backup option field.
     */
    protected static function create_field_backup() {
        $backup_folder = sprintf(
            wp_kses_post(
                            /* translators: %s: backup folder path */
                __( 'Backup files will be stored at: %s', 'ilove-pdf' )
            ),
            '<code>wp-content/uploads' . File_System::$folder_backup . '</code>'
        );

        return sprintf(
            '<div class="ipdf-input-group-switch">
                <input class="ipdf-input-slider" type="checkbox" id="%1$s" name="%1$s" %2$s />
                <span class="ipdf-input-group-switch-slider"></span>
            </div>
            <label for="%1$s">%3$s</label>
            <p>%4$s</p>
            <p>%5$s</p>',
            Settings::get_field_backup(),
            Settings::get_general_settings( Settings::get_field_backup() ) ? 'checked' : '',
            esc_html_x( 'Backup original Files', 'checkbox field label', 'ilove-pdf' ),
            esc_html__( 'Enable this option to make a backup of your files before being compress or watermarked. These backups will allow you to restore your original files at cost of taking server memory space.', 'ilove-pdf' ),
            $backup_folder
        );
    }

    /**
     * Creates the restore section in the options form.
     *
     * This section includes warnings, backup size info, and buttons to restore
     * or clear all backup files.
     *
     * @since 3.0.0
     * @return string HTML markup for the restore section.
     */
    protected static function create_section_restore_files() {
        $message_warning_restore = sprintf(
            wp_kses_post(
                /* translators: %1$s and %2$s: html tags */
                __( 'All backup files can be restored. This will restore the original files as they were before compression or watermarking. %1$s Warning: Any changes made AFTER Watermark/Compress would be also restored. %2$s', 'ilove-pdf' )
            ),
            '<span style="color: red;">',
            '</span>'
        );

        $message_warning_clear = sprintf(
            wp_kses_post(
                /* translators: %1$s and %2$s: html tags */
                __( 'You can also clear all your backup files to free memory space. %1$s Warning: Clear backups will prevent you to restore original files. %2$s', 'ilove-pdf' )
            ),
            '<span style="color: red;">',
            '</span>'
        );

        $backup_folder_size = sprintf(
            wp_kses_post(
                /* translators: %s: backup size */
                __( 'Backup size: %s', 'ilove-pdf' )
            ),
            File_System::get_size_backup()
        );

        return sprintf(
            '<h4>%1$s</h4>
            <p>%2$s</p>
            <p>%3$s</p>
            <div class="ipdf-spacer"><span>%6$s</span></div>
            <div class="ilovepdf-base__layout-flex ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small">
                %4$s
                %5$s
            </div>
            ',
            esc_html_x( 'Restore Original Files', 'section title', 'ilove-pdf' ),
            $message_warning_restore,
            $message_warning_clear,
            self::create_restoreall_button(),
            self::create_clear_button(),
            $backup_folder_size
        );
    }

    /**
     * Creates the "Restore All" button.
     *
     * @since 3.0.0
     * @return string HTML markup for the restore all button.
     */
    protected static function create_restoreall_button() {
        return sprintf(
            '<button type="button" class="ipdf-btn--outline-primary" id="ilovepdf_restore_all" %1$s>%2$s</button>',
            self::state_button(),
            esc_html_x( 'Restore All', 'button', 'ilove-pdf' )
        );
    }

    /**
     * Creates the "Clear Backup" button.
     *
     * Button is currently disabled by default and must be enabled via JS.
     *
     * @since 3.0.0
     * @return string HTML markup for the clear backup button.
     */
    protected static function create_clear_button() {
        return sprintf(
            '<button type="button" class="ipdf-btn ipdf-btn--remove ipdf-btn--outline-primary" id="ilovepdf_clear_backup" %1$s>%2$s</button>',
            self::state_button(),
            esc_html_x( 'Clear backup', 'button', 'ilove-pdf' )
        );
    }

    /**
     * Check the status of buttons.
     *
     * Checks if a backup exists and returns disabled if no backup exists.
     *
     * @since 3.0.0
     * @return string 'disabled' or '' depending on the backup state.
     */
    protected static function state_button() {
        $state         = 'disabled';
        $option_backup = Settings::get_general_settings( Settings::get_field_backup() );

        if ( isset( $option_backup ) && File_System::get_size_backup() ) {
            $state = '';
        }

        return $state;
    }
}
