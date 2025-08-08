<?php

namespace Ilove_Pdf_WP\Helpers;

use Ilove_Pdf_WP\Helpers\File_System;

/**
 * Renders admin notice messages in the WordPress dashboard.
 *
 * @package Ilove_Pdf_WP
 * @since 3.0.0
 */
class Admin_Notice {
    /**
     * The transient name for storing notice messages.
     *
     * @since 3.0.0
     * @var string
     */
    private $transient_name = 'ilovepdf_notices';

    /**
     * Constructor to initialize the admin notice rendering.
     *
     * @since 3.0.0
     */
    public function __construct() {
        add_action( 'admin_notices', array( $this, 'show_notice_admin' ) );
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
            '<div class="ipdf-notice ilovepdf-base__layout-flex %1$s">
                <figure class="ipdf-logo ilovepdf-base__layout-flex ilovepdf-base__layout-items--center">
                    <img src="%2$s" alt="logo ilovepdf" />
                </figure>
                <p>%3$s</p>
            </div>',
            esc_attr( $class ),
            esc_url( File_System::get_assets_url( 'img/logo_ilovepdf.svg' ) ),
            wp_kses_post( $message ),
        );
    }

    /**
     * Displays notices on the media page based on transient data.
     *
     * This method checks for transient data set during bulk actions and displays success or error messages accordingly.
     *
     * @since 3.0.0
     */
    public function show_notice_admin() {
        $notices = get_transient( $this->transient_name );

        if ( $notices ) {

            foreach ( $notices as $notice ) {
                if ( ! isset( $notice['message'] ) || ! isset( $notice['type'] ) ) {
                    continue;
                }

                self::render( $notice['message'], $notice['type'] );
            }

            delete_transient( $this->transient_name );
        }
    }

    /**
     * Adds a notice message to the transient storage.
     *
     * @since 3.0.0
     * @param string $message The message to display.
     * @param string $type    The type of notice: 'error', 'success', 'warning', or 'info'. Default 'info'.
     */
    public static function add_notice( $message, $type = 'info' ) {
        $instance = new self();
        $notices  = get_transient( $instance->transient_name );

        if ( ! is_array( $notices ) ) {
            $notices = array();
        }

        array_push(
            $notices,
            array(
				'message' => $message,
				'type'    => $type,
            )
        );

        set_transient( $instance->transient_name, $notices, MINUTE_IN_SECONDS * 5 );
    }
}
