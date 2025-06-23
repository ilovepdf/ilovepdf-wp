<?php
/**
 * View: General settings page
 *
 * @var string $logo_svg
 * @package Ilove_Pdf
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
    
    <?php require_once 'components/tab-menu.php'; ?>
     
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
                                    <h3 style="margin-bottom: 20px;"><?php esc_html_e( 'Register as iLoveAPI developer', 'ilove-pdf' ); ?></h3>
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
            <?php require_once 'compress-settings.php'; ?>
        <?php elseif ( 'watermark_options' === $active_tab ) : ?>
            <?php require_once 'watermark-settings.php'; ?>
        <?php endif; ?>

</div>