<?php
    
use Ilovepdf\WatermarkTask;

/****************
* API FUNCTIONS *
*****************/
function ilove_pdf_watermark_pdf($id_file, $auto=false, $bulk=false) {
    $general_options_watermark = get_option('ilove_pdf_display_settings_watermark');
    $options = get_option('ilove_pdf_display_settings_format_watermark');
    $html = true;
    if (!isset($general_options_watermark['ilove_pdf_watermark_active']) || intval($general_options_watermark['ilove_pdf_watermark_active']) !== 1) {
        $html = '<div class="settings-error notice is-dismissible error">';
        $html .= '<p>'.__('Enable Watermark PDF option on Settings -> iLovePDF -> Watermark','ilovepdf').'</p>';
        $html .= '</div>';

    } else {

        try {
            // you can call task class directly
            // to get your key pair, please visit https://developer.ilovepdf.com/user/projects
            $myTask = new WatermarkTask(get_option('ilovepdf_user_public_key', true),get_option('ilovepdf_user_private_key', true));

            // file var keeps info about server file id, name...
            // it can be used latter to cancel file
            if($id_file !== null){
                $file = $myTask->addFile(get_attached_file($id_file));
            }else{
                $count = 1;
                $files_pdf = ilove_pdf_initialize_list_watermark_pdf();
                foreach($files_pdf as $file_pdf){
                    ${'file'.$count} = $myTask->addFile(get_attached_file($file_pdf->ID));  
                    $count ++;
                }
            }

            switch ($options['ilove_pdf_format_watermark_mode']) {
                case 1:
                     // set mode to text
                    $myTask->setMode("image");

                    if(isset($options['ilove_pdf_format_watermark_image'])){
                        $image = $myTask->addFile(get_attached_file($options['ilove_pdf_format_watermark_image']));
                        $myTask->setImage($image->getServerFilename());
                    }

                    if(isset($options['ilove_pdf_format_watermark_opacity']))
                        $myTask->setTransparency($options['ilove_pdf_format_watermark_opacity']);

                    if(isset($options['ilove_pdf_format_watermark_rotation']))
                        $myTask->setRotation($options['ilove_pdf_format_watermark_rotation']);

                    $layer = array('above','below');
                    if(isset($options['ilove_pdf_format_watermark_layer']))
                        $myTask->setLayer($layer[ $options['ilove_pdf_format_watermark_layer'] ]);

                    if(isset($options['ilove_pdf_format_watermark_mosaic']) && intval($options['ilove_pdf_format_watermark_mosaic']) == 1)
                        $myTask->setMosaic(true);

                    break;

                case 0:
                
                default:         
                    // set mode to text
                    $myTask->setMode("text");

                    // set the text
                    if(isset($options['ilove_pdf_format_watermark_text'])) {
                        $myTask->setText($options['ilove_pdf_format_watermark_text']);
                    }else{
                        $myTask->setText(get_bloginfo());
                    }

                    // set mode to text
                    if(isset($options['ilove_pdf_format_watermark_font_family']))
                        $myTask->setFontFamily($options['ilove_pdf_format_watermark_font_family']);

                    // set the font size
                    if(isset($options['ilove_pdf_format_watermark_text_size']))
                        $myTask->setFontSize($options['ilove_pdf_format_watermark_text_size']);

                    // set color to red
                    if(isset($options['ilove_pdf_format_watermark_text_color']))
                        $myTask->setFontColor($options['ilove_pdf_format_watermark_text_color']);

                    break;
            }

            $vertical_position = array('bottom','top','middle');
            if(isset($options['ilove_pdf_format_watermark_vertical']))
                $myTask->setVerticalPosition($vertical_position[ $options['ilove_pdf_format_watermark_vertical'] ]);
               
            $horizontal_position = array('left','right','center');
            if(isset($options['ilove_pdf_format_watermark_horizontal']))
                $myTask->setHorizontalPosition($horizontal_position[ $options['ilove_pdf_format_watermark_horizontal'] ]);
            

            // process files
            $myTask->execute();

            $upload_dir = wp_upload_dir();

            // and finally download the unlocked file. If no path is set, it will be downloaded on current folder
            $myTask->download($upload_dir['basedir'].'/pdf/watermark');

            if(is_null($id_file)){
                $zip = new ZipArchive;
                $zip->open($upload_dir['basedir'].'/pdf/watermark/output.zip');
                $zip->extractTo($upload_dir['basedir'].'/pdf/watermark');
                $zip->close();
                wp_delete_file($upload_dir['basedir'].'/pdf/watermark/output.zip');
            }

            if($id_file !== null){                
                ilove_pdf_upload_watermark_file(get_attached_file( $id_file ) , $id_file);
            }else{
                foreach($files_pdf as $file_pdf){                    
                    ilove_pdf_upload_watermark_file(get_attached_file( $file_pdf->ID ) , $file_pdf->ID);
                }
            }

            if (!$auto) {
                $html = '<div class="settings-error notice is-dismissible updated">';
                $html .= '<p>'.__('PDF file saved!','ilovepdf').'</p>';
                $html .= '</div>';
            }

            if ($bulk)
                $html = 200;

        } catch (\Ilovepdf\Exceptions\StartException $e) {
            //echo "An error occured on start: " . $e->getMessage() . " ";
            $html = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>'.__('An error occured on start: ','ilovepdf') . $e->getMessage() .'</p>';
            $html .= '</div>';
            if ($bulk)
                $html = 'error_start';

            if($auto)
                $html = '<p>'.__('An error occured on start: ','ilovepdf') . $e->getMessage() .'</p>';

            // Authentication errors
        } catch (\Ilovepdf\Exceptions\AuthException $e) {
            //echo "An error occured on auth: " . $e->getMessage() . " ";
            //echo implode(', ', $e->getErrors());
           /* $html = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>'.__('An error occured on auth: ','ilovepdf') . $e->getMessage() .'</p>';
            $html .= '</div>';*/
             $html = 'error_auth';

            if ($bulk)
                $html = 'error_auth';

            if($auto)
                //$html = '<p>'.__('An error occured on auth: ','ilovepdf') . $e->getMessage() .'</p>';
                 $html = 'error_auth';
            // Uploading files errors
        } catch (\Ilovepdf\Exceptions\UploadException $e) {
            //echo "An error occured on upload: " . $e->getMessage() . " ";
            //echo implode(', ', $e->getErrors());
            $html = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>'.__('An error occured on upload: ','ilovepdf') . $e->getMessage() .'</p>';
            $html .= '</div>';
            if ($bulk)
                $html = 'error_upload';

            if($auto)
                $html = '<p>'.__('An error occured on upload: ','ilovepdf') . $e->getMessage() .'</p>';
            // Processing files errors
        } catch (\Ilovepdf\Exceptions\ProcessException $e) {
            //echo "An error occured on process: " . $e->getMessage() . " ";
            //echo implode(', ', $e->getErrors());
            $html = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>'.__('An error occured on process: ','ilovepdf') . $e->getMessage() .'</p>';
            $html .= '</div>';
            if ($bulk)
                $html = 'error_proccess';

            if($auto)
                $html = '<p>'.__('An error occured on process: ','ilovepdf') . $e->getMessage() .'</p>';
            // Downloading files errors
        } catch (\Ilovepdf\Exceptions\DownloadException $e) {
            //echo "An error occured on process: " . $e->getMessage() . " ";
            //echo implode(', ', $e->getErrors());
            $html = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>'.__('An error occured on process: ','ilovepdf') . $e->getMessage() .'</p>';
            $html .= '</div>';
            if ($bulk)
                $html = 'error_proccess';

            if($auto)
                $html = '<p>'.__('An error occured on process: ','ilovepdf') . $e->getMessage() .'</p>';
            // Other errors (as connexion errors and other)
        } catch (\Exception $e) {
            //echo "An error occured: " . $e->getMessage();
            $html = '<div class="settings-error notice is-dismissible error">';
            $html .= '<p>'.__('An error occured: ','ilovepdf') . $e->getMessage() .'</p>';
            $html .= '</div>';
            if ($bulk)
                $html = 'error_occured';

            if($auto){
                //$html = '<p>'.__('An error occured: ','ilovepdf') . $e->getMessage() .'</p>';
                $html = '<p>' . __('An error occured: ','ilovepdf')  .  __('No image selected','ilovepdf')  . '</p>';
            }
        }
    }

    return $html;

}

