<?php

namespace Ilove_Pdf_WP\Account;

use Ilove_Pdf_WP\Helpers\DB_Handler;
use Ilove_Pdf_WP\Helpers\Admin_Notice;
use Ilove_Pdf_WP\Helpers\HTTP_Handler;

/**
 * Handles user authentication.
 *
 * @package Ilove_Pdf_WP\Account
 * @since 3.0.0
 */
class User_Auth {
    use User_Form_Options;
    use HTTP_Handler;

    /**
     * IloveAPI User URL
     *
     * @since 3.0.0
     * @var string $ilove_api_url
     */
    public static $ilove_api_url = 'https://api.ilovepdf.com/v1/user';

    /**
     * Initialize hooks.
     *
     * @since 3.0.0
     */
    public function __construct() {
        add_action( 'admin_post_ilovepdf_user_register', array( $this, 'register_action' ) );
        add_action( 'admin_post_ilovepdf_user_login', array( $this, 'login_action' ) );
        add_action( 'admin_post_ilovepdf_user_logout', array( $this, 'logout_action' ) );
        add_action( 'admin_post_ilovepdf_user_change_project', array( $this, 'change_project_action' ) );
    }

    /**
     * Register Action.
     *
     * Handles the registration of a new user.
     *
     * @since 1.0.0
     */
    public function register_action() {

        if ( ! ( current_user_can( 'manage_options' ) ) ) {
            Admin_Notice::add_notice(
                _x( 'You do not have permission to edit the options.', 'Error message, user without permissions.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        if ( isset( $_POST['_wpnonce_register'] ) && ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce_register'] ) ) ) ) {
            Admin_Notice::add_notice(
                _x( 'There was a problem validating the nonce code, please try again later.', 'Error message, invalid nonce code.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        if ( isset( $_POST['action'] ) && self::get_action_register_key() !== $_POST['action'] ) {
            Admin_Notice::add_notice(
                _x( 'There was a problem creating your account. Please try again later.', 'Error message: account creation failed.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $field_email = self::get_field_email();
        if ( isset( $_POST[ $field_email ] ) && empty( trim( sanitize_email( wp_unslash( $_POST[ $field_email ] ) ) ) ) ) {
            Admin_Notice::add_notice(
                _x( 'The email field is required.', 'Form submission: Error message, invalid input field.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $field_password = self::get_field_password();
        if ( isset( $_POST[ $field_password ] ) && empty( trim( $_POST[ $field_password ] ) ) ) {//phpcs:ignore
            Admin_Notice::add_notice(
                _x( 'The password field is required.', 'Form submission: Error message, invalid input field.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $field_name = self::get_field_name();
        if ( isset( $_POST[ $field_name ] ) && empty( trim( sanitize_text_field( wp_unslash( $_POST[ $field_name ] ) ) ) ) ) {
            Admin_Notice::add_notice(
                _x( 'The name field is required.', 'Form submission: Error message, invalid input field.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $wordpress_id = User_Data::get_settings( User_Data::get_db_wordpress_id_key(), '' );

        $response = wp_remote_post(
            self::$ilove_api_url,
            array(
                'body' => array(
                    'name'         => sanitize_text_field( wp_unslash( $_POST[ $field_name ] ) ),
                    'email'        => sanitize_email( wp_unslash( $_POST[ $field_email ] ) ),
                    'new_password' => $_POST[ $field_password ], // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,
                    'wordpress_id' => $wordpress_id,
                ),
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
            $error_message = self::get_message_error( $error_body, _x( 'There was a problem creating your account. Please try again later.', 'Error message: account creation failed.', 'ilove-pdf' ) );

            Admin_Notice::add_notice(
                $error_message,
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $user                 = json_decode( $response['body'], true );
        $user['wordpress_id'] = $wordpress_id;

        User_Data::update_user_data( $user );

        Admin_Notice::add_notice(
            _x( 'User created successfully.', 'Form submission: Success message.', 'ilove-pdf' ),
            'success',
        );

        wp_safe_redirect( wp_get_referer() );
        exit;
    }

    /**
     * Login Action.
     *
     * Handles the login of a user.
     *
     * @since 1.0.0
     */
    public function login_action() {

        if ( ! ( current_user_can( 'manage_options' ) ) ) {
            Admin_Notice::add_notice(
                _x( 'You do not have permission to edit the options.', 'Error message, user without permissions.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        if ( isset( $_POST['_wpnonce_login'] ) && ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce_login'] ) ) ) ) {
            Admin_Notice::add_notice(
                _x( 'There was a problem validating the nonce code, please try again later.', 'Error message, invalid nonce code.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        if ( isset( $_POST['action'] ) && self::get_action_login_key() !== $_POST['action'] ) {
            Admin_Notice::add_notice(
                _x( 'There was a problem logging in. Please try again later.', 'Error message, login failed.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $field_email = self::get_field_email();
        if ( isset( $_POST[ $field_email ] ) && empty( trim( sanitize_email( wp_unslash( $_POST[ $field_email ] ) ) ) ) ) {
            Admin_Notice::add_notice(
                _x( 'The email field is required.', 'Form submission: Error message, invalid input field.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $field_password = self::get_field_password();
        if ( isset( $_POST[ $field_password ] ) && empty( trim( $_POST[ $field_password ] ) ) ) {//phpcs:ignore
            Admin_Notice::add_notice(
                _x( 'The password field is required.', 'Form submission: Error message, invalid input field.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $wordpress_id = User_Data::get_settings( User_Data::get_db_wordpress_id_key(), '' );

        $response = wp_remote_post(
            self::$ilove_api_url . '/login',
            array(
                'body' => array(
                    'email'        => sanitize_email( wp_unslash( $_POST[ $field_email ] ) ),
                    'password'     => $_POST[ $field_password ], // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
                    'wordpress_id' => $wordpress_id,
                ),
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
            $error_message = self::get_message_error( $error_body, _x( 'There was a problem logging in. Please try again later.', 'Error message, login failed.', 'ilove-pdf' ) );

            Admin_Notice::add_notice(
                $error_message,
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $user                 = json_decode( $response['body'], true );
        $user['wordpress_id'] = $wordpress_id;

        User_Data::update_user_data( $user );

        Admin_Notice::add_notice(
            _x( 'You have successfully logged in.', 'Form submission: Success message.', 'ilove-pdf' ),
            'success',
        );

        wp_safe_redirect( wp_get_referer() );
        exit;
    }

    /**
     * Logout Action.
     *
     * Handles the logout of a user.
     *
     * @since 1.0.0
     */
    public function logout_action() {
        if ( ! ( current_user_can( 'manage_options' ) ) ) {
            Admin_Notice::add_notice(
                _x( 'You do not have permission to edit the options.', 'Error message, user without permissions.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        if ( isset( $_POST['_wpnonce_logout'] ) && ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce_logout'] ) ) ) ) {
            Admin_Notice::add_notice(
                _x( 'There was a problem validating the nonce code, please try again later.', 'Error message, invalid nonce code.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        if ( isset( $_POST['action'] ) && self::get_action_logout_key() !== $_POST['action'] ) {
            Admin_Notice::add_notice(
                _x( 'There was a problem trying to log out. Please try again later.', 'Form submission: Error message, invalid action.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $wordpress_id = User_Data::get_settings( User_Data::get_db_wordpress_id_key(), '' );
        DB_Handler::delete_option( User_Data::get_db_key_account() );
        DB_Handler::delete_transient( User_Data::get_transient_key() );
        DB_Handler::update_option(
            User_Data::get_db_key_account(),
            array(
                User_Data::get_db_wordpress_id_key() => $wordpress_id,
            ),
        );

        Admin_Notice::add_notice(
            _x( 'You have successfully logged out.', 'Form submission: Success message.', 'ilove-pdf' ),
            'success',
        );

        wp_safe_redirect( wp_get_referer() );
        exit;
    }

    /**
     * Project Action.
     * Handles the change of user project.
     *
     * @since 1.0.0
     */
    public function change_project_action() {
        if ( ! ( current_user_can( 'manage_options' ) ) ) {
            Admin_Notice::add_notice(
                _x( 'You do not have permission to edit the options.', 'Error message, user without permissions.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        if ( isset( $_POST['_wpnonce_project'] ) && ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce_project'] ) ) ) ) {
            Admin_Notice::add_notice(
                _x( 'There was a problem validating the nonce code, please try again later.', 'Error message, invalid nonce code.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        if ( isset( $_POST['action'] ) && self::get_action_change_project_key() !== $_POST['action'] ) {
            Admin_Notice::add_notice(
                _x( 'There was a problem changing the project. Please try again later.', 'Error message.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        if ( ! array_key_exists( User_Data::get_db_user_projects_key(), $_POST ) ) {
            Admin_Notice::add_notice(
                _x( 'The project field is required.', 'Error message, invalid input field.', 'ilove-pdf' ),
                'error',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $settings      = User_Data::get_settings();
        $projects      = User_Data::get_settings( User_Data::get_db_user_projects_key() );
        $project_found = array_search( (int) $_POST[ User_Data::get_db_user_projects_key() ], array_column( $projects, 'id' ), true );

        if ( false === $project_found ) {
            Admin_Notice::add_notice(
                _x( 'The selected project is not valid.', 'Form submission: Error message, invalid input field.', 'ilove-pdf' ),
                'warning',
            );

            wp_safe_redirect( wp_get_referer() );
            exit;
        }

        $settings[ User_Data::get_db_user_name_key() ]       = $projects[ $project_found ]['name'];
        $settings[ User_Data::get_db_user_publickey_key() ]  = $projects[ $project_found ]['public_key'];
        $settings[ User_Data::get_db_user_privatekey_key() ] = $projects[ $project_found ]['secret_key'];

        DB_Handler::update_option( User_Data::get_db_key_account(), $settings );

        DB_Handler::delete_transient( User_Data::get_transient_key() );

        Admin_Notice::add_notice(
            _x( 'Project changed successfully.', 'Form submission: Success message.', 'ilove-pdf' ),
            'success',
        );

        wp_safe_redirect( wp_get_referer() );
        exit;
    }

    /**
     * Checks if the user is logged in.
     *
     * @return bool True if the user is logged in, false otherwise.
     * @since 3.0.0
     */
    public static function is_user_logged_in() {
        $user_public_key = User_Data::get_settings( User_Data::get_db_user_publickey_key(), '' );
        return ! empty( $user_public_key );
    }
}
