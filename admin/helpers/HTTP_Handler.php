<?php

namespace Ilove_Pdf_WP\Helpers;

trait HTTP_Handler {
    /**
     * Redirects the user back to the referring admin page with optional query arguments.
     *
     * @param array $args Optional. Query arguments to append to the redirect URL.
     */
	private static function redirect( $args = array() ) {

        if ( ! isset( $_POST['_wp_http_referer'] ) ) {
            $_POST['_wp_http_referer'] = wp_login_url();
        }

        $url = sanitize_text_field(
            wp_unslash( $_POST['_wp_http_referer'] )
        );

        wp_safe_redirect( add_query_arg( $args, urldecode( $url ) ) );
        exit;
    }

    /**
     * Get API error message.
     *
     * @param array  $response API response.
     * @param string $message_default Default message if no error found.
     *
     * @return string Error message.
     */
    private static function get_message_error( $response, $message_default ) {
        $message = $message_default;

        if ( array_key_exists( 'error', $response ) ) {
            $message = $response['error']['message'];

            if ( array_key_exists( 'param', $response['error'] ) ) {
                foreach ( $response['error']['param'] as $param ) {
                    if ( is_array( $param ) ) {
                        $params   = implode( ', ', $param );
                        $message .= ' ' . $params;
                    } else {
                        $message .= '. ' . $param;
                    }
                }
            }
        } elseif ( array_key_exists( 'message', $response ) ) {
            $message = $response['message'];
        }

        return $message;
    }
}
