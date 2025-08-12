<?php

namespace Ilove_Pdf_WP\Tools\Compress\Views;

use Ilove_Pdf_WP\Tools\Base\Form;
use Ilove_Pdf_WP\Tools\Compress\Settings;

/**
 * Handles rendering of the compress options form.
 *
 * Contains methods for creating form fields, buttons and sections.
 *
 * @package Ilove_Pdf_WP\Tools\Compress\Views
 * @since 3.0.0
 */
class Form_Options extends Form {
    /**
     * Renders the compress options form.
     *
     * This method generates the HTML for the compress options form.
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
                <div class="ilovepdf-settings__main__form_fields">
                    <div class="ilovepdf-settings__main__form_fields_inner_field">
                        %6$s
                    </div>
                </div>
                <hr class="ipdf-divisor" />
                <div class="ilovepdf-settings__main__form_fields">
                    <div class="ilovepdf-settings__main__form_fields_inner_field">
                        %7$s
                    </div>
                </div>
                <hr class="ipdf-divisor" />
                <div class="ilovepdf-settings__main__form_fields">
                    <div class="ilovepdf-settings__main__form_fields_inner_field">
                        %8$s
                    </div>
                </div>
                %4$s
            </form>',
            esc_html( admin_url( 'admin-post.php' ) ),
            Settings::get_action_key(),
            wp_nonce_field( -1, '_wpnonce', true, false ),
            self::create_submit_button(),
            esc_html_x( 'Compress Settings', 'form title', 'ilove-pdf' ),
            self::create_field_compress_active(),
            self::create_field_auto_compress(),
            self::create_field_compression_level()
        );
    }

    /**
     * Creates the HTML for the tool active toggle field in the options form.
     *
     * This field allows the user to enable or disable the compress tool.
     *
     * @since 3.0.0
     * @return string HTML markup for the option field.
     */
    protected static function create_field_compress_active() {

        return sprintf(
            '<div class="ipdf-input-group-switch">
                <input class="ipdf-input-slider" type="checkbox" id="%1$s" name="%1$s" %2$s />
                <span class="ipdf-input-group-switch-slider"></span>
            </div>
            <label for="%1$s">%3$s</label>
            <p>%4$s</p>',
            Settings::get_field_compress_active(),
            Settings::get_compress_settings( Settings::get_field_compress_active() ) ? 'checked' : '',
            esc_html_x( 'Compress Activated', 'checkbox field label', 'ilove-pdf' ),
            esc_html__( 'Activate this tool in your WordPress dashboard. Activation will work only once you have registered and login as an iLoveAPI developer.', 'ilove-pdf' ),
        );
    }

    /**
     * Creates the HTML for the auto-compress active toggle field in the options form.
     *
     * This field allows the user to enable or disable the auto-compress feature.
     *
     * @since 3.0.0
     * @return string HTML markup for the option field.
     */
    protected static function create_field_auto_compress() {

        return sprintf(
            '<div class="ipdf-input-group-switch">
                <input class="ipdf-input-slider" type="checkbox" id="%1$s" name="%1$s" %2$s />
                <span class="ipdf-input-group-switch-slider"></span>
            </div>
            <label for="%1$s">%3$s</label>
            <p>%4$s</p>',
            Settings::get_field_auto_compress(),
            Settings::get_compress_settings( Settings::get_field_auto_compress() ) ? 'checked' : '',
            esc_html_x( 'Enable Autocompress Files', 'checkbox field label', 'ilove-pdf' ),
            esc_html__( 'With auto-compression enabled, any file uploaded to the Media folder will be automatically compressed. However, you can compress uncompressed files from Media.', 'ilove-pdf' ),
        );
    }

    /**
     * Creates the HTML for the compress level field in the options form.
     *
     * This field allows the user to select the compression level of the tool.
     *
     * @since 3.0.0
     * @return string HTML markup for the option field.
     */
    protected static function create_field_compression_level() {

        $db_compression_level = Settings::get_compress_settings( Settings::get_field_compression_level() );

        return sprintf(
            '<h4>%1$s</h4>
            <p>%2$s</p>
            <div class="ipdf-spacer">
                <div class="ipdf-input-group-switch">
                    <input class="ipdf-input-slider" type="radio" id="%4$s" name="%3$s" value="%4$s" %10$s />
                    <span class="ipdf-input-group-switch-slider"></span>
                </div>
                <label for="%4$s">%5$s</label>
            </div>
            <div class="ipdf-spacer">
                <div class="ipdf-input-group-switch">
                    <input class="ipdf-input-slider" type="radio" id="%6$s" name="%3$s" value="%6$s" %11$s />
                    <span class="ipdf-input-group-switch-slider"></span>
                </div>
                <label for="%6$s">%7$s</label>
            </div>
            <div class="ipdf-spacer">
                <div class="ipdf-input-group-switch">
                    <input class="ipdf-input-slider" type="radio" id="%8$s" name="%3$s" value="%8$s" %12$s />
                    <span class="ipdf-input-group-switch-slider"></span>
                </div>
                <label for="%8$s">%9$s</label>
            </div>',
            esc_html_x( 'Compression level', 'subtitle section', 'ilove-pdf' ),
            esc_html__( 'You can choose the appropriate compression level for your files. By default, it will be recommended.', 'ilove-pdf' ),
            Settings::get_field_compression_level(),
            Settings::get_compress_level( 'extreme' ),
            esc_html_x( 'Extreme: Less quality, high compression', 'radio button field label', 'ilove-pdf' ),
            Settings::get_compress_level( 'recommended' ),
            esc_html_x( 'Recommended: Good quality, good compression', 'radio button field label', 'ilove-pdf' ),
            Settings::get_compress_level( 'low' ),
            esc_html_x( 'Low: High quality, less compression', 'radio button field label', 'ilove-pdf' ),
            checked( $db_compression_level, Settings::get_compress_level( 'extreme' ), false ),
            checked( $db_compression_level, Settings::get_compress_level( 'recommended' ), false ),
            checked( $db_compression_level, Settings::get_compress_level( 'low' ), false ),
        );
    }
}