function pdf_handle_file_upload_watermark($attachment_id){
    if(get_post_mime_type($attachment_id) == 'application/pdf'){
        $options = get_option('ilove_pdf_display_settings_watermark');
        $options_compress = get_option('ilove_pdf_display_settings_compress');

        if (isset($options['ilove_pdf_watermark_auto']) && !ilove_pdf_is_file_watermarked($attachment_id) && !isset($options_compress['ilove_pdf_compress_autocompress_new'])) {
        
            $html = ilove_pdf_watermark_pdf($attachment_id,1);

            if( !ilove_pdf_is_file_compressed($attachment_id) && get_user_option( 'media_library_mode', get_current_user_id() ) == 'list' && !wp_doing_ajax()) {

                echo '<img class="pinkynail" src="'.includes_url().'/images/media/document.png" alt="">';
                echo '<span class="title custom-title">'.get_the_title($attachment_id).'</span><span class="pdf-id">ID: ';

                ?><script type='text/javascript' id="my-script-<?php echo $attachment_id;?>">
                    jQuery( function( $ ) {                        
                        
                        var response = '<?php echo $html;?>';
                        var currentElem = $('#my-script-<?php echo $attachment_id;?>');
                        var parentTag = currentElem.parent();
                        var parentDiv = parentTag.parent();
                        parentDiv.find('.progress').find('.percent').html('Applying Watermark...');
                        window.setTimeout(function(){
                            if (response != '1') {
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

                </script><?php
            } else if (!ilove_pdf_is_file_compressed($attachment_id) && get_user_option( 'media_library_mode', get_current_user_id() ) == 'grid' || wp_doing_ajax()) {
                if ($html != '1') {
                    $return = array('message' => strip_tags($html));
                    wp_send_json_error($return);
                } else {
                    $attachment = wp_prepare_attachment_for_js( $attachment_id );
                    $attachment['message'] = 'PDF Stamped!';
                    wp_send_json_success($attachment);
                }
            }
        }
    }
}
add_filter('add_attachment', 'pdf_handle_file_upload_watermark', 9);

function ilovepdf_watermark_action() {
    if (isset($_GET['action']) && $_GET['action'] == 'ilovepdf_watermark' && intval($_GET['id'])) {
        $id = intval($_GET['id']);
        $html = ilove_pdf_watermark_pdf(intval($_GET['id']), 1);
    } else if (isset($_GET['action']) && $_GET['action'] == 'ilovepdf_watermark') {
        ilove_pdf_watermark_pdf(null, 0);
    }

    if (isset($_GET['ajax'])) {
        $return = array();
        
        $return['library'] = 0;
        if(isset($_GET['library']))
            $return['library'] = 1;

        $return['editpdf'] = 0;
        if(isset($_GET['editpdf']))
            $return['editpdf'] = 1;

        $return['status'] = 1;
        if(isset($id))
            $return['id_restore'] = $id; 

        if(is_string($html))
            $return['api_error'] = $html;

        $return['total_files'] = get_option('ilovepdf_watermarked_files');

        echo json_encode($return);

    } else {
        wp_safe_redirect(wp_get_referer());
    }
}
add_action('admin_post_ilovepdf_watermark', 'ilovepdf_watermark_action');

function ilovepdf_watermark_list_action() {
    $files = ilove_pdf_initialize_list_watermark_pdf();
    $id_files = array();
                                
    foreach ( $files as $file ) {
        $id_files[] = $file->ID;
    }
    
    $return = array();
    $return['status'] = 1;
    $return['list_pdf'] = $id_files;
    echo json_encode($return);

}
add_action('admin_post_ilovepdf_watermark_list', 'ilovepdf_watermark_list_action');

function ilovepdf_restore_action() {
    if (isset($_GET['action']) && $_GET['action'] == 'ilovepdf_restore' && intval($_GET['id'])) {
        ilove_pdf_restore_pdf($_GET['id']);
    }

    if (!isset($_GET['ajax']))
        wp_safe_redirect(wp_get_referer());
}
add_action('admin_post_ilovepdf_restore', 'ilovepdf_restore_action');        