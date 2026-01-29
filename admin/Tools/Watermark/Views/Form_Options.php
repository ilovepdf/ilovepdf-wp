<?php

namespace Ilove_Pdf_WP\Tools\Watermark\Views;

use Ilove_Pdf_WP\Tools\Base\Form;
use Ilove_Pdf_WP\Tools\Watermark\Settings;

/**
 * Handles rendering of the watermark options form.
 *
 * Contains methods for creating form fields, buttons and sections.
 *
 * @package Ilove_Pdf_WP\Tools\Watermark\Views
 * @since 3.0.0
 */
class Form_Options extends Form {
    /**
     * Renders the watermark options form.
     *
     * This method generates the HTML for the watermark options form.
     *
     * @since 3.0.0
     *
     * phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
     */
    public static function render() {
        printf(
            '<form method="post" action="%1$s" class="ipdf-form form-%2$s">
                <input type="hidden" name="action" value="%2$s" />
                %3$s
                %4$s
                <h2>%5$s</h2>
                <hr class="ipdf-divisor" />
                <div class="ilovepdf-settings__main__form_fields">
                    <div class="ilovepdf-settings__main__form_fields_inner_field">
                        %6$s
                    </div>
                </div>
                <hr class="ipdf-divisor" />
                <div class="ilovepdf-settings__main__form_fields">
                    <div class="ilovepdf-settings__main__form_fields_inner_field">
                        %7$s
                    </div>
                </div>
                <hr class="ipdf-divisor" />
                <div class="ilovepdf-settings__main__form_fields">
                    <div class="ilovepdf-settings__main__form_fields_inner_field">
                        %8$s
                    </div>
                </div>
                %4$s
            </form>',
            esc_html( admin_url( 'admin-post.php' ) ),
            Settings::get_action_key(),
            wp_nonce_field( -1, '_wpnonce', true, false ),
            self::create_submit_button(),
            esc_html_x( 'Watermark Settings', 'form title', 'ilove-pdf' ),
            self::create_field_watermark_active(),
            self::create_field_auto_watermark(),
            self::create_section_preview_watermark(),
        );
    }

    /**
     * Creates the HTML for the preview section of the watermark options.
     * This section includes the watermark mode selection, file upload, and format options.
     *
     * @since 3.0.0
     * @return string HTML markup for the preview section.
     */
    private static function create_section_preview_watermark() {
        return sprintf(
            '<h4>%1$s</h4>
            <p>%2$s</p>
            %3$s
            <p class="ilovepdf_font_none_style">%4$s</p>
            <div class="ilovepdf_options-preview-wrapper">
                %5$s
                %6$s
            </div>
            ',
            esc_html_x( 'Watermark preview', 'subtitle for watermark options section', 'ilove-pdf' ),
            esc_html_x( 'Use text or upload an image as your watermark.', 'Help text for the watermark options section', 'ilove-pdf' ),
            self::create_field_mode_watermark(),
            esc_html_x( 'This font does not support bold or italic styles', 'Warning message: for family font selector.', 'ilove-pdf' ),
            self::create_subsection_file(),
            self::create_subsection_format_options(),
        );
    }

    /**
     * Creates the HTML for the subsection that handles the file sample prewiew.
     * This subsection includes the file preview and the watermark element upload field.
     *
     * @since 3.0.0
     * @return string HTML markup for the file subsection.
     */
    private static function create_subsection_file() {
        return sprintf(
            '<div class="ilovepdf_option-col-wrapper ilovepdf_option-file-wrapper">
                <div class="ilovepdf_option-file-preview"></div>
                <div id="ipdf-element-watermark">%1$s</div>
            </div>
            ',
            self::value_readeable_watermark(),
        );
    }

    /**
     * Creates the HTML for the subsection that handles the format options.
     * This subsection includes the text and image format options for the watermark.
     *
     * @since 3.0.0
     * @return string HTML markup for the format options subsection.
     */
    private static function create_subsection_format_options() {
        return sprintf(
            '<div class="ilovepdf_option-col-wrapper ilovepdf_option-settings-wrapper">
                %1$s
                %2$s
                %3$s
            </div>',
            self::create_subsection_format_texts(),
            self::create_field_mode_image(),
            self::create_subsection_common(),
        );
    }

