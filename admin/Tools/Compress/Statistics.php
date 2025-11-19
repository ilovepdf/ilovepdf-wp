<?php

namespace Ilove_Pdf_WP\Tools\Compress;

use Ilove_Pdf_WP\Helpers\DB_Handler;

/**
 * Handles compression statistics for PDF files.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP\Tools\Compress
 */
class Statistics {

    /**
     * Transient key for storing compression statistics.
     *
     * @since 3.0.0
     * @var string
     */
    private static $transient_key = 'ipdf_compress_statistics';

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
     * Computes and caches the compression statistics.
     *
     * @since 3.0.0
     * @return array
     */
    private static function compute_stats() {
        if ( DB_Handler::get_transient( self::$transient_key ) ) {
            return DB_Handler::get_transient( self::$transient_key );
        }

        $attachments = get_posts( self::get_query_args() );

        $total_original_size   = 0;
        $total_compressed_size = 0;
        $total_files_processed = array();
        $total_saved           = 0;

        foreach ( $attachments as $attachment_id ) {
            $compress_process_data = get_post_meta( $attachment_id, Tool_Compress::get_db_key_process(), true );

            if ( is_array( $compress_process_data ) ) {
                $original   = isset( $compress_process_data['original_size'] ) ? (int) $compress_process_data['original_size'] : 0;
                $compressed = isset( $compress_process_data['compressed_size'] ) ? (int) $compress_process_data['compressed_size'] : 0;

                $total_original_size   += $original;
                $total_compressed_size += $compressed;
                $total_saved           += $original - $compressed;

                array_push( $total_files_processed, $attachment_id );
            } else {
                $file                 = get_attached_file( $attachment_id );
                $original_size        = filesize( $file );
                $total_original_size += $original_size;
            }
        }

        $average_reduction = 0;

        if ( $total_saved > 0 && $total_original_size > 0 ) {
            $average_reduction = ( $total_saved / $total_original_size ) * 100;
        }

        $after_compression_size = $total_original_size - $total_saved;

        $stats = array(
            'count'                 => count( $total_files_processed ),
            'total_original_size'   => $total_original_size,
            'total_compressed_size' => $after_compression_size,
            'space_saved'           => size_format( $total_saved, 2 ),
            'average_reduction'     => round( $average_reduction, 2 ) . '%',
        );

        DB_Handler::set_transient( self::$transient_key, $stats, DAY_IN_SECONDS );

        return $stats;
    }

    /**
     * Retrieves the number of files processed.
     *
     * @since 3.0.0
     * @return int
     */
    public static function get_files_processed() {
        $stats = self::compute_stats();
        return $stats['count'];
    }

    /**
     * Retrieves the average reduction percentage.
     *
     * @since 3.0.0
     * @return string
     */
    public static function get_average_reduction() {
        $stats = self::compute_stats();
        return $stats['average_reduction'];
    }

    /**
     * Retrieves the total space saved by compression.
     *
     * @since 3.0.0
     * @return string
     */
    public static function get_space_saved() {
        $stats = self::compute_stats();
        return $stats['space_saved'];
    }

    /**
     * Retrieves a summary of the compression results.
     *
     * @since 3.0.0
     * @return string
     */
    public static function get_resume() {
        $stats = self::compute_stats();

        /* translators: %1$s: Original size, %2$s: Compressed size */
        $line_scaped_summary = esc_html_x( "Your files, summary:\nOriginal size: %1\$s\nAfter compression: %2\$s", 'Compress Overview: Tool Resume.', 'ilove-pdf' );
        $formatted_summary   = nl2br( $line_scaped_summary );
        $allowed_tags        = array(
            'br'   => array(),
            'br/'  => array(),
            'br /' => array(),
        );
        $output_html         = wp_kses( $formatted_summary, $allowed_tags );

        return sprintf(
            $output_html,
            size_format( $stats['total_original_size'], 2 ),
            size_format( $stats['total_compressed_size'], 2 ),
        );
    }

    /**
     * Resets the compression statistics.
     *
     * @since 3.0.0
     */
    public static function reset_statistics() {
        DB_Handler::delete_transient( self::$transient_key );
    }
}
