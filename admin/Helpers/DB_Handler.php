<?php

namespace Ilove_Pdf_WP\Helpers;

/**
 * Database management and related operations.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP\Helpers
 */
class DB_Handler {
    /**
	 * Update option, works with multisite if enabled
	 *
	 * @since  2.1.5
	 * @param  string    $option Name of the option to update. Expected to not be SQL-escaped.
	 * @param  mixed     $value Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
	 * @param  bool      $update_all_sites Optional. Whether to update all sites in the network.
	 * @param  bool|null $autoload Optional. Whether to load the option when WordPress starts up. Accepts a boolean, or null.
	 */
	public static function update_option( $option, $value, $update_all_sites = false, $autoload = null ) {

		if ( ! is_multisite() ) {
			update_option( $option, $value, $autoload );
			return;
		}

        if ( ! $update_all_sites ) {
            self::switch_update_blog( get_current_blog_id(), $option, $value, $autoload );
            return;
        }

        $blogs = get_sites( array( 'fields' => 'ids' ) );
        foreach ( $blogs as $blog_id ) {
            self::switch_update_blog( $blog_id, $option, $value, $autoload );
        }
	}

    /**
     * Switch to blog and update option
     *
     * @since  2.1.6
     * @param  int       $blog_id ID of the blog to switch to.
     * @param  string    $option Name of the option to update.
     * @param  mixed     $value Option value.
     * @param  bool|null $autoload Whether to load the option when WordPress starts up.
     */
    private static function switch_update_blog( $blog_id, $option, $value, $autoload ) {
        switch_to_blog( $blog_id );
        update_option( $option, $value, $autoload );
        restore_current_blog();
    }

    /**
     * Delete option, works with multisite if enabled
     *
     * @since  3.0.0
     * @param  string $option Name of the option to delete.
     */
    public static function delete_option( $option ) {
        if ( ! is_multisite() ) {
            delete_option( $option );
            return;
        }

        switch_to_blog( get_current_blog_id() );
        delete_option( $option );
        restore_current_blog();
    }

    /**
     * Get option, works with multisite if enabled
     *
     * @since  3.0.0
     * @param  string $option Name of the option to get.
     * @param  mixed  $default_value Optional. Default value to return if the option does not exist.
     * @return mixed Value set for the option.
     */
    public static function get_option( $option, $default_value = false ) {
        $db_option = get_option( $option, $default_value );

        if ( ! is_multisite() ) {
            return $db_option;
        }

        switch_to_blog( get_current_blog_id() );
        $db_option = get_option( $option, $default_value );
        restore_current_blog();

        return $db_option;
    }

    /**
     * Set transient, works with multisite if enabled
     *
     * @since  3.0.0
     * @param  string $key Transient key.
     * @param  mixed  $value Transient value.
     * @param  int    $expiration Transient expiration time in seconds.
     */
	public static function set_transient( $key, $value, $expiration ) {
		if ( ! is_multisite() ) {
			set_transient( $key, $value, $expiration );
			return;
		}

		switch_to_blog( get_current_blog_id() );
		set_transient( $key, $value, $expiration );
		restore_current_blog();
	}

    /**
     * Get transient, works with multisite if enabled
     *
     * @since  3.0.0
     * @param  string $key Transient key.
     * @return mixed Transient value.
     */
    public static function get_transient( $key ) {
        $transient = get_transient( $key );

        if ( ! is_multisite() ) {
            return $transient;
        }

        switch_to_blog( get_current_blog_id() );
        $transient = get_transient( $key );
        restore_current_blog();

        return $transient;
    }

    /**
     * Delete transient, works with multisite if enabled
     *
     * @since  3.0.0
     * @param  string $key Transient key.
     */
    public static function delete_transient( $key ) {
        if ( ! is_multisite() ) {
            delete_transient( $key );
            return;
        }

        switch_to_blog( get_current_blog_id() );
        delete_transient( $key );
        restore_current_blog();
    }
}
