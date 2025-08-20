<?php

namespace Ilove_Pdf_WP\Account;

use Ilove_Pdf_WP\Helpers\DB_Handler;
use Ilove_Pdf_WP\Helpers\Admin_Notice;
use Ilove_Pdf_WP\Helpers\HTTP_Handler;

/**
 * Class User_Data
 *
 * Handles user data and statistics.
 *
 * @package Ilove_Pdf_WP\Account
 * @since 3.0.0
 */
class User_Data {
    use HTTP_Handler;
    use User_Statistics;

    /**
     * Database key for account information.
     *
     * @since 3.0.0
     * @var string
     */
    private static $db_key_account = 'ilovepdf_account';

    /**
     * Legacy database keys for account information.
     *
     * @since 3.0.0
     * @var array
     */
    private static $legacy_db_account_keys = array(
        'ilovepdf_user_email',
        'ilovepdf_user_id',
        'ilovepdf_user_private_key',
        'ilovepdf_user_public_key',
        'ilovepdf_user_token',
        'ilovepdf_wordpress_id',
    );

    /**
     * Database key for user public key.
     *
     * @since 3.0.0
     * @var string
     */
    private static $user_public_key = 'user_public_key';

    /**
     * Database key for user private key.
     *
     * @since 3.0.0
     * @var string
     */
    private static $user_private_key = 'user_private_key';

    /**
     * Database key for user token.
     *
     * @since 3.0.0
     * @var string
     */
    private static $user_token = 'user_token';

    /**
     * Database key for user WordPress ID.
     *
     * @since 3.0.0
     * @var string
     */
    private static $wordpress_id = 'user_wp_id';

    /**
     * Database key for user ID.
     *
     * @since 3.0.0
     * @var string
     */
    private static $user_id = 'user_id';

    /**
     * Database key for user email.
     *
     * @since 3.0.0
     * @var string
     */
    private static $user_email = 'user_email';

    /**
     * Database key for user name.
     *
     * @since 3.0.0
     * @var string
     */
    private static $user_name = 'user_name';

    /**
     * Database key for user projects.
     *
     * @since 3.0.0
     * @var string
     */
    private static $user_projects = 'user_projects';

    /**
     * Transient key for user data.
     *
     * @since 3.0.0
     * @var string
     */
    private static $transient_key = 'ipdf_account_info';

    /**
     * Creates a WordPress ID for the user.
     *
     * This method generates a unique WordPress ID based on the site URL and admin email,
     * and stores it in the database if it does not already exist.
     *
     * @since 3.0.0
     */
    public static function create_wordpress_id() {
        $wordpress_id = self::get_settings( self::$wordpress_id, false );

        if ( ! $wordpress_id ) {
            $wordpress_id = md5( get_option( 'siteurl' ) . get_option( 'admin_email' ) );
            DB_Handler::update_option(
                self::$db_key_account,
                array(
                    self::$wordpress_id => $wordpress_id,
                ),
            );
        }
    }

    /**
     * Retrieves the database key for account information.
     *
     * @return string The database key for account information.
     * @since 3.0.0
     */
    public static function get_db_key_account() {
        return self::$db_key_account;
    }

    /**
     * Retrieves the database key for user name.
     *
     * @return string The database key for user name.
     * @since 3.0.0
     */
    public static function get_db_user_name_key() {
        return self::$user_name;
    }

    /**
     * Retrieves the database key for user email.
     *
     * @return string The database key for user email.
     * @since 3.0.0
     */
    public static function get_db_user_email_key() {
        return self::$user_email;
    }

    /**
     * Retrieves the database key for user token.
     *
     * @return string The database key for user token.
     * @since 3.0.0
     */
    public static function get_db_user_token_key() {
        return self::$user_token;
    }

    /**
     * Retrieves the database key for user public key.
     *
     * @return string The database key for user public key.
     * @since 3.0.0
     */
    public static function get_db_user_publickey_key() {
        return self::$user_public_key;
    }

    /**
     * Retrieves the database key for user private key.
     *
     * @return string The database key for user private key.
     * @since 3.0.0
     */
    public static function get_db_user_privatekey_key() {
        return self::$user_private_key;
    }

    /**
     * Retrieves the database key for user ID.
     *
     * @return string The database key for user ID.
     * @since 3.0.0
     */
    public static function get_db_user_id_key() {
        return self::$user_id;
    }

    /**
     * Retrieves the database key for user projects.
     *
     * @return string The database key for user projects.
     * @since 3.0.0
     */
    public static function get_db_user_projects_key() {
        return self::$user_projects;
    }

    /**
     * Retrieves the database key for user WordPress ID.
     *
     * @return string The database key for user WordPress ID.
     * @since 3.0.0
     */
    public static function get_db_wordpress_id_key() {
        return self::$wordpress_id;
    }

    /**
     * Retrieves the transient key for user data.
     *
     * @return string The transient key for user data.
     * @since 3.0.0
     */
    public static function get_transient_key() {
        return self::$transient_key;
    }

