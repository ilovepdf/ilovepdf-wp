<?php

namespace Ilove_Pdf_WP\Tools\Watermark;

use Ilove_Pdf_WP\Helpers\DB_Handler;
use Ilove_Pdf_WP\Helpers\Admin_Notice;
use Ilove_Pdf_WP\Helpers\HTTP_Handler;
use Ilove_Pdf_WP\Tools\Watermark\Options;

/**
 * Handles tool watermark settings.
 *
 * @package Ilove_Pdf_WP\Tools\Watermark
 * @since 3.0.0
 */
class Settings extends Options {

    use HTTP_Handler;

    /**
     * Action key for the settings form submission.
     *
     * @var string
     */
    private static $action_key = 'ipdf_watermark_settings';

    /**
     * Main database key where watermark settings are stored.
     *
     * @var string
     */
    private static $db_key_settings = 'ilovepdf_watermark_settings';

    /**
     * Legacy database key for backward compatibility.
     *
     * @since 3.0.0 The key is obsolete, it is replaced by ilovepdf_watermark_settings.
     * @since 1.0.0
     * @var string
     */
    private static $legacy_db_key_settings = 'ilove_pdf_display_settings_watermark';

    /**
     * Legacy database key for backward compatibility.
     *
     * @since 3.0.0 The key is obsolete, it is replaced by ilovepdf_watermark_settings.
     * @since 1.0.0
     * @var string
     */
    private static $legacy_db_key_settings_format = 'ilove_pdf_display_settings_format_watermark';

    /**
     * Constructor. Hooks into WordPress to handle the admin form submission.
     */
    public function __construct() {
        add_action( 'admin_post_' . self::$action_key, array( $this, 'handle_action_save' ) );
    }

