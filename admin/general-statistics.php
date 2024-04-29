<?php
/**
 * General Statistics Functions
 *
 * @link       https://ilovepdf.com/
 * @since      1.0.0
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/admin
 */

/**
 * Compress Add Media Column.
 *
 * @since    1.0.0
 * @param    array $cols    Columns.
 */
function ilove_pdf_compress_media_column( $cols ) {
    $cols['ilovepdf-status'] = 'iLovePDF';
    return $cols;
}

/**
 * Compress and Watermark Display Button on Library Page.
 *
 * @since    1.0.0
 * @param    string $column_name    Column Name.
 * @param    int    $id             File ID.
 */
function ilove_pdf_buttons_value( $column_name, $id ) {
    if ( 'ilovepdf-status' === $column_name ) {
        $filetype                 = wp_check_filetype( basename( get_attached_file( $id ) ) );
        $options_general_settings = get_option( 'ilove_pdf_display_general_settings' );

        if ( strcasecmp( $filetype['ext'], 'pdf' ) === 0 ) {
            $backup_files_is_active = (int) $options_general_settings['ilove_pdf_general_backup'];
            $html                   = '<div class="row-library"><div class="row-child-library row-compress-tool">';

            if ( ! ilove_pdf_is_file_compressed( $id ) ) {
                $html .= ' <a href="' . admin_url( 'admin-post.php' ) . '?action=ilovepdf_compress&id=' . $id . '&library=1&nonce_ilove_pdf_compress=' . wp_create_nonce( 'admin-post' ) . '" class="button-primary media-ilovepdf-box btn-compress">' . __( 'Compress PDF', 'ilove-pdf' ) . '</a> ';
                $html .= '<span class="stats-compress"></span>';
            } else {
                $original_current_file_size = get_post_meta( $id, '_wp_attached_original_size', true );
                $html                      .= '<span class="stats-compress"><i class="fa fa-check" aria-hidden="true"></i> ' . __( 'Compressed', 'ilove-pdf' ) . '<br />' . __( 'Savings', 'ilove-pdf' ) . ' ' . ilove_pdf_get_percentage_compress( $original_current_file_size, $original_current_file_size - get_post_meta( $id, '_wp_attached_compress_size', true ) ) . '%</span>';
            }
            $html .= '<span class="compressing pdf-status">' . __( 'Compressing', 'ilove-pdf' ) . '...</span>';
            $html .= '<span class="error pdf-status">' . __( 'Error', 'ilove-pdf' ) . '</span>';
            $html .= ' <span class="success pdf-status">' . __( 'Completed', 'ilove-pdf' ) . '</span>';

            $html .= '</div><div class="row-child-library row-watermark-tool">';

            if ( ! ilove_pdf_is_file_watermarked( $id ) ) {
                $html .= sprintf( '<a href="%s" class="%s">%s</a>', add_query_arg( 'nonce_ilove_pdf_watermark', wp_create_nonce( 'admin-post' ), admin_url( 'admin-post.php' ) . '?action=ilovepdf_watermark&id=' . $id . '&library=1' ), 'button-primary media-ilovepdf-box btn-watermark', __( 'Apply Watermark', 'ilove-pdf' ) );
            } else {
                $html .= '<i class="fa fa-check t" aria-hidden="true"></i> ' . __( 'Stamped', 'ilove-pdf' );
            }

            $html .= '<span class="loading pdf-status">' . __( 'Loading', 'ilove-pdf' ) . '...</span>';
            $html .= '<span class="applying-watermark pdf-status">' . __( 'Applying Watermark', 'ilove-pdf' ) . '...</span>';
            $html .= '<span class="error pdf-status">' . __( 'Error', 'ilove-pdf' ) . '</span>';
            $html .= '<span class="success pdf-status">' . __( 'Completed', 'ilove-pdf' ) . '</span>';
            $html .= '</div>';

            if ( $backup_files_is_active ) {
                if ( get_post_meta( $id, '_wp_attached_file_backup', true ) ) {
                    $html .= '<div class="row-child-library"><a class="btn-restore" href="' . admin_url( 'admin-post.php' ) . '?action=ilovepdf_restore&id=' . $id . '&nonce_ilove_pdf_restore=' . wp_create_nonce( 'admin-post' ) . '">(' . __( 'Restore original file', 'ilove-pdf' ) . ') </a><span class="loading pdf-status">' . __( 'Loading', 'ilove-pdf' ) . '...</span><span class="error pdf-status">' . __( 'Error', 'ilove-pdf' ) . '</span><span class="success pdf-status">' . __( 'Completed, please refresh the page.', 'ilove-pdf' ) . '</span><div id="dialog"><div class="no-close"></div></div></div></div>';
                }
            } else {
                $html .= '</div>';
            }
		} else {
            $html = '';
        }

        echo wp_kses( $html, wp_kses_allowed_html( 'post' ) );
    }
}

