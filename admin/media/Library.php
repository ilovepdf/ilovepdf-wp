<?php

namespace Ilove_Pdf_WP\Media;

use Ilove_Pdf_WP\Media\Views\Status_Renderer;
use Ilove_Pdf_WP\Tools\Compress\Views\Actions as Compress_Actions;
use Ilove_Pdf_WP\Tools\Watermark\Views\Actions as Watermark_Actions;

/**
 * Handles the iLovePDF actions in the media library.
 *
 * This class integrates the tools actions into the media library,
 * allowing users to manage PDF files directly from the media list.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP\Media
 */
class Library {
    use Compress_Actions;
    use Watermark_Actions;

    /**
     * The name of the column for iLovePDF actions.
     *
     * @since 3.0.0
     * @var string
     */
    private $column_name = 'ilovepdf-status';

    /**
     * Constructor to initialize the iLovePDF actions in the media library.
     *
     * @since 3.0.0
     */
    public function __construct() {
        add_filter( 'manage_media_columns', array( $this, 'add_ilovepdf_column' ) );
        add_action( 'manage_media_custom_column', array( $this, 'add_tools_action' ), 10, 2 );
    }

    /**
     * Compress Add Media Column.
     *
     * @since 1.0.0
     * @param array $posts_columns Columns.
     */
    public function add_ilovepdf_column( $posts_columns ) {
        $posts_columns[ $this->column_name ] = 'iLovePDF';
        return $posts_columns;
    }

    /**
     * Compress and Watermark Display Button on Library Page.
     *
     * @since 1.0.0
     * @param string $column_name Column Name.
     * @param int    $post_id     File ID.
     */
    public function add_tools_action( $column_name, $post_id ) {
        if ( $this->column_name === $column_name ) {
            $filetype = wp_check_filetype( basename( get_attached_file( $post_id ) ) );

            if ( ! in_array( $filetype['ext'], array( 'pdf' ), true ) ) {
                return;
            }

            printf(
                '<div class="ilovepdf-media-library-actions-container ilovepdf-media-library-actions ilovepdf-base__layout-flex ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small">
                    %1$s
                    %2$s
                    %3$s
                </div>%4$s',
                $this->render_compress_action( $post_id ),
                $this->render_watermark_action( $post_id ),
                $this->render_restore_action( $post_id ),
                Status_Renderer::create( $post_id ),
            );
        }
    }
}
