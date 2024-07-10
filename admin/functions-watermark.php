<?php
/**
 * Watermark API Functions
 *
 * @link       https://ilovepdf.com/
 * @since      1.0.0
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/admin
 */

use Ilovepdf\WatermarkTask;

/**
 * Watermark PDF.
 *
 * @since    1.0.0
 * @param    int|null $id_file    File ID.
 * @param    boolean  $auto       Auto compress.
 * @param    boolean  $bulk       Bulk.
 */
function ilove_pdf_watermark_pdf( $id_file, $auto = false, $bulk = false ) {
    $general_options_watermark = get_option( 'ilove_pdf_display_settings_watermark' );
    $options                   = get_option( 'ilove_pdf_display_settings_format_watermark' );
    $html                      = true;
    if ( ! isset( $general_options_watermark['ilove_pdf_watermark_active'] ) || intval( $general_options_watermark['ilove_pdf_watermark_active'] ) !== 1 ) {
        $html  = '<div class="settings-error notice is-dismissible error">';
        $html .= '<p>' . __( 'Enable Watermark PDF option on Settings -> iLovePDF -> Watermark', 'ilove-pdf' ) . '</p>';
        $html .= '</div>';

    } else {

        try {
            // you can call task class directly
            // to get your key pair, please visit https://iloveapi.com/user/projects
            $my_task = new WatermarkTask( get_option( 'ilovepdf_user_public_key', true ), get_option( 'ilovepdf_user_private_key', true ) );

            // file var keeps info about server file id, name...
            // it can be used latter to cancel file
            if ( null !== $id_file ) {
                $file = $my_task->addFile( get_attached_file( $id_file ) );
            } else {
                $count     = 1;
                $files_pdf = ilove_pdf_initialize_list_watermark_pdf();
                foreach ( $files_pdf as $file_pdf ) {
                    ${'file' . $count} = $my_task->addFile( get_attached_file( $file_pdf->ID ) );
                    ++$count;
                }
            }

            switch ( $options['ilove_pdf_format_watermark_mode'] ) {
                case 1:
                    // set mode to text
                    $my_task->setMode( 'image' );

                    if ( isset( $options['ilove_pdf_format_watermark_image'] ) && ! empty( $options['ilove_pdf_format_watermark_image'] ) ) {
                        $image = $my_task->addFile( get_attached_file( $options['ilove_pdf_format_watermark_image'] ) );
                        $my_task->setImage( $image->getServerFilename() );
                    }

                    if ( isset( $options['ilove_pdf_format_watermark_opacity'] ) ) {
                        $my_task->setTransparency( $options['ilove_pdf_format_watermark_opacity'] );
                    }

                    if ( isset( $options['ilove_pdf_format_watermark_rotation'] ) ) {
                        $my_task->setRotation( $options['ilove_pdf_format_watermark_rotation'] );
                    }

                    $layer = array( 'above', 'below' );
                    if ( isset( $options['ilove_pdf_format_watermark_layer'] ) ) {
                        $my_task->setLayer( $layer[ $options['ilove_pdf_format_watermark_layer'] ] );
                    }

                    if ( isset( $options['ilove_pdf_format_watermark_mosaic'] ) && intval( $options['ilove_pdf_format_watermark_mosaic'] ) === 1 ) {
                        $my_task->setMosaic( true );
                    }

                    break;

                case 0:
					// default:
                    // set mode to text
                    $my_task->setMode( 'text' );

                    // set the text
                    if ( isset( $options['ilove_pdf_format_watermark_text'] ) ) {
                        $my_task->setText( $options['ilove_pdf_format_watermark_text'] );
                    } else {
                        $my_task->setText( get_bloginfo() );
                    }

                    // set mode to text
                    if ( isset( $options['ilove_pdf_format_watermark_font_family'] ) ) {
                        $my_task->setFontFamily( $options['ilove_pdf_format_watermark_font_family'] );
                    }

                    // set the font size
                    if ( isset( $options['ilove_pdf_format_watermark_text_size'] ) ) {
                        $my_task->setFontSize( $options['ilove_pdf_format_watermark_text_size'] );
                    }

                    // set color to red
                    if ( isset( $options['ilove_pdf_format_watermark_text_color'] ) ) {
                        $my_task->setFontColor( $options['ilove_pdf_format_watermark_text_color'] );
                    }

                    break;
            }

            $vertical_position = array( 'bottom', 'top', 'middle' );
            if ( isset( $options['ilove_pdf_format_watermark_vertical'] ) ) {
                $my_task->setVerticalPosition( $vertical_position[ $options['ilove_pdf_format_watermark_vertical'] ] );
            }

            $horizontal_position = array( 'left', 'right', 'center' );
            if ( isset( $options['ilove_pdf_format_watermark_horizontal'] ) ) {
                $my_task->setHorizontalPosition( $horizontal_position[ $options['ilove_pdf_format_watermark_horizontal'] ] );
            }

            // process files
            $my_task->execute();

            $upload_dir = wp_upload_dir();

            // and finally download the unlocked file. If no path is set, it will be downloaded on current folder
            $my_task->download( $upload_dir['basedir'] . '/pdf/watermark' );

            if ( is_null( $id_file ) ) {
                $zip = new ZipArchive();
                $zip->open( $upload_dir['basedir'] . '/pdf/watermark/output.zip' );
                $zip->extractTo( $upload_dir['basedir'] . '/pdf/watermark' );
                $zip->close();
                wp_delete_file( $upload_dir['basedir'] . '/pdf/watermark/output.zip' );
            }

            if ( null !== $id_file ) {
                ilove_pdf_upload_watermark_file( get_attached_file( $id_file ), $id_file );
            } else {
                foreach ( $files_pdf as $file_pdf ) {
                    ilove_pdf_upload_watermark_file( get_attached_file( $file_pdf->ID ), $file_pdf->ID );
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

            if ( $bulk ) {
                $html = 'error_auth';
            }

            if ( $auto ) {
                $html = 'error_auth';
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
                $html = '<p>' . __( 'An error occured: ', 'ilove-pdf' ) . __( 'No image selected', 'ilove-pdf' ) . '</p>';
            }
        }
    }

    return $html;
}

/**
 * Watermark File Upload.
 *
 * @since    1.0.0
 * @param    int $attachment_id    File ID.
 */
function ilove_pdf_handle_file_upload_watermark( $attachment_id ) {
    if ( get_post_mime_type( $attachment_id ) === 'application/pdf' ) {
        $options          = get_option( 'ilove_pdf_display_settings_watermark' );
        $options_compress = get_option( 'ilove_pdf_display_settings_compress' );

        if ( isset( $options['ilove_pdf_watermark_auto'] ) && ! ilove_pdf_is_file_watermarked( $attachment_id ) && ! isset( $options_compress['ilove_pdf_compress_autocompress_new'] ) ) {

            $html = ilove_pdf_watermark_pdf( $attachment_id, true );

            if ( ! ilove_pdf_is_file_compressed( $attachment_id ) && get_user_option( 'media_library_mode', get_current_user_id() ) === 'list' && ! wp_doing_ajax() ) {

                echo '<img class="pinkynail" src="' . esc_url( includes_url() ) . '/images/media/document.png" alt="">';
                echo '<span class="title custom-title">' . esc_html( get_the_title( $attachment_id ) ) . '</span><span class="pdf-id">ID: ';

                ?><script type='text/javascript' id="my-script-<?php echo (int) $attachment_id; ?>">
                    jQuery( function( $ ) {                        
                        
                        var response = '<?php echo wp_kses( $html, 'ilove_pdf_expanded_alowed_tags' ); ?>';
                        var currentElem = $('#my-script-<?php echo (int) $attachment_id; ?>');
                        var parentTag = currentElem.parent();
                        var parentDiv = parentTag.parent();
                        parentDiv.find('.progress').find('.percent').html('Applying Watermark...');
                        window.setTimeout(function(){
                            if (response !==  '1') {
                                parentDiv.find('.progress').find('.percent').html(response.replace(/<\/?p[^>]*>/g, "").replace(/<\/?div[^>]*>/g, ""));
                                parentDiv.find('.progress').css('width','600px');
                                parentDiv.find('.progress').find('.percent').css('width','600px');
                                parentDiv.find('.progress').find('.bar').css({'width':'600px','background-color':'#a00'});
                            } else {
                                parentDiv.find('.progress').find('.bar').css({'background-color':'#46b450'});
                                parentDiv.find('.progress').find('.percent').html('Watermark applied!');
                            }                         
                        },3000);                        
                        
                    });

                </script>
                <?php
            } elseif ( ! ilove_pdf_is_file_compressed( $attachment_id ) && get_user_option( 'media_library_mode', get_current_user_id() ) === 'grid' || wp_doing_ajax() ) {
                if ( '1' !== $html ) {
                    $return = array( 'message' => wp_strip_all_tags( $html ) );
                    wp_send_json_error( $return );
                } else {
                    $attachment            = wp_prepare_attachment_for_js( $attachment_id );
                    $attachment['message'] = 'PDF Stamped!';
                    wp_send_json_success( $attachment );
                }
            }
        }
    }
}
add_filter( 'add_attachment', 'ilove_pdf_handle_file_upload_watermark', 9 );

/**
 * Watermark Action.
 *
 * @since    1.0.0
 */
function ilove_pdf_watermark_action() {

    $html = '';

    if ( isset( $_GET['action'] ) && 'ilovepdf_watermark' === $_GET['action'] && isset( $_GET['id'] ) && intval( $_GET['id'] ) ) {// phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $id   = intval( $_GET['id'] );// phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $html = ilove_pdf_watermark_pdf( intval( $_GET['id'] ), true );// phpcs:ignore WordPress.Security.NonceVerification.Recommended

    } elseif ( isset( $_GET['action'] ) && 'ilovepdf_watermark' === $_GET['action'] ) {// phpcs:ignore WordPress.Security.NonceVerification.Recommended
        ilove_pdf_watermark_pdf( null, false );
    }

    if ( isset( $_GET['ajax'] ) ) {// phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $return = array();

        $return['library'] = 0;
        if ( isset( $_GET['library'] ) ) {// phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $return['library'] = 1;
        }

        $return['editpdf'] = 0;
        if ( isset( $_GET['editpdf'] ) ) {// phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $return['editpdf'] = 1;
        }

        $return['status'] = 1;
        if ( isset( $id ) ) {
            $return['id_restore'] = $id;
        }

        if ( is_string( $html ) ) {
            $return['api_error'] = $html;
        }

        $return['total_files'] = get_option( 'ilovepdf_watermarked_files' );

        echo wp_json_encode( $return );

    } else {
        wp_safe_redirect( wp_get_referer() );
    }
}
add_action( 'admin_post_ilovepdf_watermark', 'ilove_pdf_watermark_action' );

/**
 * Watermark list action.
 *
 * @since    1.0.0
 */
function ilove_pdf_watermark_list_action() {
    $files    = ilove_pdf_initialize_list_watermark_pdf();
    $id_files = array();

    foreach ( $files as $file ) {
        $id_files[] = $file->ID;
    }

    $return             = array();
    $return['status']   = 1;
    $return['list_pdf'] = $id_files;
    echo wp_json_encode( $return );
}
add_action( 'admin_post_ilovepdf_watermark_list', 'ilove_pdf_watermark_list_action' );

/**
 * Watermark restore action.
 *
 * @since    1.0.0
 */
function ilove_pdf_restore_action() {
    if ( isset( $_GET['action'] ) && 'ilovepdf_restore' === $_GET['action'] && isset( $_GET['nonce_ilove_pdf_restore'] ) && wp_verify_nonce( sanitize_key( $_GET['nonce_ilove_pdf_restore'] ), 'admin-post' ) && isset( $_GET['id'] ) && intval( $_GET['id'] ) ) {
        ilove_pdf_restore_pdf( (int) sanitize_text_field( wp_unslash( $_GET['id'] ) ) );
    }

    if ( ! isset( $_GET['ajax'] ) ) {
        wp_safe_redirect( wp_get_referer() );
    }
}
add_action( 'admin_post_ilovepdf_restore', 'ilove_pdf_restore_action' );
