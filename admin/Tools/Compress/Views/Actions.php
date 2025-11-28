<?php

namespace Ilove_Pdf_WP\Tools\Compress\Views;

use Ilove_Pdf_WP\Account\User_Auth;
use Ilove_Pdf_WP\Tools\Compress\Tool_Compress;
use Ilove_Pdf_WP\Tools\Base\Actions as Base_Actions;

/**
 * Provides actions for the iLovePDF Compress tool in the WordPress admin.
 *
 * This trait includes methods to render buttons and statuses related to PDF compression.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP\Tools\Compress\Views
 */
trait Actions {
    use Base_Actions;

    /**
     * Render the compress action button in the media library.
     *
     * @param int $post_id The ID of the media item.
     * @return string HTML for the compress action button.
     */
    protected function render_compress_action( $post_id ) {
        return sprintf(
            '<button id="ipdf-action-compress" data-post-id="%4$s" data-action="%5$s" data-nonce="%1$s" class="ipdf-btn ipdf-tooltip ipdf-btn--media-action ipdf-btn--media-action-compress %6$s" %7$s>
                %2$s
                <span class="ipdf-tooltip-text">
                    %3$s
                </span>
            </button>',
            esc_attr( wp_create_nonce( 'ilovepdf_action_compress' ) ),
            '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 50 50"><path d="M31.523 28h14.953c1.223 0 1.668.13 2.117.367.44.234.805.598 1.04 1.04.242.45.367.895.367 2.117v14.953c0 1.223-.13 1.668-.367 2.117-.234.44-.598.805-1.04 1.04-.45.242-.895.367-2.117.367H31.523c-1.223 0-1.668-.13-2.117-.367-.44-.234-.805-.598-1.04-1.04-.242-.45-.367-.895-.367-2.117V31.523c0-1.223.13-1.668.367-2.117.234-.44.598-.805 1.04-1.04.45-.242.895-.367 2.117-.367zm0-28h14.953c1.223 0 1.668.13 2.117.367.44.234.805.598 1.04 1.04.242.45.367.895.367 2.117v14.953c0 1.223-.13 1.668-.367 2.117-.234.44-.598.805-1.04 1.04-.45.242-.895.367-2.117.367H31.523c-1.223 0-1.668-.13-2.117-.367-.44-.234-.805-.598-1.04-1.04-.242-.45-.367-.895-.367-2.117V3.523c0-1.223.13-1.668.367-2.117.234-.44.598-.805 1.04-1.04C29.855.125 30.3 0 31.523 0zm-28 28h14.953c1.223 0 1.668.13 2.117.367.44.234.805.598 1.04 1.04.242.45.367.895.367 2.117v14.953c0 1.223-.13 1.668-.367 2.117-.234.44-.598.805-1.04 1.04-.45.242-.895.367-2.117.367H3.523c-1.223 0-1.668-.13-2.117-.367-.44-.234-.805-.598-1.04-1.04C.125 48.145 0 47.7 0 46.477V31.523c0-1.223.13-1.668.367-2.117.234-.44.598-.805 1.04-1.04.45-.242.895-.367 2.117-.367zm0-28h14.953c1.223 0 1.668.13 2.117.367.44.234.805.598 1.04 1.04.242.45.367.895.367 2.117v14.953c0 1.223-.13 1.668-.367 2.117-.234.44-.598.805-1.04 1.04-.45.242-.895.367-2.117.367H3.523c-1.223 0-1.668-.13-2.117-.367-.44-.234-.805-.598-1.04-1.04C.125 20.145 0 19.7 0 18.477V3.523C0 2.3.13 1.852.367 1.406A2.56 2.56 0 0 1 1.406.367C1.855.13 2.3 0 3.523 0zm0 0" fill-rule="evenodd" fill="rgb(56.078431%,73.72549%,36.470588%)"></path><path d="M35 41.8c0 .48.398.867.883.867a.88.88 0 0 0 .883-.867v-3.844l5.145 5.05a.89.89 0 0 0 1.246 0 .85.85 0 0 0 .262-.613c0-.23-.094-.45-.262-.613l-5.14-5.047h3.914a.88.88 0 0 0 .883-.867c0-.48-.395-.867-.883-.867h-6.05c-.117 0-.23.023-.34.066-.215.086-.387.258-.477.47-.047.102-.066.22-.066.328zm7.3-26.387c.48 0 .867-.398.867-.883a.88.88 0 0 0-.867-.883h-3.844l5.05-5.14a.9.9 0 0 0 0-1.25.86.86 0 0 0-1.227 0l-5.047 5.148V8.492c0-.488-.39-.883-.867-.883a.87.87 0 0 0-.867.879v6.05c0 .113.023.23.066.336.086.215.254.387.47.477.105.047.215.07.332.07H42.3zM8.46 35c-.48 0-.867.398-.867.883s.387.883.867.883h3.844L7.254 41.9c-.34.348-.34.902 0 1.25a.86.86 0 0 0 .613.258c.23 0 .45-.094.613-.258l5.047-5.145v3.914c0 .488.387.883.867.883s.867-.402.867-.883v-6.05c0-.113-.023-.23-.066-.336-.086-.215-.258-.387-.47-.477a.82.82 0 0 0-.332-.07H8.46zm6.074-27.406c-.488 0-.883.387-.883.867v3.844l-5.145-5.05a.9.9 0 0 0-1.25 0A.86.86 0 0 0 7 7.867c0 .23.094.45.258.613l5.145 5.047H8.488c-.488 0-.883.387-.883.867s.402.867.883.867h6.05a.89.89 0 0 0 .336-.066c.215-.1.39-.258.477-.47.05-.102.07-.22.07-.332V8.46c0-.48-.395-.867-.883-.867zm0 0" fill="rgb(100%,100%,100%)"></path></svg>',
            User_Auth::is_user_logged_in() ? esc_html_x( 'Compress', 'Button action', 'ilove-pdf' ) : esc_html_x( 'Please log in or sign up to use the tool.', 'tooltip: Appears when the user is not logged in.', 'ilove-pdf' ),
            $post_id,
            'ilovepdf_action_compress',
            Tool_Compress::is_file_compressed( $post_id ) ? 'ipdf-btn--media-action-trigger' : '',
            User_Auth::is_user_logged_in() ? '' : 'disabled',
        );
    }

