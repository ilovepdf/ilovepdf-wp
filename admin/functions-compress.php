<?php
/**
 * Compress API Functions
 *
 * @link       https://ilovepdf.com/
 * @since      1.0.0
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/admin
 */

use Ilovepdf\CompressTask;

/**
 * Compress PDF File.
 *
 * @since    1.0.0
 * @param    int|null $id_file    File ID.
 * @param    boolean  $auto    Auto compress.
 * @param    boolean  $bulk    Bulk compress.
 */
function ilove_pdf_compress_pdf( $id_file, $auto = false, $bulk = false ) {
    $options           = get_option( 'ilove_pdf_display_settings_compress' );
    $compression_level = 'recommended';
    $html              = true;
    if ( ! isset( $options['ilove_pdf_compress_active'] ) || intval( $options['ilove_pdf_compress_active'] ) !== 1 ) {
        $html  = '<div class="settings-error notice is-dismissible error">';
        $html .= '<p>' . __( 'Enable Compress PDF option on Settings -> iLovePDF -> Compress PDF', 'ilove-pdf' ) . '</p>';
        $html .= '</div>';
    } else {
        try {
            // you can call task class directly
            // to get your key pair, please visit https://iloveapi.com/user/projects
            $my_task = new CompressTask( get_option( 'ilovepdf_user_public_key', true ), get_option( 'ilovepdf_user_private_key', true ) );

            // file var keeps info about server file id, name...
            // it can be used latter to cancel file
            if ( null !== $id_file ) {
                $file = $my_task->addFile( get_attached_file( $id_file ) );
            } else {
                $count     = 1;
                $files_pdf = ilove_pdf_initialize_list_compress_pdf();
                foreach ( $files_pdf as $file_pdf ) {
                    ${'file' . $count} = $my_task->addFile( get_attached_file( $file_pdf->ID ) );
                    ++$count;
                }
            }

            if ( isset( $options['ilove_pdf_compress_quality'] ) ) {
                switch ( $options['ilove_pdf_compress_quality'] ) {
                    case 0:
                        $compression_level = 'low';
                        break;
                    case 1:
                        $compression_level = 'recommended';
                        break;
                    case 2:
                        $compression_level = 'extreme';
                        break;
                }
            }

            // Set your tool options
            $my_task->setCompressionLevel( $compression_level );

            // process files
            $my_task->execute();

            $upload_dir = wp_upload_dir();

            // and finally download file. If no path is set, it will be downloaded on current folder
            $my_task->download( $upload_dir['basedir'] . '/pdf/compress' );

            if ( is_null( $id_file ) ) {
                $zip = new ZipArchive();
                $zip->open( $upload_dir['basedir'] . '/pdf/compress/output.zip' );
                $zip->extractTo( $upload_dir['basedir'] . '/pdf/compress' );
                $zip->close();
                wp_delete_file( $upload_dir['basedir'] . '/pdf/compress/output.zip' );
            }

            if ( null !== $id_file ) {
                ilove_pdf_upload_compress_file( get_attached_file( $id_file ), $id_file );
            } else {
                foreach ( $files_pdf as $file_pdf ) {
                    ilove_pdf_upload_compress_file( get_attached_file( $file_pdf->ID ), $file_pdf->ID );
                }
            }

            if ( ! $auto ) {
                $html  = '<div class="settings-error notice is-dismissible updated">';
                $html .= '<p>' . __( 'PDF file saved!', 'ilove-pdf' ) . '</p>';
                $html .= '</div>';
            }

            if ( $bulk ) {
                $html = 200;
            }
		} catch ( \Ilovepdf\Exceptions\StartException $e ) {

            $html  = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>' . __( 'An error occured on start: ', 'ilove-pdf' ) . $e->getMessage() . '</p>';
            $html .= '</div>';
            if ( $bulk ) {
                $html = 'error_start';
			}

            if ( $auto ) {
                $html = '<p>' . __( 'An error occured on start: ', 'ilove-pdf' ) . $e->getMessage() . '</p>';
			}

            // Authentication errors
        } catch ( \Ilovepdf\Exceptions\AuthException $e ) {

            $html = 'error_auth';

            if ( $bulk || $auto ) {
                $html = 'error_auth';
            }

            if ( 'Unauthorized (Key may not be empty)' === $e->getMessage() ) {
                $html = 'Check your credentials in the plugin settings page. If you recently deleted a project in your iloveapi account, try switching to another project to correctly save your API Keys.';
            }

            // Uploading files errors
        } catch ( \Ilovepdf\Exceptions\UploadException $e ) {

            $html  = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>' . __( 'An error occured on upload: ', 'ilove-pdf' ) . $e->getMessage() . '</p>';
            $html .= '</div>';
            if ( $bulk ) {
                $html = 'error_upload';
            }

            if ( $auto ) {
                $html = '<p>' . __( 'An error occured on upload: ', 'ilove-pdf' ) . $e->getMessage() . '</p>';
            }
            // Processing files errors
        } catch ( \Ilovepdf\Exceptions\ProcessException $e ) {

            $html  = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>' . __( 'An error occured on process: ', 'ilove-pdf' ) . $e->getMessage() . '</p>';
            $html .= '</div>';
            if ( $bulk ) {
                $html = 'error_proccess';
            }

            if ( $auto ) {
                $html = '<p>' . __( 'An error occured on process: ', 'ilove-pdf' ) . $e->getMessage() . '</p>';
            }
            // Downloading files errors
        } catch ( \Ilovepdf\Exceptions\DownloadException $e ) {

            $html  = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>' . __( 'An error occured on process: ', 'ilove-pdf' ) . $e->getMessage() . '</p>';
            $html .= '</div>';
            if ( $bulk ) {
                $html = 'error_proccess';
            }

            if ( $auto ) {
                $html = '<p>' . __( 'An error occured on process: ', 'ilove-pdf' ) . $e->getMessage() . '</p>';
            }
            // Other errors (as connexion errors and other)
        } catch ( \Exception $e ) {

            $html  = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>' . __( 'An error occured: ', 'ilove-pdf' ) . $e->getMessage() . '</p>';
            $html .= '</div>';
            if ( $bulk ) {
                $html = 'error_occured';
            }

            if ( $auto ) {
                $html = '<p>' . __( 'An error occured: ', 'ilove-pdf' ) . $e->getMessage() . '</p>';
            }
        }
	}

    return $html;
}

