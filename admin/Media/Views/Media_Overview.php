<?php

namespace Ilove_Pdf_WP\Media\Views;

use Ilove_Pdf_WP\Tools\Compress\Views\Overview as Compress_Overview;
use Ilove_Pdf_WP\Tools\Watermark\Views\Overview as Watermark_Overview;

/**
 * Creates the HTML for the media overview.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP\Media\Views
 */
class Media_Overview {
    /**
     * Creates the HTML for the media overview.
     *
     * @since 3.0.0
     *
     * phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
     */
    public static function render() {
        printf(
            '<div class="ilovepdf-media__overview-inner">
                <h2>%1$s</h2>
                <div class="ilovepdf-media__overview-inner-tools ilovepdf-base__layout-flex ilovepdf-base__layout-flex-wrap ilovepdf-base__layout-justify--between ilovepdf-base__layout-gap--normal">
                    %2$s
                    %3$s
                </div>
            </div>',
            esc_html_x( 'Overview', 'Section Title', 'ilove-pdf' ),
            Compress_Overview::render(),
            Watermark_Overview::render(),
        );
    }
}
