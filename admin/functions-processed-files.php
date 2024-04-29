<?php
/**
 * Functions of Processed Files
 *
 * @link       https://ilovepdf.com/
 * @since      1.0.0
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/admin
 */

/**
 * Check if file is compressed.
 *
 * @since    1.0.0
 * @param    int $file_id    File ID.
 */
function ilove_pdf_is_file_compressed( $file_id ) {
	return get_post_meta( $file_id, '_compressed_file', true );
}

/**
 * Check if file is watermarked.
 *
 * @since    1.0.0
 * @param    int $file_id    File ID.
 */
function ilove_pdf_is_file_watermarked( $file_id ) {
	return get_post_meta( $file_id, '_watermarked_file', true );
}

/**
 * Upload Compress File.
 *
 * @since    1.0.0
 * @param    string $filename         File Name.
 * @param    int    $attachment_id    File ID.
 */
function ilove_pdf_upload_compress_file( $filename, $attachment_id ) {
	$wp_upload_dir = wp_upload_dir();

	if ( ! ilove_pdf_is_file_watermarked( $attachment_id ) ) {
		$original_file_size = filesize( get_attached_file( $attachment_id ) );
		update_post_meta( $attachment_id, '_wp_attached_original_size', $original_file_size );
	}

	$options_general_settings = get_option( 'ilove_pdf_display_general_settings' );

	if ( (int) $options_general_settings['ilove_pdf_general_backup'] && ! get_post_meta( $attachment_id, '_wp_attached_file_backup', true ) ) {

		copy( get_attached_file( $attachment_id ), $wp_upload_dir['basedir'] . '/pdf/backup/' . basename( get_attached_file( $attachment_id ) ) );
		update_post_meta( $attachment_id, '_wp_attached_file_backup', get_post_meta( $attachment_id, '_wp_attached_file', true ) );

	}

	copy( $wp_upload_dir['basedir'] . '/pdf/compress/' . basename( get_attached_file( $attachment_id ) ), get_attached_file( $attachment_id ) );

	if ( get_option( 'ilovepdf_compressed_files' ) || get_option( 'ilovepdf_compressed_files' ) === '0' ) {
		$n_compressed_files = intval( get_option( 'ilovepdf_compressed_files' ) ) + 1;
		update_option( 'ilovepdf_compressed_files', $n_compressed_files );
	} else {
		add_option( 'ilovepdf_compressed_files', 1 );
	}

	wp_delete_file( $wp_upload_dir['basedir'] . '/pdf/compress/' . basename( get_attached_file( $attachment_id ) ) );

	update_post_meta( $attachment_id, '_wp_attached_compress_size', filesize( get_attached_file( $attachment_id ) ) );
	update_post_meta( $attachment_id, '_compressed_file', 1 );
}

/**
 * Upload Watermark File.
 *
 * @since    1.0.0
 * @param    string $filename         File Name.
 * @param    int    $attachment_id    File ID.
 */
function ilove_pdf_upload_watermark_file( $filename, $attachment_id ) {
	$wp_upload_dir = wp_upload_dir();
	if ( ! ilove_pdf_is_file_compressed( $attachment_id ) ) {
		$original_file_size = filesize( get_attached_file( $attachment_id ) );
		update_post_meta( $attachment_id, '_wp_attached_original_size', $original_file_size );
	}

	$options_general_settings = get_option( 'ilove_pdf_display_general_settings' );

	if ( (int) $options_general_settings['ilove_pdf_general_backup'] && ! get_post_meta( $attachment_id, '_wp_attached_file_backup', true ) ) {

			copy( get_attached_file( $attachment_id ), $wp_upload_dir['basedir'] . '/pdf/backup/' . basename( get_attached_file( $attachment_id ) ) );
			update_post_meta( $attachment_id, '_wp_attached_file_backup', get_post_meta( $attachment_id, '_wp_attached_file', true ) );

	}

	copy( $wp_upload_dir['basedir'] . '/pdf/watermark/' . basename( get_attached_file( $attachment_id ) ), get_attached_file( $attachment_id ) );

	// Regenerate attachment metadata
	ilove_pdf_regenerate_attachment_data( $attachment_id );

	if ( get_option( 'ilovepdf_watermarked_files' ) || get_option( 'ilovepdf_watermarked_files' ) === '0' ) {
		$n_watermarked_files = intval( get_option( 'ilovepdf_watermarked_files' ) ) + 1;
		update_option( 'ilovepdf_watermarked_files', $n_watermarked_files );

	} else {
		add_option( 'ilovepdf_watermarked_files', 1 );
	}

	wp_delete_file( $wp_upload_dir['basedir'] . '/pdf/watermark/' . basename( get_attached_file( $attachment_id ) ) );

	update_post_meta( $attachment_id, '_watermarked_file', 1 );
}