/**
 * PDF File Upload Compress.
 *
 * @since    1.0.0
 * @param    int $attachment_id    File ID.
 */
function ilove_pdf_handle_file_upload_compress( $attachment_id ) {
    if ( get_post_mime_type( $attachment_id ) === 'application/pdf' ) {
        $options           = get_option( 'ilove_pdf_display_settings_compress' );
        $options_watermark = get_option( 'ilove_pdf_display_settings_watermark' );

		if ( isset( $options['ilove_pdf_compress_autocompress_new'] ) && ! ilove_pdf_is_file_compressed( $attachment_id ) && ! isset( $options_watermark['ilove_pdf_watermark_auto'] ) ) {

            $html = ilove_pdf_compress_pdf( $attachment_id, true );

            if ( ! ilove_pdf_is_file_watermarked( $attachment_id ) && get_user_option( 'media_library_mode', get_current_user_id() ) === 'list' && ! wp_doing_ajax() ) {

                echo '<img class="pinkynail" src="' . esc_url( includes_url() ) . '/images/media/document.png" alt="">';
                echo '<span class="title custom-title">' . esc_html( get_the_title( $attachment_id ) ) . '</span><span class="pdf-id">ID: ';

                ?><script type='text/javascript' id="my-script-<?php echo (int) $attachment_id; ?>">
                    jQuery( function( $ ) {
                        var response = '<?php echo wp_kses_post( $html ); ?>';
                        var currentElem = $('#my-script-<?php echo (int) $attachment_id; ?>');
                        var parentTag = currentElem.parent();
                        var parentDiv = parentTag.parent();
                        parentDiv.find('.progress').find('.percent').html('Compressing...');
                        window.setTimeout(function(){
                            if (response !==  '1') {
                                parentDiv.find('.progress').find('.percent').html(response.replace(/<\/?p[^>]*>/g, "").replace(/<\/?div[^>]*>/g, ""));
                                parentDiv.find('.progress').css('width','600px');
                                parentDiv.find('.progress').find('.percent').css('width','600px');
                                parentDiv.find('.progress').find('.bar').css({'width':'600px','background-color':'#a00'});
                            } else {
                                parentDiv.find('.progress').find('.bar').css({'background-color':'#46b450'});
                                parentDiv.find('.progress').find('.percent').html('Compressed!');
                            }
                        },3000);
                    });

                </script>
                <?php
			} elseif ( ! ilove_pdf_is_file_watermarked( $attachment_id ) && get_user_option( 'media_library_mode', get_current_user_id() ) === 'grid' || wp_doing_ajax() ) {
                if ( '1' !== $html ) {
                    $return = array( 'message' => wp_strip_all_tags( $html ) );
                    wp_send_json_error( $return );
                } else {
                    $attachment            = wp_prepare_attachment_for_js( $attachment_id );
                    $attachment['message'] = 'PDF Compressed!';
                    wp_send_json_success( $attachment );
                }
			}
		}
    }
}
add_filter( 'add_attachment', 'ilove_pdf_handle_file_upload_compress', 8 );