    /**
     * Creates the HTML for the common settings subsection.
     * This subsection includes fields for position, mosaic, layer, transparency, and rotation.
     *
     * @since 3.0.0
     * @return string HTML markup for the common settings subsection.
     */
    private static function create_subsection_common() {
        return sprintf(
            '<div class="ilovepdf_option-settings-item ilovepdf_option-settings-common">
                <div class="ilovepdf_option-settings-common-col">
                    %1$s
                    <div class="ilovepdf_option-settings-common-row">
                        %2$s
                    </div>
                    <div class="ilovepdf_option-settings-common-row">
                        %3$s
                    </div>
                </div>
                <div class="ilovepdf_option-settings-common-col">
                    <div class="ilovepdf_option-settings-common-row ilovepdf_option-settings-transparency">
                        %4$s
                    </div>
                    <div class="ilovepdf_option-settings-common-row ilovepdf_option-settings-rotation">
                        %5$s
                    </div>
                </div>
            </div>',
            self::create_field_position(),
            self::create_field_mosaic(),
            self::create_field_layer(),
            self::create_field_transparency(),
            self::create_field_rotation(),
        );
    }

    /**
     * Creates the HTML for the text format subsection.
     * This subsection includes fields for font family, style, color, size, and mode text.
     *
     * @since 3.0.0
     * @return string HTML markup for the text format subsection.
     */
    private static function create_subsection_format_texts() {
        $value_mode_checked = self::value_mode_checked();

        return sprintf(
            '<div class="ilovepdf_option-settings-item ilovepdf_option-settings-text %6$s">
                <div class="ilovepdf_settings__options__texts-toolbar">
                    %1$s
                    %2$s
                    %3$s
                    <div class="ilovepdf_option-settings-common-row ilovepdf_option-settings-common-size-stamp">
                        %4$s
                    </div>
                </div>
                <div class="ilovepdf_settings__options__text">
                    %5$s
                </div>
            </div>',
            self::create_field_font_family(),
            self::create_field_font_style(),
            self::create_field_font_color(),
            self::create_field_font_size(),
            self::create_field_mode_text(),
            Settings::get_mode_values( 'text' ) === $value_mode_checked ? 'ipdf-option-selected' : '',
        );
    }

    /**
     * Creates the HTML for the tool active toggle field in the options form.
     *
     * This field allows the user to enable or disable the watermark tool.
     *
     * @since 3.0.0
     * @return string HTML markup for the option field.
     */
    private static function create_field_watermark_active() {

        return sprintf(
            '<div class="ipdf-input-group-switch">
                <input class="ipdf-input-slider" type="checkbox" id="%1$s" name="%1$s" %2$s />
                <span class="ipdf-input-group-switch-slider"></span>
            </div>
            <label for="%1$s">%3$s</label>
            <p>%4$s</p>',
            Settings::get_field_watermark_active(),
            checked( Settings::get_settings( Settings::get_field_watermark_active() ), 'on', false ),
            esc_html_x( 'Watermark enabled', 'checkbox field label', 'ilove-pdf' ),
            esc_html_x( 'Add a watermark to protect PDFs from unauthorized use.', 'help text for checkbox field', 'ilove-pdf' ),
        );
    }

    /**
     * Creates the HTML for the auto-watermark active toggle field in the options form.
     *
     * This field allows the user to enable or disable the auto-watermark feature.
     *
     * @since 3.0.0
     * @return string HTML markup for the option field.
     */
    private static function create_field_auto_watermark() {

        return sprintf(
            '<div class="ipdf-input-group-switch">
                <input class="ipdf-input-slider" type="checkbox" id="%1$s" name="%1$s" %2$s />
                <span class="ipdf-input-group-switch-slider"></span>
            </div>
            <label for="%1$s">%3$s</label>
            <p>%4$s</p>',
            Settings::get_field_auto_watermark(),
            checked( Settings::get_settings( Settings::get_field_auto_watermark() ), 'on', false ),
            esc_html_x( 'Automatically apply watermark to uploaded files', 'checkbox field label', 'ilove-pdf' ),
            esc_html__( 'Automatically stamp new Media uploads with your selected watermark. You can still apply it manually to other files.', 'ilove-pdf' ),
        );
    }