    /**
     * Retrieves the account settings from the database.
     *
     * @param string $option_name Optional. The specific option key to retrieve.
     * @param mixed  $default_value Optional. The default value to return if the option does not exist.
     * @return mixed An option value or the full settings array.
     */
    public static function get_settings( $option_name = '', $default_value = array() ) {
        $settings = get_option( self::$db_key_account, $default_value );

        if ( ! empty( $option_name ) ) {
            return isset( $settings[ $option_name ] ) ? $settings[ $option_name ] : $default_value;
        }

        return $settings;
    }

    /**
     * Migrates account settings from the legacy database key to the current one.
     *
     * This function checks if legacy settings exist, and if so, transfers them
     * to the current option key and deletes the legacy option to avoid redundancy.
     */
    public static function migrate_account_settings() {
        $values_migrated = array();

        if ( get_option( 'ilovepdf_user_email' ) ) {
            $values_migrated[ self::$user_email ] = get_option( 'ilovepdf_user_email' );
        }

        if ( get_option( 'ilovepdf_user_id' ) ) {
            $values_migrated[ self::$user_id ] = get_option( 'ilovepdf_user_id' );
        }

        if ( get_option( 'ilovepdf_user_public_key' ) ) {
            $values_migrated[ self::$user_public_key ] = get_option( 'ilovepdf_user_public_key' );
        }

        if ( get_option( 'ilovepdf_user_private_key' ) ) {
            $values_migrated[ self::$user_private_key ] = get_option( 'ilovepdf_user_private_key' );
        }

        if ( get_option( 'ilovepdf_user_token' ) ) {
            $values_migrated[ self::$user_token ] = get_option( 'ilovepdf_user_token' );
        }
        if ( get_option( 'ilovepdf_wordpress_id' ) ) {
            $values_migrated[ self::$wordpress_id ] = get_option( 'ilovepdf_wordpress_id' );
        }

        if ( ! empty( $values_migrated ) ) {

            delete_transient( self::get_transient_key() );
            DB_Handler::update_option( self::$db_key_account, $values_migrated );
            self::get_user_data();

            foreach ( self::$legacy_db_account_keys as $key ) {
                delete_option( $key );
            }
        }
    }