/**
 * Register Compress Action.
 *
 * @since    1.0.0
 */
function ilove_pdf_compress_action() {

    $html = '';

    if ( isset( $_GET['action'] ) && 'ilovepdf_compress' === $_GET['action'] && isset( $_GET['id'] ) && intval( $_GET['id'] ) ) {// phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $id   = intval( $_GET['id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $html = ilove_pdf_compress_pdf( $id, true );

    } elseif ( isset( $_GET['action'] ) && 'ilovepdf_compress' === $_GET['action'] ) {// phpcs:ignore WordPress.Security.NonceVerification.Recommended
        ilove_pdf_compress_pdf( null, false );
    }

    if ( isset( $_GET['ajax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $return = array();

        if ( isset( $id ) ) {
            $original_current_file_size = get_post_meta( $id, '_wp_attached_original_size', true ) ? get_post_meta( $id, '_wp_attached_original_size', true ) : 0;
            $compress_file_size         = get_post_meta( $id, '_wp_attached_compress_size', true ) ? get_post_meta( $id, '_wp_attached_compress_size', true ) : 0;
            $return['percent']          = ilove_pdf_get_percentage_compress( $original_current_file_size, $original_current_file_size - $compress_file_size );
            $return['compress_size']    = size_format( $compress_file_size, 2 );
        }

        $return['library'] = 0;
        if ( isset( $_GET['library'] ) ) {// phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $return['library'] = 1;
        }

        $return['editpdf'] = 0;
        if ( isset( $_GET['editpdf'] ) ) {// phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $return['editpdf'] = 1;
        }

        if ( is_string( $html ) ) {
            $return['api_error'] = $html;
        }

        $return['status']       = 1;
        $return['total_files']  = get_option( 'ilovepdf_compressed_files' );
        $return['initial_size'] = size_format( ilove_pdf_get_all_pdf_original_size(), 2 );
        $return['current_size'] = size_format( ilove_pdf_get_all_pdf_current_size(), 2 );
        $return['percentage']   = ilove_pdf_get_percentage_compress( ilove_pdf_get_all_pdf_original_size(), ilove_pdf_get_all_pdf_original_size() - ilove_pdf_get_all_pdf_current_size() );

        echo wp_json_encode( $return );
    } else {
        wp_safe_redirect( wp_get_referer() );
    }
}
add_action( 'admin_post_ilovepdf_compress', 'ilove_pdf_compress_action' );

/**
 * Compress List.
 *
 * @since    1.0.0
 */
function ilove_pdf_compress_list_action() {
    $files    = ilove_pdf_initialize_list_compress_pdf();
    $id_files = array();

    foreach ( $files as $file ) {
        $id_files[] = $file->ID;
    }

    $return             = array();
    $return['status']   = 1;
    $return['list_pdf'] = $id_files;
    echo wp_json_encode( $return );
}
add_action( 'admin_post_ilovepdf_compress_list', 'ilove_pdf_compress_list_action' );