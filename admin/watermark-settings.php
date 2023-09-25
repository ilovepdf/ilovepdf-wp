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
        __( 'Enable Watermark PDF', 'ilovepdf' ),
        'ilove_pdf_watermark_active_callback',
        'ilove_pdf_display_settings_watermark',
        'watermark_settings_section',
        array(
            __( 'Activate this setting for active/inactive Watermark on PDF files.', 'ilovepdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_watermark_backup',
        __( 'Backup Original', 'ilovepdf' ),
        'ilove_pdf_watermark_backup_callback',
        'ilove_pdf_display_settings_watermark',
        'watermark_settings_section',
        array(
            'No',
            'Yes',
        )
    );

    add_settings_field(
        'ilove_pdf_watermark_auto',
        __( 'Enable Auto Watermark', 'ilovepdf' ),
        'ilove_pdf_watermark_auto_callback',
        'ilove_pdf_display_settings_watermark',
        'watermark_settings_section',
        array(
            __( 'Activate this setting for Auto Watermark on new PDF uploads.', 'ilovepdf' ),
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
    echo '<h3>' . esc_html( __( 'Configure your Watermark PDF settings.', 'ilovepdf' ) ) . '</h3>';
}

/**
 * Active Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_watermark_active_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_watermark' );
    $html    = sprintf(
        '<input type="checkbox" id="ilove_pdf_watermark_active" name="ilove_pdf_display_settings_watermark[ilove_pdf_watermark_active]" value="%s" %s><label for="ilove_pdf_watermark_active">%s</label>',
        '1',
        checked( 1, $options['ilove_pdf_watermark_active'], false ),
        $args[0]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}

/**
 * Backup Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_watermark_backup_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_watermark' );
    $html    = '<input type="radio" id="ilove_pdf_watermark_backup" name="ilove_pdf_display_settings_watermark[ilove_pdf_watermark_backup]" value="0"' . ( isset( $options['ilove_pdf_watermark_backup'] ) ? checked( 0, $options['ilove_pdf_watermark_backup'], false ) : '' ) . '">';
    $html   .= '<label for="ilove_pdf_watermark_backup"> ' . $args[0] . '</label>&nbsp;';

    $html .= '<input type="radio" id="ilove_pdf_watermark_backup" name="ilove_pdf_display_settings_watermark[ilove_pdf_watermark_backup]" value="1"' . ( isset( $options['ilove_pdf_watermark_backup'] ) ? checked( 1, $options['ilove_pdf_watermark_backup'], false ) : '' ) . '">';
    $html .= '<label for="ilove_pdf_watermark_backup"> ' . $args[1] . '</label>';

    echo $html;
}

/**
 * Auto Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_watermark_auto_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_watermark' );
    $html    = '<input type="checkbox" id="ilove_pdf_watermark_auto" name="ilove_pdf_display_settings_watermark[ilove_pdf_watermark_auto]" value="1"' . ( isset( $options['ilove_pdf_watermark_auto'] ) ? checked( 1, $options['ilove_pdf_watermark_auto'], false ) : '' ) . '">';
    $html   .= '<label for="ilove_pdf_watermark_auto"> ' . $args[0] . '</label>';

    echo $html;
}

/**
 * Initialize Options for Watermark the admin area.
 *
 * @since    1.0.0
 */
function ilove_pdf_initialize_options_format_watermark() {

    if ( ! empty( $_SERVER['PHP_SELF'] ) && 'options-general.php' === basename( $_SERVER['PHP_SELF'] ) ) {
		wp_enqueue_media(); }

    // Add the color picker css file
    wp_enqueue_style( 'wp-color-picker' );

    // Include our custom jQuery file with WordPress Color Picker dependency
    wp_enqueue_script( 'ilove-pdf-admin', plugins_url( 'js/ilove-pdf-admin.js', __FILE__ ), array( 'wp-color-picker' ), false, true );

    if ( false === get_option( 'ilove_pdf_display_settings_format_watermark' ) ) {
        add_option( 'ilove_pdf_display_settings_format_watermark' );
    }

    add_settings_section(
        'format_watermark_settings_section',
        '',
        'ilove_pdf_format_watermark_options_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section'
    );

    add_settings_field(
        'ilove_pdf_format_watermark_mode',
        __( 'Watermark mode', 'ilovepdf' ),
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
        __( 'Watermark Text', 'ilovepdf' ),
        'ilove_pdf_format_watermark_text_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_text',
        array(
            __( 'Watermark text.', 'ilovepdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_text_size',
        __( 'Watermark Text Size', 'ilovepdf' ),
        'ilove_pdf_format_watermark_text_size_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_size',
        array(
            __( 'Indicate text size in pixels. From 5 to 80.', 'ilovepdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_font_family',
        __( 'Watermark Font Family', 'ilovepdf' ),
        'ilove_pdf_format_watermark_font_family_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_font_family',
        array(
            __( 'Select Watermark font Family.', 'ilovepdf' ),
            'Verdana',
            'Courier',
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_text_color',
        __( 'Watermark Text Color', 'ilovepdf' ),
        'ilove_pdf_format_watermark_text_color_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_text_color'
    );

    add_settings_field(
        'ilove_pdf_format_watermark_image',
        __( 'Watermark image', 'ilovepdf' ),
        'ilove_pdf_format_watermark_image_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_image'
    );

    add_settings_field(
        'ilove_pdf_format_watermark_vertical',
        __( 'Watermark Vertical position', 'ilovepdf' ),
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
        __( 'Watermark Horizontal position', 'ilovepdf' ),
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
        __( 'Watermark Opacity', 'ilovepdf' ),
        'ilove_pdf_format_watermark_opacity_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_opacity',
        array(
            __( 'From 0 to 100.', 'ilovepdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_rotation',
        __( 'Watermark Rotation', 'ilovepdf' ),
        'ilove_pdf_format_watermark_rotation_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_rotation',
        array(
            __( 'From 0 to 360.', 'ilovepdf' ),
        )
    );

    add_settings_field(
        'ilove_pdf_format_watermark_layer',
        __( 'Watermark layer depth', 'ilovepdf' ),
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
        __( 'Enable Mosaic Watermark', 'ilovepdf' ),
        'ilove_pdf_format_watermark_mosaic_callback',
        'ilove_pdf_display_settings_format_watermark',
        'format_watermark_settings_section_mosaic',
        array(
            __( 'Activate this setting for enable Watermark Mosaic.', 'ilovepdf' ),
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
    echo '<h3>' . esc_html( __( 'Configure your Watermark format.', 'ilovepdf' ) ) . '</h3>';
}

/**
 * Watermark Text Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_text_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = '<input type="text" id="ilove_pdf_format_watermark_text" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_text]" value="' . ( isset( $options['ilove_pdf_format_watermark_text'] ) ? $options['ilove_pdf_format_watermark_text'] : get_bloginfo() ) . '">';
    $html   .= '<label for="ilove_pdf_format_watermark_text"> ' . $args[0] . '</label>';

    echo $html;
}

/**
 * Watermark Text Size Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_text_size_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = '<input type="number" id="ilove_pdf_format_watermark_text_size" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_text_size]" min="5" max="80" value="' . ( isset( $options['ilove_pdf_format_watermark_text_size'] ) ? $options['ilove_pdf_format_watermark_text_size'] : '' ) . '">';
    $html   .= '<label for="ilove_pdf_format_watermark_text_size"> ' . $args[0] . '</label><br /><br />';

    echo $html;
}

/**
 * Watermark Font Family Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_font_family_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = '<select id="ilove_pdf_format_watermark_font_family" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_font_family]">
                <option value="' . $args[1] . '" ' . ( isset( $options['ilove_pdf_format_watermark_font_family'] ) ? selected( $args[1], $options['ilove_pdf_format_watermark_font_family'], false ) : '' ) . '>Verdana</option>
                <option value="' . $args[2] . '" ' . ( isset( $options['ilove_pdf_format_watermark_font_family'] ) ? selected( $args[2], $options['ilove_pdf_format_watermark_font_family'], false ) : '' ) . '>Courier</option>
            </select>';
    $html   .= '<label for="ilove_pdf_format_watermark_font_family"> ' . $args[0] . '</label>';

    echo $html;
}

/**
 * Watermark Text Color Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_text_color_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = '<input type="text" class="color-field" id="ilove_pdf_format_watermark_text_color" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_text_color]" value="' . ( isset( $options['ilove_pdf_format_watermark_text_color'] ) ? $options['ilove_pdf_format_watermark_text_color'] : '' ) . '">';

    echo $html;
}

/**
 * Watermark Vertical Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_vertical_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = '<input type="radio" id="ilove_pdf_format_watermark_vertical" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_vertical]" value="0"' . ( isset( $options['ilove_pdf_format_watermark_vertical'] ) ? checked( 0, $options['ilove_pdf_format_watermark_vertical'], false ) : '' ) . '">';
    $html   .= '<label for="ilove_pdf_format_watermark_vertical"> ' . $args[0] . '</label><br /><br />';

    $html .= '<input type="radio" id="ilove_pdf_format_watermark_vertical" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_vertical]" value="1"' . ( isset( $options['ilove_pdf_format_watermark_vertical'] ) ? checked( 1, $options['ilove_pdf_format_watermark_vertical'], false ) : '' ) . '">';
    $html .= '<label for="ilove_pdf_format_watermark_vertical"> ' . $args[1] . '</label><br /><br />';

    $html .= '<input type="radio" id="ilove_pdf_format_watermark_vertical" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_vertical]" value="2"' . ( isset( $options['ilove_pdf_format_watermark_vertical'] ) ? checked( 2, $options['ilove_pdf_format_watermark_vertical'], false ) : '' ) . '">';
    $html .= '<label for="ilove_pdf_format_watermark_vertical"> ' . $args[2] . '</label><br /><br />';

    echo $html;
}

/**
 * Watermark Horizontal Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_horizontal_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = '<input type="radio" id="ilove_pdf_format_watermark_horizontal" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_horizontal]" value="0"' . ( isset( $options['ilove_pdf_format_watermark_horizontal'] ) ? checked( 0, $options['ilove_pdf_format_watermark_horizontal'], false ) : '' ) . '">';
    $html   .= '<label for="ilove_pdf_format_watermark_horizontal"> ' . $args[0] . '</label><br /><br />';

    $html .= '<input type="radio" id="ilove_pdf_format_watermark_horizontal" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_horizontal]" value="1"' . ( isset( $options['ilove_pdf_format_watermark_horizontal'] ) ? checked( 1, $options['ilove_pdf_format_watermark_horizontal'], false ) : '' ) . '">';
    $html .= '<label for="ilove_pdf_format_watermark_horizontal"> ' . $args[1] . '</label><br /><br />';

    $html .= '<input type="radio" id="ilove_pdf_format_watermark_horizontal" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_horizontal]" value="2"' . ( isset( $options['ilove_pdf_format_watermark_horizontal'] ) ? checked( 2, $options['ilove_pdf_format_watermark_horizontal'], false ) : '' ) . '">';
    $html .= '<label for="ilove_pdf_format_watermark_horizontal"> ' . $args[2] . '</label><br /><br />';

    echo $html;
}

/**
 * Watermark Opacity Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_opacity_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = '<input type="number" id="ilove_pdf_format_watermark_opacity" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_opacity]" min="0" max="100" value="' . ( isset( $options['ilove_pdf_format_watermark_opacity'] ) ? $options['ilove_pdf_format_watermark_opacity'] : '' ) . '">';
    $html   .= '<label for="ilove_pdf_format_watermark_opacity"> ' . $args[0] . '</label><br /><br />';

    echo $html;
}

/**
 * Watermark Rotation Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_rotation_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = '<input type="number" id="ilove_pdf_format_watermark_rotation" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_rotation]" min="0" max="360" value="' . ( isset( $options['ilove_pdf_format_watermark_rotation'] ) ? $options['ilove_pdf_format_watermark_rotation'] : '' ) . '">';
    $html   .= '<label for="ilove_pdf_format_watermark_rotation"> ' . $args[0] . '</label><br /><br />';

    echo $html;
}

/**
 * Watermark Layer Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_layer_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = '<input type="radio" id="ilove_pdf_format_watermark_layer" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_layer]" value="0"' . ( isset( $options['ilove_pdf_format_watermark_layer'] ) ? checked( 0, $options['ilove_pdf_format_watermark_layer'], false ) : '' ) . '">';
    $html   .= '<label for="ilove_pdf_format_watermark_layer"> ' . $args[0] . '</label>&nbsp;';

    $html .= '<input type="radio" id="ilove_pdf_format_watermark_layer" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_layer]" value="1"' . ( isset( $options['ilove_pdf_format_watermark_layer'] ) ? checked( 1, $options['ilove_pdf_format_watermark_layer'], false ) : '' ) . '">';
    $html .= '<label for="ilove_pdf_format_watermark_layer"> ' . $args[1] . '</label>';

    echo $html;
}

/**
 * Watermark Mosaic Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_mosaic_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = '<input type="checkbox" id="ilove_pdf_format_watermark_mosaic" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_mosaic]" value="1"' . ( isset( $options['ilove_pdf_format_watermark_mosaic'] ) ? checked( 1, $options['ilove_pdf_format_watermark_mosaic'], false ) : '' ) . '">';
    $html   .= '<label for="ilove_pdf_format_watermark_mosaic"> ' . $args[0] . '</label>';

    echo $html;
}

/**
 * Watermark Mode Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_mode_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html    = '<input type="radio" id="ilove_pdf_format_watermark_mode" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_mode]" value="0"' . ( isset( $options['ilove_pdf_format_watermark_mode'] ) ? checked( 0, $options['ilove_pdf_format_watermark_mode'], false ) : 'checked="checked"' ) . '">';
    $html   .= '<label for="ilove_pdf_format_watermark_mode"> ' . $args[0] . '</label>&nbsp;';

    $html .= '<input type="radio" id="ilove_pdf_format_watermark_mode" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_mode]" value="1"' . ( isset( $options['ilove_pdf_format_watermark_mode'] ) ? checked( 1, $options['ilove_pdf_format_watermark_mode'], false ) : '' ) . '">';
    $html .= '<label for="ilove_pdf_format_watermark_mode"> ' . $args[1] . '</label>';

    echo $html;
}

/**
 * Watermark Image Callback.
 *
 * @since    1.0.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_format_watermark_image_callback( $args ) {
    $options = get_option( 'ilove_pdf_display_settings_format_watermark' );

	$image = isset( $options['ilove_pdf_format_watermark_image'] ) ? '<img id="image-preview" src="' . wp_get_attachment_url( $options['ilove_pdf_format_watermark_image'] ) . '" height="100">' : '<img id="image-preview" height="100" style="max-width: 100px">';
	$html  = '<div class="image-preview-wrapper">
                ' . $image . '
            </div>
            <input id="upload_image_button" type="button" class="button" value="' . __( 'Upload image', 'ilovepdf' ) . '" />
            <input type="hidden" name="ilove_pdf_display_settings_format_watermark[ilove_pdf_format_watermark_image]" id="ilove_pdf_format_watermark_image" value="' . ( isset( $options['ilove_pdf_format_watermark_image'] ) ? $options['ilove_pdf_format_watermark_image'] : '' ) . '">
            <input type="submit" name="submit_image_selector" value="' . __( 'Save', 'ilovepdf' ) . '" class="button-primary">';

    echo $html;
}

/**
 * Watermark Selector Print Callback.
 *
 * @since    1.0.0
 */
function ilove_pdf_media_selector_print_scripts() {
    $options                     = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $my_saved_attachment_post_id = isset( $options['ilove_pdf_format_watermark_image'] ) && '' !== $options['ilove_pdf_format_watermark_image'] ? $options['ilove_pdf_format_watermark_image'] : 0;
    ?><script type='text/javascript'>
        jQuery( document ).ready( function( $ ) {
            // Uploading files
            var file_frame;
            var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
            var set_to_post_id = <?php echo esc_attr( $my_saved_attachment_post_id ); ?>; // Set this
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
                    title: '<?php echo esc_html( __( 'Select a image to upload', 'ilovepdf' ) ); ?>',
                    button: {
                        text: '<?php echo esc_html( __( 'Use this image', 'ilovepdf' ) ); ?>',
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