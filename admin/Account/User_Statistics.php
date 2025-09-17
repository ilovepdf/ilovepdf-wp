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

        $readable_credits = '';

        if ( 'free' === $account_type ) {
            $readable_credits = sprintf(
                /* translators: %1$s: used credits, %2$s: total credits */
                __( '%1$s / %2$s credits used this month.', 'ilove-pdf' ),
                number_format_i18n( self::get_credits( $account_type, 'used' ) ),
                number_format_i18n( self::get_credits( $account_type, 'limit' ) ),
            );
        }

        if ( 'package' === $account_type ) {
            $readable_credits = sprintf(
                /* translators: %1$s: used credits, %2$s: total credits */
                __( '%1$s / %2$s credits used this month.', 'ilove-pdf' ),
                number_format_i18n( self::get_credits( $account_type, 'used' ) ),
                number_format_i18n( self::get_credits( $account_type, 'limit' ) ),
            );
        }

        if ( 'suscription' === $account_type ) {
            $readable_credits = sprintf(
                /* translators: %1$s: used credits, %2$s: total credits */
                __( '%1$s / %2$s credits used this month.', 'ilove-pdf' ),
                number_format_i18n( self::get_credits( $account_type, 'used' ) ),
                number_format_i18n( self::get_credits( $account_type, 'limit' ) ),
            );
        }

        return $readable_credits;
    }

    /**
     * Get the percentage of credits used for the specified account type.
     *
     * @param string $account_type The type of account (free, package, suscription).
     * @return int Percentage of credits used.
     */
    public static function get_percent_credits_used( $account_type ) {
        $percent   = 0;
        $user_data = User_Data::get_user_data();

        if ( ! isset( $user_data[ $account_type . '_files_used' ], $user_data[ $account_type . '_files_limit' ] ) ) {
            return $percent;
        }

        $percent = self::get_percentage( $user_data[ $account_type . '_files_used' ], $user_data[ $account_type . '_files_limit' ] );

        return $percent;
    }

    /**
     * Get the readable subscription type.
     *
     * @return string Readable subscription type.
     */
    public static function get_readable_suscription_type() {
        $user_data = User_Data::get_user_data();

        if ( 'month' === $user_data['subscription']['period'] ) {
            return esc_html_x( 'Monthly plan.', 'subtitle suscription section', 'ilove-pdf' );
        }

        if ( 'year' === $user_data['subscription']['period'] ) {
            return esc_html_x( 'Yearly plan.', 'subtitle suscription section', 'ilove-pdf' );
        }

        return '';
    }

    /**
     * Check if the user has a specific account type.
     *
     * @param string $account_type The type of account to check (free, package, suscription).
     * @return bool True if the user has the specified account type, false otherwise.
     */
    public static function user_has( $account_type ) {
        $user_data = User_Data::get_user_data();

        if ( ! isset( $user_data[ $account_type . '_files_limit' ] ) ) {
            return false;
        }

        return $user_data[ $account_type . '_files_limit' ] > 0;
    }

    /**
     * Get credits for the specified account type and quantity key.
     *
     * @param string $account_type The type of account (free, package, suscription).
     * @param string $quantity_key The key for the quantity (used, limit).
     * @return int Credits for the specified account type and quantity key.
     */
    private static function get_credits( $account_type, $quantity_key ) {

        $credits_limit = 0;
        $user_data     = User_Data::get_user_data();

        if ( ! isset( $user_data[ $account_type . '_files_' . $quantity_key ] ) ) {
            return $credits_limit;
        }

        return $user_data[ $account_type . '_files_' . $quantity_key ];
    }

    /**
     * Calculate the percentage of credits used.
     *
     * @param int $credits_used The number of credits used.
     * @param int $credits_limit The total number of credits available.
     * @return int The percentage of credits used, capped at 100%.
     */
    private static function get_percentage( $credits_used, $credits_limit ) {
        if ( 0 === $credits_limit ) {
            return 0;
        }

        $percentage = $credits_used * 100 / $credits_limit;

        return ( $percentage > 100 ) ? 100 : $percentage;
    }
}
