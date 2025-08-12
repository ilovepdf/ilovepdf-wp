<?php

namespace Ilove_Pdf_WP\Tools\General;

use Ilove_Pdf_WP\Helpers\Admin_Notice;
use Ilove_Pdf_WP\Helpers\DB_Handler;
use Ilove_Pdf_WP\Helpers\HTTP_Handler;

/**
 * Handles general settings for the iLovePDF WordPress plugin.
 *
 * @package Ilove_Pdf_WP\Tools\General
 * @since 3.0.0
 */
class Settings {

    use HTTP_Handler;

    /**
     * Action key for the settings form submission.
     *
     * @var string
     */
    private static $action_key = 'ipdf_general_settings';

    /**
     * Main database key where general settings are stored.
     *
     * @var string
     */
    private static $db_key_general_settings = 'ilovepdf_general_settings';

    /**
     * Legacy database key for backward compatibility.
     *
     * @since 3.0.0 The key is obsolete, it is replaced by ilovepdf_general_settings.
     * @since 1.0.0
     * @var string
     */
    private static $legacy_db_key_general_settings = 'ilove_pdf_display_general_settings';

    /**
     * Field option key used for backup purposes.
     *
     * @since 3.0.0
     * @var string
     */
    private static $field_backup = 'ipdf_option_backup';

    /**
     * Constructor. Hooks into WordPress to handle the admin form submission.
     */
    public function __construct() {
        add_action( 'admin_post_' . self::$action_key, array( $this, 'handle_action_save' ) );
    }

    /**
     * Handles the saving of general settings submitted via the admin form.
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

        DB_Handler::update_option( self::$db_key_general_settings, $posts_value );

        Admin_Notice::add_notice(
            _x( 'General settings saved successfully.', 'Form submission: Success message', 'ilove-pdf' ),
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
     * Returns the field option key used for backups.
     *
     * @return string
     */
    public static function get_field_backup() {
        return self::$field_backup;
    }

    /**
     * Returns the database key for general settings.
     *
     * @return string
     */
    public static function get_db_key_general_settings() {
        return self::$db_key_general_settings;
    }

    /**
     * Returns the legacy database key for general settings.
     *
     * @return string
     */
    public static function get_legacy_db_key_general_settings() {
        return self::$legacy_db_key_general_settings;
    }

    /**
     * Retrieves the general settings from the database.
     *
     * @param string $option_name Optional. The specific option key to retrieve.
     * @return mixed An option value or the full settings array.
     */
    public static function get_general_settings( $option_name = '' ) {
        $general_settings = get_option( self::$db_key_general_settings, array() );

        if ( ! empty( $option_name ) ) {
            return isset( $general_settings[ $option_name ] ) ? $general_settings[ $option_name ] : '';
        }

        return $general_settings;
    }

    /**
     * Migrates general settings from the legacy database key to the current one.
     *
     * This function checks if legacy settings exist, and if so, transfers them
     * to the current option key and deletes the legacy option to avoid redundancy.
     */
    public static function migrate_general_settings() {
        $legacy_general_settings = get_option( self::$legacy_db_key_general_settings, array() );
        $values_migrated         = array();

        if ( ! empty( $legacy_general_settings ) ) {
            if ( ! isset( $legacy_general_settings['ilove_pdf_general_backup'] ) ) {
                $values_migrated['ilovepdf_general_settings'] = 'on';
            }

            DB_Handler::update_option( self::$db_key_general_settings, $values_migrated );
            delete_option( self::$legacy_db_key_general_settings );
        }
    }
}
