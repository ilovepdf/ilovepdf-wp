<?php
/**
 * Component: Banner info.
 *
 * @package Ilove_Pdf_WP\views\account\components
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<article class="ilovepdf-settings__main__account-info">
    <h3><?php esc_html_e( 'Easily optimize your PDFs in WordPress!', 'ilove-pdf' ); ?></h3>
    <p>
        <?php
        esc_html_e(
            'Make your site lighter and safer by compressing and watermarking PDFs. 
    iLovePDF reduces file size and protects your documents, helping improve performance, security, and loading speed.',
            'ilove-pdf'
        );
        ?>
    </p>
    <p><?php esc_html_e( 'Sign up and get 2,500 free credits to start optimizing with iLovePDF.', 'ilove-pdf' ); ?></p>
</article>
