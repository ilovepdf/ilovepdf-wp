<?php

namespace Ilove_Pdf_WP\Account;

/**
 * Provides options for user forms in the account section.
 *
 * @package Ilove_Pdf_WP\Account
 * @since 3.0.0
 */
trait User_Form_Options {

    /**
     * Key for the login action.
     *
     * @since 3.0.0
     * @var string
     */
    private static $action_login_key = 'ilovepdf_user_login';

    /**
     * Key for the register action.
     *
     * @since 3.0.0
     * @var string
     */
    private static $action_register_key = 'ilovepdf_user_register';

    /**
     * Key for the logout action.
     *
     * @since 3.0.0
     * @var string
     */
    private static $action_logout_key = 'ilovepdf_user_logout';

    /**
     * Key for changing the project.
     *
     * @since 3.0.0
     * @var string
     */
    private static $action_change_project_key = 'ilovepdf_user_change_project';

    /**
     * Field name for the user email.
     *
     * @since 3.0.0
     * @var string
     */
    private static $field_email = 'ipdf_user_email';

    /**
     * Field name for the user password.
     *
     * @since 3.0.0
     * @var string
     */
    private static $field_password = 'ipdf_user_password';

    /**
     * Field name for the user name.
     *
     * @since 3.0.0
     * @var string
     */
    private static $field_name = 'ipdf_user_name';

    /**
     * Get the action login key.
     *
     * @since 3.0.0
     * @return string
     */
    public static function get_action_login_key() {
        return self::$action_login_key;
    }

    /**
     * Get the action register key.
     *
     * @since 3.0.0
     * @return string
     */
    public static function get_action_register_key() {
        return self::$action_register_key;
    }

    /**
     * Get the action logout key.
     *
     * @since 3.0.0
     * @return string
     */
    public static function get_action_logout_key() {
        return self::$action_logout_key;
    }

    /**
     * Get the action change project key.
     *
     * @since 3.0.0
     * @return string
     */
    public static function get_action_change_project_key() {
        return self::$action_change_project_key;
    }

    /**
     * Get the field email name.
     *
     * @since 3.0.0
     * @return string
     */
    public static function get_field_email() {
        return self::$field_email;
    }

    /**
     * Get the field password name.
     *
     * @since 3.0.0
     * @return string
     */
    public static function get_field_password() {
        return self::$field_password;
    }

    /**
     * Get the field name for the user.
     *
     * @since 3.0.0
     * @return string
     */
    public static function get_field_name() {
        return self::$field_name;
    }
}