/**
 * Restore File.
 *
 * @since    1.0.0
 * @param    int $attachment_id    File ID.
 */
function ilove_pdf_restore_pdf( $attachment_id ) {
	$wp_upload_dir = wp_upload_dir();

	if ( ilove_pdf_is_file_compressed( $attachment_id ) ) {
		if ( get_option( 'ilovepdf_compressed_files' ) === 1 ) {
			delete_option( 'ilovepdf_compressed_files' );
		} else {
			update_option( 'ilovepdf_compressed_files', get_option( 'ilovepdf_compressed_files' ) - 1 );
			if ( get_option( 'ilovepdf_compressed_files' ) <= '0' ) {
				delete_option( 'ilovepdf_compressed_files' ); }
		}
	}

	if ( ilove_pdf_is_file_watermarked( $attachment_id ) ) {
		if ( get_option( 'ilovepdf_watermarked_files' ) === 1 ) {
			delete_option( 'ilovepdf_watermarked_files' );
		} else {
			update_option( 'ilovepdf_watermarked_files', get_option( 'ilovepdf_watermarked_files' ) - 1 );
			if ( get_option( 'ilovepdf_watermarked_files' ) <= '0' ) {
				delete_option( 'ilovepdf_watermarked_files' ); }
		}
	}

	copy( $wp_upload_dir['basedir'] . '/pdf/backup/' . basename( get_attached_file( $attachment_id ) ), get_attached_file( $attachment_id ) );

	// Regenerate attachment metadata
	ilove_pdf_regenerate_attachment_data( $attachment_id );

	delete_post_meta( $attachment_id, '_wp_attached_file_backup' );
	delete_post_meta( $attachment_id, '_compressed_file' );
	delete_post_meta( $attachment_id, '_watermarked_file' );
	delete_post_meta( $attachment_id, '_wp_attached_compress_size' );
}

/**
 * Delete File.
 *
 * @since    1.0.0
 * @param    int $attachment_id    File ID.
 */
function ilove_pdf_handle_delete_file( $attachment_id ) {
    if ( get_post_mime_type( $attachment_id ) === 'application/pdf' ) {
    	$result = 0;
    	if ( get_post_meta( $attachment_id, '_wp_attached_original_size', true ) ) {
    		$result = get_option( 'ilovepdf_initial_pdf_files_size' ) - get_post_meta( $attachment_id, '_wp_attached_original_size', true );
    	}
    	update_option( 'ilovepdf_initial_pdf_files_size', $result );
    	$wp_upload_dir = wp_upload_dir();
    	$file_name     = basename( get_attached_file( $attachment_id ) );
        if ( ilove_pdf_is_file_compressed( $attachment_id ) ) {
        	if ( get_option( 'ilovepdf_compressed_files' ) === 1 ) {
				delete_option( 'ilovepdf_compressed_files' );
			} else {
				update_option( 'ilovepdf_compressed_files', get_option( 'ilovepdf_compressed_files' ) - 1 );
				if ( get_option( 'ilovepdf_compressed_files' ) <= '0' ) {
					delete_option( 'ilovepdf_compressed_files' ); }
			}
        }

		if ( ilove_pdf_is_file_watermarked( $attachment_id ) ) {
			if ( get_option( 'ilovepdf_watermarked_files' ) === 1 ) {
				delete_option( 'ilovepdf_watermarked_files' );
			} else {
				update_option( 'ilovepdf_watermarked_files', get_option( 'ilovepdf_watermarked_files' ) - 1 );
				if ( get_option( 'ilovepdf_watermarked_files' ) <= '0' ) {
					delete_option( 'ilovepdf_watermarked_files' ); }
			}
		}

		if ( file_exists( $wp_upload_dir['basedir'] . '/pdf/compress/' . $file_name ) ) {
			wp_delete_file( $wp_upload_dir['basedir'] . '/pdf/compress/' . $file_name );
        }

		if ( file_exists( $wp_upload_dir['basedir'] . '/pdf/watermark/' . $file_name ) ) {
			wp_delete_file( $wp_upload_dir['basedir'] . '/pdf/watermark/' . $file_name );
        }

		if ( file_exists( $wp_upload_dir['basedir'] . '/pdf/backup/' . $file_name ) ) {
			wp_delete_file( $wp_upload_dir['basedir'] . '/pdf/backup/' . $file_name );
        }
	}
}
add_filter( 'delete_attachment', 'ilove_pdf_handle_delete_file' );

