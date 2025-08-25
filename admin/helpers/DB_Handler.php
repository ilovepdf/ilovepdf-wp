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

        $sites = get_sites();
        foreach ( $sites as $site ) {
            self::switch_update_blog( (int) $site->blog_id, $option, $value, $autoload );
        }
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

        delete_site_option( $option );
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
}
