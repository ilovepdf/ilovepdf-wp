<?php

use Ilovepdf\CompressTask;

/****************
 * API FUNCTIONS *
 *****************/
function ilove_pdf_compress_pdf( $id_file, $auto = false, $bulk = false ) {
    $options = get_option( 'ilove_pdf_display_settings_compress' );
    $html    = true;
    if ( ! isset( $options['ilove_pdf_compress_active'] ) || intval( $options['ilove_pdf_compress_active'] ) !== 1 ) {
        $html  = '<div class="settings-error notice is-dismissible error">';
        $html .= '<p>' . __( 'Enable Compress PDF option on Settings -> iLovePDF -> Compress PDF', 'ilovepdf' ) . '</p>';
        $html .= '</div>';
    } else {
        try {
            // you can call task class directly
            // to get your key pair, please visit https://developer.ilovepdf.com/user/projects
            $myTask = new CompressTask( get_option( 'ilovepdf_user_public_key', true ), get_option( 'ilovepdf_user_private_key', true ) );

            // file var keeps info about server file id, name...
            // it can be used latter to cancel file
            if ( $id_file !== null ) {
                $file = $myTask->addFile( get_attached_file( $id_file ) );
            } else {
                $count     = 1;
                $files_pdf = ilove_pdf_initialize_list_compress_pdf();
                foreach ( $files_pdf as $file_pdf ) {
                    ${'file' . $count} = $myTask->addFile( get_attached_file( $file_pdf->ID ) );
                    ++$count;
                }
            }

            if ( isset( $options['ilove_pdf_compress_quality'] ) ) {
                switch ( $options['ilove_pdf_compress_quality'] ) {
                    case 0:
                        $compressionLevel = 'low';
                        break;
                    case 1:
                        $compressionLevel = 'recommended';
                        break;
                    case 2:
                        $compressionLevel = 'extreme';
                        break;
                }
            } else {
                $compressionLevel = 'recommended';
            }

            // Set your tool options
            $myTask->setCompressionLevel( $compressionLevel );

            // process files
            $myTask->execute();

            $upload_dir = wp_upload_dir();

            // and finally download file. If no path is set, it will be downloaded on current folder
            $myTask->download( $upload_dir['basedir'] . '/pdf/compress' );

            if ( is_null( $id_file ) ) {
                $zip = new ZipArchive();
                $zip->open( $upload_dir['basedir'] . '/pdf/compress/output.zip' );
                $zip->extractTo( $upload_dir['basedir'] . '/pdf/compress' );
                $zip->close();
                wp_delete_file( $upload_dir['basedir'] . '/pdf/compress/output.zip' );
            }

            if ( $id_file !== null ) {
                ilove_pdf_upload_compress_file( get_attached_file( $id_file ), $id_file );
            } else {
                foreach ( $files_pdf as $file_pdf ) {
                    ilove_pdf_upload_compress_file( get_attached_file( $file_pdf->ID ), $file_pdf->ID );
                }
            }

            if ( ! $auto ) {
                $html  = '<div class="settings-error notice is-dismissible updated">';
                $html .= '<p>' . __( 'PDF file saved!', 'ilovepdf' ) . '</p>';
                $html .= '</div>';
            }

            if ( $bulk ) {
                $html = 200;
            }
		} catch ( \Ilovepdf\Exceptions\StartException $e ) {
            // echo "An error occured on start: " . $e->getMessage() . " ";
            $html  = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>' . __( 'An error occured on start: ', 'ilovepdf' ) . $e->getMessage() . '</p>';
            $html .= '</div>';
            if ( $bulk ) {
                $html = 'error_start';
			}

            if ( $auto ) {
                $html = '<p>' . __( 'An error occured on start: ', 'ilovepdf' ) . $e->getMessage() . '</p>';
			}

            // Authentication errors
        } catch ( \Ilovepdf\Exceptions\AuthException $e ) {
            // echo "An error occured on auth: " . $e->getMessage() . " ";
            // echo implode(', ', $e->getErrors());
            /*
            $html = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>'.__('An error occured on auth: ','ilovepdf') . $e->getMessage() .'</p>';
            $html .= '</div>';*/
            $html = 'error_auth';

            if ( $bulk ) {
                $html = 'error_auth';
            }

            if ( $auto ) {
                // $html = '<p>'.__('An error occured on auth: ','ilovepdf') . $e->getMessage() .'</p>';
                $html = 'error_auth';
            }

            // Uploading files errors
        } catch ( \Ilovepdf\Exceptions\UploadException $e ) {
            // echo "An error occured on upload: " . $e->getMessage() . " ";
            // echo implode(', ', $e->getErrors());
            $html  = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>' . __( 'An error occured on upload: ', 'ilovepdf' ) . $e->getMessage() . '</p>';
            $html .= '</div>';
            if ( $bulk ) {
                $html = 'error_upload';
            }

            if ( $auto ) {
                $html = '<p>' . __( 'An error occured on upload: ', 'ilovepdf' ) . $e->getMessage() . '</p>';
            }
            // Processing files errors
        } catch ( \Ilovepdf\Exceptions\ProcessException $e ) {
            // echo "An error occured on process: " . $e->getMessage() . " ";
            // echo implode(', ', $e->getErrors());
            $html  = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>' . __( 'An error occured on process: ', 'ilovepdf' ) . $e->getMessage() . '</p>';
            $html .= '</div>';
            if ( $bulk ) {
                $html = 'error_proccess';
            }

            if ( $auto ) {
                $html = '<p>' . __( 'An error occured on process: ', 'ilovepdf' ) . $e->getMessage() . '</p>';
            }
            // Downloading files errors
        } catch ( \Ilovepdf\Exceptions\DownloadException $e ) {
            // echo "An error occured on process: " . $e->getMessage() . " ";
            // echo implode(', ', $e->getErrors());
            $html  = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>' . __( 'An error occured on process: ', 'ilovepdf' ) . $e->getMessage() . '</p>';
            $html .= '</div>';
            if ( $bulk ) {
                $html = 'error_proccess';
            }

            if ( $auto ) {
                $html = '<p>' . __( 'An error occured on process: ', 'ilovepdf' ) . $e->getMessage() . '</p>';
            }
            // Other errors (as connexion errors and other)
        } catch ( \Exception $e ) {
            // echo "An error occured: " . $e->getMessage();
            $html  = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>' . __( 'An error occured: ', 'ilovepdf' ) . $e->getMessage() . '</p>';
            $html .= '</div>';
            if ( $bulk ) {
                $html = 'error_occured';
            }

            if ( $auto ) {
                $html = '<p>' . __( 'An error occured: ', 'ilovepdf' ) . $e->getMessage() . '</p>';
            }
        }
	}

    return $html;
}

