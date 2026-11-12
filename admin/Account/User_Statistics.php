<?php

namespace Ilove_Pdf_WP\Account;

/**
 * Handles user statistics for the account user.
 *
 * @package Ilove_Pdf_WP\Account
 * @since 3.0.0
 */
trait User_Statistics {
    /**
     * Get readable credits for the specified account type.
     *
     * @param string $account_type The type of account (free, package, suscription).
     * @return string Readable credits information.
     */
    public static function get_readable_credits( $account_type ) {

        if ( ! in_array( $account_type, self::get_account_types(), true ) ) {
            return '';
        }

        return sprintf(
            /* translators: %1$s: used credits, %2$s: total credits */
            __( '%1$s / %2$s credits used this month.', 'ilove-pdf' ),
            number_format_i18n( self::get_credits( $account_type, 'used' ) ),
            number_format_i18n( self::get_credits( $account_type, 'limit' ) ),
        );
    }

    /**
     * Get the percentage of credits used for the specified account type.
     *
     * @param string $account_type The type of account (free, package, suscription).
     * @return int Percentage of credits used.
     */
    public static function get_percent_credits_used( $account_type ) {
        return self::get_percentage(
            self::get_credits( $account_type, 'used' ),
            self::get_credits( $account_type, 'limit' )
        );
    }

    /**
     * Get the readable subscription type.
     *
     * @return string Readable subscription type.
     */
    public static function get_readable_suscription_type() {
        $user_data = User_Data::get_user_data();
        $period    = isset( $user_data['subscription']['period'] ) ? $user_data['subscription']['period'] : '';

        if ( 'month' === $period ) {
            return esc_html_x( 'Monthly plan.', 'subtitle suscription section', 'ilove-pdf' );
        }

        if ( 'year' === $period ) {
            return esc_html_x( 'Yearly plan.', 'subtitle suscription section', 'ilove-pdf' );
        }

        return esc_html_x( 'Subscription.', 'subtitle suscription section', 'ilove-pdf' );
    }

    /**
     * Check if the user has a specific account type.
     *
     * @param string $account_type The type of account to check (free, package, suscription).
     * @return bool True if the user has the specified account type, false otherwise.
     */
    public static function user_has( $account_type ) {
        return self::get_credits( $account_type, 'limit' ) > 0;
    }

    /**
     * Count the credits still available across every funded tier.
     *
     * The free tier resets monthly and is usually the first to run out, so reporting it
     * alone makes a working account look exhausted. This is the number that decides
     * whether files can still be processed.
     *
     * @return int The credits remaining.
     */
    public static function get_remaining_credits() {
        $remaining = 0;

        foreach ( self::get_account_types() as $account_type ) {
            $limit = self::get_credits( $account_type, 'limit' );

            if ( $limit > 0 ) {
                $remaining += max( 0, $limit - self::get_credits( $account_type, 'used' ) );
            }
        }

        return $remaining;
    }

    /**
     * Get the account types the plugin reports on.
     *
     * @return string[] The account types.
     */
    private static function get_account_types() {
        return array( 'free', 'package', 'suscription' );
    }

    /**
     * Map a plugin account type to the field prefix used by the API.
     *
     * The plugin has always used the misspelled 'suscription' type, while the API returns
     * 'subscription_files_*' fields. Without this mapping the subscription tier can never
     * be read, so it is always reported as absent.
     *
     * @param string $account_type The type of account (free, package, suscription).
     * @return string The field prefix used by the API.
     */
    private static function get_api_prefix( $account_type ) {
        $prefixes = array(
            'free'         => 'free',
            'package'      => 'package',
            'suscription'  => 'subscription',
            'subscription' => 'subscription',
        );

        return isset( $prefixes[ $account_type ] ) ? $prefixes[ $account_type ] : $account_type;
    }

    /**
     * Read a numeric value from the cached account data.
     *
     * @param string $key The key to read.
     * @param int    $default_value Returned when the key is missing or not numeric.
     * @return int The value for the key.
     */
    private static function get_user_data_value( $key, $default_value = 0 ) {
        $user_data = User_Data::get_user_data();

        if ( ! is_array( $user_data ) || ! isset( $user_data[ $key ] ) || ! is_numeric( $user_data[ $key ] ) ) {
            return $default_value;
        }

        return (int) $user_data[ $key ];
    }

    /**
     * Get credits for the specified account type and quantity key.
     *
     * @param string $account_type The type of account (free, package, suscription).
     * @param string $quantity_key The key for the quantity (used, limit).
     * @return int Credits for the specified account type and quantity key.
     */
    private static function get_credits( $account_type, $quantity_key ) {
        return self::get_user_data_value( self::get_api_prefix( $account_type ) . '_files_' . $quantity_key );
    }

    /**
     * Calculate the percentage of credits used.
     *
     * @param int $credits_used The number of credits used.
     * @param int $credits_limit The total number of credits available.
     * @return int The percentage of credits used, capped at 100%.
     */
    private static function get_percentage( $credits_used, $credits_limit ) {
        $credits_limit = (int) $credits_limit;

        if ( $credits_limit <= 0 ) {
            return 0;
        }

        $percentage = (int) $credits_used * 100 / $credits_limit;

        return ( $percentage > 100 ) ? 100 : $percentage;
    }
}
