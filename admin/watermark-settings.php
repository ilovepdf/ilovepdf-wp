<?php
/**
 * Watermark settings
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
function ilove_pdf_initialize_options_watermark() {

    if ( false === get_option( 'ilove_pdf_display_settings_watermark' ) ) {
        add_option( 'ilove_pdf_display_settings_watermark' );
    }

    add_settings_section(
        'watermark_settings_section',
        '',
        'ilove_pdf_watermark_options_callback',
        'ilove_pdf_display_settings_watermark'
    );

    add_settings_field(
        'ilove_pdf_watermark_active',
        __( 'Enable Watermark PDF', 'ilove-pdf' ),
        'ilove_pdf_watermark_active_callback',
        'ilove_pdf_display_settings_watermark',
        'watermark_settings_section',
        array(
            __( 'Activate this setting for active/inactive Watermark on PDF files.', 'ilove-pdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_watermark_auto',
        __( 'Enable Auto Watermark', 'ilove-pdf' ),
        'ilove_pdf_watermark_auto_callback',
        'ilove_pdf_display_settings_watermark',
        'watermark_settings_section',
        array(
            __( 'Activate this setting for Auto Watermark on new PDF uploads.', 'ilove-pdf' ),
        )
    );

    register_setting(
        'ilove_pdf_display_settings_watermark',
        'ilove_pdf_display_settings_watermark'
    );
}
add_action( 'admin_init', 'ilove_pdf_initialize_options_watermark' );

/**
 * Options Callback.
 *
 * @since    1.0.0
 */
function ilove_pdf_watermark_options_callback() {
    echo '<h3>' . esc_html( __( 'Configure your Watermark PDF settings.', 'ilove-pdf' ) ) . '</h3>';
}

/**
 * Active Watermark Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_watermark_active_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_watermark' );
    $html    = sprintf(
        '<input type="checkbox" id="ilove_pdf_watermark_active" name="ilove_pdf_display_settings_watermark[ilove_pdf_watermark_active]" value="%s" %s><label for="ilove_pdf_watermark_active">%s</label>',
        '1',
        isset( $options['ilove_pdf_watermark_active'] ) ? checked( 1, $options['ilove_pdf_watermark_active'], false ) : '',
        $args[0]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Enable Auto Watermark Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_watermark_auto_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_watermark' );
    $html    = sprintf(
        '<input type="checkbox" id="ilove_pdf_watermark_auto" name="ilove_pdf_display_settings_watermark[ilove_pdf_watermark_auto]" value="1" %s><label for="ilove_pdf_watermark_auto">%s</label>',
        isset( $options['ilove_pdf_watermark_auto'] ) ? checked( 1, $options['ilove_pdf_watermark_auto'], false ) : '',
        $args[0]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Initialize Options for Watermark the admin area.
 *
 * @since    1.0.0
 */
