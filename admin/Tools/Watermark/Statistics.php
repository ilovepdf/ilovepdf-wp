<?php

namespace Ilove_Pdf_WP\Tools\Watermark;

use Ilove_Pdf_WP\Helpers\DB_Handler;
use Ilove_Pdf_WP\Tools\Base\Status_Process;

/**
 * Handles watermark statistics for PDF files.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP\Tools\Watermark
 */
class Statistics {

    use Status_Process;

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
        if ( DB_Handler::get_transient( self::$transient_key ) ) {
            return DB_Handler::get_transient( self::$transient_key );
        }

        $attachments = get_posts( self::get_query_args() );

        $total_watermarked = 0;

        foreach ( $attachments as $attachment_id ) {
            $watermarked = get_post_meta( $attachment_id, Tool_Watermark::get_db_key_status(), true );
            if ( ( new self() )->get_ready_status() === $watermarked ) {
                ++$total_watermarked;
            }
        }

        $stats = array(
            'total'       => count( $attachments ),
            'watermarked' => $total_watermarked,
        );

        DB_Handler::set_transient( self::$transient_key, $stats, DAY_IN_SECONDS );

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
        /* translators: %1$d: Total files, %2$d: Protected files */
        $line_scaped_summary = esc_html_x( "Your files, summary:\nTotal files: %1\$d\nProtected files: %2\$d", 'Watermark Overview: Tool Resume.', 'ilove-pdf' );
        $formatted_summary   = nl2br( $line_scaped_summary );
        $allowed_tags        = array(
            'br'   => array(),
            'br/'  => array(),
            'br /' => array(),
        );
        $output_html         = wp_kses( $formatted_summary, $allowed_tags );

        return sprintf(
            $output_html,
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
        DB_Handler::delete_transient( self::$transient_key );
    }
}
