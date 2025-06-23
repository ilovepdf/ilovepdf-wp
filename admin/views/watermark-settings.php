<?php
/**
 * View: Watermark settings page
 *
 * @package Ilove_Pdf
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
    <div class="panel">
        <form method="post" name="ilove_pdf_form_watermark" action="options.php">
            <?php settings_fields( 'ilove_pdf_display_settings_watermark' ); ?>
            <?php do_settings_sections( 'ilove_pdf_display_settings_watermark' ); ?>

            <div class="ilove_pdf_wrapper_buttons">
                <?php submit_button(); ?>
                <a href="<?php echo esc_url( admin_url( 'upload.php?page=ilove-pdf-content-statistics&tab=watermark_statistic' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Go to Watermark Tool', 'ilove-pdf' ); ?></a>
            </div>
        </form>
    </div>

    <?php if ( isset( $options['ilove_pdf_watermark_active'] ) ) : ?>
    <div class="panel">
        <form method="post" name="ilove_pdf_form_watermark_format" action="options.php">
            <div class="">
                <?php settings_fields( 'ilove_pdf_display_settings_format_watermark' ); ?>
                <?php do_settings_sections( 'ilove_pdf_display_settings_format_watermark' ); ?>
                <table class="form-table">
                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_vertical' ); ?></tr>
                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_horizontal' ); ?></tr>
                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_mode' ); ?></tr>
                </table>
                <?php
                    $options     = get_option( 'ilove_pdf_display_settings_format_watermark' );
                    $div_display = ( isset( $options['ilove_pdf_format_watermark_mode'] ) ? $options['ilove_pdf_format_watermark_mode'] : 0 );
                ?>
                <div class="watermark-mode" id="div-mode0" style="<?php echo ( 0 === (int) $div_display ? '' : 'display: none' ); ?>">
                    <table class="form-table">
                        <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_text' ); ?></tr>
                        <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_size' ); ?></tr>
                        <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_font_family' ); ?></tr>
                        <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_text_color' ); ?></tr>
                    </table>
                </div>
                <div class="watermark-mode" id="div-mode1" style="<?php echo ( 1 === (int) $div_display ? '' : 'display: none' ); ?>">
                    <table class="form-table">
                        <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_image' ); ?></tr>
                        <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_opacity' ); ?></tr>
                        <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_rotation' ); ?></tr>
                        <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_layer' ); ?></tr>
                        <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_mosaic' ); ?></tr>
                    </table>
                </div>
                <?php submit_button(); ?>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>