function ilove_pdf_initialize_options_format_watermark() {

    if ( ! empty( $_SERVER['PHP_SELF'] ) && 'options-general.php' === basename( sanitize_url( wp_unslash( $_SERVER['PHP_SELF'] ) ) ) ) {
		wp_enqueue_media(); }

    if ( false === get_option( 'ilove_pdf_display_settings_format_watermark' ) ) {
        add_option( 'ilove_pdf_display_settings_format_watermark' );
    }

    add_settings_section(
        'format_watermark_settings_section',
        '',
        'ilove_pdf_format_watermark_options_callback',
        'ilove_pdf_display_settings_format_watermark'
    );

    add_settings_field(
        'ilove_pdf_format_watermark_mode',
        __( 'Watermark mode', 'ilove-pdf' ),
        'ilove_pdf_format_watermark_mode_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_mode',
        array(
            'Text',
            'Image',
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_text',
        __( 'Watermark Text', 'ilove-pdf' ),
        'ilove_pdf_format_watermark_text_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_text',
        array(
            __( 'Watermark text.', 'ilove-pdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_text_size',
        __( 'Watermark Text Size', 'ilove-pdf' ),
        'ilove_pdf_format_watermark_text_size_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_size',
        array(
            __( 'Indicate text size in pixels. From 5 to 80.', 'ilove-pdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_font_family',
        __( 'Watermark Font Family', 'ilove-pdf' ),
        'ilove_pdf_format_watermark_font_family_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_font_family',
        array(
            __( 'Select Watermark font Family.', 'ilove-pdf' ),
            'Verdana',
            'Courier',
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_text_color',
        __( 'Watermark Text Color', 'ilove-pdf' ),
        'ilove_pdf_format_watermark_text_color_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_text_color'
    );

    add_settings_field(
        'ilove_pdf_format_watermark_image',
        __( 'Watermark image', 'ilove-pdf' ),
        'ilove_pdf_format_watermark_image_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_image'
    );

    add_settings_field(
        'ilove_pdf_format_watermark_vertical',
        __( 'Watermark Vertical position', 'ilove-pdf' ),
        'ilove_pdf_format_watermark_vertical_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_vertical',
        array(
            'Bottom',
            'Top',
            'Middle',
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_horizontal',
        __( 'Watermark Horizontal position', 'ilove-pdf' ),
        'ilove_pdf_format_watermark_horizontal_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_horizontal',
        array(
            'Left',
            'Right',
            'Center',
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_opacity',
        __( 'Watermark Opacity', 'ilove-pdf' ),
        'ilove_pdf_format_watermark_opacity_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_opacity',
        array(
            __( 'From 0 to 100.', 'ilove-pdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_rotation',
        __( 'Watermark Rotation', 'ilove-pdf' ),
        'ilove_pdf_format_watermark_rotation_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_rotation',
        array(
            __( 'From 0 to 360.', 'ilove-pdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_layer',
        __( 'Watermark layer depth', 'ilove-pdf' ),
        'ilove_pdf_format_watermark_layer_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_layer',
        array(
            'Over',
            'Below',
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_mosaic',
        __( 'Enable Mosaic Watermark', 'ilove-pdf' ),
        'ilove_pdf_format_watermark_mosaic_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_mosaic',
        array(
            __( 'Activate this setting for enable Watermark Mosaic.', 'ilove-pdf' ),
        )
    );

    register_setting(
        'ilove_pdf_display_settings_format_watermark',
        'ilove_pdf_display_settings_format_watermark'
    );
}
add_action( 'admin_init', 'ilove_pdf_initialize_options_format_watermark' );

/**
 * Watermark Options Callback.
 *
 * @since    1.0.0
 */
function ilove_pdf_format_watermark_options_callback() {
    echo '<h3>' . esc_html( __( 'Configure your Watermark format.', 'ilove-pdf' ) ) . '</h3>';
}

/**
 * Watermark Content Text Callback (Mode Text).
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_text_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = sprintf(
        '<input type="text" id="ilove_pdf_format_watermark_text" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_text]" value="%s"><label for="ilove_pdf_format_watermark_text">%s</label>',
        isset( $options['ilove_pdf_format_watermark_text'] ) ? $options['ilove_pdf_format_watermark_text'] : get_bloginfo(),
        $args[0]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Watermark Text Size Callback (Mode Text).
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_text_size_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = sprintf(
        '<input type="number" id="ilove_pdf_format_watermark_text_size" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_text_size]" min="5" max="80" value="%d"><label for="ilove_pdf_format_watermark_text_size">%s</label>',
        isset( $options['ilove_pdf_format_watermark_text_size'] ) ? $options['ilove_pdf_format_watermark_text_size'] : 22,
        $args[0]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Watermark Font Family Callback (Mode Text).
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_font_family_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = sprintf(
        '<select id="ilove_pdf_format_watermark_font_family" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_font_family]">
        <option value="%s" %s>Verdana</option>
        <option value="%s" %s>Courier</option></select>
        <label for="ilove_pdf_format_watermark_font_family"> %s</label>',
        $args[1],
        isset( $options['ilove_pdf_format_watermark_font_family'] ) ? selected( $args[1], $options['ilove_pdf_format_watermark_font_family'], false ) : 'selected="selected"',
        $args[2],
        isset( $options['ilove_pdf_format_watermark_font_family'] ) ? selected( $args[2], $options['ilove_pdf_format_watermark_font_family'], false ) : '',
        $args[0]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Watermark Text Color Callback (Mode Text).
 *
 * @since    1.0.0
 */
function ilove_pdf_format_watermark_text_color_callback() {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = sprintf(
        '<input type="text" class="color-field" id="ilove_pdf_format_watermark_text_color" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_text_color]" value="%s">',
        isset( $options['ilove_pdf_format_watermark_text_color'] ) ? $options['ilove_pdf_format_watermark_text_color'] : '#dd3333'
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Watermark Vertical Position Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_vertical_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = sprintf(
        '<input type="radio" id="ilove_pdf_format_watermark_vertical" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_vertical]" value="0" %s><label for="ilove_pdf_format_watermark_vertical"> %s</label><br/>
        <input type="radio" id="ilove_pdf_format_watermark_vertical" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_vertical]" value="1" %s><label for="ilove_pdf_format_watermark_vertical"> %s</label><br/>
        <input type="radio" id="ilove_pdf_format_watermark_vertical" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_vertical]" value="2" %s><label for="ilove_pdf_format_watermark_vertical"> %s</label>',
        isset( $options['ilove_pdf_format_watermark_vertical'] ) ? checked( 0, $options['ilove_pdf_format_watermark_vertical'], false ) : '',
        $args[0],
        isset( $options['ilove_pdf_format_watermark_vertical'] ) ? checked( 1, $options['ilove_pdf_format_watermark_vertical'], false ) : '',
        $args[1],
        isset( $options['ilove_pdf_format_watermark_vertical'] ) ? checked( 2, $options['ilove_pdf_format_watermark_vertical'], false ) : 'checked="checked"',
        $args[2]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Watermark Horizontal Position Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_horizontal_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = sprintf(
        '<input type="radio" id="ilove_pdf_format_watermark_horizontal" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_horizontal]" value="0" %s><label for="ilove_pdf_format_watermark_horizontal"> %s</label><br/>
        <input type="radio" id="ilove_pdf_format_watermark_horizontal" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_horizontal]" value="1" %s><label for="ilove_pdf_format_watermark_horizontal"> %s</label><br/>
        <input type="radio" id="ilove_pdf_format_watermark_horizontal" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_horizontal]" value="2" %s><label for="ilove_pdf_format_watermark_horizontal"> %s</label>',
        isset( $options['ilove_pdf_format_watermark_horizontal'] ) ? checked( 0, $options['ilove_pdf_format_watermark_horizontal'], false ) : '',
        $args[0],
        isset( $options['ilove_pdf_format_watermark_horizontal'] ) ? checked( 1, $options['ilove_pdf_format_watermark_horizontal'], false ) : '',
        $args[1],
        isset( $options['ilove_pdf_format_watermark_horizontal'] ) ? checked( 2, $options['ilove_pdf_format_watermark_horizontal'], false ) : 'checked="checked"',
        $args[2]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Watermark Opacity Callback (Mode Image).
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_opacity_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = sprintf(
        '<input type="number" id="ilove_pdf_format_watermark_opacity" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_opacity]" min="0" max="100" value="%s"><label for="ilove_pdf_format_watermark_opacity"> %s</label>',
        isset( $options['ilove_pdf_format_watermark_opacity'] ) ? $options['ilove_pdf_format_watermark_opacity'] : '50',
        $args[0]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Watermark Rotation Callback (Mode Image).
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_rotation_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = sprintf(
        '<input type="number" id="ilove_pdf_format_watermark_rotation" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_rotation]" min="0" max="360" value="%s"><label for="ilove_pdf_format_watermark_rotation"> %s</label>',
        isset( $options['ilove_pdf_format_watermark_rotation'] ) ? $options['ilove_pdf_format_watermark_rotation'] : '30',
        $args[0]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Watermark Layer Depth Callback (Mode Image).
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_layer_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = sprintf(
        '<input type="radio" id="ilove_pdf_format_watermark_layer" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_layer]" value="0" %s><label for="ilove_pdf_format_watermark_layer"> %s </label> 
        <input type="radio" id="ilove_pdf_format_watermark_layer" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_layer]" value="1" %s><label for="ilove_pdf_format_watermark_layer"> %s</label>',
        isset( $options['ilove_pdf_format_watermark_layer'] ) ? checked( 0, $options['ilove_pdf_format_watermark_layer'], false ) : '',
        $args[0],
        isset( $options['ilove_pdf_format_watermark_layer'] ) ? checked( 1, $options['ilove_pdf_format_watermark_layer'], false ) : 'checked="checked"',
        $args[1]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Watermark Mosaic Callback (Mode Image).
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_mosaic_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = sprintf(
        '<input type="checkbox" id="ilove_pdf_format_watermark_mosaic" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_mosaic]" value="1" %s><label for="ilove_pdf_format_watermark_mosaic"> %s</label>',
        isset( $options['ilove_pdf_format_watermark_mosaic'] ) ? checked( 1, $options['ilove_pdf_format_watermark_mosaic'], false ) : '',
        $args[0]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Watermark Select Mode Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_mode_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = sprintf(
        '<input type="radio" id="ilove_pdf_format_watermark_mode" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_mode]" value="0" %s><label for="ilove_pdf_format_watermark_mode"> %s</label> 
        <input type="radio" id="ilove_pdf_format_watermark_mode" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_mode]" value="1" %s><label for="ilove_pdf_format_watermark_mode"> %s</label>',
        isset( $options['ilove_pdf_format_watermark_mode'] ) ? checked( 0, $options['ilove_pdf_format_watermark_mode'], false ) : 'checked="checked"',
        $args[0],
        isset( $options['ilove_pdf_format_watermark_mode'] ) ? checked( 1, $options['ilove_pdf_format_watermark_mode'], false ) : '',
        $args[1]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Watermark Upload Image Callback (Mode Image).
 *
 * @since    1.0.0
 */
function ilove_pdf_format_watermark_image_callback() {
    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );

	$image = ( isset( $options['ilove_pdf_format_watermark_image'] ) && ! empty( $options['ilove_pdf_format_watermark_image'] ) ) ? '<img id="image-preview image-user-select" src="' . wp_get_attachment_url( $options['ilove_pdf_format_watermark_image'] ) . '" height="100">' : '<img id="image-preview" height="100" style="max-width: 100px">';

    $html = sprintf(
        '<div class="image-preview-wrapper">%s</div>
        <input id="upload_image_button" type="button" class="button" value="%s" />
        <input type="hidden" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_image]" id="ilove_pdf_format_watermark_image" value="%s" /> 
        <input class="button-primary" type="submit" name="submit_image_selector" value="%s" />',
        $image,
        __( 'Upload image', 'ilove-pdf' ),
        ( isset( $options['ilove_pdf_format_watermark_image'] ) && ! empty( $options['ilove_pdf_format_watermark_image'] ) ) ? $options['ilove_pdf_format_watermark_image'] : '',
        __( 'Save', 'ilove-pdf' )
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Watermark Selector Print Callback.
 *
 * @since    1.0.0
 */
function ilove_pdf_media_selector_print_scripts() {
    $options                     = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $my_saved_attachment_post_id = isset( $options['ilove_pdf_format_watermark_image'] ) && '' !== $options['ilove_pdf_format_watermark_image'] ? $options['ilove_pdf_format_watermark_image'] : 0;
    ?> <script type='text/javascript'>
        jQuery( document ).ready( function( $ ) {

            if ( typeof wp === 'undefined' || ! wp.media ) {
                return;
            }

            // Uploading files
            var file_frame;
            var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
            var set_to_post_id = <?php echo (int) $my_saved_attachment_post_id; ?>; // Set this
            jQuery('#upload_image_button').on('click', function( event ){
                event.preventDefault();
                // If the media frame already exists, reopen it.
                if ( file_frame ) {
                    // Set the post ID to what we want
                    file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                    // Open frame
                    file_frame.open();
                    return;
                } else {
                    // Set the wp.media post id so the uploader grabs the ID we want when initialised
                    wp.media.model.settings.post.id = set_to_post_id;
                }
                // Create the media frame.
                file_frame = wp.media.frames.file_frame = wp.media({
                    title: '<?php echo esc_html( __( 'Select a image to upload', 'ilove-pdf' ) ); ?>',
                    library : {
                        type : 'image'
                    },
                    button: {
                        text: '<?php echo esc_html( __( 'Use this image', 'ilove-pdf' ) ); ?>',
                    },
                    multiple: false // Set to true to allow multiple files to be selected
                });
                // When an image is selected, run a callback.
                file_frame.on( 'select', function() {
                    // We set multiple to false so only get one image from the uploader
                    attachment = file_frame.state().get('selection').first().toJSON();
                    // Do something with attachment.id and/or attachment.url here
                    $( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );                    
                    $( '#image_attachment_id' ).val( attachment.url );                    
                    $( '#ilove_pdf_format_watermark_image' ).val( attachment.id );
                    // Restore the main post ID
                    wp.media.model.settings.post.id = wp_media_post_id;
                });
                    // Finally, open the modal
                    file_frame.open();
            });
            // Restore the main ID when the add media button is pressed
            jQuery( 'a.add_media' ).on( 'click', function() {
                wp.media.model.settings.post.id = wp_media_post_id;
            });
        });
    </script>
    <?php
}
add_action( 'admin_footer', 'ilove_pdf_media_selector_print_scripts' );