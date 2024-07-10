<?php
/**
 * Function showing configuration page
 */
function ilove_pdf_content_page_setting() {

    $logo_svg = ILOVE_PDF_ASSETS_PLUGIN_PATH . 'assets/img/logo_ilovepdf.svg';
    $options  = get_option( 'ilove_pdf_display_settings_watermark' );

	?>
	<div class="wrap">
		<div class="plugin-logo">
            <img src="<?php echo esc_url( $logo_svg ); ?>" alt="logo ilovepdf" />
        </div>
        <?php if ( isset( $_GET['response_code'] ) && isset( $_GET['nonce_ilove_pdf_response'] ) && wp_verify_nonce( sanitize_key( $_GET['nonce_ilove_pdf_response'] ) ) ) : ?>

            <?php
            switch ( $_GET['response_code'] ) {
                case 400:
                    echo '<div class="settings-error notice is-dismissible error"><p>' . esc_html( __( 'Bad request.', 'ilove-pdf' ) );
                    break;

                case 401:
                    echo '<div class="settings-error notice is-dismissible error"><p>' . esc_html( __( 'Incorrect email or password.', 'ilove-pdf' ) );
                    break;

                case 200:
                    echo '<div class="settings-error notice is-dismissible updated"><p>' . esc_html( __( 'Welcome!', 'ilove-pdf' ) );
                    break;

                case 500:
                    echo '<div class="settings-error notice is-dismissible error"><p>' . esc_html( __( 'Error on register/login.', 'ilove-pdf' ) );
                    break;
            }
            ?>
            </p></div>
        <?php endif; ?>
        <?php
            $nonce_settings = wp_create_nonce();
            $active_tab     = isset( $_GET['tab'] ) && isset( $_GET['nonce_ilove_pdf_settings_tab'] ) && wp_verify_nonce( sanitize_key( $_GET['nonce_ilove_pdf_settings_tab'] ) ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'setting_options';
        ?>
         
        <h2 class="nav-tab-wrapper">
            <a href="?page=ilove-pdf-content-setting&tab=setting_options&nonce_ilove_pdf_settings_tab=<?php echo sanitize_key( $nonce_settings ); ?>" class="nav-tab <?php echo 'setting_options' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php echo esc_html( __( 'General', 'ilove-pdf' ) ); ?></a>
            <a href="?page=ilove-pdf-content-setting&tab=compress_options&nonce_ilove_pdf_settings_tab=<?php echo sanitize_key( $nonce_settings ); ?>" class="nav-tab <?php echo 'compress_options' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php echo esc_html( __( 'Compress PDF', 'ilove-pdf' ) ); ?></a>
            <a href="?page=ilove-pdf-content-setting&tab=watermark_options&nonce_ilove_pdf_settings_tab=<?php echo sanitize_key( $nonce_settings ); ?>" class="nav-tab <?php echo 'watermark_options' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php echo esc_html( __( 'Watermark', 'ilove-pdf' ) ); ?></a>
        </h2> 	        
      		<?php if ( 'setting_options' === $active_tab ) : ?>
            <div class="wrap">           
                <div class="container no-center">
                    <div class="row">
                        <?php if ( get_option( 'ilovepdf_user_id' ) ) : ?>
                            <?php $stats = ilove_pdf_get_statistics(); ?>
                            <div class="col-md-4">
                                <div class="panel" style="margin-right: 10px;">
                                    <form action="options.php" method="POST">
                                        <?php settings_fields( 'ilove_pdf_display_general_settings' ); ?>
                                        <?php do_settings_sections( 'ilove_pdf_display_general_settings' ); ?>
                                        <?php submit_button(); ?>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="panel" style="margin-left: 10px;">
                                    <h3><?php esc_html_e( 'Credits available to process files', 'ilove-pdf' ); ?></h3>
                                    <div class="progress">
                                        <div class="progress__text"><?php esc_html_e( 'Free', 'ilove-pdf' ); ?></div>
                                        <div class="progress__total">
                                            <div class="progress__total__percent" style="width: <?php echo esc_html( ilove_pdf_get_percentage( $stats['files_used'], $stats['free_files_limit'] ) ); ?>%;"></div>
                                            <div class="progress__total_text"><?php echo esc_html( ( $stats['files_used'] < $stats['free_files_limit'] ) ? $stats['files_used'] : 250 ); ?> / <?php echo esc_html( $stats['free_files_limit'] ); ?> <?php esc_html_e( 'credits used this month. Free Tier.', 'ilove-pdf' ); ?></div>
                                        </div>
                                    </div>
                                    <?php if ( $stats['subscription_files_limit'] ) : ?>
                                        <div class="progress">
                                            <div class="progress__text"><?php esc_html_e( 'Subscription', 'ilove-pdf' ); ?></div>
                                            <div class="progress__total">
                                                <?php
                                                    $paid_files = ( $stats['files_used'] < $stats['free_files_limit'] ) ? 0 : $stats['files_used'] - $stats['free_files_limit'];
                                                ?>
                                                <div class="progress__total__percent" style="width: <?php echo esc_html( ilove_pdf_get_percentage( $paid_files, $stats['subscription_files_limit'] ) ); ?>%;"></div>
                                                <div class="progress__total_text"><?php echo (int) $paid_files; ?> / <?php echo (int) $stats['subscription_files_limit']; ?> <?php echo wp_kses( ( 'yearly' === $stats['subscription']['period'] ) ? __( 'credits used this month. <strong>Yearly</strong> subscription.', 'ilove-pdf' ) : __( 'credits used this month. <strong>Monthly</strong> subscription.', 'ilove-pdf' ), 'ilove_pdf_expanded_alowed_tags' ); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ( $stats['package_files_limit'] ) : ?>
                                        <div class="progress">
                                            <div class="progress__text"><?php esc_html_e( 'Prepaid', 'ilove-pdf' ); ?></div>
                                            <div class="progress__total">
                                                <div class="progress__total__percent" style="width: <?php echo esc_html( ilove_pdf_get_percentage( $stats['package_files_used'], $stats['package_files_limit'] ) ); ?>%;"></div>
                                                <div class="progress__total_text"><?php echo esc_html( $stats['package_files_used'] ); ?> / <?php echo esc_html( $stats['package_files_limit'] ); ?> <?php esc_html_e( 'credits used. Prepaid credits.', 'ilove-pdf' ); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <a href="https://iloveapi.com/login" target="_blank" class="link"><?php esc_html_e( 'Account info', 'ilove-pdf' ); ?> (<?php echo esc_html( get_option( 'ilovepdf_user_email' ) ); ?>) &raquo;</a>
                                    <a href="https://iloveapi.com/pricing" target="_blank" class="button button-primary"><?php esc_html_e( 'Buy more credits', 'ilove-pdf' ); ?></a>
                                </div>
                                <div class="panel" style="margin-left: 10px;">
                                    <h3><?php esc_html_e( 'Account', 'ilove-pdf' ); ?></h3>
                                    <p><i class="fa fa-check" aria-hidden="true"></i> <?php esc_html_e( 'Logged as', 'ilove-pdf' ); ?><strong> <?php echo esc_html( get_option( 'ilovepdf_user_email' ) ); ?></strong>&nbsp;&nbsp;&nbsp;<a href="<?php echo esc_url( add_query_arg( 'nonce_ilove_pdf_logout', wp_create_nonce( 'admin-post' ), admin_url( 'admin-post.php' ) . '?action=ilovepdf_logout' ) ); ?>" class="button button-primary" style="margin-top: 10px;"><?php esc_html_e( 'Logout', 'ilove-pdf' ); ?></a></p>

                                    <hr style="margin: 30px 0px;" />
                                    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>?action=ilovepdf_change_project" method="POST">
                                    <?php wp_nonce_field( 'admin-post', 'nonce_ilove_pdf_change_project' ); ?>
                                    <select name="ilovepdf_select_project">
                                    <?php $total_projects = 0; ?>
                                    <?php foreach ( $stats['projects'] as $project ) : ?>
                                        <option value="<?php echo (int) $total_projects; ?>" <?php echo esc_attr( ( get_option( 'ilovepdf_user_public_key' ) === $project['public_key'] ? 'selected' : '' ) ); ?>><?php echo esc_html( $project['name'] ); ?></option>
                                        <?php ++$total_projects; ?>
                                    <?php endforeach; ?>
                                    </select>
                                    <input type="submit" class="button button-primary pull-right" value="<?php esc_html_e( 'Change Project', 'ilove-pdf' ); ?>">
                                    </form>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="col-md-6">
                                <div class="panel" style="margin-right: 10px; height: 350px;">
                                    <h3 style="margin-bottom: 20px;"><?php esc_html_e( 'Register as iLovePDF developer', 'ilove-pdf' ); ?></h3>
                                    <form method="post" id="ilovepdf_register_form" name="ilove_pdf_form_settings_section" action="">
                                        <input type="hidden" name="action" value="ilovepdf_register" />
                                        <?php wp_nonce_field( 'admin-post', 'nonce_ilove_pdf_register' ); ?>
                                        <p><?php esc_html_e( 'Provide your name and email address to generate keys.', 'ilove-pdf' ); ?></p>
                                        <input type="text" id="ilove_pdf_account_name" name="ilove_pdf_account_name" placeholder="<?php esc_html_e( 'Name', 'ilove-pdf' ); ?>"><br><br>   
                                        <input type="text" id="ilove_pdf_account_email" name="ilove_pdf_account_email" placeholder="<?php esc_html_e( 'Email', 'ilove-pdf' ); ?>" value=""><br><br>   
                                        <input type="password" id="ilove_pdf_account_password" name="ilove_pdf_account_password" placeholder="<?php esc_html_e( 'Password', 'ilove-pdf' ); ?>" value=""><br><br> 
                                        <input type="password" id="ilove_pdf_account_confirm_password" name="ilove_pdf_account_confirm_password" placeholder="<?php esc_html_e( 'Confirm Password', 'ilove-pdf' ); ?>" value=""><span id="check_password_match"></span><br><br>   
                                        <input type="submit" class="button-primary" id="ilove_pdf_account_register" name="ilove_pdf_account_register" value="<?php esc_html_e( 'Register &amp; Generate keys', 'ilove-pdf' ); ?>">  
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel" style="margin-left: 10px; height: 350px;">
                                    <h3 style="margin-bottom: 20px;"><?php esc_html_e( 'Login', 'ilove-pdf' ); ?></h3>
                                    <form method="post" name="ilove_pdf_form_settings_section" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                                        <input type="hidden" name="action" value="ilovepdf_login" />
                                        <?php wp_nonce_field( 'admin-post', 'nonce_ilove_pdf_login' ); ?>  
                                        <p><?php esc_html_e( 'If you have an account, please log in.', 'ilove-pdf' ); ?></p>
                                        <input type="text" id="ilove_pdf_account_email" name="ilove_pdf_account_email" placeholder="<?php esc_html_e( 'Email', 'ilove-pdf' ); ?>" value=""><br><br>   
                                        <input type="password" id="ilove_pdf_account_password" name="ilove_pdf_account_password" placeholder="<?php esc_html_e( 'Password', 'ilove-pdf' ); ?>" value=""><br><br>  
                                        <input type="submit" class="button-primary" id="ilove_pdf_account_login" name="ilove_pdf_account_login" value="<?php esc_html_e( 'Login', 'ilove-pdf' ); ?>">  
                                    </form>
                                </div>
                            </div>
                            <script type="text/javascript">
                                jQuery( document ).ready( function( $ ) {
                                    $("#ilove_pdf_account_confirm_password").keyup(function(){
                                        var password = $("#ilove_pdf_account_password").val();
                                        var confirmPassword = $("#ilove_pdf_account_confirm_password").val();

                                        if (password !==  confirmPassword){
                                            $("#check_password_match").html(" <?php esc_html_e( 'Incorrect password.', 'ilove-pdf' ); ?>");
                                            $("#check_password_match").css("color","red");
                                            $("#ilovepdf_register_form").attr("action", "#");
                                        }else{
                                            $("#check_password_match").html(" <?php esc_html_e( 'Correct password.', 'ilove-pdf' ); ?>");
                                            $("#check_password_match").css("color","green");
                                            $("#ilovepdf_register_form").attr("action", "<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>");
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

                        <div class="ilove_pdf_wrapper_buttons">
                            <?php submit_button(); ?>
                            <a href="<?php echo esc_url( admin_url( 'upload.php?page=ilove-pdf-content-statistics&tab=compress_statistic' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Go to Compress Tool', 'ilove-pdf' ); ?></a>
                        </div>
                    </form>
                </div>
            </div>
            <?php elseif ( 'watermark_options' === $active_tab ) : ?>
            <div class="wrap">
                <div class="panel">
                    <form method="post" name="ilove_pdf_form_watermark" action="options.php">
                        <?php settings_fields( 'ilove_pdf_display_settings_watermark' ); ?>
                        <?php do_settings_sections( 'ilove_pdf_display_settings_watermark' ); ?>

                        <div class="ilove_pdf_wrapper_buttons">
                            <?php submit_button(); ?>
                            <a href="<?php echo esc_url( admin_url( 'upload.php?page=ilove-pdf-content-statistics&tab=watermark_statistic' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Go to Watermark Tool', 'ilove-pdf' ); ?></a>
                        </div>
                    </form>
                </div>

                <?php if ( isset( $options['ilove_pdf_watermark_active'] ) ) : ?>
                <div class="panel">
                    <form method="post" name="ilove_pdf_form_watermark_format" action="options.php">
                        <div class="">
                            <?php settings_fields( 'ilove_pdf_display_settings_format_watermark' ); ?>
                            <?php do_settings_sections( 'ilove_pdf_display_settings_format_watermark' ); ?>
                            <table class="form-table">
                                <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_vertical' ); ?></tr>
                                <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_horizontal' ); ?></tr>
                                <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_mode' ); ?></tr>
                            </table>
                            <?php
                                $options     = get_option( 'ilove_pdf_display_settings_format_watermark' );
                                $div_display = ( isset( $options['ilove_pdf_format_watermark_mode'] ) ? $options['ilove_pdf_format_watermark_mode'] : 0 );
                            ?>
                            <div class="watermark-mode" id="div-mode0" style="<?php echo ( 0 === (int) $div_display ? '' : 'display: none' ); ?>">
                                <table class="form-table">
                                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_text' ); ?></tr>
                                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_size' ); ?></tr>
                                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_font_family' ); ?></tr>
                                    <tr><?php do_settings_fields( 'ilove_pdf_display_settings_format_watermark', 'format_watermark_settings_section_text_color' ); ?></tr>
                                </table>
                            </div>
                            <div class="watermark-mode" id="div-mode1" style="<?php echo ( 1 === (int) $div_display ? '' : 'display: none' ); ?>">
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
                <?php endif; ?>
            </div>
 			<?php endif; ?>

	</div>

	<?php
}