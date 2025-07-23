<?php

namespace Ilove_Pdf_WP\Helpers;

/**
 * Renders admin notice messages in the WordPress dashboard.
 *
 * @package Ilove_Pdf_WP
 * @since 3.0.0
 */
class Admin_Notice {

    /**
     * Displays a notice message in the WordPress admin area.
     *
     * @since 3.0.0
     * @param string $message The message to display.
     * @param string $type    The type of notice: 'error', 'success', 'warning', or 'info'. Default 'info'.
     */
    public static function render( $message, $type = 'info' ) {
        $class = 'notice is-dismissible notice-' . $type;

        printf(
            '<div class="%s"><p>%s</p></div>',
            esc_attr( $class ),
            wp_kses_post( $message )
        );
    }
}