    /**
     * Creates the HTML for the watermark mode selection field.
     *
     * This field allows the user to choose between text or image watermark modes.
     *
     * @since 3.0.0
     * @return string HTML markup for the option field.
     */
    private static function create_field_mode_watermark() {
        $db_key_mode = Settings::get_field_mode();

        $mode_value_text  = Settings::get_mode_values( 'text' );
        $mode_value_image = Settings::get_mode_values( 'image' );

        $value_mode_checked = self::value_mode_checked();

        return sprintf(
            '<div id="ilovepdf_field_type" class="ilovepdf_field-mode">
                <label for="%1$s">
                    <input type="radio" name="%2$s" class="ilovepdf_field_type" id="%1$s" value="%3$s" %8$s />
                    <span>%4$s</span>
                </label>
                <label for="%5$s">
                    <input type="radio" name="%2$s" class="ilovepdf_field_type" id="%5$s" value="%6$s" %9$s />
                    <span>%7$s</span>
                </label>
            </div>
            ',
            $db_key_mode . $mode_value_text,
            $db_key_mode,
            $mode_value_text,
            esc_html_x( 'Use text', 'radio button label', 'ilove-pdf' ),
            $db_key_mode . $mode_value_image,
            $mode_value_image,
            esc_html_x( 'Use image', 'radio button label', 'ilove-pdf' ),
            checked( $value_mode_checked, $mode_value_text, false ),
            checked( $value_mode_checked, $mode_value_image, false ),
        );
    }

    /**
     * Creates the HTML for Field Position.
     * This field allows the user to select the position of the watermark on the PDF.
     *
     * @since 3.0.0
     * @return string HTML markup for the position field.
     */
    private static function create_field_position() {
        $db_key_position  = Settings::get_field_position();
        $position_checked = ! empty( Settings::get_settings( $db_key_position ) ) ? Settings::get_settings( $db_key_position ) : 'center middle';

        return sprintf(
            '<label for="%1$s">%2$s</label>
            <table class="ilovepdf_option-settings-position-container">
                <tbody>
                    <tr>
                        <td><input type="radio" name="%1$s" value="left top" %4$s/></td>
                        <td><input type="radio" name="%1$s" value="center top" %5$s/></td>
                        <td><input type="radio" name="%1$s" value="right top" %6$s/></td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="%1$s" value="left middle" %7$s/></td>
                        <td><input type="radio" name="%1$s" id="%1$s" value="center middle" %3$s /></td>
                        <td><input type="radio" name="%1$s" value="right middle" %8$s/></td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="%1$s" value="left bottom" %9$s/></td>
                        <td><input type="radio" name="%1$s" value="center bottom" %10$s/></td>
                        <td><input type="radio" name="%1$s" value="right bottom" %11$s/></td>
                    </tr>
                </tbody>
            </table>',
            $db_key_position,
            esc_html_x( 'Position', 'radio field label', 'ilove-pdf' ),
            checked( $position_checked, 'center middle', false ),
            checked( $position_checked, 'left top', false ),
            checked( $position_checked, 'center top', false ),
            checked( $position_checked, 'right top', false ),
            checked( $position_checked, 'left middle', false ),
            checked( $position_checked, 'right middle', false ),
            checked( $position_checked, 'left bottom', false ),
            checked( $position_checked, 'center bottom', false ),
            checked( $position_checked, 'right bottom', false ),
        );
    }

