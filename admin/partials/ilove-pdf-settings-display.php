<?php
/**
 * Función que pinta la página de configuración
 */
function ilove_pdf_content_page_setting() {

    $logo_svg = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="150px" height="75.51px" viewBox="0 0 300 75.51" enable-background="new 0 0 300 75.51" xml:space="preserve">
                    <g>
                        <path fill="#E5322D" d="M94.313,2.543c-4.785,2.309-8.374,6.2-10.995,10.612C79.104,6.071,72.405,0.326,62.259,0.326   c-10.15,0-22.594,8.614-22.594,23.165c0,14.732,12.293,21.715,18.382,25.658c6.508,4.211,17.613,11.867,25.27,26.036   c7.66-14.168,18.763-21.825,25.273-26.036c4.574-2.965,12.655-7.647,16.387-16.047L94.313,2.543z M93.946,33.938V6.254   l27.684,27.683H93.946z"/>
                        <g>
                            <path d="M0.458,59.164H3.89c1.088,0,2.344-1.507,2.344-2.511V20.24c0-1.004-1.256-2.427-2.344-2.427H0.458v-8.79h27.54v8.79    h-3.516c-1.088,0-2.26,1.423-2.26,2.427v36.413c0,1.005,1.172,2.511,2.26,2.511h3.516v8.455H0.458V59.164z"/>
                            <path d="M133.383,59.164h2.846c1.172,0,2.427-1.507,2.427-2.511V20.24c0-1.004-1.256-2.427-2.427-2.427h-2.846v-8.79h26.619    c15.654,0,24.192,5.525,24.192,18.583c0,12.724-9.041,18.499-24.778,18.499h-4.855v13.059h6.78v8.455h-27.958V59.164z     M159.166,37.484c7.031,0,8.873-4.018,8.873-9.626c0-5.525-1.842-9.459-8.873-9.459h-4.855v19.086H159.166z"/>
                            <path d="M189.3,59.164h2.846c1.172,0,2.427-1.507,2.427-2.511V20.24c0-1.004-1.255-2.427-2.427-2.427H189.3v-8.79h25.531    c20.843,0,31.725,9.041,31.725,28.963c0,19.588-11.049,29.633-32.144,29.633H189.3V59.164z M214.412,58.746    c10.547,0,15.737-6.278,15.737-20.341c0-13.979-5.106-20.007-15.737-20.007h-3.934v40.347H214.412z"/>
                            <path d="M251.912,59.164h2.846c1.172,0,2.427-1.507,2.427-2.511V20.24c0-1.004-1.255-2.427-2.427-2.427h-2.846v-8.79h47.63v18.081    h-9.375l-1.423-8.622H273.09v16.407h7.617l0.67-5.525H290v20.341h-8.622l-0.67-5.776h-7.617v15.235h6.781v8.455h-27.959V59.164z"/>
                        </g>
                        <polygon fill="#FFFFFF" points="93.946,33.938 93.946,6.254 121.63,33.938  "/>
                    </g>
                    </svg>';
	?>
	<div class="wrap">
		<div class="plugin-logo">
            <?php echo $logo_svg; ?>
        </div>
        <?php if ( isset( $_GET['response_code'] ) ) : ?>

            <?php
            switch ( $_GET['response_code'] ) {
                case 400:
                    echo '<div class="settings-error notice is-dismissible error"><p>' . __( 'Bad request.' );
                    break;

                case 401:
                    echo '<div class="settings-error notice is-dismissible error"><p>' . __( 'Incorrect email or password.' );
                    break;

                case 200:
                    echo '<div class="settings-error notice is-dismissible updated"><p>' . __( 'Welcome!' );
                    break;

                case 500:
                    echo '<div class="settings-error notice is-dismissible error"><p>' . __( 'Error on register/login.' );
                    break;
            }
            ?>
            </p></div>
        <?php endif; ?>
        <?php
            $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'setting_options';
        ?>
         
        <h2 class="nav-tab-wrapper">
            <a href="?page=ilove-pdf-content-setting&tab=setting_options" class="nav-tab <?php echo 'setting_options' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php echo __( 'General', 'ilovepdf' ); ?></a>
            <a href="?page=ilove-pdf-content-setting&tab=compress_options" class="nav-tab <?php echo 'compress_options' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php echo __( 'Compress PDF', 'ilovepdf' ); ?></a>
            <a href="?page=ilove-pdf-content-setting&tab=watermark_options" class="nav-tab <?php echo 'watermark_options' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php echo __( 'Watermark', 'ilovepdf' ); ?></a>
        </h2> 	        
      		<?php if ( 'setting_options' === $active_tab ) : ?>
            <div class="wrap">           
                <div class="container no-center">
                    <div class="row">
                        <?php if ( get_option( 'ilovepdf_user_id' ) ) : ?>
                            <?php $stats = ilove_pdf_get_statistics(); ?>
                            <div class="col-md-4">
                                <div class="panel" style="margin-right: 10px;">
                                    <h3><?php echo __( 'Account', 'ilovepdf' ); ?></h3>
                                    <p><i class="fa fa-check" aria-hidden="true"></i> <?php echo __( 'Logged as', 'ilovepdf' ); ?><strong> <?php echo get_option( 'ilovepdf_user_email' ); ?></strong>&nbsp;&nbsp;&nbsp;<a href="<?php echo admin_url( 'admin-post.php' ); ?>?action=ilovepdf_logout" class="button button-primary" style="    margin-top: 10px;"><?php echo __( 'Logout', 'ilovepdf' ); ?></a></p>

                                    <hr style="    margin: 30px 0px;" />
                                    <form action="<?php echo admin_url( 'admin-post.php' ); ?>?action=ilovepdf_change_project" method="POST">
                                    <select name="ilovepdf_select_project">
                                    <?php $total_projects = 0; ?>
                                    <?php foreach ( $stats['projects'] as $project ) : ?>
                                        <option value="<?php echo $total_projects; ?>" <?php echo ( get_option( 'ilovepdf_user_public_key' ) === $project['public_key'] ? 'selected' : '' ); ?>><?php echo $project['name']; ?></option>
                                        <?php ++$total_projects; ?>
                                    <?php endforeach; ?>
                                    </select>
                                    <input type="submit" class="button button-primary pull-right" value="<?php echo __( 'Change Project' ); ?>">
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="panel" style="margin-left: 10px; height: 350px;">
                                    <h3><?php echo __( 'Available files to process', 'ilovepdf' ); ?></h3>
                                    <div class="progress">
                                        <div class="progress__text"><?php echo __( 'Free', 'ilovepdf' ); ?></div>
                                        <div class="progress__total">
                                            <div class="progress__total__percent" style="width: <?php echo ilove_pdf_get_percentage( $stats['files_used'], $stats['free_files_limit'] ); ?>%;"></div>
                                            <div class="progress__total_text"><?php echo ( $stats['files_used'] < $stats['free_files_limit'] ) ? $stats['files_used'] : 250; ?> / <?php echo $stats['free_files_limit']; ?> <?php echo __( 'processed files this month. Free Tier.', 'ilovepdf' ); ?></div>
                                        </div>
                                    </div>
                                    <?php if ( $stats['subscription_files_limit'] ) : ?>
                                        <div class="progress">
                                            <div class="progress__text"><?php echo __( 'Subscription', 'ilovepdf' ); ?></div>
                                            <div class="progress__total">
                                                <?php
                                                    $paid_files = ( $stats['files_used'] < $stats['free_files_limit'] ) ? 0 : $stats['files_used'] - $stats['free_files_limit'];
                                                ?>
                                                <div class="progress__total__percent" style="width: <?php echo ilove_pdf_get_percentage( $paid_files, $stats['subscription_files_limit'] ); ?>%;"></div>
                                                <div class="progress__total_text"><?php echo $paid_files; ?> / <?php echo $stats['subscription_files_limit']; ?> <?php echo ( 'yearly' === $stats['subscription']['period'] ) ? __( 'processed files this month. <strong>Yearly</strong> subscription.', 'ilovepdf' ) : __( 'processed files this month. <strong>Monthly</strong> subscription.', 'ilovepdf' ); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ( $stats['package_files_limit'] ) : ?>
                                        <div class="progress">
                                            <div class="progress__text"><?php echo __( 'Prepaid', 'ilovepdf' ); ?></div>
                                            <div class="progress__total">
                                                <div class="progress__total__percent" style="width: <?php echo ilove_pdf_get_percentage( $stats['package_files_used'], $stats['package_files_limit'] ); ?>%;"></div>
                                                <div class="progress__total_text"><?php echo $stats['package_files_used']; ?> / <?php echo $stats['package_files_limit']; ?> <?php echo __( 'processed files. Prepaid files.', 'ilovepdf' ); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <a href="https://developer.ilovepdf.com/user/account" target="_blank" class="link"><?php echo __( 'Account info', 'ilovepdf' ); ?> (<?php echo get_option( 'ilovepdf_user_email' ); ?>) &raquo;</a>
                                    <a href="https://developer.ilovepdf.com/pricing" target="_blank" class="button button-primary"><?php echo __( 'Buy more files', 'ilovepdf' ); ?></a>
                                    <br /><br />
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="col-md-6">
                                <div class="panel" style="margin-right: 10px; height: 350px;">
                                    <h3 style="margin-bottom: 20px;"><?php echo __( 'Register as iLovePDF developer', 'ilovepdf' ); ?></h3>
                                    <form method="post" id="ilovepdf_register_form" name="ilove_pdf_form_settings_section" action="">
                                        <input type="hidden" name="action" value="ilovepdf_register" />
                                        <p><?php echo __( 'Provide your name and email address to generate keys.', 'ilovepdf' ); ?></p>
                                        <input type="text" id="ilove_pdf_account_name" name="ilove_pdf_account_name" placeholder="<?php echo __( 'Name', 'ilovepdf' ); ?>"><br><br>   
                                        <input type="text" id="ilove_pdf_account_email" name="ilove_pdf_account_email" placeholder="<?php echo __( 'Email', 'ilovepdf' ); ?>" value=""><br><br>   
                                        <input type="password" id="ilove_pdf_account_password" name="ilove_pdf_account_password" placeholder="<?php echo __( 'Password', 'ilovepdf' ); ?>" value=""><br><br> 
                                        <input type="password" id="ilove_pdf_account_confirm_password" name="ilove_pdf_account_confirm_password" placeholder="<?php echo __( 'Confirm Password', 'ilovepdf' ); ?>" value=""><span id="check_password_match"></span><br><br>   
                                        <input type="submit" class="button-primary" id="ilove_pdf_account_register" name="ilove_pdf_account_register" value="<?php echo __( 'Register &amp; Generate keys', 'ilovepdf' ); ?>">  
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel" style="margin-left: 10px; height: 350px;">
                                    <h3 style="margin-bottom: 20px;"><?php echo __( 'Login', 'ilovepdf' ); ?></h3>
                                    <form method="post" name="ilove_pdf_form_settings_section" action="<?php echo admin_url( 'admin-post.php' ); ?>">
                                        <input type="hidden" name="action" value="ilovepdf_login" />  
                                        <p><?php echo __( 'If you have an account, please log in.', 'ilovepdf' ); ?></p>
                                        <input type="text" id="ilove_pdf_account_email" name="ilove_pdf_account_email" placeholder="<?php echo __( 'Email', 'ilovepdf' ); ?>" value=""><br><br>   
                                        <input type="password" id="ilove_pdf_account_password" name="ilove_pdf_account_password" placeholder="<?php echo __( 'Password', 'ilovepdf' ); ?>" value=""><br><br>  
                                        <input type="submit" class="button-primary" id="ilove_pdf_account_login" name="ilove_pdf_account_login" value="<?php echo __( 'Login', 'ilovepdf' ); ?>">  
                                    </form>
                                </div>
                            </div>
                            <script type="text/javascript">
                                jQuery( document ).ready( function( $ ) {
                                    $("#ilove_pdf_account_confirm_password").keyup(function(){
                                        var password = $("#ilove_pdf_account_password").val();
                                        var confirmPassword = $("#ilove_pdf_account_confirm_password").val();

                                        if (password !==  confirmPassword){
                                            $("#check_password_match").html(" <?php echo __( 'Incorrect password.' ); ?>");
                                            $("#check_password_match").css("color","red");
                                            $("#ilovepdf_register_form").attr("action", "#");
                                        }else{
                                            $("#check_password_match").html(" <?php echo __( 'Correct password.' ); ?>");
                                            $("#check_password_match").css("color","green");
                                            $("#ilovepdf_register_form").attr("action", "<?php echo admin_url( 'admin-post.php' ); ?>");
                                        }
                                    });
                                });
                            </script>
                        <?php endif; ?>
                    </div>
                </div>        
            </div>
 			<?php elseif ( 'compress_options' === $active_tab ) : ?>
            <div class="wrap">
                <div class="panel">
                    <form method="post" name="ilove_pdf_form_compress" action="options.php">
         				<?php settings_fields( 'ilove_pdf_display_settings_compress' ); ?>
        		        <?php do_settings_sections( 'ilove_pdf_display_settings_compress' ); ?>
                        <?php submit_button(); ?>
                    </form>
                </div>
            </div>
            <?php elseif ( 'watermark_options' === $active_tab ) : ?>
            <div class="wrap">
                <div class="panel">
                    <form method="post" name="ilove_pdf_form_watermark" action="options.php">
                        <?php settings_fields( 'ilove_pdf_display_settings_watermark' ); ?>
                        <?php do_settings_sections( 'ilove_pdf_display_settings_watermark' ); ?>
                        <?php submit_button(); ?>
                    </form>
                </div>
                <div class="panel">
                    <form method="post" name="ilove_pdf_form_watermark_format" action="options.php">
                        <div class="">
                            <?php settings_fields( 'ilove_pdf_display_settings_format_watermark' ); ?>
                            <?php do_settings_sections( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section' ); ?>
                            <table class="form-table">
                                <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_vertical' ); ?></tr>
                                <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_horizontal' ); ?></tr>
                                <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_mode' ); ?></tr>
                            </table>
                            <?php
                                $options     = get_option( 'ilove_pdf_display_settings_format_watermark' );
                                $div_display = ( isset( $options['ilove_pdf_format_watermark_mode'] ) ? $options['ilove_pdf_format_watermark_mode'] : '0' );
                            ?>
                            <div class="watermark-mode" id="div-mode0" style="<?php echo ( 0 === $div_display ? '' : 'display: none' ); ?>">
                                <table class="form-table">
                                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_text' ); ?></tr>
                                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_size' ); ?></tr>
                                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_font_family' ); ?></tr>
                                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_text_color' ); ?></tr>
                                </table>
                            </div>
                            <div class="watermark-mode" id="div-mode1" style="<?php echo ( 1 === $div_display ? '' : 'display: none' ); ?>">
                                <table class="form-table">
                                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_image' ); ?></tr>
                                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_opacity' ); ?></tr>
                                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_rotation' ); ?></tr>
                                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_layer' ); ?></tr>
                                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_mosaic' ); ?></tr>
                                </table>
                            </div>
                            <?php submit_button(); ?>
                        </div>
                    </form>
                </div>
            </div>
 			<?php endif; ?>

	</div>

	<?php
}