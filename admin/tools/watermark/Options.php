<?php

namespace Ilove_Pdf_WP\Tools\Watermark;

/**
 * Handles tool watermark options.
 *
 * @package Ilove_Pdf_WP\Tools\Watermark
 * @since 3.0.0
 */
class Options {
    /**
     * Field option key used for tool activation.
     *
     * @since 3.0.0
     * @var string
     */
    private static $field_watermark_active = 'ipdf_option_watermark_active';

    /**
     * Field option key used to activate auto watermark.
     *
     * @since 3.0.0
     * @var string
     */
    private static $field_auto_watermark = 'ipdf_option_auto_watermark';

    /**
     * Field option key for watermark mode selection.
     *
     * @var string
     */
    private static $field_mode = 'ipdf_option_mode_watermark';

    /**
     * Available watermark mode values.
     *
     * @var array
     */
    private static $mode_values = array( 'text', 'image' );

    /**
     * Field option key for text mode.
     *
     * @var string
     */
    private static $field_text_mode = 'ipdf_option_mode_text';

    /**
     * Field option key for image mode.
     *
     * @var string
     */
    private static $field_image_mode = 'ipdf_option_mode_image';

    /**
     * Field option key for font family selection.
     *
     * @var string
     */
    private static $field_font_family = 'ipdf_option_font_family';

    /**
     * Available font family values.
     *
     * @var array
     */
    private static $font_family_values = array(
		'Arial',
		'Arial Unicode MS',
		'Verdana',
		'Courier',
		'Times New Roman',
		'Comic Sans MS',
		'WenQuanYi Zen Hei',
		'Lohit Marathi',
    );

    /**
     * Field option key for font style selection.
     *
     * @var string
     */
    private static $field_font_style = 'ipdf_option_font_style';

    /**
     * Available font style values.
     *
     * @var array
     */
    private static $font_style_values = array(
        'Bold',
        'Italic',
    );

    /**
     * Field option key for font size.
     *
     * @var string
     */
    private static $field_font_size = 'ipdf_option_font_size';

    /**
     * Font size value range (min, max).
     *
     * @var array
     */
    private static $font_size_values = array(
        'min' => 1,
        'max' => 100,
    );

    /**
     * Field option key for font color.
     *
     * @var string
     */
    private static $field_font_color = 'ipdf_option_font_color';

    /**
     * Field option key for position.
     *
     * @var string
     */
    private static $field_position = 'ipdf_option_position';

    /**
     * Available position values.
     *
     * - horizontal can be: 'left', 'center', 'right'
     * - vertical can be: 'top', 'middle', 'bottom'
     *
     * @var array
     */
    private static $position_values = array(
        'left top',
        'center top',
        'right top',
        'left middle',
        'center middle',
        'right middle',
        'left bottom',
        'center bottom',
        'right bottom',
    );

    /**
     * Field option key for transparency.
     *
     * @var string
     */
    private static $field_transparency = 'ipdf_option_transparency';

    /**
     * Transparency value range (min, max).
     *
     * @var array
     */
    private static $transparency_values = array(
        'min' => 1,
        'max' => 100,
    );

    /**
     * Field option key for mosaic mode.
     *
     * @var string
     */
    private static $field_mosaic = 'ipdf_option_mosaic';

    /**
     * Field option key for rotation.
     *
     * @var string
     */
    private static $field_rotation = 'ipdf_option_rotation';

    /**
     * Rotation value range (min, max degrees).
     *
     * @var array
     */
    private static $rotation_values = array(
        'min' => 0,
        'max' => 360,
    );

    /**
     * Field option key for layer positioning.
     *
     * @var string
     */
    private static $field_layer = 'ipdf_option_layer';

    /**
     * Available layer position values.
     *
     * @var array
     */
    private static $layer_values = array( 'above', 'below' );

    /**
     * Returns the field watermark active key.
     *
     * @return string
     */
    public static function get_field_watermark_active() {
        return self::$field_watermark_active;
    }

    /**
     * Returns the field auto watermark key.
     *
     * @return string
     */
    public static function get_field_auto_watermark() {
        return self::$field_auto_watermark;
    }

    /**
     * Returns the field mode key.
     *
     * @return string
     */
    public static function get_field_mode() {
        return self::$field_mode;
    }