/**
 * File Upload Compress Watermark.
 *
 * @since    1.0.0
 * @param    int $attachment_id    File ID.
 */
function ilove_pdf_handle_file_upload_compress_watermark( $attachment_id ) {
    if ( get_post_mime_type( $attachment_id ) === 'application/pdf' ) {
        $options_compress  = get_option( 'ilove_pdf_display_settings_compress' );
        $options_watermark = get_option( 'ilove_pdf_display_settings_watermark' );
        update_option( 'ilovepdf_initial_pdf_files_size', get_option( 'ilovepdf_initial_pdf_files_size' ) + filesize( get_attached_file( $attachment_id ) ) );

        if ( isset( $options_compress['ilove_pdf_compress_autocompress_new'] ) && isset( $options_watermark['ilove_pdf_watermark_auto'] ) ) {
            $html_compress  = ilove_pdf_compress_pdf( $attachment_id, true );
            $html_watermark = ilove_pdf_watermark_pdf( $attachment_id, true );

            if ( get_user_option( 'media_library_mode', get_current_user_id() ) === 'list' && ! wp_doing_ajax() ) {

	            echo '<img class="pinkynail" src="' . esc_url( includes_url() ) . '/images/media/document.png" alt="">';
	            echo '<span class="title custom-title">' . esc_html( get_the_title( $attachment_id ) ) . '</span><span class="pdf-id">ID: ';

	            ?><script type='text/javascript' id="my-script-<?php echo (int) $attachment_id; ?>">
	                jQuery( function( $ ) {
	                    var response_compress = '<?php echo wp_kses_post( $html_compress ); ?>';
	                    var currentElem = $('#my-script-<?php echo (int) $attachment_id; ?>');
	                    var parentTag = currentElem.parent();
	                    var parentDiv = parentTag.parent();
	                    parentDiv.find('.progress').find('.percent').html('Compressing...');
	                    window.setTimeout(function(){
	                        if (response_compress !==  '1') {
	                            parentDiv.find('.progress').find('.percent').html(response_compress.replace(/<\/?p[^>]*>/g, "").replace(/<\/?div[^>]*>/g, ""));
	                            parentDiv.find('.progress').css('width','600px');
	                            parentDiv.find('.progress').find('.percent').css('width','600px');
	                            parentDiv.find('.progress').find('.bar').css({'width':'600px','background-color':'#a00'});
	                        } else {
	                        	var response_watermark = '<?php echo wp_kses_post( $html_watermark ); ?>';
	                        	parentDiv.find('.progress').find('.percent').html('Applying Watermark...');
			                    window.setTimeout(function(){
			                        if (response_watermark !==  '1') {
			                            parentDiv.find('.progress').find('.percent').html(response_watermark.replace(/<\/?p[^>]*>/g, "").replace(/<\/?div[^>]*>/g, ""));
			                            parentDiv.find('.progress').css('width','600px');
			                            parentDiv.find('.progress').find('.percent').css('width','600px');
			                            parentDiv.find('.progress').find('.bar').css({'width':'600px','background-color':'#a00'});
			                        } else {
			                        	parentDiv.find('.progress').css('width','250px');
			                        	parentDiv.find('.progress').find('.percent').css('width','250px');
			                            parentDiv.find('.progress').find('.bar').css({'width':'250px','background-color':'#46b450'});
			                            parentDiv.find('.progress').find('.percent').html('Compressed and Stamped!');
			                        }                         
			                    },3000);
	                        }
	                    },3000);
	                });

	            </script>
                <?php
            } elseif ( get_user_option( 'media_library_mode', get_current_user_id() ) === 'grid' || wp_doing_ajax() ) {
            	if ( '1' !== $html_compress || '1' !== $html_watermark ) {
            		if ( '1' !== $html_compress ) {
            			$return = array( 'message' => wp_strip_all_tags( $html_compress ) );
                    }

            		if ( '1' !== $html_watermark ) {
                    	$return = array( 'message' => wp_strip_all_tags( $html_watermark ) );
                    }

                    wp_send_json_error( $return );
                } else {
                    $attachment            = wp_prepare_attachment_for_js( $attachment_id );
                    $attachment['message'] = 'PDF Compressed & Stamped!';
                    wp_send_json_success( $attachment );
                }
            }
        }
    }
}
add_filter( 'add_attachment', 'ilove_pdf_handle_file_upload_compress_watermark' );