    /**
     * Handles the saving of watermark settings submitted via the admin form.
     */
    public function handle_action_save() {
        if ( ! ( current_user_can( 'manage_options' ) ) ) {
            Admin_Notice::add_notice(
                _x( 'You do not have permission to edit the options.', 'Error message, user without permissions.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        if ( isset( $_POST['_wpnonce'] ) && ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ) ) ) {
            Admin_Notice::add_notice(
                _x( 'There was a problem validating the nonce code, please try again later.', 'Error message, invalid nonce code.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $posts_value = array();

        foreach ( $_POST as $key => $post_value ) {
            if ( strpos( $key, 'ipdf_option_' ) === 0 ) {
                $posts_value[ $key ] = wp_unslash( $post_value );
            }
        }

        DB_Handler::update_option( self::$db_key_settings, $posts_value );

        Admin_Notice::add_notice(
            _x( 'Changes saved.', 'Form submission: Success message', 'ilove-pdf' ),
            'success',
        );

        wp_safe_redirect( wp_get_referer() );
        exit;
    }

    /**
     * Returns the action key used for the form submission.
     *
     * @return string
     */
    public static function get_action_key() {
        return self::$action_key;
    }

    /**
     * Returns the database key for watermark settings.
     *
     * @return string
     */
    public static function get_db_key_settings() {
        return self::$db_key_settings;
    }

    /**
     * Returns the legacy database key for watermark settings.
     *
     * @param string $key key type, can be 'settings' or 'format'.
     * @return string Key for watermark settings or format settings.
     */
    public static function get_legacy_db_key_settings( $key = 'settings' ) {
        if ( 'format' === $key ) {
            return self::$legacy_db_key_settings_format;
        }

        return self::$legacy_db_key_settings;
    }

    /**
     * Retrieves the watermark settings from the database.
     *
     * @param string $option_name Optional. The specific option key to retrieve.
     * @param mixed  $default_value     Optional. Default value to return if the option is not set.
     * @return mixed An option value or the full settings array.
     */
    public static function get_settings( $option_name = '', $default_value = array() ) {
        $settings = get_option( self::$db_key_settings, $default_value );

        if ( ! empty( $option_name ) ) {
            return isset( $settings[ $option_name ] ) ? $settings[ $option_name ] : '';
        }

        return $settings;
    }

    /**
     * Migrates watermark settings from the legacy database key to the current one.
     *
     * This function checks if legacy settings exist, and if so, transfers them
     * to the current option key and deletes the legacy option to avoid redundancy.
     */
    public static function migrate() {
        $legacy_watermark_settings        = get_option( self::$legacy_db_key_settings, array() );
        $legacy_watermark_settings_format = get_option( self::$legacy_db_key_settings_format, array() );
        $values_migrated                  = array();

        if ( ! empty( $legacy_watermark_settings ) ) {

            if ( isset( $legacy_watermark_settings['ilove_pdf_watermark_active'] ) ) {
                $values_migrated[ self::get_field_watermark_active() ] = 'on';
            }

            if ( isset( $legacy_watermark_settings['ilove_pdf_watermark_auto'] ) ) {
                $values_migrated[ self::get_field_auto_watermark() ] = 'on';
            }

            DB_Handler::update_option( self::$db_key_settings, $values_migrated );
            delete_option( self::$legacy_db_key_settings );
        }

        if ( ! empty( $legacy_watermark_settings_format ) ) {

            if ( isset( $legacy_watermark_settings_format['ilove_pdf_format_watermark_mode'] ) ) {
                $values_migrated[ self::get_field_mode() ] = $legacy_watermark_settings_format['ilove_pdf_format_watermark_mode'];
            }

            if ( isset( $legacy_watermark_settings_format['ilove_pdf_format_watermark_text'] ) ) {
                $values_migrated[ self::get_field_text_mode() ] = $legacy_watermark_settings_format['ilove_pdf_format_watermark_text'];
            }

            if ( isset( $legacy_watermark_settings_format['ilove_pdf_format_watermark_text_size'] ) ) {
                $values_migrated[ self::get_field_font_size() ] = $legacy_watermark_settings_format['ilove_pdf_format_watermark_text_size'];
            }

            if ( isset( $legacy_watermark_settings_format['ilove_pdf_format_watermark_font_family'] ) ) {
                $values_migrated[ self::get_field_font_family() ] = $legacy_watermark_settings_format['ilove_pdf_format_watermark_font_family'];
            }

            if ( isset( $legacy_watermark_settings_format['ilove_pdf_format_watermark_text_color'] ) ) {
                $values_migrated[ self::get_field_font_color() ] = $legacy_watermark_settings_format['ilove_pdf_format_watermark_text_color'];
            }

            if ( isset( $legacy_watermark_settings_format['ilove_pdf_format_watermark_image'] ) ) {
                $values_migrated[ self::get_field_image_mode() ] = $legacy_watermark_settings_format['ilove_pdf_format_watermark_image'];
            }

            if ( isset( $legacy_watermark_settings_format['ilove_pdf_format_watermark_vertical'] ) && isset( $legacy_watermark_settings_format['ilove_pdf_format_watermark_horizontal'] ) ) {
                $position_horizontal = 'center';
                $position_vertical   = 'middle';

                switch ( $legacy_watermark_settings_format['ilove_pdf_format_watermark_vertical'] ) {
                    case 0:
                        $position_vertical = 'bottom';
                        break;
                    case 1:
                        $position_vertical = 'top';
                        break;
                    case 2:
                        $position_vertical = 'middle';
                        break;
                }

                switch ( $legacy_watermark_settings_format['ilove_pdf_format_watermark_horizontal'] ) {
                    case 0:
                        $position_horizontal = 'left';
                        break;
                    case 1:
                        $position_horizontal = 'right';
                        break;
                    case 2:
                        $position_horizontal = 'center';
                        break;
                }

                $values_migrated[ self::get_field_position() ] = "$position_horizontal $position_vertical";
            }

            if ( isset( $legacy_watermark_settings_format['ilove_pdf_format_watermark_opacity'] ) ) {
                $values_migrated[ self::get_field_transparency() ] = $legacy_watermark_settings_format['ilove_pdf_format_watermark_opacity'];
            }

            if ( isset( $legacy_watermark_settings_format['ilove_pdf_format_watermark_rotation'] ) ) {
                $values_migrated[ self::get_field_rotation() ] = $legacy_watermark_settings_format['ilove_pdf_format_watermark_rotation'];
            }

            if ( isset( $legacy_watermark_settings_format['ilove_pdf_format_watermark_layer'] ) ) {
                $layer = 'above';

                switch ( $legacy_watermark_settings_format['ilove_pdf_format_watermark_layer'] ) {
                    case 0:
                        $layer = 'above';
                        break;
                    case 1:
                        $layer = 'below';
                        break;
                }

                $values_migrated[ self::get_field_layer() ] = $layer;
            }

            if ( isset( $legacy_watermark_settings_format['ilove_pdf_format_watermark_mosaic'] ) ) {
                $values_migrated[ self::get_field_mosaic() ] = 'on';
            }

            DB_Handler::update_option( self::$db_key_settings, $values_migrated );
            delete_option( self::$legacy_db_key_settings_format );
        }
    }
}