    /**
     * Creates the HTML for the mosaic field.
     * This field allows the user to enable or disable the mosaic effect for the watermark.
     *
     * @since 3.0.0
     * @return string HTML markup for the mosaic field.
     */
    private static function create_field_mosaic() {
        return sprintf(
            '<div class="ipdf-input-group-switch">
                <input class="ipdf-input-slider" type="checkbox" id="%1$s" name="%1$s" %2$s />
                <span class="ipdf-input-group-switch-slider"></span>
            </div>
            <label for="%1$s">%3$s<span>%4$s</span></label>
            ',
            Settings::get_field_mosaic(),
            Settings::get_settings( Settings::get_field_mosaic() ) ? 'checked' : '',
            esc_html_x( 'Mosaic', 'checkbox field label', 'ilove-pdf' ),
            esc_html__( 'If enabled, this overrides manual position settings to print image or text 9 times per page.', 'ilove-pdf' ),
        );
    }

    /**
     * Creates the HTML for the layer selection field.
     * This field allows the user to choose whether the watermark is above or below the PDF content.
     *
     * @since 3.0.0
     * @return string HTML markup for the layer field.
     */
    private static function create_field_layer() {
        $db_key_layer      = Settings::get_field_layer();
        $layer_above_value = Settings::get_layer_values( 'above' );
        $layer_below_value = Settings::get_layer_values( 'below' );

        $layer_value_checked = ! empty( Settings::get_settings( $db_key_layer ) ) ? Settings::get_settings( $db_key_layer ) : $layer_above_value;

        return sprintf(
            '<p>Layer</p>
            <div class="ilovepdf_field-layer">
                <label for="%1$s">
                    <input type="radio" name="%2$s" class="ilovepdf_field_layer" id="%1$s" value="%3$s" %8$s />
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="52" viewBox="0 0 48 52">
                        <path fill-rule="evenodd" d="M1110,733 L1133.999,751.667 L1129.626,755.054 L1124.875,758.752 L1133.999,765.848 L1129.626,769.235 L1110,784.515 L1090.347,769.235 L1086,765.848 L1095.114,758.76 L1090.347,755.054 L1086,751.667 L1110,733 Z M1122.434,760.653 L1110,770.333 L1097.557,760.66 L1090.883,765.85 L1109.998,780.714 L1129.106,765.844 L1122.434,760.653 Z" transform="translate(-1086 -733)"></path>
                    </svg>
                    <span>%4$s</span>
                </label>
                <label for="%5$s">
                    <input type="radio" name="%2$s" class="ilovepdf_field_layer" id="%5$s" value="%6$s" %9$s />
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="52" viewBox="0 0 48 52">
                        <path fill-rule="evenodd" d="M1327,721.0001 L1303,739.6671 L1307.347,743.0541 L1312.114,746.7601 L1303,753.8481 L1307.347,757.2351 L1327,772.5151 L1346.626,757.2351 L1350.999,753.8481 L1341.875,746.7521 L1346.626,743.0541 L1350.999,739.6671 L1327,721.0001 Z M1307.883,739.6691 L1327,724.8011 L1346.106,739.6611 L1326.998,754.5331 L1307.883,739.6691 Z" transform="translate(-1303 -721)"></path>
                    </svg>
                    <span>%7$s</span>
                </label>
            </div>',
            $db_key_layer . $layer_above_value,
            $db_key_layer,
            $layer_above_value,
            esc_html_x( 'Above PDF content', 'radio button label', 'ilove-pdf' ),
            $db_key_layer . $layer_below_value,
            $layer_below_value,
            esc_html_x( 'Below PDF content', 'radio button label', 'ilove-pdf' ),
            checked( $layer_value_checked, $layer_above_value, false ),
            checked( $layer_value_checked, $layer_below_value, false ),
        );
    }

