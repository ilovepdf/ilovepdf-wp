<?php

namespace Ilove_Pdf_WP\Tools\Compress;

use Ilove_Pdf_WP\Helpers\DB_Handler;
use Ilove_Pdf_WP\Helpers\Admin_Notice;
use Ilove_Pdf_WP\Helpers\HTTP_Handler;
use Ilove_Pdf_WP\Tools\Compress\Options;

/**
 * Handles tool compress settings.
 *
 * @package Ilove_Pdf_WP\Tools\Compress
 * @since 3.0.0
 */
class Settings extends Options {

    use HTTP_Handler;

    /**
     * Action key for the settings form submission.
     *
     * @var string
     */
    private static $action_key = 'ipdf_compress_settings';

    /**
     * Main database key where compress settings are stored.
     *
     * @var string
     */
    private static $db_key_compress_settings = 'ilovepdf_compress_settings';

    /**
     * Legacy database key for backward compatibility.
     *
     * @since 3.0.0 The key is obsolete, it is replaced by ilovepdf_compress_settings.
     * @since 1.0.0
     * @var string
     */
    private static $legacy_db_key_compress_settings = 'ilove_pdf_display_settings_compress';

    /**
     * Constructor. Hooks into WordPress to handle the admin form submission.
     */
    public function __construct() {
        add_action( 'admin_post_' . self::$action_key, array( $this, 'handle_action_save' ) );
    }

    /**
     * Handles the saving of compress settings submitted via the admin form.
     */
    public function handle_action_save() {
        if ( ! ( current_user_can( 'manage_options' ) ) ) {
            Admin_Notice::add_notice(
                _x( 'You do not have permission to edit these settings', 'Error message, user without permissions.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        if ( isset( $_POST['_wpnonce'] ) && ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ) ) ) {
            Admin_Notice::add_notice(
                _x( 'Couldn\'t complete the request. Please refresh and try again.', 'Error message, invalid nonce code.', 'ilove-pdf' ),
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

        DB_Handler::update_option( self::$db_key_compress_settings, $posts_value );

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
     * Returns the database key for compress settings.
     *
     * @return string
     */
    public static function get_db_key_settings() {
        return self::$db_key_compress_settings;
    }

    /**
     * Returns the legacy database key for compress settings.
     *
     * @return string
     */
    public static function get_legacy_db_key_compress_settings() {
        return self::$legacy_db_key_compress_settings;
    }

    /**
     * Retrieves the compress settings from the database.
     *
     * @param string $option_name Optional. The specific option key to retrieve.
     * @return mixed An option value or the full settings array.
     */
    public static function get_compress_settings( $option_name = '' ) {
        $compress_settings = DB_Handler::get_option( self::$db_key_compress_settings, array() );

        if ( ! empty( $option_name ) ) {
            return isset( $compress_settings[ $option_name ] ) ? $compress_settings[ $option_name ] : '';
        }

        return $compress_settings;
    }

    /**
     * Migrates compress settings from the legacy database key to the current one.
     *
     * This function checks if legacy settings exist, and if so, transfers them
     * to the current option key and deletes the legacy option to avoid redundancy.
     *
     * @since 3.0.0
     */
    public static function migrate() {
        $settings                 = DB_Handler::get_option( self::$db_key_compress_settings, array() );
        $legacy_compress_settings = DB_Handler::get_option( self::$legacy_db_key_compress_settings, array() );
        $values_migrated          = array();

        if ( isset( $legacy_compress_settings['ilove_pdf_compress_active'] ) ) {
            $values_migrated[ self::get_field_compress_active() ] = 'on';
        }

        if ( isset( $legacy_compress_settings['ilove_pdf_compress_autocompress_new'] ) ) {
            $values_migrated[ self::get_field_auto_compress() ] = 'on';
        }

        if ( isset( $legacy_compress_settings['ilove_pdf_compress_quality'] ) ) {

            switch ( $legacy_compress_settings['ilove_pdf_compress_quality'] ) {
                case 0:
                    $values_migrated[ self::get_field_compression_level() ] = 'low';
                    break;
                case 1:
                    $values_migrated[ self::get_field_compression_level() ] = 'recommended';
                    break;
                case 2:
                    $values_migrated[ self::get_field_compression_level() ] = 'extreme';
                    break;
            }
        }

        DB_Handler::update_option( self::$db_key_compress_settings, array_merge( $settings, $values_migrated ) );
        DB_Handler::delete_option( self::$legacy_db_key_compress_settings );
    }
}