    /**
     * Render the status for a file that is not compressed.
     *
     * @param int $post_id The ID of the media item.
     * @return string HTML for the status.
     * @since 3.0.0
     */
    protected function status_not_compressed( $post_id ) {
        return sprintf(
            '<div class="ipdf-item-status ipdf-item-status-not-compressed ilovepdf-base__layout-flex ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small %1$s">
                <svg width="14px" aria-hidden="true" focusable="false" data-icon="minus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"></path></svg>
                <span class="">%2$s</span>
            </div>',
            Tool_Compress::is_file_compressed( $post_id ) ? '' : 'ipdf-item-status-active',
            esc_html_x( 'Not compressed', 'Status: Not compressed', 'ilove-pdf' ),
        );
    }

    /**
     * Render the status for a file that is currently being compressed.
     *
     * @return string HTML for the status.
     * @since 3.0.0
     */
    protected function status_compressing() {
        return sprintf(
            '<div class="ipdf-item-status ipdf-item-status-compressing ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small">
                %1$s
                <span class="">%2$s</span>
            </div>',
            $this->get_loading(),
            esc_html_x( 'Compressing...', 'Status: Compressing', 'ilove-pdf' ),
        );
    }

    /**
     * Render the status for a file that has been compressed.
     *
     * @param int $post_id The ID of the media item.
     * @return string HTML for the status.
     * @since 3.0.0
     */
    protected function status_compressed( $post_id ) {
        $percentage = Tool_Compress::get_compressed_reabable_percentage( 0, 0 );

        if ( Tool_Compress::is_file_compressed( $post_id ) ) {
            $metadata   = get_post_meta( $post_id, Tool_Compress::get_db_key_process(), false )[0];
            $percentage = Tool_Compress::get_compressed_reabable_percentage(
                $metadata['original_size'],
                $metadata['compressed_size'],
            );
        }

        return sprintf(
            '<div class="ipdf-item-status ipdf-item-status-compressed ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small %1$s">
                <svg width="14" height="14" aria-hidden="true" focusable="false" data-icon="circle-check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"></path></svg>
                <span class="">%2$s</span>
            </div>',
            Tool_Compress::is_file_compressed( $post_id ) ? 'ipdf-item-status-active' : '',
            $percentage,
        );
    }
}