    /**
     * Creates the HTML for the transparency field.
     * This field allows the user to set the opacity of the watermark.
     *
     * @since 3.0.0
     * @return string HTML markup for the transparency field.
     */
    private static function create_field_transparency() {
        $db_key_transparency = Settings::get_field_transparency();

        return sprintf(
            '<label for="%1$s">%2$s: <strong class="ipdf-value-selected" data-prefix="%%">%5$s%%</strong></label>
            <div class="ipdf-range-group">
                <input class="ipdf-input-range" type="range" name="%1$s" id="%1$s" min="%3$s" max="%4$s" value="%5$s" />
                <div class="ipdf-range-labels">
                    <span>%3$s%%</span>
                    <span>25%%</span>
                    <span>50%%</span>
                    <span>75%%</span>
                    <span>%4$s%%</span>
                </div>
            </div>',
            $db_key_transparency,
            esc_html_x( 'Opacity', 'input range field label', 'ilove-pdf' ),
            Settings::get_transparency_values( false, 'min' ),
            Settings::get_transparency_values( false, 'max' ),
            ! empty( Settings::get_settings( $db_key_transparency ) ) ? Settings::get_settings( $db_key_transparency ) : Settings::get_transparency_values( false, 'max' ),
        );
    }

    /**
     * Creates the HTML for the rotation field.
     * This field allows the user to set the rotation angle of the watermark.
     *
     * @since 3.0.0
     * @return string HTML markup for the rotation field.
     */
    private static function create_field_rotation() {
        $db_key_rotation = Settings::get_field_rotation();

        return sprintf(
            '<label for="%1$s">%2$s: <strong class="ipdf-value-selected" data-prefix="º">%5$sº</strong></label>
            <div class="ipdf-range-group">
                <input class="ipdf-input-range" type="range" name="%1$s" id="%1$s" min="%3$s" max="%4$s" value="%5$s" />
                <div class="ipdf-range-labels">
                    <span>%3$sº</span>
                    <span>90º</span>
                    <span>180º</span>
                    <span>270º</span>
                    <span>%4$sº</span>
                </div>
            </div>',
            $db_key_rotation,
            esc_html_x( 'Rotation', 'input range field label', 'ilove-pdf' ),
            Settings::get_rotation_values( false, 'min' ),
            Settings::get_rotation_values( false, 'max' ),
            ! empty( Settings::get_settings( $db_key_rotation ) ) ? Settings::get_settings( $db_key_rotation ) : Settings::get_rotation_values( false, 'min' ),
        );
    }

    /**
     * Creates the HTML for the text input field for watermark text.
     *
     * This field allows the user to enter the text that will be used as a watermark.
     *
     * @since 3.0.0
     * @return string HTML markup for the text input field.
     */
    private static function create_field_mode_text() {
        $db_key_mode_text = Settings::get_field_text_mode();

        return sprintf(
            '<input type="text" name="%1$s" id="%1$s" placeholder="%2$s" value="%3$s" />',
            $db_key_mode_text,
            esc_html_x( 'Enter text for watermark', 'input text: placeholder', 'ilove-pdf' ),
            ! empty( Settings::get_settings( $db_key_mode_text ) ) ? Settings::get_settings( $db_key_mode_text ) : get_bloginfo( 'name' ),
        );
    }

    /**
     * Creates the HTML for the font color field.
     *
     * This field allows the user to select a color for the watermark text.
     *
     * @since 3.0.0
     * @return string HTML markup for the font color field.
     */
    private static function create_field_font_color() {
        $db_value_font_color = Settings::get_settings( Settings::get_field_font_color() );

        return sprintf(
            '<input type="hidden" name="%1$s" id="%1$s" value="%2$s" />
            <a href="#" id="ipdf-color-picker" data-color="%2$s"></a>',
            Settings::get_field_font_color(),
            ! empty( $db_value_font_color ) ? $db_value_font_color : '#000000',
        );
    }