    /**
     * Checks if the user has any credits available.
     *
     * @return bool True if the user has credits, false otherwise.
     * @since 3.0.0
     */
    public static function has_credits() {
        if ( ! User_Auth::is_user_logged_in() ) {
            return true;
        }

        $user_credits = array(
            'free'        => array(
                'used'  => self::get_credits( 'free', 'used' ),
                'limit' => self::get_credits( 'free', 'limit' ),
            ),
            'package'     => array(
                'used'  => self::get_credits( 'package', 'used' ),
                'limit' => self::get_credits( 'package', 'limit' ),
            ),
            'suscription' => array(
                'used'  => self::get_credits( 'suscription', 'used' ),
                'limit' => self::get_credits( 'suscription', 'limit' ),
            ),
        );

        foreach ( $user_credits as $type => $credits ) {
            if ( $credits['used'] < $credits['limit'] ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Popup Buymore Action
     *
     * @since 1.0.0
     */
    public static function popup_buymore_action() {

        add_thickbox();

        printf(
            '<div id="ipdf-popup-buymore" style="display:none;">
                <div class="ipdf-popup-buymore-content-box">
                    <figure class="ilovepdf-base__layout-flex ilovepdf-base__layout-justify--center">
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="150px" height="75.51px" viewBox="0 0 300 75.51" enable-background="new 0 0 300 75.51" xml:space="preserve"><g><path fill="#E5322D" d="M94.313,2.543c-4.785,2.309-8.374,6.2-10.995,10.612C79.104,6.071,72.405,0.326,62.259,0.326   c-10.15,0-22.594,8.614-22.594,23.165c0,14.732,12.293,21.715,18.382,25.658c6.508,4.211,17.613,11.867,25.27,26.036   c7.66-14.168,18.763-21.825,25.273-26.036c4.574-2.965,12.655-7.647,16.387-16.047L94.313,2.543z M93.946,33.938V6.254   l27.684,27.683H93.946z"></path><g><path d="M0.458,59.164H3.89c1.088,0,2.344-1.507,2.344-2.511V20.24c0-1.004-1.256-2.427-2.344-2.427H0.458v-8.79h27.54v8.79    h-3.516c-1.088,0-2.26,1.423-2.26,2.427v36.413c0,1.005,1.172,2.511,2.26,2.511h3.516v8.455H0.458V59.164z"></path><path d="M133.383,59.164h2.846c1.172,0,2.427-1.507,2.427-2.511V20.24c0-1.004-1.256-2.427-2.427-2.427h-2.846v-8.79h26.619    c15.654,0,24.192,5.525,24.192,18.583c0,12.724-9.041,18.499-24.778,18.499h-4.855v13.059h6.78v8.455h-27.958V59.164z     M159.166,37.484c7.031,0,8.873-4.018,8.873-9.626c0-5.525-1.842-9.459-8.873-9.459h-4.855v19.086H159.166z"></path><path d="M189.3,59.164h2.846c1.172,0,2.427-1.507,2.427-2.511V20.24c0-1.004-1.255-2.427-2.427-2.427H189.3v-8.79h25.531    c20.843,0,31.725,9.041,31.725,28.963c0,19.588-11.049,29.633-32.144,29.633H189.3V59.164z M214.412,58.746    c10.547,0,15.737-6.278,15.737-20.341c0-13.979-5.106-20.007-15.737-20.007h-3.934v40.347H214.412z"></path><path d="M251.912,59.164h2.846c1.172,0,2.427-1.507,2.427-2.511V20.24c0-1.004-1.255-2.427-2.427-2.427h-2.846v-8.79h47.63v18.081    h-9.375l-1.423-8.622H273.09v16.407h7.617l0.67-5.525H290v20.341h-8.622l-0.67-5.776h-7.617v15.235h6.781v8.455h-27.959V59.164z"></path></g><polygon fill="#FFFFFF" points="93.946,33.938 93.946,6.254 121.63,33.938  "></polygon></g></svg>
                    </figure>
                    <h3>%1$s</h3>
                    <p>%2$s</p>
                    <div class="ilovepdf-base__layout-flex ilovepdf-base__layout-gap--small ilovepdf-base__layout-justify--center">
                        <a href="https://iloveapi.com/pricing" target="_blank" class="ipdf-btn ipdf-btn--primary">%3$s</a>
                        <a href="#" onClick="tb_remove();" class="ipdf-btn ipdf-btn--secondary">%4$s</a>
                    </div>
                </div>
            </div>',
            esc_html_x( 'You have no more credits!', 'credits popup: title', 'ilove-pdf' ),
            esc_html_x( 'You can purchase more credits to continue processing files, or you can wait until your free credits are restored.', 'credits popup: content', 'ilove-pdf' ),
            esc_html_x( 'Buy credits', 'credits popup: button link', 'ilove-pdf' ),
            esc_html_x( 'Cancel', 'credits popup: button action', 'ilove-pdf' ),
        );
    }

    /**
     * Updates user data in the database.
     * This method updates the user data in the database with the provided user data.
     *
     * @param array $user_data The user data to update.
     * @since 3.0.0
     * @return void
     */
    public static function update_user_data( $user_data ) {
        $data = array(
            self::$user_name        => $user_data['name'],
            self::$user_email       => $user_data['email'],
            self::$user_token       => $user_data['token'],
            self::$user_public_key  => $user_data['projects'][0]['public_key'],
            self::$user_private_key => $user_data['projects'][0]['secret_key'],
            self::$user_id          => $user_data['id'],
            self::$user_projects    => $user_data['projects'],
            self::$wordpress_id     => $user_data['wordpress_id'],
        );

        DB_Handler::update_option( self::get_db_key_account(), $data );
    }

    /**
     * Retrieves user data from the iLoveAPI.
     *
     * @return array|false User data if successful, false if not logged in or an error occurs.
     */
    public static function get_user_data() {
        if ( ! User_Auth::is_user_logged_in() ) {
            return false;
        }

        if ( ! ( current_user_can( 'manage_options' ) ) ) {
            Admin_Notice::add_notice(
                _x( 'You do not have permission to edit the options.', 'Error message, user without permissions.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        if ( false !== get_transient( self::get_transient_key() ) ) {
            return get_transient( self::get_transient_key() );
        }

        $user_id    = self::get_settings( self::get_db_user_id_key(), false );
        $user_token = self::get_settings( self::get_db_user_token_key(), '' );

        $response = wp_remote_get(
            User_Auth::$ilove_api_url . '/' . $user_id,
            array(
                'headers' => array( 'Authorization' => 'Bearer ' . $user_token ),
            )
        );

        if ( is_wp_error( $response ) ) {
            Admin_Notice::add_notice(
                $response->get_error_message(),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        if ( 200 !== $response['response']['code'] ) {
            $error_body    = json_decode( $response['body'], true );
            $error_message = self::get_message_error( $error_body, _x( 'There was a problem trying to get the user data. Please try again later.', 'User Account: Error message.', 'ilove-pdf' ) );

            Admin_Notice::add_notice(
                $error_message,
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $data         = json_decode( $response['body'], true );
        $wordpress_id = self::get_settings( self::$wordpress_id, false );
        $new_data     = array(
            'name'         => $data['name'],
            'email'        => $data['email'],
            'token'        => $user_token,
            'id'           => $user_id,
            'projects'     => $data['projects'],
            'wordpress_id' => $wordpress_id,
        );

        self::update_user_data( $new_data );
        set_transient( self::get_transient_key(), $data, DAY_IN_SECONDS );

        return $data;
    }
}
