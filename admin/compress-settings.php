<?php
/**
 * Compress Settings
 *
 * @link       https://ilovepdf.com/
 * @since      1.0.0
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/admin
 */

/**
 * Initialize Options for the admin area.
 *
 * @since    1.0.0
 */
function ilove_pdf_initialize_options_compress() {

    if ( false === get_option( 'ilove_pdf_display_settings_compress' ) ) {
        add_option( 'ilove_pdf_display_settings_compress' );
    }

    add_settings_section(
        'compress_settings_section',
        '',
        'ilove_pdf_compress_options_callback',
        'ilove_pdf_display_settings_compress'
    );

    add_settings_field(
        'ilove_pdf_compress_active',
        __( 'Enable Compress PDF', 'ilove-pdf' ),
        'ilove_pdf_compress_active_callback',
        'ilove_pdf_display_settings_compress',
        'compress_settings_section',
        array(
            __( 'Activate this setting for active/inactive Compress PDF.', 'ilove-pdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_compress_quality',
        __( 'PDF Quality', 'ilove-pdf' ),
        'ilove_pdf_compress_quality_callback',
        'ilove_pdf_display_settings_compress',
        'compress_settings_section',
        array(
            __( 'Low', 'ilove-pdf' ),
            __( 'Recommended', 'ilove-pdf' ),
            __( 'Extreme', 'ilove-pdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_compress_autocompress_new',
        __( 'Enable Autocompress PDF', 'ilove-pdf' ),
        'ilove_pdf_compress_autocompress_new_callback',
        'ilove_pdf_display_settings_compress',
        'compress_settings_section',
        array(
            __( 'Activate this setting for Autocompress new PDF uploads.', 'ilove-pdf' ),
        )
    );

    register_setting(
        'ilove_pdf_display_settings_compress',
        'ilove_pdf_display_settings_compress'
    );
}
add_action( 'admin_init', 'ilove_pdf_initialize_options_compress' );

/**
 * Options Callback.
 *
 * @since    1.0.0
 */
function ilove_pdf_compress_options_callback() {
    echo '<h3>' . esc_html( __( 'Configure your Compress PDF settings.', 'ilove-pdf' ) ) . '</h3>';
}

/**
 * Active Compress PDF Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_compress_active_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_compress' );
    $html    = sprintf(
        '<input type="checkbox" id="ilove_pdf_compress_active" name="ilove_pdf_display_settings_compress[ilove_pdf_compress_active]" value="1" %s /><label for="ilove_pdf_compress_active"> %s</label>',
        isset( $options['ilove_pdf_compress_active'] ) ? checked( 1, $options['ilove_pdf_compress_active'], false ) : '',
        $args[0]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Compress Quality Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_compress_quality_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_compress' );
    $html    = sprintf(
        '<input type="radio" id="ilove_pdf_compress_quality" name="ilove_pdf_display_settings_compress[ilove_pdf_compress_quality]" value="0" %s /><label for="ilove_pdf_compress_quality"> %s</label><br>
        <input type="radio" id="ilove_pdf_compress_quality" name="ilove_pdf_display_settings_compress[ilove_pdf_compress_quality]" value="1" %s /><label for="ilove_pdf_compress_quality"> %s</label><br>
        <input type="radio" id="ilove_pdf_compress_quality" name="ilove_pdf_display_settings_compress[ilove_pdf_compress_quality]" value="2" %s /><label for="ilove_pdf_compress_quality"> %s</label>',
        isset( $options['ilove_pdf_compress_quality'] ) ? checked( 0, $options['ilove_pdf_compress_quality'], false ) : '',
        $args[0] . ' (' . __( 'High quality, less compression', 'ilove-pdf' ) . ')',
        isset( $options['ilove_pdf_compress_quality'] ) ? checked( 1, $options['ilove_pdf_compress_quality'], false ) : '',
        $args[1] . ' (' . __( 'Good quality, good compression', 'ilove-pdf' ) . ')',
        isset( $options['ilove_pdf_compress_quality'] ) ? checked( 2, $options['ilove_pdf_compress_quality'], false ) : '',
        $args[2] . ' (' . __( 'Less quality, high compression', 'ilove-pdf' ) . ')',
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Autocompress new Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_compress_autocompress_new_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_compress' );
    $html    = sprintf(
        '<input type="checkbox" id="ilove_pdf_compress_autocompress_new" name="ilove_pdf_display_settings_compress[ilove_pdf_compress_autocompress_new]" value="1" %s /><label for="ilove_pdf_compress_autocompress_new"> %s</label>',
        isset( $options['ilove_pdf_compress_autocompress_new'] ) ? checked( 1, $options['ilove_pdf_compress_autocompress_new'], false ) : '',
        $args[0]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}
