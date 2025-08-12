<?php

namespace Ilove_Pdf_WP\Tools\Watermark;

/**
 * Handles watermark statistics for PDF files.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP\Tools\Watermark
 */
class Statistics {
    /**
     * Transient key for storing watermark statistics.
     *
     * @since 3.0.0
     * @var string
     */
    private static $transient_key = 'ipdf_watermark_statistics';

    /**
     * Retrieves the query arguments for fetching PDF attachments.
     *
     * @since 3.0.0
     * @return array
     */
    private static function get_query_args() {
        return array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'application/pdf',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',

        );
    }

    /**
     * Computes and caches the watermark statistics.
     *
     * @since 3.0.0
     * @return array
     */
    private static function compute_stats() {
        if ( get_transient( self::$transient_key ) ) {
            return get_transient( self::$transient_key );
        }

        $attachments = get_posts( self::get_query_args() );

        $total_watermarked = 0;

        foreach ( $attachments as $attachment_id ) {
            $watermarked = get_post_meta( $attachment_id, Tool_Watermark::get_db_key_status(), true );
            if ( $watermarked ) {
                ++$total_watermarked;
            }
        }

        $stats = array(
            'total'       => count( $attachments ),
            'watermarked' => $total_watermarked,
        );

        set_transient( self::$transient_key, $stats, DAY_IN_SECONDS );

        return $stats;
    }

    /**
     * Gets the number of files that have been watermarked.
     *
     * @since 3.0.0
     * @return int
     */
    public static function get_protected_files() {
        $stats = self::compute_stats();
        return $stats['watermarked'];
    }

    /**
     * Gets the summary of the watermarking tool's activity.
     *
     * @since 3.0.0
     * @return string
     */
    public static function get_resume() {
        $stats = self::compute_stats();

        return sprintf(
            /* translators: %1$d: total files, %2$d: watermarked files */
            esc_html__( 'Your files, summary: Total files %1$d → Protected files %2$d', 'ilove-pdf' ),
            $stats['total'],
            $stats['watermarked'],
        );
    }

    /**
     * Resets the watermark statistics.
     *
     * @since 3.0.0
     */
    public static function reset_statistics() {
        delete_transient( self::$transient_key );
    }
}
