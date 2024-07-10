<?php
/**
 * Admin General Settings
 *
 * @link       https://ilovepdf.com/
 * @since      1.0.0
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/admin
 */

/**
 * Register Options
 *
 * @since    1.0.0
 */
function ilove_pdf_register_action() {

    $response = array();

    if ( isset( $_POST['action'] ) && 'ilovepdf_register' === $_POST['action'] && isset( $_POST['nonce_ilove_pdf_register'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce_ilove_pdf_register'] ), 'admin-post' ) ) {

        if ( isset( $_POST['ilove_pdf_account_name'] ) && isset( $_POST['ilove_pdf_account_email'] ) && isset( $_POST['ilove_pdf_account_password'] ) ) {

            if ( get_option( 'ilovepdf_user_register_times' ) !== null ) {
                $response = wp_remote_post(
                    ILOVE_PDF_REGISTER_URL,
                    array(
                        'body' => array(
                            'name'         => sanitize_text_field( wp_unslash( $_POST['ilove_pdf_account_name'] ) ),
                            'email'        => sanitize_email( wp_unslash( $_POST['ilove_pdf_account_email'] ) ),
                            'new_password' => esc_attr( $_POST['ilove_pdf_account_password'] ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,
                            'free_files'   => 0,
                            'wordpress_id' => get_option( 'ilovepdf_wordpress_id' ),
                        ),
                    )
                );
            } else {
                $response = wp_remote_post(
                    ILOVE_PDF_REGISTER_URL,
                    array(
                        'body' => array(
                            'name'         => sanitize_text_field( wp_unslash( $_POST['ilove_pdf_account_name'] ) ),
                            'email'        => sanitize_email( wp_unslash( $_POST['ilove_pdf_account_email'] ) ),
                            'new_password' => esc_attr( $_POST['ilove_pdf_account_password'] ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,
                            'wordpress_id' => get_option( 'ilovepdf_wordpress_id' ),
                        ),
                    )
                );
            }
            if ( isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
                $user = json_decode( $response['body'], true );
                add_option( 'ilovepdf_user_token', $user['token'] );
                add_option( 'ilovepdf_user_email', $user['email'] );
                add_option( 'ilovepdf_user_private_key', $user['projects'][0]['secret_key'] );
                add_option( 'ilovepdf_user_public_key', $user['projects'][0]['public_key'] );
                add_option( 'ilovepdf_user_id', $user['id'] );
                add_option( 'ilovepdf_user_register_times', 1 );
            }
        } else {
            $response['response']['code'] = 400;
        }
    }

    wp_safe_redirect( wp_get_referer() . '&response_code=' . $response['response']['code'] . '&nonce_ilove_pdf_response=' . wp_create_nonce() );
}
add_action( 'admin_post_ilovepdf_register', 'ilove_pdf_register_action' );

/**
 * Login Action
 *
 * @since    1.0.0
 */
function ilove_pdf_login_action() {

    $response = 400;

    if ( isset( $_POST['action'] ) && 'ilovepdf_login' === $_POST['action'] && isset( $_POST['nonce_ilove_pdf_login'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce_ilove_pdf_login'] ), 'admin-post' ) && ( isset( $_POST['ilove_pdf_account_email'] ) && ! empty( $_POST['ilove_pdf_account_email'] ) ) && ( isset( $_POST['ilove_pdf_account_password'] ) && ! empty( $_POST['ilove_pdf_account_password'] ) ) ) {
        $response = wp_remote_post(
            ILOVE_PDF_LOGIN_URL,
            array(
				'body' => array(
					'email'        => sanitize_email( wp_unslash( $_POST['ilove_pdf_account_email'] ) ),
					'password'     => $_POST['ilove_pdf_account_password'], // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
					'wordpress_id' => get_option( 'ilovepdf_wordpress_id' ),
				),
            )
        );

        if ( isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
            $user = json_decode( $response['body'], true );
            add_option( 'ilovepdf_user_token', $user['token'] );
            add_option( 'ilovepdf_user_email', $user['email'] );
            add_option( 'ilovepdf_user_private_key', $user['projects'][0]['secret_key'] );
            add_option( 'ilovepdf_user_public_key', $user['projects'][0]['public_key'] );
            add_option( 'ilovepdf_user_id', $user['id'] );
        }

        wp_safe_redirect( wp_get_referer() . '&response_code=' . $response['response']['code'] . '&nonce_ilove_pdf_response=' . wp_create_nonce() );
        exit();
    }

    wp_safe_redirect( wp_get_referer() . '&response_code=' . $response . '&nonce_ilove_pdf_response=' . wp_create_nonce() );
    exit();
}
add_action( 'admin_post_ilovepdf_login', 'ilove_pdf_login_action' );

/**
 * Logout Action
 *
 * @since    1.0.0
 */
function ilove_pdf_logout_action() {
    if ( isset( $_GET['action'] ) && 'ilovepdf_logout' === $_GET['action'] && isset( $_GET['nonce_ilove_pdf_logout'] ) && wp_verify_nonce( sanitize_key( $_GET['nonce_ilove_pdf_logout'] ), 'admin-post' ) ) {
        delete_option( 'ilovepdf_user_token' );
        delete_option( 'ilovepdf_user_email' );
        delete_option( 'ilovepdf_user_private_key' );
        delete_option( 'ilovepdf_user_public_key' );
        delete_option( 'ilovepdf_user_id' );
    }

    wp_safe_redirect( wp_get_referer() );
}
add_action( 'admin_post_ilovepdf_logout', 'ilove_pdf_logout_action' );

/**
 * Project Action
 *
 * @since    1.0.0
 */
function ilove_pdf_change_project_action() {
    if ( isset( $_GET['action'] ) && 'ilovepdf_change_project' === $_GET['action'] && isset( $_POST['nonce_ilove_pdf_change_project'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce_ilove_pdf_change_project'] ), 'admin-post' ) && isset( $_POST['ilovepdf_select_project'] ) ) {
        $stats = ilove_pdf_get_statistics();
        update_option( 'ilovepdf_user_private_key', $stats['projects'][ sanitize_text_field( wp_unslash( $_POST['ilovepdf_select_project'] ) ) ]['secret_key'] );
        update_option( 'ilovepdf_user_public_key', $stats['projects'][ sanitize_text_field( wp_unslash( $_POST['ilovepdf_select_project'] ) ) ]['public_key'] );
    }

    wp_safe_redirect( wp_get_referer() );
}
add_action( 'admin_post_ilovepdf_change_project', 'ilove_pdf_change_project_action' );

/**
 * Popup Buymore Action
 *
 * @since    1.0.0
 */
function ilove_pdf_popup_buymore_action() {

    add_thickbox();
    echo '<div id="pricing_ilovepdf" style="display:none;"><div class="popup_buymore"><h3>Your files have been exceeded! </h3><p>Please purchase more files to process.</p><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="150px" height="75.51px" viewBox="0 0 300 75.51" enable-background="new 0 0 300 75.51" xml:space="preserve">
                    <g>
                        <path fill="#E5322D" d="M94.313,2.543c-4.785,2.309-8.374,6.2-10.995,10.612C79.104,6.071,72.405,0.326,62.259,0.326   c-10.15,0-22.594,8.614-22.594,23.165c0,14.732,12.293,21.715,18.382,25.658c6.508,4.211,17.613,11.867,25.27,26.036   c7.66-14.168,18.763-21.825,25.273-26.036c4.574-2.965,12.655-7.647,16.387-16.047L94.313,2.543z M93.946,33.938V6.254   l27.684,27.683H93.946z"></path>
                        <g>
                            <path d="M0.458,59.164H3.89c1.088,0,2.344-1.507,2.344-2.511V20.24c0-1.004-1.256-2.427-2.344-2.427H0.458v-8.79h27.54v8.79    h-3.516c-1.088,0-2.26,1.423-2.26,2.427v36.413c0,1.005,1.172,2.511,2.26,2.511h3.516v8.455H0.458V59.164z"></path>
                            <path d="M133.383,59.164h2.846c1.172,0,2.427-1.507,2.427-2.511V20.24c0-1.004-1.256-2.427-2.427-2.427h-2.846v-8.79h26.619    c15.654,0,24.192,5.525,24.192,18.583c0,12.724-9.041,18.499-24.778,18.499h-4.855v13.059h6.78v8.455h-27.958V59.164z     M159.166,37.484c7.031,0,8.873-4.018,8.873-9.626c0-5.525-1.842-9.459-8.873-9.459h-4.855v19.086H159.166z"></path>
                            <path d="M189.3,59.164h2.846c1.172,0,2.427-1.507,2.427-2.511V20.24c0-1.004-1.255-2.427-2.427-2.427H189.3v-8.79h25.531    c20.843,0,31.725,9.041,31.725,28.963c0,19.588-11.049,29.633-32.144,29.633H189.3V59.164z M214.412,58.746    c10.547,0,15.737-6.278,15.737-20.341c0-13.979-5.106-20.007-15.737-20.007h-3.934v40.347H214.412z"></path>
                            <path d="M251.912,59.164h2.846c1.172,0,2.427-1.507,2.427-2.511V20.24c0-1.004-1.255-2.427-2.427-2.427h-2.846v-8.79h47.63v18.081    h-9.375l-1.423-8.622H273.09v16.407h7.617l0.67-5.525H290v20.341h-8.622l-0.67-5.776h-7.617v15.235h6.781v8.455h-27.959V59.164z"></path>
                        </g>
                        <polygon fill="#FFFFFF" points="93.946,33.938 93.946,6.254 121.63,33.938  "></polygon>
                    </g>
                    </svg><div><a href="https://iloveapi.com/pricing" target="_blank" class="button button-primary">' . esc_html( __( 'Accept', 'ilove-pdf' ) ) . '</a> <a href="#" onClick="tb_remove();"  class="button button-primary">' . esc_html( __( 'Cancel', 'ilove-pdf' ) ) . '</a></div></div></div>';
}
add_action( 'admin_footer', 'ilove_pdf_popup_buymore_action' );

/**
 * Initialize Options for the general admin area.
 *
 * @since    2.1.0
 */
function ilove_pdf_initialize_general_options() {

    if ( false === get_option( 'ilove_pdf_display_general_settings' ) ) {
        add_option( 'ilove_pdf_display_general_settings' );
    }

    add_settings_section(
        'general_settings_section',
        '',
        'ilove_pdf_general_options_callback',
        'ilove_pdf_display_general_settings'
    );

    add_settings_field(
        'ilove_pdf_general_backup',
        __( 'Create backup of original files?', 'ilove-pdf' ),
        'ilove_pdf_general_backup_callback',
        'ilove_pdf_display_general_settings',
        'general_settings_section',
        array(
            'No',
            'Yes',
        )
    );

    register_setting(
        'ilove_pdf_display_general_settings',
        'ilove_pdf_display_general_settings'
    );
}
add_action( 'admin_init', 'ilove_pdf_initialize_general_options' );

/**
 * Options General Callback.
 *
 * @since    2.1.0
 */
function ilove_pdf_general_options_callback() {
    echo '<h3>' . esc_html( __( 'Configure your Tools settings.', 'ilove-pdf' ) ) . '</h3>';
}

/**
 * Backup Original File Callback.
 *
 * @since    2.1.0
 * @param    array $args    Arguments options.
 */
function ilove_pdf_general_backup_callback( $args ) {

    $options = get_option( 'ilove_pdf_display_general_settings' );
    $html    = sprintf(
        '<input type="radio" id="ilove_pdf_general_backup" name="ilove_pdf_display_general_settings[ilove_pdf_general_backup]" value="0" %s><label for="ilove_pdf_general_backup">%s</label>&nbsp;
        <input type="radio" id="ilove_pdf_general_backup" name="ilove_pdf_display_general_settings[ilove_pdf_general_backup]" value="1" %s><label for="ilove_pdf_general_backup">%s</label><div><p><small>The backup files can be found on your server:</small></p><p><strong>wp-content/uploads/pdf/backup</strong></p></div>',
        isset( $options['ilove_pdf_general_backup'] ) ? checked( 0, $options['ilove_pdf_general_backup'], false ) : '',
        $args[0],
        isset( $options['ilove_pdf_general_backup'] ) ? checked( 1, $options['ilove_pdf_general_backup'], false ) : 'checked="checked"',
        $args[1]
    );

    echo wp_kses( $html, ilove_pdf_expanded_alowed_tags() );
}