    /**
     * Creates the HTML for the font style field.
     *
     * This field allows the user to select bold and italic styles for the watermark text.
     *
     * @since 3.0.0
     * @return string HTML markup for the font style field.
     */
    private static function create_field_font_style() {
        $db_value_font_style     = Settings::get_settings( Settings::get_field_font_style(), null );
        $font_style_bold_value   = Settings::get_font_style_values( 'Bold' );
        $font_style_italic_value = Settings::get_font_style_values( 'Italic' );

        return sprintf(
            '<input type="radio" name="%1$s" id="%1$s_normal" class="ipdf-input--checkbox ipdf-input-font-style ipdf-input-font-normal" value="%2$s" %5$s />
            <input type="radio" name="%1$s" id="%1$s_bold" class="ipdf-input--checkbox ipdf-input-font-style ipdf-input-font-bold" value="%3$s" %6$s />
            <input type="radio" name="%1$s" id="%1$s_italic" class="ipdf-input--checkbox ipdf-input-font-style ipdf-input-font-italic" value="%4$s" %7$s />',
            Settings::get_field_font_style(),
            null,
            $font_style_bold_value,
            $font_style_italic_value,
            checked( null, $db_value_font_style, false ),
            checked( $font_style_bold_value, $db_value_font_style, false ),
            checked( $font_style_italic_value, $db_value_font_style, false ),
        );
    }

    /**
     * Creates the HTML for the font size input field.
     *
     * This field allows the user to set the size of the watermark text.
     *
     * @since 3.0.0
     * @return string HTML markup for the font size input field.
     */
    private static function create_field_font_size() {
        $db_key_font_size = Settings::get_field_font_size();

        return sprintf(
            '<div class="ipdf-number-group">
                <input type="number" name="%1$s" id="%1$s" min="%2$s" max="%3$s" value="%4$s" />
                <span class="prefix">px</span>
            </div>',
            $db_key_font_size,
            Settings::get_font_size_values( false, 'min' ),
            Settings::get_font_size_values( false, 'max' ),
            Settings::get_settings( $db_key_font_size ),
        );
    }

    /**
     * Creates the HTML for the font family selection field.
     *
     * This field allows the user to select a font family for the watermark text.
     *
     * @since 3.0.0
     * @return string HTML markup for the font family selection field.
     */
    private static function create_field_font_family() {
        $font_family_options  = '';
        $db_value_font_family = Settings::get_settings( Settings::get_field_font_family() );
        $font_family_selected = ! empty( $db_value_font_family ) ? $db_value_font_family : Settings::get_font_family_values( 'Arial Unicode MS' );

        foreach ( Settings::get_font_family_values() as $font ) {
            $font_family_options .= sprintf(
                '<option value="%1$s" %2$s>%1$s</option>',
                $font,
                selected( $font_family_selected, $font, false )
            );
        }

        return sprintf(
            '<select class="ipdf-select" name="%1$s" id="%1$s">
                %2$s
            </select>',
            Settings::get_field_font_family(),
            $font_family_options,
        );
    }

