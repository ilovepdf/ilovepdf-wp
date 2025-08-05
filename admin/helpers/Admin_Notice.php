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
     * Constructor to initialize the admin notice rendering.
     *
     * @since 3.0.0
     */
    public function __construct() {
        add_action( 'admin_notices', array( $this, 'show_notice_on_media_page' ) );
    }

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

    /**
     * Displays notices on the media page based on transient data.
     *
     * This method checks for transient data set during bulk actions and displays success or error messages accordingly.
     *
     * @since 3.0.0
     */
    public function show_notice_on_media_page() {
        $bulk_notices = get_transient( 'ilovepdf_bulk' );

        if ( $bulk_notices ) {

            if ( ! empty( $bulk_notices['success'] ) ) {
                foreach ( $bulk_notices['success'] as $message ) {
                    self::render( $message, 'success' );
                }
            }

            if ( ! empty( $bulk_notices['errors'] ) ) {
                foreach ( $bulk_notices['errors'] as $error ) {
                    self::render( $error['message'], 'error' );
                }
            }

            delete_transient( 'ilovepdf_bulk' );
        }
    }
}
