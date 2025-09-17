<?php

namespace Ilove_Pdf_WP\Tools\Compress;

/**
 * Handles tool compress options.
 *
 * @package Ilove_Pdf_WP\Tools\Compress
 * @since 3.0.0
 */
class Options {
    /**
     * Field option key used for tool activation.
     *
     * @since 3.0.0
     * @var string
     */
    private static $field_compress_active = 'ipdf_option_compress_active';

    /**
     * Field option key used to activate auto compress.
     *
     * @since 3.0.0
     * @var string
     */
    private static $field_auto_compress = 'ipdf_option_auto_compress';

    /**
     * Field option key used for compression level.
     *
     * @since 3.0.0
     * @var string
     */
    private static $field_compression_level = 'ipdf_option_compression_level';

    /**
     * Accepted compression levels.
     *
     * @since 3.0.0
     * @var array
     */
    private static $compression_level = array( 'extreme', 'recommended', 'low' );

    /**
     * Returns the field option key used for compress active.
     *
     * @return string
     */
    public static function get_field_compress_active() {
        return self::$field_compress_active;
    }

    /**
     * Returns the field option key used for auto compress.
     *
     * @return string
     */
    public static function get_field_auto_compress() {
        return self::$field_auto_compress;
    }

    /**
     * Returns the field option key used for compression level.
     *
     * @return string
     */
    public static function get_field_compression_level() {
        return self::$field_compression_level;
    }

    /**
     * Returns the accepted compression levels.
     *
     * @param string $value Optional. A specific compression level to check.
     * @return array|string If $value is provided, returns the specific level if valid, otherwise returns all.
     */
    public static function get_compress_level( $value = '' ) {
        if ( in_array( $value, self::$compression_level, true ) ) {
            return $value;
        }

        return self::$compression_level;
    }
}
