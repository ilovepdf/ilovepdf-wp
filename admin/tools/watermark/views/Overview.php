<?php

namespace Ilove_Pdf_WP\Tools\Watermark\Views;

use Ilove_Pdf_WP\Tools\Watermark\Statistics;

/**
 * Creates the HTML for the watermark overview.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP\Tools\Watermark\Views
 */
class Overview {
    /**
     * Renders the watermark overview.
     *
     * @since 3.0.0
     * @return string
     */
    public static function render() {
        return sprintf(
            '<article class="ilovepdf-media__overview-tool ilovepdf-media__overview-watermark-overview">
                <h2 class="ilovepdf-base__layout-flex ilovepdf-base__layout-gap--small ilovepdf-base__layout-items-center">
                    %1$s
                    %2$s
                </h2>
                <div class="ilovepdf-media__overview-watermark-overview-inner ilovepdf-base__layout-flex ilovepdf-base__layout-flex-wrap ilovepdf-base__layout-gap--normal">
                    %3$s
                </div>
                <div>%4$s</div>
            </article>',
            '<svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 50 50"><path d="M8.012 0h33.977c2.785 0 3.797.29 4.813.836a5.65 5.65 0 0 1 2.363 2.363C49.71 4.215 50 5.227 50 8.012v33.977c0 2.785-.29 3.797-.836 4.813a5.65 5.65 0 0 1-2.363 2.363c-1.016.547-2.027.836-4.812.836H8.012c-2.785 0-3.797-.29-4.816-.836S1.38 47.82.836 46.8 0 44.773 0 41.988V8.012C0 5.227.29 4.215.836 3.2A5.65 5.65 0 0 1 3.199.836C4.215.29 5.227 0 8.012 0zm0 0" fill-rule="evenodd" fill="rgb(67.058824%,41.176471%,57.647059%)"></path><path d="M22.7 22.523c0 .863-1.094 3.023-2.11 4.668a.68.68 0 0 0-.078.523c.078.277.328.47.61.47h7.75a.63.63 0 0 0 .566-.352.65.65 0 0 0-.055-.672c-1.445-1.97-2.1-3.398-2.1-4.633s.645-2.66 2.094-4.637c.664-.937 1.012-2.043 1.012-3.195 0-3.02-2.422-5.477-5.398-5.477s-5.398 2.45-5.398 5.473a5.49 5.49 0 0 0 1.02 3.203c1.44 1.97 2.086 3.398 2.086 4.63zm14.02 6.55H13.266a.64.64 0 0 0-.633.645v6.465c0 .352.285.645.633.645H36.72a.64.64 0 0 0 .633-.645V29.72c0-.352-.285-.645-.633-.645zm-3.582 8.7H16.863a.64.64 0 0 0-.633.645v.727c0 .352.285.645.633.645h16.273a.64.64 0 0 0 .633-.645v-.727c0-.352-.285-.645-.633-.645zm0 0" fill="rgb(100%,100%,100%)"></path></svg>',
            esc_html_x( 'Watermark PDF', 'Overview: tool title', 'ilove-pdf' ),
            self::create_files_protected(),
            self::create_tool_resume(),
        );
    }

    /**
     * Creates the HTML for the protected files overview.
     *
     * @since 3.0.0
     * @return string
     */
    private static function create_files_protected() {
        return sprintf(
            '<div class="ilovepdf-media__overview-subitem ilovepdf-media__overview-watermark-files-processed">
                <p>%1$s</p>
                <h3>%2$s</h3>
            </div>',
            Statistics::get_protected_files(),
            esc_html_x( 'Files Protected', 'Watermark Overview: Files protected', 'ilove-pdf' ),
        );
    }

    /**
     * Creates the HTML for the watermark tool's resume.
     *
     * @since 3.0.0
     * @return string
     */
    private static function create_tool_resume() {
        return sprintf(
            '<span class="ilovepdf-media__overview-subitem-resume ilovepdf-media__overview-watermark-tool-resume">
                <p>%1$s</p>
            </span>',
            Statistics::get_resume(),
        );
    }
}