    /**
     * Creates the HTML for the image mode field.
     *
     * This field allows the user to upload an image to be used as a watermark.
     *
     * @since 3.0.0
     * @return string HTML markup for the image mode field.
     */
    private static function create_field_mode_image() {
        $db_key_mode_image = Settings::get_field_image_mode();

        $value_mode_checked = self::value_mode_checked();

        return sprintf(
            '<div class="ilovepdf_option-settings-item ilovepdf_option-settings-image %8$s">
                <h5>%1$s</h5>
                <p>%2$s</p>
                <button class="ipdf-btn--upload-file">
                    <div>
                        <svg width="24px" height="20px" viewBox="0 0 24 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g transform="translate(-736.000000, -1133.000000)" fill="#FFFFFF">
                                    <g transform="translate(736.000000, 1133.000000)">
                                        <path d="M21.8965758,16.9653135 L21.8965758,2.28467061 L2.07407407,2.28467061 L2.07407407,16.9653135 L21.8965758,16.9653135 Z M23.9706499,17.3072845 C23.9896561,17.360927 24,17.418667 24,17.4788229 L24,18.5258782 C24,18.8094816 23.770094,19.0393876 23.4864906,19.0393876 L0.513509434,19.0393876 C0.229906005,19.0393876 2.35828404e-16,18.8094816 2.01097001e-16,18.5258782 L0,17.4788229 C-3.75676683e-18,17.4481466 0.00268988762,17.4180985 0.00784666393,17.3889017 C0.00268988762,17.3609836 0,17.3322516 0,17.3029188 L0,0.724105965 C-3.47314032e-17,0.440502536 0.229906005,0.210596531 0.513509434,0.210596531 L23.4864906,0.210596531 C23.770094,0.210596531 24,0.440502536 24,0.724105965 L24,1.77116117 C24,1.83131713 23.9896561,1.88905714 23.9706499,1.94269958 L23.9706499,17.3072845 Z" id="Combined-Shape"></path>
                                        <path d="M20.608707,15.3962264 L3.78320483,15.3962264 C3.74083938,15.3962264 3.7064954,15.3618824 3.7064954,15.319517 C3.7064954,15.3019755 3.71250743,15.2849639 3.7235294,15.2713176 L8.31768306,9.58331783 C8.34430285,9.55036 8.39260005,9.545222 8.42555788,9.57184178 C8.42977827,9.57525056 8.43362514,9.57909743 8.43703392,9.58331783 L10.3380665,11.9369772 L14.1770363,6.90660307 C14.2137534,6.85849098 14.2825211,6.84925353 14.3306332,6.88597065 C14.3383996,6.89189767 14.3453386,6.89883664 14.3512656,6.90660307 L20.6958216,15.2201593 C20.7325388,15.2682714 20.7233013,15.337039 20.6751892,15.3737562 C20.6560907,15.3883313 20.6327317,15.3962264 20.608707,15.3962264 Z" id="Combined-Shape"></path>
                                        <circle cx="6.56603774" cy="6.11320755" r="1.58490566"></circle>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </div>
                    %3$s
                </button>
                <span>%4$s</span>
                <input type="url" name="%5$s" id="%5$s_url" value="%6$s" placeholder="%7$s" />
            </div>',
            esc_html_x( 'Watermark image', 'subtitle for watermark image mode', 'ilove-pdf' ),
            esc_html_x( 'Select an image stamp from Media or a URL. Then adjust its position, size, opacity, and rotation', 'Help text for image stamp', 'ilove-pdf' ),
            esc_html_x( 'Add Image', 'button', 'ilove-pdf' ),
            esc_html_x( 'or enter an URL', 'before text: add image button, after text: input image URL', 'ilove-pdf' ),
            $db_key_mode_image,
            Settings::get_settings( $db_key_mode_image ),
            esc_html_x( 'Enter image URL', 'input text: placeholder', 'ilove-pdf' ),
            Settings::get_mode_values( 'image' ) === $value_mode_checked ? 'ipdf-option-selected' : '',
        );
    }

    /**
     * Returns the currently selected watermark mode.
     *
     * This method checks the database for the watermark mode setting and returns it.
     * If no mode is set, it defaults to 'text'.
     *
     * @since 3.0.0
     * @return string The selected watermark mode ('text' or 'image').
     */
    private static function value_mode_checked() {
        $db_value_mode   = Settings::get_settings( Settings::get_field_mode() );
        $mode_value_text = Settings::get_mode_values( 'text' );

        return $db_value_mode ? $db_value_mode : $mode_value_text;
    }