/**
 * Add Columns to Hooks.
 *
 * @since    1.0.0
 */
function ilove_pdf_hook_new_media_columns() {
    if ( ! get_option( 'ilovepdf_user_id' ) ) {
		return;
    }

    add_filter( 'manage_media_columns', 'ilove_pdf_compress_media_column' );
    add_action( 'manage_media_custom_column', 'ilove_pdf_buttons_value', 10, 2 );
}
add_action( 'admin_init', 'ilove_pdf_hook_new_media_columns' );

/**
 * List PDF Files compress.
 *
 * @since    1.0.0
 */
function ilove_pdf_initialize_list_compress_pdf() {
    $query_files_args = array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'post_mime_type' => 'application/pdf',
        'posts_per_page' => - 1,
    );

    $query_files = new WP_Query( $query_files_args );

    $files = array();
    foreach ( $query_files->posts as $file ) {
        if ( ! ilove_pdf_is_file_compressed( $file->ID ) ) {
            $files[] = $file;
        }
    }

    return $files;
}

/**
 * List PDF Files watermark.
 *
 * @since    1.0.0
 */
function ilove_pdf_initialize_list_watermark_pdf() {
    $query_files_args = array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'post_mime_type' => 'application/pdf',
        'posts_per_page' => - 1,
    );

    $query_files = new WP_Query( $query_files_args );

    $files = array();
    foreach ( $query_files->posts as $file ) {
        if ( ! ilove_pdf_is_file_watermarked( $file->ID ) ) {
            $files[] = $file;
        }
    }

    return $files;
}

/**
 * Custom Meta Box Callback on Media Single Edit Post.
 * Show compress and watermark buttons.
 *
 * @since    1.0.0
 * @param    object $file_object   File Object.
 */
function ilove_pdf_custom_meta_box( $file_object ) {

    $html = '';

    if ( get_option( 'ilovepdf_user_id' ) ) {
        wp_nonce_field( basename( __FILE__ ), 'meta-box-nonce' );

        $filetype                 = wp_check_filetype( basename( get_attached_file( $file_object->ID ) ) );
        $options_general_settings = get_option( 'ilove_pdf_display_general_settings' );

        if ( strcasecmp( $filetype['ext'], 'pdf' ) === 0 ) {
            $backup_files_is_active = (int) $options_general_settings['ilove_pdf_general_backup'];

            if ( get_post_meta( $file_object->ID, '_wp_attached_original_size' ) ) {
                $html .= '<span>' . __( 'Original size: ', 'ilove-pdf' ) . '<strong>' . size_format( get_post_meta( $file_object->ID, '_wp_attached_original_size', true ), 2 ) . '</strong></span><br /><br />';
                $html .= '<span id="current-size">' . __( 'Current size: ', 'ilove-pdf' ) . '<strong>' . size_format( filesize( get_attached_file( $file_object->ID ) ), 2 ) . '</strong></span><br /><br />';
            }

            $html .= '<div class="ilovepdf--meta-box-container">';

            if ( ! ilove_pdf_is_file_compressed( $file_object->ID ) ) {
				$html .= '<a href="' . admin_url( 'admin-post.php' ) . '?action=ilovepdf_compress&id=' . $file_object->ID . '&editpdf=1&nonce_ilove_pdf_compress=' . wp_create_nonce( 'admin-post' ) . '" class="button-primary media-ilovepdf-box btn-compress">' . __( 'Compress PDF', 'ilove-pdf' ) . '</a> ';
            }

            if ( ! ilove_pdf_is_file_watermarked( $file_object->ID ) ) {
				$html .= ' <a href="' . admin_url( 'admin-post.php' ) . '?action=ilovepdf_watermark&id=' . $file_object->ID . '&editpdf=1&nonce_ilove_pdf_watermark=' . wp_create_nonce( 'admin-post' ) . '" class="button-primary media-ilovepdf-box btn-watermark">' . __( 'Apply Watermark', 'ilove-pdf' ) . '</a>';
            } else {
                $html .= '<i class="fa fa-check t" aria-hidden="true"></i> ' . __( 'Stamped', 'ilove-pdf' );
            }

            $html .= '<span class="compressing pdf-status">' . __( 'Compressing', 'ilove-pdf' ) . '...</span>';
            $html .= '<span class="applying-watermark pdf-status">' . __( 'Applying Watermark', 'ilove-pdf' ) . '...</span>';
            $html .= '<span class="error pdf-status">' . __( 'Error', 'ilove-pdf' ) . '</span>';
            $html .= '<span class="success pdf-status">' . __( 'Completed', 'ilove-pdf' ) . '</span>';

            if ( $backup_files_is_active ) {

                if ( get_post_meta( $file_object->ID, '_wp_attached_file_backup', true ) ) {
                    $html .= '</br><a class="link-restore" href="' . admin_url( 'admin-post.php' ) . '?action=ilovepdf_restore&id=' . $file_object->ID . '&nonce_ilove_pdf_restore=' . wp_create_nonce( 'admin-post' ) . '">(' . __( 'Restore original file', 'ilove-pdf' ) . ') </a>';
                }
            }

            $html .= '</div>';

        }
    }

    echo wp_kses( $html, wp_kses_allowed_html( 'post' ) );
}

