<?php

namespace Ilove_Pdf_WP\Media\Views;

use Ilove_Pdf_WP\Tools\Compress\Tool_Compress;
use Ilove_Pdf_WP\Tools\Watermark\Tool_Watermark;
use Ilove_Pdf_WP\Tools\Compress\Views\Actions as Compress_Actions;
use Ilove_Pdf_WP\Tools\Watermark\Views\Actions as Watermark_Actions;

/**
 * Class Status_Renderer
 *
 * Renders the status of iLovePDF actions on media items.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP\Media\Views
 */
class Status_Renderer {
    use Compress_Actions;
    use Watermark_Actions;

    /**
     * Create the status HTML for a media item.
     *
     * @param int  $post_id The ID of the media item.
     * @param bool $show_on_list_page Whether to show the status on the list page.
     * @return string HTML for the status.
     * @since 3.0.0
     */
    public static function create( $post_id, $show_on_list_page = false ) {

        $instance = new self();

        return sprintf(
            '<div class="ipdf-status %8$s">
                %1$s
                %2$s
                %3$s
                %4$s
                %5$s
                %6$s
                %7$s
                %9$s
                %10$s
            </div>',
            $show_on_list_page ? $instance->status_not_compressed( $post_id ) : '',
            $instance->status_compressing(),
            $instance->status_compressed( $post_id ),
            $show_on_list_page ? $instance->status_not_watermarked( $post_id ) : '',
            $instance->status_watermark_processing(),
            $instance->status_watermark_applied( $post_id ),
            $instance->status_fail(),
            ( Tool_Compress::is_file_compressed( $post_id ) || Tool_Watermark::is_file_watermarked( $post_id ) ) ? 'ipdf-status-process' : '',
            $instance->status_restore_processing(),
            $instance->status_restored(),
        );
    }
}
