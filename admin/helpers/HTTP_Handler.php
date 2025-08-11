<?php

namespace Ilove_Pdf_WP\Helpers;

/**
 * HTTP Request Handler Trait
 * Provides methods for handling HTTP requests and responses.
 *
 * @package Ilove_Pdf_WP\Helpers
 * @since 3.0.0
 */
trait HTTP_Handler {
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
