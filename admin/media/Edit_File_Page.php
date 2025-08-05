<?php

namespace Ilove_Pdf_WP\Media;

use Ilove_Pdf_WP\Media\Views\Status_Renderer;
use Ilove_Pdf_WP\Tools\Compress\Views\Actions as Compress_Actions;
use Ilove_Pdf_WP\Tools\Watermark\Views\Actions as Watermark_Actions;

/**
 * Handles the iLovePDF actions on the Edit File page in the WordPress admin Media area.
 *
 * This class integrates the tools actions into the Edit File page,
 * allowing users to manage PDF files directly from the edit screen.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP\Media
 */
class Edit_File_Page {
    use Compress_Actions;
    use Watermark_Actions;

    /**
     * Constructor to initialize the iLovePDF actions on the Edit File page.
     *
     * @since 3.0.0
     */
    public function __construct() {
        add_filter( 'attachment_fields_to_edit', array( $this, 'render_ilovepdf_meta_box_edit' ), 10, 2 );
    }

    /**
     * Render the iLovePDF tools meta box on the Edit File page.
     *
     * This method adds the iLovePDF tools actions to the attachment edit screen,
     * allowing users to compress, watermark, and restore PDF files.
     *
     * @since 3.0.0
     * @param array  $form_fields The existing form fields for the attachment.
     * @param object $post The current post object.
     * @return array Modified form fields with iLovePDF tools actions.
     */
    public function render_ilovepdf_meta_box_edit( $form_fields, $post ) {
        if ( 'application/pdf' !== $post->post_mime_type ) {
            return $form_fields;
        }

        $html = sprintf(
            '<input id="%5$s" type="text" name="%5$s" hidden>
                <div class="ilovepdf-media-library-actions-container ilovepdf-media-library-edit-file ilovepdf-media-library-actions ilovepdf-base__layout-flex ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small">
                    %1$s
                    %2$s
                    %3$s
                </div>%4$s',
            $this->render_compress_action( $post->ID ),
            $this->render_watermark_action( $post->ID ),
            $this->render_restore_action( $post->ID ),
            Status_Renderer::create( $post->ID ),
            'attachments-' . $post->ID . '-ilovepdf_tools',
        );

		$form_fields['ilovepdf_tools'] = array(
			'label' => '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="75.51" viewBox="0 0 300 75.51"><path fill="#E5322D" d="M94.313 2.543c-4.785 2.309-8.374 6.2-10.995 10.612C79.104 6.071 72.405.326 62.259.326c-10.15 0-22.594 8.614-22.594 23.165 0 14.732 12.293 21.715 18.382 25.658 6.508 4.211 17.613 11.867 25.27 26.036 7.66-14.168 18.763-21.825 25.273-26.036 4.574-2.965 12.655-7.647 16.387-16.047L94.313 2.543zm-.367 31.395V6.254l27.684 27.683H93.946z"/><path d="M.458 59.164H3.89c1.088 0 2.344-1.507 2.344-2.511V20.24c0-1.004-1.256-2.427-2.344-2.427H.458v-8.79h27.54v8.79h-3.516c-1.088 0-2.26 1.423-2.26 2.427v36.413c0 1.005 1.172 2.511 2.26 2.511h3.516v8.455H.458v-8.455zM133.383 59.164h2.846c1.172 0 2.427-1.507 2.427-2.511V20.24c0-1.004-1.256-2.427-2.427-2.427h-2.846v-8.79h26.619c15.654 0 24.192 5.525 24.192 18.583 0 12.724-9.041 18.499-24.778 18.499h-4.855v13.059h6.78v8.455h-27.958v-8.455zm25.783-21.68c7.031 0 8.873-4.018 8.873-9.626 0-5.525-1.842-9.459-8.873-9.459h-4.855v19.086h4.855zM189.3 59.164h2.846c1.172 0 2.427-1.507 2.427-2.511V20.24c0-1.004-1.255-2.427-2.427-2.427H189.3v-8.79h25.531c20.843 0 31.725 9.041 31.725 28.963 0 19.588-11.049 29.633-32.144 29.633H189.3v-8.455zm25.112-.418c10.547 0 15.737-6.278 15.737-20.341 0-13.979-5.106-20.007-15.737-20.007h-3.934v40.347h3.934zM251.912 59.164h2.846c1.172 0 2.427-1.507 2.427-2.511V20.24c0-1.004-1.255-2.427-2.427-2.427h-2.846v-8.79h47.63v18.081h-9.375l-1.423-8.622H273.09v16.407h7.617l.67-5.525H290v20.341h-8.622l-.67-5.776h-7.617v15.235h6.781v8.455h-27.959v-8.455z"/><path fill="#FFF" d="M93.946 33.938V6.254l27.684 27.684z"/></svg>',
			'input' => 'html',
			'html'  => $html,
		);

        return $form_fields;
    }
}
