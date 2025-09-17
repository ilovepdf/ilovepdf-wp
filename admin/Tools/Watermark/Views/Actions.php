<?php

namespace Ilove_Pdf_WP\Tools\Watermark\Views;

use Ilove_Pdf_WP\Account\User_Auth;
use Ilove_Pdf_WP\Tools\Watermark\Tool_Watermark;

/**
 * Provides actions for the watermark tool in the media library.
 *
 * This trait is used to render the watermark action button and statuses in the media library.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP\Tools\Watermark\Views
 */
trait Actions {
    /**
     * Render the watermark action button in the media library.
     *
     * @param int $post_id The ID of the media item.
     * @return string HTML for the watermark action button.
     */
    protected function render_watermark_action( $post_id ) {
        return sprintf(
            '<button id="ipdf-action-watermark" data-post-id="%4$s" data-action="%5$s" data-nonce="%3$s" class="ipdf-btn ipdf-tooltip ipdf-btn--media-action ipdf-btn--media-action-watermark %6$s" %7$s>
                %1$s
                <span class="ipdf-tooltip-text">
                    %2$s
                </span>
            </button>',
            '<svg xmlns="http://www.w3.org/2000/svg" width="26px" height="26px" viewBox="0 0 50 50"><path d="M8.012 0h33.977c2.785 0 3.797.29 4.813.836a5.65 5.65 0 0 1 2.363 2.363C49.71 4.215 50 5.227 50 8.012v33.977c0 2.785-.29 3.797-.836 4.813a5.65 5.65 0 0 1-2.363 2.363c-1.016.547-2.027.836-4.812.836H8.012c-2.785 0-3.797-.29-4.816-.836S1.38 47.82.836 46.8 0 44.773 0 41.988V8.012C0 5.227.29 4.215.836 3.2A5.65 5.65 0 0 1 3.199.836C4.215.29 5.227 0 8.012 0zm0 0" fill-rule="evenodd" fill="rgb(67.058824%,41.176471%,57.647059%)"></path><path d="M22.7 22.523c0 .863-1.094 3.023-2.11 4.668a.68.68 0 0 0-.078.523c.078.277.328.47.61.47h7.75a.63.63 0 0 0 .566-.352.65.65 0 0 0-.055-.672c-1.445-1.97-2.1-3.398-2.1-4.633s.645-2.66 2.094-4.637c.664-.937 1.012-2.043 1.012-3.195 0-3.02-2.422-5.477-5.398-5.477s-5.398 2.45-5.398 5.473a5.49 5.49 0 0 0 1.02 3.203c1.44 1.97 2.086 3.398 2.086 4.63zm14.02 6.55H13.266a.64.64 0 0 0-.633.645v6.465c0 .352.285.645.633.645H36.72a.64.64 0 0 0 .633-.645V29.72c0-.352-.285-.645-.633-.645zm-3.582 8.7H16.863a.64.64 0 0 0-.633.645v.727c0 .352.285.645.633.645h16.273a.64.64 0 0 0 .633-.645v-.727c0-.352-.285-.645-.633-.645zm0 0" fill="#fff"></path></svg>',
            User_Auth::is_user_logged_in() ? esc_html( _x( 'Apply Watermark', 'Button action', 'ilove-pdf' ) ) : esc_html( _x( 'Register or login with us to add watermark', 'tooltip: Appears when the user is not logged in.', 'ilove-pdf' ) ),
            esc_attr( wp_create_nonce( 'ilovepdf_action_watermark' ) ),
            $post_id,
            'ilovepdf_action_watermark',
            Tool_Watermark::is_file_watermarked( $post_id ) ? 'ipdf-btn--media-action-trigger' : '',
            User_Auth::is_user_logged_in() ? '' : 'disabled',
        );
    }

    /**
     * Render the status not watermarked.
     *
     * @since 3.0.0
     * @param int $post_id The ID of the post.
     * @return string HTML for the status not watermarked.
     */
    protected function status_not_watermarked( $post_id ) {
        return sprintf(
            '<div class="ipdf-item-status ipdf-item-status-not-watermarked ilovepdf-base__layout-flex ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small %1$s">
                <svg width="14px" aria-hidden="true" focusable="false" data-icon="minus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"></path></svg>
                <span class="">%2$s</span>
            </div>',
            Tool_Watermark::is_file_watermarked( $post_id ) ? '' : 'ipdf-item-status-active',
            esc_html_x( 'No watermark applied', 'Status: No watermark applied', 'ilove-pdf' ),
        );
    }

    /**
     * Render the status for watermark applied.
     *
     * @since 3.0.0
     * @param int $post_id The ID of the post.
     * @return string HTML for the status watermark applied.
     */
    protected function status_watermark_applied( $post_id ) {
        return sprintf(
            '<div class="ipdf-item-status ipdf-item-status-watermark-applied ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small %1$s">
                <svg width="14px" height="14px" aria-hidden="true" focusable="false" data-icon="stamp" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M312 201.8c0-17.4 9.2-33.2 19.9-47C344.5 138.5 352 118.1 352 96c0-53-43-96-96-96s-96 43-96 96c0 22.1 7.5 42.5 20.1 58.8c10.7 13.8 19.9 29.6 19.9 47c0 29.9-24.3 54.2-54.2 54.2H112C50.1 256 0 306.1 0 368c0 20.9 13.4 38.7 32 45.3V464c0 26.5 21.5 48 48 48H432c26.5 0 48-21.5 48-48V413.3c18.6-6.6 32-24.4 32-45.3c0-61.9-50.1-112-112-112H366.2c-29.9 0-54.2-24.3-54.2-54.2zM416 416v32H96V416H416z"></path></svg>
                <span>%2$s</span>
            </div>',
            Tool_Watermark::is_file_watermarked( $post_id ) ? 'ipdf-item-status-active' : '',
            esc_html_x( 'Watermark applied', 'Status: Watermark applied', 'ilove-pdf' ),
        );
    }

    /**
     * Render the status for watermark processing.
     *
     * @since 3.0.0
     * @return string HTML for the status watermark processing.
     */
    protected function status_watermark_processing() {
        return sprintf(
            '<div class="ipdf-item-status ipdf-item-status-watermark-processing ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small">
                %1$s
                <span class="">%2$s</span>
            </div>',
            $this->get_loading(),
            esc_html_x( 'Processing watermark...', 'Status: Processing watermark', 'ilove-pdf' ),
        );
    }
}
