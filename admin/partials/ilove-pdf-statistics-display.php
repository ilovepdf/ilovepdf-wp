<?php
/**
 * Function that shows the statistics page.
 */
function ilove_pdf_content_page_statistics() {

    if ( isset( $_POST['file'] ) ) {
        if ( isset( $_GET['tab'] ) && strcmp( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), 'compress_statistic' ) === 0 ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            ilove_pdf_compress_pdf( (int) sanitize_text_field( wp_unslash( $_POST['file'] ) ) );
        } elseif ( isset( $_GET['tab'] ) && strcmp( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), 'watermark_statistic' ) === 0 ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            ilove_pdf_watermark_pdf( (int) sanitize_text_field( wp_unslash( $_POST['file'] ) ) );
        }
    } elseif ( isset( $_POST['multi'] ) ) {
        if ( isset( $_GET['tab'] ) && strcmp( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), 'compress_statistic' ) === 0 ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            ilove_pdf_compress_pdf( null );
        } elseif ( isset( $_GET['tab'] ) && strcmp( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), 'watermark_statistic' ) === 0 ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            ilove_pdf_watermark_pdf( null );
        }
    }

    $logo_svg = ILOVE_PDF_ASSETS_PLUGIN_PATH . 'assets/img/logo_ilovepdf.svg';

	$stats                    = ilove_pdf_get_statistics();
    $options_general_settings = get_option( 'ilove_pdf_display_general_settings' );
    $backup_files_is_active   = (int) $options_general_settings['ilove_pdf_general_backup'];

	?>
    <div class="wrap">
        <h2 class="plugin-logo-full"><img src="<?php echo esc_url( $logo_svg ); ?>" alt="logo ilovepdf" /></h2>
        <?php if ( get_option( 'ilovepdf_user_id' ) ) : ?>         
            <?php
                $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'statistic_statistic'; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            ?>
             
            <h2 class="nav-tab-wrapper">
                <a href="?page=ilove-pdf-content-statistics&tab=statistic_statistic" class="nav-tab <?php echo 'statistic_statistic' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php esc_html_e( 'Overview', 'ilove-pdf' ); ?></a>
                <a href="?page=ilove-pdf-content-statistics&tab=compress_statistic" class="nav-tab <?php echo 'compress_statistic' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php esc_html_e( 'Compress PDF', 'ilove-pdf' ); ?></a>
                <a href="?page=ilove-pdf-content-statistics&tab=watermark_statistic" class="nav-tab <?php echo 'watermark_statistic' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php esc_html_e( 'Watermark', 'ilove-pdf' ); ?></a>
            </h2>
             	        
             
          		<?php if ( 'statistic_statistic' === $active_tab ) : ?>
                    <div class="wrap">
                        <div class="container no-center">
                            <div class="row">
                                <div class="col-md-6 panel">
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
                                    <a href="https://iloveapi.com/user/account" class="link" target="_blank"><?php esc_html_e( 'Account info', 'ilove-pdf' ); ?> (<?php echo esc_attr( get_option( 'ilovepdf_user_email' ) ); ?>) &raquo;</a>
                                    <a href="https://iloveapi.com/pricing" target="_blank" class="button button-primary"><?php esc_html_e( 'Buy more credits', 'ilove-pdf' ); ?></a>
                                </div>
                                <div class="col-md-5 col-md-offset-1 panel panel-margin-left">
                                    <h3>Tools</h3>
                                    <a href="?page=ilove-pdf-content-statistics&tab=compress_statistic" class="button button-primary"><?php esc_html_e( 'Go to Compress PDF tab', 'ilove-pdf' ); ?></a>
                                    <a href="?page=ilove-pdf-content-statistics&tab=watermark_statistic" class="button button-primary"><?php esc_html_e( 'Go to Watermark tab', 'ilove-pdf' ); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    

     			<?php elseif ( 'compress_statistic' === $active_tab ) : ?>
                    <?php
                    if ( isset( $_POST['array_ids'] ) && isset( $_POST['nonce_ilove_pdf_bulk_actions'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce_ilove_pdf_bulk_actions'] ) ) ) {

                        ?>
                        <script type="text/javascript">
                        document.onreadystatechange = function(){
                            if(document.readyState === 'complete'){
                                window.exportedCompressMultiPDF(<?php echo wp_json_encode( wp_unslash( ilove_pdf_array_sanitize_text_field( $_POST['array_ids'] ) ) );//phpcs:ignore WordPress.Security.ValidatedSanitizedInput ?>);
                            }
                        }
                        </script>
                        <?php
                    }
                    ?>
                    <div class="wrap">
                        <div class="container no-center">
                            <div class="row">
                                <div class="col-md-12 col-md-offset-1 panel">
                                    <h3><?php esc_html_e( 'Total savings', 'ilove-pdf' ); ?></h3>
                                    <p style="margin-bottom: 20px;">Here you can check how much space you saved.</p>
                                    <div style="overflow: hidden;">
                                        <div class="c100 p<?php echo esc_html( ilove_pdf_get_percentage_compress( ilove_pdf_get_all_pdf_original_size(), ilove_pdf_get_all_pdf_original_size() - ilove_pdf_get_all_pdf_current_size() ) ); ?> green"> 
                                            <span style="top: -15px"><?php esc_html_e( 'Saved!', 'ilove-pdf' ); ?></span>
                                            <?php
                                            $percent = ilove_pdf_get_percentage_compress( ilove_pdf_get_all_pdf_original_size(), ilove_pdf_get_all_pdf_original_size() - ilove_pdf_get_all_pdf_current_size() );
                                            ?>
                                            <span id="stats_total_percentage"><?php echo esc_html( ( $percent > 0 ) ? $percent : 0 ); ?>%</span>
                                            <div class="slice">
                                                <div class="bar"></div>
                                                <div class="fill"></div>
                                            </div>
                                        </div>
                                        <div class="status-c100">
                                            <ul>
                                                <li><strong id="stats_total_files_compressed"><?php echo esc_html( ( get_option( 'ilovepdf_compressed_files' ) > 0 ) ? get_option( 'ilovepdf_compressed_files' ) : 0 ); ?></strong> <?php esc_html_e( 'PDF files compressed', 'ilove-pdf' ); ?></li>                                               
                                                <li><strong id="stats_initial_size"><?php echo esc_html( size_format( ilove_pdf_get_all_pdf_original_size(), 2 ) ); ?></strong> <?php esc_html_e( 'initial size', 'ilove-pdf' ); ?></li>
                                                <li><strong id="stats_current_size"><?php echo esc_html( size_format( ilove_pdf_get_all_pdf_current_size(), 2 ) ); ?></strong> <?php esc_html_e( 'current size', 'ilove-pdf' ); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="multi-process">
                                            <div class="all-compressing pdf-status">
                                                <!-- <?php esc_html_e( 'Processing', 'ilove-pdf' ); ?>... -->
                                                <span></span>
                                                <div class="progress-percent"></div>
                                            </div>
                                            <span class="compress-error pdf-status"><?php esc_html_e( 'Error', 'ilove-pdf' ); ?></span>
                                            <span class="compress-success pdf-status"><?php esc_html_e( 'Finished', 'ilove-pdf' ); ?></span>
                                            <span class="compress-abort pdf-status"><?php esc_html_e( 'Canceled', 'ilove-pdf' ); ?></span>
                                        </div>
                                        <div class="multi-process">
                                            <a href="#" class="button-primary media-ilovepdf-box btn-cancel" id="cancel-compress"><?php esc_html_e( 'Cancel Process', 'ilove-pdf' ); ?></a>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-12 panel" id="compress-pdf-list">
                                    <h3><?php esc_html_e( 'PDFs in your library', 'ilove-pdf' ); ?></h3>
                                    <p>Compress all non compressed PDF in your library at once.</p>
                                    <?php $files = ilove_pdf_initialize_list_compress_pdf(); ?>
                                    <?php
                                        $paged            = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
                                        $query_files_args = array(
                                            'post_type'   => 'attachment',
                                            'post_status' => 'inherit',
                                            'post_mime_type' => 'application/pdf',
                                            'posts_per_page' => 100,
                                            'paged'       => $paged,
                                        );

                                        $query_files = new WP_Query( $query_files_args );
										?>

                                    <!-- New Multi Ajax -->
                                    <?php if ( count( $files ) > 0 ) : ?>
                                            <p><a href="#" class="button-primary media-ilovepdf-box btn-compress-all"><?php printf( 'Compress %s PDF', (int) count( $files ) ); ?></a></p>
                                    <?php endif; ?>
                                    <!-- End Multi Ajax -->
                                    
                                    <?php if ( $query_files->have_posts() ) : ?>
                                    <table class="wp-list-table widefat optimization-pdf">
                                        <thead>
                                            <tr>
                                                <th class="column-primary"><?php esc_html_e( 'File', 'ilove-pdf' ); ?></th>
                                                <th class="column-author"><?php esc_html_e( 'Original Size', 'ilove-pdf' ); ?></th>
                                                <th class="column-author"><?php esc_html_e( 'Compressed Size', 'ilove-pdf' ); ?></th>
                                                <th class="column-author">&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ( $query_files->have_posts() ) :
												$query_files->the_post();
												?>
                                                <tr id="file-row-<?php echo (int) get_the_ID(); ?>">
                                                    <td><a href="<?php echo esc_url( get_edit_post_link( get_the_ID() ) ); ?>"><?php echo esc_html( get_the_title() ); ?></a></td>
                                                    <?php if ( ! ilove_pdf_is_file_compressed( get_the_ID() ) ) : ?>
                                                        <td><?php echo esc_html( size_format( filesize( get_attached_file( get_the_ID() ) ), 2 ) ); ?></td>
                                                        <td></td>
                                                    <?php else : ?>
                                                        <td><?php echo esc_html( size_format( get_post_meta( get_the_ID(), '_wp_attached_original_size', true ), 2 ) ); ?></td>
                                                        <td><?php echo esc_html( size_format( get_post_meta( get_the_ID(), '_wp_attached_compress_size', true ), 2 ) ); ?></td>
                                                    <?php endif; ?>
                                                    <td>
                                                    <?php if ( ! ilove_pdf_is_file_compressed( get_the_ID() ) ) : ?>
                                                        <a href="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>?action=ilovepdf_compress&id=<?php echo (int) get_the_ID(); ?>" class="button-primary media-ilovepdf-box btn-compress"><?php esc_html_e( 'Compress', 'ilove-pdf' ); ?></a>
                                                        <span class="compressing pdf-status"><?php esc_html_e( 'Compressing', 'ilove-pdf' ); ?>...</span>
                                                        <span class="error pdf-status"><?php esc_html_e( 'Error', 'ilove-pdf' ); ?></span>
                                                        <span class="success pdf-status"><?php esc_html_e( 'Completed', 'ilove-pdf' ); ?></span>
                                                    <?php else : ?>
                                                            <?php $original_current_file_size = get_post_meta( get_the_ID(), '_wp_attached_original_size', true ); ?>
                                                            <?php $compress_file_size = get_post_meta( get_the_ID(), '_wp_attached_compress_size', true ); ?>
                                                            <span style="margin-right: 10px;">
                                                                <?php echo esc_html( ilove_pdf_get_percentage_compress( $original_current_file_size, $original_current_file_size - $compress_file_size ) . '%' ); ?>
                                                            </span>
                                                            <?php if ( $backup_files_is_active ) : ?>
                                                                <?php if ( get_post_meta( get_the_ID(), '_wp_attached_file_backup', true ) ) : ?>
                                                                    <a class="btn-restore button-secondary" href="<?php echo esc_url( admin_url( 'admin-post.php' ) . '?action=ilovepdf_restore&id=' . get_the_ID() . '&nonce_ilove_pdf_restore=' . wp_create_nonce( 'admin-post' ) ); ?>"><?php esc_html_e( 'Restore original file', 'ilove-pdf' ); ?></a>
                                                                    <span class="loading pdf-status"><?php esc_html_e( 'Loading', 'ilove-pdf' ); ?>...</span>
                                                                    <span class="error pdf-status"><?php esc_html_e( 'Error', 'ilove-pdf' ); ?></span>
                                                                    <span class="success pdf-status"><?php esc_html_e( 'Completed, please refresh the page.', 'ilove-pdf' ); ?></span>
                                                                    <div id="dialog"><div class="no-close"></div></div>
                                                                <?php endif; ?>
                                                            <?php endif; ?>                                                 
                                                    <?php endif; ?>
                                                    </td>
                                                </tr>
												<?php wp_reset_postdata(); ?>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                    <div class="tablenav bottom" style="margin-bottom: 20px;">
                                        <div class="tablenav-pages">
                                        <?php if ( count( $files ) > 0 ) : ?> 
                                            <span class="displaying-num"><?php echo count( $files ); ?> <?php esc_html_e( 'PDFs non compressed', 'ilove-pdf' ); ?></span>
                                        <?php endif; ?>
                                        <?php
                                        $args_paginate_links = array(
                                            'base'    => 'upload.php%_%',
                                            'format'  => '?paged=%#%',
                                            'current' => max( 1, isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1 ), //phpcs:ignore WordPress.Security.NonceVerification.Recommended
                                            'total'   => $query_files->max_num_pages,
                                        );

                                        echo ! empty( paginate_links( $args_paginate_links ) ) ? wp_kses_post( paginate_links( $args_paginate_links ) ) : '';
										?>
                                        </div>
                                        <br class="clear">
                                    </div>
                                    <!-- New Multi Ajax -->
                                        <?php if ( count( $files ) > 0 ) : ?>
                                            <a href="#" class="button-primary media-ilovepdf-box btn-compress-all"><?php printf( 'Compress %s PDF', (int) count( $files ) ); ?></a>
                                        <?php endif; ?>
                                    <!-- End Multi Ajax --> 
                                    <?php else : ?>
                                        <span class="files-not-found"><?php esc_html_e( 'No pdf files found', 'ilove-pdf' ); ?></span>
                                    <?php endif; ?>
                                    <a href="options-general.php?page=ilove-pdf-content-setting" class="button button-primary"><?php esc_html_e( 'Settings', 'ilove-pdf' ); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
     			<?php elseif ( 'watermark_statistic' === $active_tab ) : ?>
                    <?php
                    if ( isset( $_POST['array_ids'] ) && isset( $_POST['nonce_ilove_pdf_bulk_actions'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce_ilove_pdf_bulk_actions'] ) ) ) {
                        ?>
                        <script type="text/javascript">
                        document.onreadystatechange = function(){
                            if(document.readyState === 'complete'){
                                window.exportedWatermarkMultiPDF(<?php echo wp_json_encode( wp_unslash( ilove_pdf_array_sanitize_text_field( $_POST['array_ids'] ) ) );//phpcs:ignore WordPress.Security.ValidatedSanitizedInput ?>);
                            }
                        }
                        </script>
                        <?php
                    }
                    ?>
                    <div class="wrap">
                    <?php settings_errors(); ?>
                        <div class="container no-center">
                            <div class="row">
                                <div class="col-md-12 col-md-offset-1 panel">
                                    <h3><?php esc_html_e( 'Total PDF files stamped', 'ilove-pdf' ); ?></h3>
                                    <p style="margin-bottom: 20px;">Here you can check how many PDF files have been stamped.</p>
                                    <h1><strong id="stats_total_files_watermarked"><?php echo esc_html( get_option( 'ilovepdf_watermarked_files' ) ? get_option( 'ilovepdf_watermarked_files' ) : 0 ); ?></strong> <?php esc_html_e( 'PDF files have been stamped!', 'ilove-pdf' ); ?></h1>

                                    <div class="row">
                                        <div class="multi-process">
                                            <div class="all-applying-watermark pdf-status">
                                                <!-- <?php esc_html_e( 'Processing', 'ilove-pdf' ); ?>... -->
                                                <span></span>
                                                <div class="progress-percent"></div>
                                            </div>
                                            <span class="applied-error pdf-status"><?php esc_html_e( 'Error', 'ilove-pdf' ); ?></span>
                                            <span class="applied-success pdf-status"><?php esc_html_e( 'Finished', 'ilove-pdf' ); ?></span>
                                            <span class="applied-abort pdf-status"><?php esc_html_e( 'Canceled', 'ilove-pdf' ); ?></span>
                                        </div>
                                        <div class="multi-process">
                                            <a href="#" class="button-primary media-ilovepdf-box btn-cancel" id="cancel-watermark"><?php esc_html_e( 'Cancel Process', 'ilove-pdf' ); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 panel" id="watermark-pdf-list">
                                    <h3><?php esc_html_e( 'PDFs in your library', 'ilove-pdf' ); ?></h3>
                                    <p>Stamp all non stamped PDF in your library at once.</p>
                                    <?php $files = ilove_pdf_initialize_list_watermark_pdf(); ?>
                                    <?php
                                        $paged            = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
                                        $query_files_args = array(
                                            'post_type'   => 'attachment',
                                            'post_status' => 'inherit',
                                            'post_mime_type' => 'application/pdf',
                                            'posts_per_page' => 100,
                                            'paged'       => $paged,
                                        );

                                        $query_files = new WP_Query( $query_files_args );
										?>

                                    <!-- New Multi Ajax -->
                                    <?php if ( count( $files ) > 0 ) : ?>
                                        <p><a href="#" class="button-primary media-ilovepdf-box btn-watermark-all"><?php printf( 'Apply watermark in %s PDF', (int) count( $files ) ); ?></a></p>
                                    <?php endif; ?>
                                    <!-- End Multi Ajax -->


                                    <?php if ( $query_files->have_posts() ) : ?>
                                    <table class="wp-list-table widefat optimization-pdf">
                                        <thead>
                                            <tr>
                                                <th class="column-primary"><?php esc_html_e( 'File', 'ilove-pdf' ); ?></th>
                                                <th class="column-author"><?php esc_html_e( 'Size', 'ilove-pdf' ); ?></th>
                                                <th class="column-author">&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ( $query_files->have_posts() ) :
												$query_files->the_post();
												?>
                                                <tr id="file-row-<?php echo (int) get_the_ID(); ?>">
                                                    <td><a href="<?php echo esc_url( get_edit_post_link( get_the_ID() ) ); ?>"><?php echo esc_html( get_the_title() ); ?></a></td>
                                                    <td><?php echo esc_html( size_format( filesize( get_attached_file( get_the_ID() ) ), 2 ) ); ?></td>
                                                    <td>
                                                    <?php if ( ! ilove_pdf_is_file_watermarked( get_the_ID() ) ) : ?>
                                                        <a href="<?php echo esc_url( admin_url( 'admin-post.php?action=ilovepdf_watermark&id=' . get_the_ID() ) ); ?>" class="button-primary media-ilovepdf-box btn-watermark"><?php esc_html_e( 'Apply Watermark', 'ilove-pdf' ); ?></a>
                                                        <span class="applying-watermark pdf-status"><?php esc_html_e( 'Applying Watermark', 'ilove-pdf' ); ?>...</span>
                                                        <span class="error pdf-status"><?php esc_html_e( 'Error', 'ilove-pdf' ); ?></span>
                                                        <span class="success pdf-status"><?php esc_html_e( 'Completed', 'ilove-pdf' ); ?></span>
                                                    <?php elseif ( $backup_files_is_active ) : ?>
                                                        <?php if ( get_post_meta( get_the_ID(), '_wp_attached_file_backup', true ) ) : ?>
                                                            <a class="btn-restore button-secondary" href="<?php echo esc_url( admin_url( 'admin-post.php' ) . '?action=ilovepdf_restore&id=' . get_the_ID() . '&nonce_ilove_pdf_restore=' . wp_create_nonce( 'admin-post' ) ); ?>"><?php esc_html_e( 'Restore original file', 'ilove-pdf' ); ?></a>
                                                            <span class="loading pdf-status"><?php esc_html_e( 'Loading', 'ilove-pdf' ); ?>...</span>
                                                            <span class="error pdf-status"><?php esc_html_e( 'Error', 'ilove-pdf' ); ?></span>
                                                            <span class="success pdf-status"><?php esc_html_e( 'Completed, please refresh the page.', 'ilove-pdf' ); ?></span>
                                                            <div id="dialog"><div class="no-close"></div></div>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                    <div class="tablenav bottom" style="margin-bottom: 20px;">
                                        <div class="tablenav-pages">
                                        <?php if ( count( $files ) > 0 ) : ?>
                                            <span class="displaying-num"><?php echo count( $files ); ?> <?php esc_html_e( 'PDFs without watermark', 'ilove-pdf' ); ?></span>
                                        <?php endif; ?>
                                        <?php
                                        $args_paginate_links = array(
                                            'base'    => 'upload.php%_%',
                                            'format'  => '?paged=%#%',
                                            'current' => max( 1, isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1 ), //phpcs:ignore WordPress.Security.NonceVerification.Recommended
                                            'total'   => $query_files->max_num_pages,
                                        );

                                        echo ! empty( paginate_links( $args_paginate_links ) ) ? wp_kses_post( paginate_links( $args_paginate_links ) ) : '';
										?>

                                        </div>
                                        <br class="clear">
                                    </div>
                                    <!-- New Multi Ajax -->
                                        <?php if ( count( $files ) > 0 ) : ?>
                                            <a href="#" class="button-primary media-ilovepdf-box btn-watermark-all"><?php printf( 'Apply watermark in %s PDF', (int) count( $files ) ); ?></a>
                                        <?php endif; ?>
                                    <!-- End Multi Ajax -->
                                    <?php else : ?>
                                        <span class="files-not-found"><?php esc_html_e( 'No pdf files found', 'ilove-pdf' ); ?></span>
                                    <?php endif; ?>
                                    <a href="options-general.php?page=ilove-pdf-content-setting&tab=watermark_options" class="button button-primary"><?php esc_html_e( 'Settings', 'ilove-pdf' ); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
     			<?php endif; ?>
     		</div>
        <?php else : ?>
            <div class="col-md-12">
                <div class="panel">            
                    <p><?php esc_html_e( 'You must first login or register to use this plugin', 'ilove-pdf' ); ?></p>
                    <a href="<?php echo esc_url( admin_url( 'options-general.php?page=ilove-pdf-content-setting' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Go to Settings', 'ilove-pdf' ); ?></a>
                </div>
            </div>
        <?php endif; ?>
    </div>
	<?php
}