    /**
     * Generates a readable HTML representation of the watermark settings.
     *
     * This method constructs the HTML for the watermark based on the current settings,
     * including text or image mode, position, rotation, and transparency.
     *
     * @since 3.0.0
     * @return string The HTML representation of the watermark.
     */
    private static function value_readeable_watermark() {
        $value_mode_checked    = self::value_mode_checked();
        $db_value_image        = Settings::get_settings( Settings::get_field_image_mode() );
        $db_value_rotation     = Settings::get_settings( Settings::get_field_rotation() );
        $db_value_transparency = Settings::get_settings( Settings::get_field_transparency() );
        $value_watermark       = '';
        $all_position          = self::value_readeable_position();
        $position              = implode( ';', $all_position['position'] );

        $style_common = "transform: {$all_position['transform']} rotate(calc({$db_value_rotation} * -1deg)); opacity: {$db_value_transparency}%; {$position}";

        $db_value_text        = Settings::get_settings( Settings::get_field_text_mode() );
        $db_value_font_family = Settings::get_settings( Settings::get_field_font_family() );
        $db_value_font_color  = Settings::get_settings( Settings::get_field_font_color() );
        $db_value_font_size   = Settings::get_settings( Settings::get_field_font_size() );
        $db_value_font_style  = Settings::get_settings( Settings::get_field_font_style(), null );
        $style_bold           = Settings::get_font_style_values( 'Bold' ) === $db_value_font_style ? $db_value_font_style : 'normal';
        $style_italic         = Settings::get_font_style_values( 'Italic' ) === $db_value_font_style ? $db_value_font_style : 'normal';

        $style = "font-family: '$db_value_font_family'; color: $db_value_font_color; font-size: {$db_value_font_size}px; font-style: $style_italic; font-weight: $style_bold; min-width: max-content;";

        switch ( $value_mode_checked ) {
            case 'text':
                $value_watermark = sprintf(
                    '<p style="%2$s %3$s">%1$s</p><img src="%4$s" style="%3$s" hidden="true"/>',
                    ! empty( $db_value_text ) ? $db_value_text : get_bloginfo( 'name' ),
                    $style,
                    $style_common,
                    $db_value_image,
                );
                break;
            case 'image':
                $value_watermark = sprintf(
                    '<img src="%1$s" style="%2$s" /><p style="%3$s" hidden="true">%4$s</p>',
                    $db_value_image,
                    $style_common,
                    $style_common . '; ' . $style,
                    $db_value_text,
                );
                break;
        }

        return $value_watermark;
    }

    /**
     * Generates a readable position style for the watermark.
     *
     * This method constructs the CSS styles based on the current position setting
     * and returns them in an array format suitable for inline styles.
     *
     * @since 3.0.0
     * @return array An associative array of CSS styles for the watermark position.
     */
    private static function value_readeable_position() {
        $db_value_position = Settings::get_settings( Settings::get_field_position() );
        $inline_styles     = array();

        $styles = array(
            'left'      => null,
            'right'     => null,
            'top'       => null,
            'bottom'    => null,
            'transform' => null,
        );

        switch ( $db_value_position ) {
            case Settings::get_position_values( 'left top' ):
                $styles['left']      = '0';
                $styles['top']       = '0';
                $styles['transform'] = 'translate(0)';
                break;

            case Settings::get_position_values( 'center top' ):
                $styles['left']      = '50%';
                $styles['top']       = '0';
                $styles['transform'] = 'translate(-50%, 0)';
                break;

            case Settings::get_position_values( 'right top' ):
                $styles['right']     = '0';
                $styles['top']       = '0';
                $styles['transform'] = 'translate(0)';
                break;

            case Settings::get_position_values( 'left middle' ):
                $styles['left']      = '0';
                $styles['top']       = '50%';
                $styles['transform'] = 'translate(0, -50%)';
                break;

            case Settings::get_position_values( 'center middle' ):
                $styles['left']      = '50%';
                $styles['top']       = '50%';
                $styles['transform'] = 'translate(-50%, -50%)';
                break;

            case Settings::get_position_values( 'right middle' ):
                $styles['right']     = '0';
                $styles['top']       = '50%';
                $styles['transform'] = 'translate(0, -50%)';
                break;

            case Settings::get_position_values( 'left bottom' ):
                $styles['left']      = '0';
                $styles['bottom']    = '0';
                $styles['transform'] = 'translate(0)';
                break;

            case Settings::get_position_values( 'center bottom' ):
                $styles['left']      = '50%';
                $styles['bottom']    = '0';
                $styles['transform'] = 'translate(-50%, 0)';
                break;

            case Settings::get_position_values( 'right bottom' ):
                $styles['right']     = '0';
                $styles['bottom']    = '0';
                $styles['transform'] = 'translate(0)';
                break;

            default:
                $styles['left']      = '0';
                $styles['top']       = '0';
                $styles['transform'] = 'translate(0)';
                break;
        }

        foreach ( $styles as $key => $value ) {
            if ( null !== $value ) {
                if ( 'transform' !== $key ) {
                    $inline_styles['position'][ $key ] = "$key: $value";
                } else {
                    $inline_styles['transform'] = $value;
                }
            }
        }

        return $inline_styles;
    }
}
