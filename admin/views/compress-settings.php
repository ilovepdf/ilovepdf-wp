<?php
/**
 * View: Compress settings page
 *
 * @package Ilove_Pdf
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
    <div class="panel">
        <form method="post" name="ilove_pdf_form_compress" action="options.php">
            <?php settings_fields( 'ilove_pdf_display_settings_compress' ); ?>
            <?php do_settings_sections( 'ilove_pdf_display_settings_compress' ); ?>

            <div class="ilove_pdf_wrapper_buttons">
                <?php submit_button(); ?>
                <a href="<?php echo esc_url( admin_url( 'upload.php?page=ilove-pdf-content-statistics&tab=compress_statistic' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Go to Compress Tool', 'ilove-pdf' ); ?></a>
            </div>
        </form>
    </div>
</div>