function ilove_pdf_handle_file_upload_compress( $attachment_id ) {
    if ( get_post_mime_type( $attachment_id ) == 'application/pdf' ) {
        $options           = get_option( 'ilove_pdf_display_settings_compress' );
        $options_watermark = get_option( 'ilove_pdf_display_settings_watermark' );

		if ( isset( $options['ilove_pdf_compress_autocompress_new'] ) && ! ilove_pdf_is_file_compressed( $attachment_id ) && ! isset( $options_watermark['ilove_pdf_watermark_auto'] ) ) {

            $html = ilove_pdf_compress_pdf( $attachment_id, 1 );

            if ( ! ilove_pdf_is_file_watermarked( $attachment_id ) && get_user_option( 'media_library_mode', get_current_user_id() ) == 'list' && ! wp_doing_ajax() ) {

                echo '<img class="pinkynail" src="' . includes_url() . '/images/media/document.png" alt="">';
                echo '<span class="title custom-title">' . get_the_title( $attachment_id ) . '</span><span class="pdf-id">ID: ';

                ?><script type='text/javascript' id="my-script-<?php echo $attachment_id; ?>">
                    jQuery( function( $ ) {
                        var response = '<?php echo $html; ?>';
                        var currentElem = $('#my-script-<?php echo $attachment_id; ?>');
                        var parentTag = currentElem.parent();
                        var parentDiv = parentTag.parent();
                        parentDiv.find('.progress').find('.percent').html('Compressing...');
                        window.setTimeout(function(){
                            if (response != '1') {
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
			} elseif ( ! ilove_pdf_is_file_watermarked( $attachment_id ) && get_user_option( 'media_library_mode', get_current_user_id() ) == 'grid' || wp_doing_ajax() ) {
                if ( $html != '1' ) {
                    $return = array( 'message' => strip_tags( $html ) );
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

function ilove_pdf_compress_action() {
    if ( isset( $_GET['action'] ) && $_GET['action'] == 'ilovepdf_compress' && intval( $_GET['id'] ) ) {
        $id   = intval( $_GET['id'] );
        $html = ilove_pdf_compress_pdf( $id, 1 ); // este
    } elseif ( isset( $_GET['action'] ) && $_GET['action'] == 'ilovepdf_compress' ) {
        ilove_pdf_compress_pdf( null, 0 );
    }

    if ( isset( $_GET['ajax'] ) ) {
        $return = array();

        if ( isset( $id ) ) {
            $original_current_file_size = get_post_meta( $id, '_wp_attached_original_size', true );
            $compress_file_size         = get_post_meta( $id, '_wp_attached_compress_size', true );
            $return['percent']          = ilove_pdf_get_percentage_compress( $original_current_file_size, $original_current_file_size - $compress_file_size );
            $return['compress_size']    = size_format( $compress_file_size, 2 );
        }

        $return['library'] = 0;
        if ( isset( $_GET['library'] ) ) {
            $return['library'] = 1;
        }

        $return['editpdf'] = 0;
        if ( isset( $_GET['editpdf'] ) ) {
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

        echo json_encode( $return );
    } else {
        wp_safe_redirect( wp_get_referer() );
    }
}
add_action( 'admin_post_ilovepdf_compress', 'ilove_pdf_compress_action' );

function ilove_pdf_compress_list_action() {
    $files    = ilove_pdf_initialize_list_compress_pdf();
    $id_files = array();

    foreach ( $files as $file ) {
        $id_files[] = $file->ID;
    }

    $return             = array();
    $return['status']   = 1;
    $return['list_pdf'] = $id_files;
    echo json_encode( $return );
}
add_action( 'admin_post_ilovepdf_compress_list', 'ilove_pdf_compress_list_action' );