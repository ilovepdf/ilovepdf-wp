<?php

namespace Ilove_Pdf_WP\Media;

use WP_List_Table;
use Ilove_Pdf_WP\Tools\Compress\Tool_Compress;
use Ilove_Pdf_WP\Tools\Compress\Views\Actions as Compress_Actions;
use Ilove_Pdf_WP\Tools\Watermark\Views\Actions as Watermark_Actions;

/**
 * Handles the display of PDF files in a list table format in the WordPress admin Media area.
 *
 * @since 3.0.0
 * @package Ilove_Pdf_WP\Media
 */
class Files_List_Table extends WP_List_Table {
    use Compress_Actions;
    use Watermark_Actions;

    /**
     * Prepare the items for the table to process.
     *
     * This method is called by the WP_List_Table class to prepare the data for display.
     * It retrieves the PDF files from the database, applies sorting and pagination, and sets the items to be displayed in the table.
     *
     * @since 3.0.0
     * @see WP_List_Table::prepare_items()
     * @return void
     */
    public function prepare_items() {

        global $wpdb;

        $columns               = $this->get_columns();
        $hidden                = array();
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $this->get_bulk_actions();

        $order = 'ORDER BY post_date DESC';

        if ( isset( $_GET['orderby'] ) && isset( $_GET['order'] ) ) {
            $order = 'ORDER BY ' . sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_GET['order'] ) );

            if ( 'file' === $_GET['orderby'] ) {
                $order = 'ORDER BY post_title ' . sanitize_text_field( wp_unslash( $_GET['order'] ) );
            }
        }

        $db_results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title AS file, post_author, post_date, post_status AS status
                FROM {$wpdb->posts}
                WHERE post_type = %s AND post_mime_type LIKE %s
                $order",
                'attachment',
                'application/pdf',
            ),
            ARRAY_A
        );

        if ( ! $db_results ) {
            $db_results = array();
        }

        $per_page     = 20;
        $current_page = $this->get_pagenum();
        $total_items  = count( $db_results );

        $db_results  = array_slice( $db_results, ( $current_page - 1 ) * $per_page, $per_page );
        $this->items = $db_results;

        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => (int) ceil( $total_items / $per_page ),
            )
        );
    }

    /**
     * Get the columns for the table.
     *
     * @return array The array of columns.
     * @since 3.0.0
     * @see WP_List_Table::get_columns()
     */
    public function get_columns() {
        return array(
            'cb'              => '<input type="checkbox" />',
            'file'            => _x( 'File', 'File List Table: column name', 'ilove-pdf' ),
            'size'            => _x( 'Original Size', 'File List Table: column name', 'ilove-pdf' ),
            'size_compressed' => _x( 'Compressed Size', 'File List Table: column name', 'ilove-pdf' ),
            'post_author'     => _x( 'Author', 'File List Table: column name', 'ilove-pdf' ),
            'post_date'       => _x( 'Upload Date', 'File List Table: column name', 'ilove-pdf' ),
            'status'          => _x( 'Status', 'File List Table: column name', 'ilove-pdf' ),
            'tools_action'    => _x( 'Actions', 'File List Table: column name', 'ilove-pdf' ),
        );
    }

    /**
     * Render the checkbox column.
     *
     * @param array $item The item data.
     * @return string HTML for the checkbox.
     * @since 3.0.0
     */
    protected function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                // The value of the checkbox should be the record's id
        );
    }

    /**
     * Get the sortable columns for the table.
     *
     * @return array The array of sortable columns.
     * @since 3.0.0
     * @see WP_List_Table::get_sortable_columns()
     */
    protected function get_sortable_columns() {
        return array(
            'file'        => array( 'file', false ),     // true means it's already sorted
            'post_author' => array( 'post_author', false ),
            'post_date'   => array( 'post_date', false ),
        );
    }

    /**
     * Render the default column content.
     *
     * @param array  $item The item data.
     * @param string $column_name The name of the column.
     * @return string HTML for the column content.
     * @since 3.0.0
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'file':
                return sprintf(
                    '<a href="%1$s">%2$s</a>',
                    esc_url( get_edit_post_link( $item['ID'] ) ),
                    esc_html( $item['file'] ),
                );

            case 'size':
                $file_size = size_format( filesize( get_attached_file( $item['ID'] ) ), 2 );

                if ( Tool_Compress::is_file_compressed( $item['ID'] ) ) {
                    $metadata = get_post_meta( $item['ID'], Tool_Compress::get_db_key_process() )[0];

                    $file_size = size_format( $metadata['original_size'], 2 );
                    return esc_html( $file_size );
                }

                return esc_html( $file_size );

            case 'size_compressed':
                $file_size = size_format( 0, 2 );

                if ( Tool_Compress::is_file_compressed( $item['ID'] ) ) {
                    $metadata = get_post_meta( $item['ID'], Tool_Compress::get_db_key_process() )[0];

                    $file_size = size_format( $metadata['compressed_size'], 2 );
                    return esc_html( $file_size );
                }

                return esc_html( $file_size );

            case 'post_author':
                $author = get_userdata( $item['post_author'] );
                return $author ? esc_html( $author->display_name ) : '';

            case 'post_date':
                return esc_html( date_i18n( get_option( 'date_format' ), strtotime( $item['post_date'] ) ) );

            case 'status':
                return $this->render_status( $item['ID'] );

            case 'tools_action':
                return sprintf(
                    '<nav class="ilovepdf-base__layout-flex ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small">
                        %1$s
                        %2$s
                        %3$s
                    </nav>',
                    $this->render_compress_action( $item['ID'] ),
                    $this->render_watermark_action( $item['ID'] ),
                    $this->render_restore_action( $item['ID'] ),
                );

            default:
                error_log( 'ilovepdf plugin -- File Media Table: ' . print_r( var_export( $item, true ), true ) );
                return print_r( $item, true );
        }
    }

    /**
     * Get the bulk actions for the table.
     *
     * @return array The array of bulk actions.
     * @since 3.0.0
     * @see WP_List_Table::get_bulk_actions()
     */
    protected function get_bulk_actions() {
        return array(
            'delete' => _x( 'Delete', 'File List Table: button action', 'ilove-pdf' ),
        );
    }

    /**
     * Render the status for the file.
     *
     * @param int $post_id The ID of the post.
     * @return string HTML for the status.
     * @since 3.0.0
     */
    private function render_status( $post_id ) {
        return sprintf(
            '<div class="ipdf-status %8$s">
                %1$s
                %2$s
                %3$s
                %4$s
                %5$s
                %6$s
                %7$s
                %9$s
                %10$s
            </div>',
            $this->status_not_compressed( $post_id ),
            $this->status_compressing(),
            $this->status_compressed( $post_id ),
            $this->status_not_watermarked( $post_id ),
            $this->status_watermark_processing(),
            $this->status_watermark_applied( $post_id ),
            $this->status_fail(),
            Tool_Compress::is_file_compressed( $post_id ) ? 'ipdf-status-process' : '',
            $this->status_restore_processing(),
            $this->status_restored(),
        );
    }
}
