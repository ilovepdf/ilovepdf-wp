<?php

namespace Ilove_Pdf_WP\Media;

use WP_List_Table;
use Ilove_Pdf_WP\Helpers\Admin_Notice;
use Ilove_Pdf_WP\Media\Views\Status_Renderer;
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
     * Holds the actions for the IPDF plugin.
     *
     * This array is used to define the bulk actions available in the media library for PDF files.
     *
     * @var array
     * @since 3.0.0
     */
    private $ipdf_actions = array();

    /**
     * Class constructor.
     *
     * Initializes the list table and sets up the necessary hooks.
     *
     * @since 3.0.0
     */
    public function __construct() {
        parent::__construct(
            array(
                'singular' => 'file', // Singular name of the file.
                'plural'   => 'files', // Plural name of the files.
                'ajax'     => true, // No AJAX support.
            )
        );
    }

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
        $this->process_bulk_action();

        $order = 'ORDER BY post_date DESC';

        if ( isset( $_GET['orderby'] ) && isset( $_GET['order'] ) ) {//phpcs:ignore
            $order = 'ORDER BY ' . sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_GET['order'] ) );//phpcs:ignore

            if ( 'file' === $_GET['orderby'] ) {//phpcs:ignore
                $order = 'ORDER BY post_title ' . sanitize_text_field( wp_unslash( $_GET['order'] ) );//phpcs:ignore
            }
        }

        $db_results = $wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
            $wpdb->prepare(
                "SELECT ID, post_title AS file, post_author, post_date, post_status AS status
                FROM {$wpdb->posts}
                WHERE post_type = %s AND post_mime_type LIKE %s
                ",
                'attachment',
                'application/pdf',
            ) . " {$order}",
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
            /*$1%s*/ $this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie").
            /*$2%s*/ $item['ID']                // The value of the checkbox should be the record's id.
        );
    }

    /**
     * Get the sortable columns for the table.
     *
     * @return array The array of sortable columns.
     * @since 3.0.0
     * @see WP_List_Table::get_sortable_columns().
     */
    protected function get_sortable_columns() {
        return array(
            'file'        => array( 'file', false ),     // true means it's already sorted.
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
                    $metadata = get_post_meta( $item['ID'], Tool_Compress::get_db_key_process(), false )[0];

                    $file_size = size_format( $metadata['original_size'], 2 );
                    return esc_html( $file_size );
                }

                return esc_html( $file_size );

            case 'size_compressed':
                $file_size = size_format( 0, 2 );

                if ( Tool_Compress::is_file_compressed( $item['ID'] ) ) {
                    $metadata = get_post_meta( $item['ID'], Tool_Compress::get_db_key_process(), false )[0];

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
                return Status_Renderer::create( $item['ID'], true );

            case 'tools_action':
                return sprintf(
                    '<nav class="ilovepdf-media-library-actions-container ilovepdf-base__layout-flex ilovepdf-base__layout-items--center ilovepdf-base__layout-gap--small">
                        %1$s
                        %2$s
                        %3$s
                    </nav>',
                    $this->render_compress_action( $item['ID'] ),
                    $this->render_watermark_action( $item['ID'] ),
                    $this->render_restore_action( $item['ID'] ),
                );

            default:
                error_log( 'ilovepdf plugin -- File Media Table: ' . print_r( var_export( $item, true ), true ) ); //phpcs:ignore
                return print_r( $item, true );//phpcs:ignore
        }
    }

    /**
     * Get the bulk actions for the table.
     *
     * @return array The array of bulk actions.
     * @since 3.0.0
     * @see WP_List_Table::get_bulk_actions().
     */
    protected function get_bulk_actions() {
        $this->ipdf_actions = array(
            'ilovepdf_compress'  => _x( 'Compress PDF', 'Bulk action button', 'ilove-pdf' ),
            'ilovepdf_watermark' => _x( 'Apply Watermark', 'Bulk action button', 'ilove-pdf' ),
        );

        return $this->ipdf_actions;
    }

    /**
     * Process the bulk action for the table.
     *
     * This method is called when a bulk action is performed on the table.
     * It verifies the nonce, checks if any files are selected, and processes the compression action.
     *
     * @since 3.0.0
     * @return void
     */
    public function process_bulk_action() {

        $action = $this->current_action();

        if ( ! $action ) {
            return;
        }

        if ( ! in_array( $action, array_keys( $this->ipdf_actions ), true ) ) {
            return;
        }

        if ( isset( $_POST['_wpnonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'bulk-' . $this->_args['plural'] ) ) {

            Admin_Notice::add_notice(
                _x( 'Nonce verification failed.', '', 'ilove-pdf' ),
                'error'
            );

            wp_safe_redirect(
                add_query_arg(
                    array(
                        'page' => 'ipdf-media-optimization',
                        admin_url( 'upload.php' ),
                    )
                )
            );
            exit();
        }

        $tools_message = array(
            'ilovepdf_compress'  => array(
                'no_files_selected' => __( 'No files selected for compression.', 'ilove-pdf' ),
            ),
            'ilovepdf_watermark' => array(
                'no_files_selected' => __( 'No files selected for watermarking.', 'ilove-pdf' ),
            ),
        );

        $post_ids = isset( $_POST['file'] ) ? array_map( 'absint', (array) $_POST['file'] ) : array();

        if ( empty( $post_ids ) ) {
            Admin_Notice::add_notice(
                $tools_message[ $action ]['no_files_selected'],
                'error',
            );

            wp_safe_redirect(
                add_query_arg(
                    array(
                        'page' => 'ipdf-media-optimization',
                        admin_url( 'upload.php' ),
                    )
                )
            );
            exit();
        }

        $sendback = add_query_arg(
            array(
                'page' => 'ipdf-media-optimization',
            ),
            admin_url( 'upload.php' ),
        );

		apply_filters( 'handle_bulk_actions-upload', $sendback, $action, $post_ids );//phpcs:ignore
    }
}
