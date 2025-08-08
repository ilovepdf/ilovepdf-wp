<?php

namespace Ilove_Pdf_WP\Tools\Base;

use Ilove_Pdf_WP\Tools\Backup;

/**
 * This trait provides methods for rendering various actions and statuses related to PDF files.
 *
 * @package Ilove_Pdf_WP\Tools\Base
 * @since 3.0.0
 */
trait Actions {
    /**
     * Render the loading spinner.
     *
     * @return string HTML for the loading spinner.
     */
    protected function get_loading() {
        return sprintf(
            '%1$s',
            '<svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-ring-alt"><rect x="0" y="0" width="30" height="30" fill="none"></rect><circle cx="50" cy="50" r="40" stroke="#e5e5e5" fill="none" stroke-width="10" stroke-linecap="round"></circle><circle cx="50" cy="50" r="40" stroke="#E5322D" fill="none" stroke-width="6" stroke-linecap="round"><animate attributeName="stroke-dashoffset" dur="2s" repeatCount="indefinite" from="0" to="502"></animate><animate attributeName="stroke-dasharray" dur="2s" repeatCount="indefinite" values="150.6 100.4;1 250;150.6 100.4"></animate></circle></svg>',
        );
    }

    /**
     * Render the status for a file that is not compressed.
     *
     * @return string HTML for the status.
     */
    protected function status_fail() {
        return sprintf(
            '<div class="ipdf-item-status ipdf-item-status-fail ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small">
                <svg width="14px" height="14px" aria-hidden="true" focusable="false" data-icon="circle-xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"></path></svg>
                <span class="">%1$s</span>
            </div>',
            esc_html_x( 'Process failure', '', 'ilove-pdf' ),
        );
    }

    /**
     * Render the restore action button in the media library.
     *
     * @param int $post_id The ID of the media item.
     * @return string HTML for the restore action button.
     */
    protected function render_restore_action( $post_id ) {
        return sprintf(
            '<button class="ipdf-btn ipdf-tooltip ipdf-btn--media-action ipdf-btn--media-action-restore %6$s" data-post-id="%1$s" data-action="%2$s" data-nonce="%3$s">
                %4$s
                <span class="ipdf-tooltip-text">
                    %5$s
                </span>
            </button>',
            $post_id,
            'ilovepdf_restore_file',
            esc_attr( wp_create_nonce( 'ilovepdf_restore_file' ) ),
            '<svg width="27px" height="27px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M128 64C92.7 64 64 92.7 64 128L64 512C64 547.3 92.7 576 128 576L329.2 576C293 533.4 272 478.5 272 420.4L272 389.3C272 354.9 294 324.3 326.7 313.4L438.7 276.1C441.8 275.1 444.9 274.3 448 273.6L448 234.5C448 217.5 441.3 201.2 429.3 189.2L322.7 82.7C310.7 70.7 294.5 64 277.5 64L128 64zM389.5 240L296 240C282.7 240 272 229.3 272 216L272 122.5L389.5 240zM477.3 552.5L464 558.8L464 370.7L560 402.7L560 422.3C560 478.1 527.8 528.8 477.3 552.6zM453.9 323.5L341.9 360.8C328.8 365.2 320 377.4 320 391.2L320 422.3C320 496.7 363 564.4 430.2 596L448.7 604.7C453.5 606.9 458.7 608.1 463.9 608.1C469.1 608.1 474.4 606.9 479.1 604.7L497.6 596C565 564.3 608 496.6 608 422.2L608 391.1C608 377.3 599.2 365.1 586.1 360.7L474.1 323.4C467.5 321.2 460.4 321.2 453.9 323.4z"/></svg>',
            esc_html( _x( 'Restore PDF', '', 'ilove-pdf' ) ),
            Backup::is_file_backup( $post_id ) ? 'ipdf-btn--media-action-restore-active' : '',
        );
    }

    /**
     * Render the status for a file.
     *
     * @return string HTML for the status.
     */
    protected function status_restore_processing() {
        return sprintf(
            '<div class="ipdf-item-status ipdf-item-status-processing-restore ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small">
                %1$s
                <span class="">%2$s</span>
            </div>',
            $this->get_loading(),
            esc_html_x( 'Restoring PDF file', '', 'ilove-pdf' ),
        );
    }

    /**
     * Render the status for a file that has been restored.
     *
     * @return string HTML for the status.
     */
    protected function status_restored() {
        return sprintf(
            '<div class="ipdf-item-status ipdf-item-status-restored ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small">
                <svg width="14" height="14" aria-hidden="true" focusable="false" data-icon="circle-check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"></path></svg>
                <span class="">%1$s</span>
            </div>',
            esc_html_x( 'PDF file restored', '', 'ilove-pdf' ),
        );
    }
}
