<?php

/***************
 * ** COMPRESS ***
 ****************/

function ilove_pdf_initialize_options_compress() {

    if ( false == get_option( 'ilove_pdf_display_settings_compress' ) ) {
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
        __( 'Enable Compress PDF', 'ilovepdf' ),
        'ilove_pdf_compress_active_callback',
        'ilove_pdf_display_settings_compress',
        'compress_settings_section',
        array(
            __( 'Activate this setting for active/inactive Compress PDF.', 'ilovepdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_compress_quality',
        __( 'PDF Quality', 'ilovepdf' ),
        'ilove_pdf_compress_quality_callback',
        'ilove_pdf_display_settings_compress',
        'compress_settings_section',
        array(
            __( 'Low', 'ilovepdf' ),
            __( 'Recommended', 'ilovepdf' ),
            __( 'Extreme', 'ilovepdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_compress_autocompress_new',
        __( 'Enable Autocompress PDF', 'ilovepdf' ),
        'ilove_pdf_compress_autocompress_new_callback',
        'ilove_pdf_display_settings_compress',
        'compress_settings_section',
        array(
            __( 'Activate this setting for Autocompress new PDF uploads.', 'ilovepdf' ),
        )
    );

    register_setting(
        'ilove_pdf_display_settings_compress',
        'ilove_pdf_display_settings_compress'
    );
}
add_action( 'admin_init', 'ilove_pdf_initialize_options_compress' );

function ilove_pdf_compress_options_callback() {
    echo '<h3>' . __( 'Configure your Compress PDF settings.', 'ilovepdf' ) . '</h3>';
}

function ilove_pdf_compress_active_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_compress' );
    $html    = '<input type="checkbox" id="ilove_pdf_compress_active" name="ilove_pdf_display_settings_compress[ilove_pdf_compress_active]" value="1"' . ( isset( $options['ilove_pdf_compress_active'] ) ? checked( 1, $options['ilove_pdf_compress_active'], false ) : '' ) . '">';
    $html   .= '<label for="ilove_pdf_compress_active"> ' . $args[0] . '</label>';

    echo $html;
}

function ilove_pdf_compress_quality_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_compress' );
    $html    = '<input type="radio" id="ilove_pdf_compress_quality" name="ilove_pdf_display_settings_compress[ilove_pdf_compress_quality]" value="0"' . ( isset( $options['ilove_pdf_compress_quality'] ) ? checked( 0, $options['ilove_pdf_compress_quality'], false ) : '' ) . '">';
    $html   .= '<label for="ilove_pdf_compress_active"> ' . $args[0] . ' (' . __( 'High quality, less compression', 'ilovepdf' ) . ')</label><br /><br />';

    $html .= '<input type="radio" id="ilove_pdf_compress_quality" name="ilove_pdf_display_settings_compress[ilove_pdf_compress_quality]" value="1"' . ( isset( $options['ilove_pdf_compress_quality'] ) ? checked( 1, $options['ilove_pdf_compress_quality'], false ) : 'checked="checked"' ) . '">';
    $html .= '<label for="ilove_pdf_compress_active"> ' . $args[1] . ' (' . __( 'Good quality, good compression', 'ilovepdf' ) . ')</label><br /><br />';

    $html .= '<input type="radio" id="ilove_pdf_compress_quality" name="ilove_pdf_display_settings_compress[ilove_pdf_compress_quality]" value="2"' . ( isset( $options['ilove_pdf_compress_quality'] ) ? checked( 2, $options['ilove_pdf_compress_quality'], false ) : '' ) . '">';
    $html .= '<label for="ilove_pdf_compress_active"> ' . $args[2] . ' (' . __( 'Less quality, high compression', 'ilovepdf' ) . ')</label>';

    echo $html;
}

function ilove_pdf_compress_autocompress_new_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_compress' );
    $html    = '<input type="checkbox" id="ilove_pdf_compress_autocompress_new" name="ilove_pdf_display_settings_compress[ilove_pdf_compress_autocompress_new]" value="1"' . ( isset( $options['ilove_pdf_compress_autocompress_new'] ) ? checked( 1, $options['ilove_pdf_compress_autocompress_new'], false ) : '' ) . '">';
    $html   .= '<label for="ilove_pdf_compress_autocompress_new"> ' . $args[0] . '</label>';

    echo $html;
}