/**
 * Custom Meta Box Register.
 *
 * @since    1.0.0
 * @param    string  $post_type   Post type..
 * @param    WP_Post $post   Post object.
 */
function ilove_pdf_add_custom_meta_box( $post_type, $post ) {
    if ( 'application/pdf' === $post->post_mime_type ) {
        add_meta_box( 'demo-meta-box', 'iLovePDF', 'ilove_pdf_custom_meta_box', 'attachment', 'side', 'low', null );
    }
}
add_action( 'add_meta_boxes', 'ilove_pdf_add_custom_meta_box', 10, 2 );

/**
 * Add the custom Bulk Action to the select media menus.
 *
 * @since    1.0.0
 * @param    array $bulk_actions    Actions registered.
 */
function ilove_pdf_register_bulk_actions( $bulk_actions ) {
    if ( get_option( 'ilovepdf_user_id' ) ) {
        $bulk_actions['compress']  = __( 'Compress PDF', 'ilove-pdf' );
        $bulk_actions['watermark'] = __( 'Apply Watermark', 'ilove-pdf' );
    }

    return $bulk_actions;
}
add_filter( 'bulk_actions-upload', 'ilove_pdf_register_bulk_actions' );

/**
 * Bulk Action Handler.
 *
 * @since    1.0.0
 * @param    string $redirect_to    Form action.
 * @param    string $doaction       Action.
 * @param    array  $post_ids       Posts ID.
 */
function ilove_pdf_compress_bulk_action_handler( $redirect_to, $doaction, $post_ids ) {

	if ( 'compress' === $doaction ) {

		$redirect_to  = '<form id="bulkActionsForm" name="compress_bulk_actions" action="?page=ilove-pdf-content-statistics&tab=compress_statistic" method="post">';
        $redirect_to .= '<input type="hidden" name="nonce_ilove_pdf_bulk_actions" value="' . wp_create_nonce() . '">';

		foreach ( $post_ids as $post_id ) {
			$redirect_to .= '<input type="hidden" name="array_ids[]" value="' . $post_id . '">';
		}

		$redirect_to .= '</form><script type="text/javascript">document.getElementById("bulkActionsForm").submit();</script>';

	} elseif ( 'watermark' === $doaction ) {

		$redirect_to  = '<form id="bulkActionsForm" name="watermark_bulk_actions" action="?page=ilove-pdf-content-statistics&tab=watermark_statistic" method="post">';
        $redirect_to .= '<input type="hidden" name="nonce_ilove_pdf_bulk_actions" value="' . wp_create_nonce() . '">';

		foreach ( $post_ids as $post_id ) {
			$redirect_to .= '<input type="hidden" name="array_ids[]" value="' . $post_id . '">';
		}
		$redirect_to .= '</form><script type="text/javascript">document.getElementById("bulkActionsForm").submit();</script>';
	}

    echo wp_kses( $redirect_to, ilove_pdf_expanded_alowed_tags() );
}
add_filter( 'handle_bulk_actions-upload', 'ilove_pdf_compress_bulk_action_handler', 10, 3 );

/**
 * Bulk Action Notifications.
 *
 * @since    1.0.0
 */
