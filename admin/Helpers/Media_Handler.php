<?php

namespace Ilove_Pdf_WP\Helpers;

/**
 * Attachment helper.
 *
 * Utility class for managing attachment metadata and performing
 * advanced operations on WordPress media files.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP
 */
class Media_Handler {
    /**
     * Regenerate attachment metadata
     *
     * @since 2.0.6
     * @param int $attachment_id File ID.
     */
    public static function regenerate_attachment_data( $attachment_id ) {

        if ( ! $attachment_id ) {
            return;
        }

        $filename     = get_attached_file( $attachment_id );
        $metadata_old = wp_get_attachment_metadata( $attachment_id );
        $metadata     = wp_generate_attachment_metadata( $attachment_id, $filename );

        // Delete old attachment metadata.
        if ( isset( $metadata_old['sizes'] ) ) {

            foreach ( $metadata_old['sizes'] as $size => $data ) {
                $thumb_file = pathinfo( $filename )['dirname'] . '/' . $data['file'];

                if ( file_exists( $thumb_file ) ) {
                    wp_delete_file( $thumb_file );
                }
            }
        }

        wp_update_attachment_metadata( $attachment_id, $metadata );
    }
}