    /**
     * Returns the field key for text mode.
     *
     * @return string
     */
    public static function get_field_text_mode() {
        return self::$field_text_mode;
    }

    /**
     * Returns available mode values.
     *
     * @param string $value Value to check if it is valid.
     * @return array|string Selected value or all values.
     */
    public static function get_mode_values( $value = '' ) {
        if ( in_array( $value, self::$mode_values, true ) ) {
            return $value;
        }

        return self::$mode_values;
    }

    /**
     * Returns the field image mode key.
     *
     * @return string
     */
    public static function get_field_image_mode() {
        return self::$field_image_mode;
    }

    /**
     * Returns the field font family key.
     *
     * @return string
     */
    public static function get_field_font_family() {
        return self::$field_font_family;
    }

    /**
     * Returns available font family values.
     *
     * @param string $value Value to check if it is valid.
     * @return array|string Selected value or all values.
     */
    public static function get_font_family_values( $value = '' ) {
        if ( in_array( $value, self::$font_family_values, true ) ) {
            return $value;
        }

        return self::$font_family_values;
    }

    /**
     * Returns the field font style key.
     *
     * @return string
     */
    public static function get_field_font_style() {
        return self::$field_font_style;
    }

    /**
     * Returns available font style values.
     *
     * @param string $value Value to check if it is valid.
     * @return array|string Selected value or all values.
     */
    public static function get_font_style_values( $value = '' ) {
        if ( in_array( $value, self::$font_style_values, true ) ) {
            return $value;
        }

        return self::$font_style_values;
    }

    /**
     * Returns the field font size key.
     *
     * @return string
     */
    public static function get_field_font_size() {
        return self::$field_font_size;
    }

    /**
     * Returns Font size value range.
     *
     * @param bool   $all_values If we need all the values.
     * @param string $type The type of value we need. It can be min or max.
     * @return array|int Selected value or all values.
     */
    public static function get_font_size_values( $all_values = true, $type = 'min' ) {
        if ( array_key_exists( $type, self::$font_size_values ) ) {
            return self::$font_size_values[ $type ];
        }

        return self::$font_size_values;
    }

    /**
     * Returns the field font color key.
     *
     * @return string
     */
    public static function get_field_font_color() {
        return self::$field_font_color;
    }

    public static function get_field_position() {
        return self::$field_position;
    }

    /**
     * Returns available position values.
     *
     * @param string $value Value to check if it is valid.
     * @return array|string Selected value or all values.
     */
    public static function get_position_values( $value = '' ) {
        if ( in_array( $value, self::$position_values, true ) ) {
            return $value;
        }

        return self::$position_values;
    }

    /**
     * Returns the field transparency key.
     *
     * @return string
     */
    public static function get_field_transparency() {
        return self::$field_transparency;
    }

    /**
     * Returns transparency value range.
     *
     * @param bool   $all_values If we need all the values.
     * @param string $type The type of value we need. It can be min or max.
     * @return array|int Selected value or all values.
     */
    public static function get_transparency_values( $all_values = true, $type = 'min' ) {
        if ( array_key_exists( $type, self::$transparency_values ) ) {
            return self::$transparency_values[ $type ];
        }

        return self::$transparency_values;
    }

    /**
     * Returns the field mosaic key.
     *
     * @return string
     */
    public static function get_field_mosaic() {
        return self::$field_mosaic;
    }

    /**
     * Returns the field rotation key.
     *
     * @return string
     */
    public static function get_field_rotation() {
        return self::$field_rotation;
    }

    /**
     * Returns rotation value range.
     *
     * @param bool   $all_values If we need all the values.
     * @param string $type The type of value we need. It can be min or max.
     * @return array|int Selected value or all values.
     */
    public static function get_rotation_values( $all_values = true, $type = 'min' ) {
        if ( array_key_exists( $type, self::$rotation_values ) ) {
            return self::$rotation_values[ $type ];
        }

        return self::$rotation_values;
    }

    /**
     * Returns the field layer key.
     *
     * @return string
     */
    public static function get_field_layer() {
        return self::$field_layer;
    }

    /**
     * Returns available layer values.
     *
     * @param string $value Value to check if it is valid.
     * @return array|string Selected value or all values.
     */
    public static function get_layer_values( $value = '' ) {
        if ( in_array( $value, self::$layer_values, true ) ) {
            return $value;
        }

        return self::$layer_values;
    }
}