function ilove_pdf_bulk_action_admin_notice() {
    // phpcs:disable
	if ( ! empty( $_REQUEST['ilovepdf_notification'] ) ) {
		if ( 200 === $_REQUEST['ilovepdf_notification'] ) {
			printf( '<div id="message" class="updated fade">' . esc_html( __( 'Process complete!', 'ilove-pdf' ) ) . '</div>' );
		}

		if ( 'error_start' === $_REQUEST['ilovepdf_notification'] ) {
			printf( '<div id="message" class="error fade">' . esc_html( __( 'An error occured on start.', 'ilove-pdf' ) ) . '</div>' );
		}

		if ( 'error_auth' === $_REQUEST['ilovepdf_notification'] ) {
			printf( '<div id="message" class="error fade">' . esc_html( __( 'An error occured on auth.', 'ilove-pdf' ) ) . '</div>' );
		}

		if ( 'error_upload' === $_REQUEST['ilovepdf_notification'] ) {
			printf( '<div id="message" class="error fade">' . esc_html( __( 'An error occured on upload.', 'ilove-pdf' ) ) . '</div>' );
		}

		if ( 'error_proccess' === $_REQUEST['ilovepdf_notification'] ) {
			printf( '<div id="message" class="error fade">' . esc_html( __( 'An error occured on process.', 'ilove-pdf' ) ) . '</div>' );
		}

		if ( 'error_occured' === $_REQUEST['ilovepdf_notification'] ) {
			printf( '<div id="message" class="error fade">' . esc_html( __( 'An error occured.', 'ilove-pdf' ) ) . '</div>' );
		}
	}
    // phpcs:enable
}
add_action( 'admin_notices', 'ilove_pdf_bulk_action_admin_notice' );

/**
 * Attachment fields to edit.
 *
 * @since    1.0.0
 * @param    array   $form_fields    An array of attachment form fields..
 * @param    WP_Post $post           The WP_Post attachment object..
 */
function ilove_pdf_be_attachment_field_mode_grid( $form_fields, $post ) {
    if ( get_option( 'ilovepdf_user_id' ) && isset( $_SERVER['SCRIPT_NAME'] ) && substr( sanitize_url( $_SERVER['SCRIPT_NAME'] ), strrpos( sanitize_url( $_SERVER['SCRIPT_NAME'] ), '/' ) + 1 ) !== 'post.php' ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $filetype                 = wp_check_filetype( basename( get_attached_file( $post->ID ) ) );
        $options_general_settings = get_option( 'ilove_pdf_display_general_settings' );

        if ( strcasecmp( $filetype['ext'], 'pdf' ) === 0 ) {
            $backup_files_is_active = (int) $options_general_settings['ilove_pdf_general_backup'];

            $html = '';

            if ( ! ilove_pdf_is_file_compressed( $post->ID ) ) {
				$html .= '<a href="' . admin_url( 'admin-post.php' ) . '?action=ilovepdf_compress&id=' . $post->ID . '&editpdf=1&nonce_ilove_pdf_compress=' . wp_create_nonce( 'admin-post' ) . '" class="button-primary media-ilovepdf-box btn-compress">' . __( 'Compress PDF', 'ilove-pdf' ) . '</a> ';
            }

            if ( ! ilove_pdf_is_file_watermarked( $post->ID ) ) {
				$html .= ' <a href="' . admin_url( 'admin-post.php' ) . '?action=ilovepdf_watermark&id=' . $post->ID . '&editpdf=1&nonce_ilove_pdf_watermark=' . wp_create_nonce( 'admin-post' ) . '" class="button-primary media-ilovepdf-box btn-watermark">' . __( 'Apply Watermark', 'ilove-pdf' ) . '</a>';
            } else {
                $html .= '<i class="fa fa-check t" aria-hidden="true"></i> ' . __( 'Stamped', 'ilove-pdf' );
            }

            $html .= '<span class="compressing pdf-status">' . __( 'Compressing', 'ilove-pdf' ) . '...</span>';
            $html .= '<span class="applying-watermark pdf-status">' . __( 'Applying Watermark', 'ilove-pdf' ) . '...</span>';
            $html .= '<span class="error pdf-status">' . __( 'Error', 'ilove-pdf' ) . '</span>';
            $html .= '<span class="success pdf-status">' . __( 'Completed', 'ilove-pdf' ) . '</span>';

            if ( $backup_files_is_active ) {
                if ( get_post_meta( $post->ID, '_wp_attached_file_backup', true ) ) {
                    $html .= '</br><a class="link-restore" href="' . admin_url( 'admin-post.php' ) . '?action=ilovepdf_restore&id=' . $post->ID . '&nonce_ilove_pdf_restore=' . wp_create_nonce( 'admin-post' ) . '">(' . __( 'Restore original file', 'ilove-pdf' ) . ') </a>';
                    $html .= '<script type="text/javascript" src="' . esc_url( ILOVE_PDF_ASSETS_PLUGIN_PATH . 'assets/js/main.min.js' ) . '"></script>'; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
                }
            }

            $form_fields['iLovePDF-tools'] = array(
                'label' => 'iLovePDF',
                'input' => 'html',
                'html'  => $html,
            );
        }
    }

    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'ilove_pdf_be_attachment_field_mode_grid', 10, 2 );
