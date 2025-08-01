<?php

namespace Ilove_Pdf_WP\Media;

use Ilove_Pdf_WP\Tools\Compress\Views\Compress_Overview;
use Ilove_Pdf_WP\Tools\Watermark\Views\Watermark_Overview;

class Media_Overview {
    /**
     * Render the media overview.
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
            esc_html_x( 'Overview', '', 'ilove-pdf' ),
            Compress_Overview::render(),
            Watermark_Overview::render(),
        );
    }
}
