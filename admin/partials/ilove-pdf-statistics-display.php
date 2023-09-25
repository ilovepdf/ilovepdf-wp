<?php
/**
 * Function that shows the statistics page.
 */
function ilove_pdf_content_page_statistics() {
    if ( isset( $_POST['file'] ) ) {
        if ( isset( $_GET['tab'] ) && strcmp( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), 'compress_statistic' ) === 0 ) {
            ilove_pdf_compress_pdf( sanitize_text_field( wp_unslash( $_POST['file'] ) ) );
        } elseif ( isset( $_GET['tab'] ) && strcmp( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), 'watermark_statistic' ) === 0 ) {
            ilove_pdf_watermark_pdf( sanitize_text_field( wp_unslash( $_POST['file'] ) ) );
        }
    } elseif ( isset( $_POST['multi'] ) ) {
        if ( isset( $_GET['tab'] ) && strcmp( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), 'compress_statistic' ) === 0 ) {
            ilove_pdf_compress_pdf( null );
        } elseif ( isset( $_GET['tab'] ) && strcmp( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), 'watermark_statistic' ) === 0 ) {
            ilove_pdf_watermark_pdf( null );
        }
    }

    $logo_svg = ILOVE_PDF_ASSETS_PLUGIN_PATH . 'assets/img/logo_ilovepdf.svg';

	$stats = ilove_pdf_get_statistics();
	?>
    <div class="wrap">
        <h2 class="plugin-logo-full"><img src="<?php echo esc_url( $logo_svg ); ?>" alt="logo ilovepdf" /></h2>
        <?php if ( get_option( 'ilovepdf_user_id' ) ) : ?>         
            <?php
                $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'statistic_statistic';
            ?>
             
            <h2 class="nav-tab-wrapper">
                <a href="?page=ilove-pdf-content-statistics&tab=statistic_statistic" class="nav-tab <?php echo 'statistic_statistic' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php esc_html_e( 'Overview', 'ilovepdf' ); ?></a>
                <a href="?page=ilove-pdf-content-statistics&tab=compress_statistic" class="nav-tab <?php echo 'compress_statistic' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php esc_html_e( 'Compress PDF', 'ilovepdf' ); ?></a>
                <a href="?page=ilove-pdf-content-statistics&tab=watermark_statistic" class="nav-tab <?php echo 'watermark_statistic' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php esc_html_e( 'Watermark', 'ilovepdf' ); ?></a>
            </h2>
             	        
             
          		<?php if ( 'statistic_statistic' === $active_tab ) : ?>
                    <div class="wrap">
                        <div class="container no-center">
                            <div class="row">
                                <div class="col-md-6 panel">
                                    <h3><?php esc_html_e( 'Available files to process', 'ilovepdf' ); ?></h3>
                                    <div class="progress">
                                        <div class="progress__text"><?php esc_html_e( 'Free', 'ilovepdf' ); ?></div>
                                        <div class="progress__total">
                                            <div class="progress__total__percent" style="width: <?php echo esc_html( ilove_pdf_get_percentage( $stats['files_used'], $stats['free_files_limit'] ) ); ?>%;"></div>
                                            <div class="progress__total_text"><?php echo esc_html( ( $stats['files_used'] < $stats['free_files_limit'] ) ? $stats['files_used'] : 250 ); ?> / <?php echo esc_html( $stats['free_files_limit'] ); ?> <?php esc_html_e( 'processed files this month. Free Tier.', 'ilovepdf' ); ?></div>
                                        </div>
                                    </div>
                                    <?php if ( $stats['subscription_files_limit'] ) : ?>
                                        <div class="progress">
                                            <div class="progress__text"><?php esc_html_e( 'Subscription', 'ilovepdf' ); ?></div>
                                            <div class="progress__total">
                                                <?php
                                                    $paid_files = ( $stats['files_used'] < $stats['free_files_limit'] ) ? 0 : $stats['files_used'] - $stats['free_files_limit'];
                                                ?>
                                                <div class="progress__total__percent" style="width: <?php echo esc_html( ilove_pdf_get_percentage( $paid_files, $stats['subscription_files_limit'] ) ); ?>%;"></div>
                                                <div class="progress__total_text"><?php echo esc_html( $paid_files ); ?> / <?php echo esc_html( $stats['subscription_files_limit'] ); ?> <?php echo wp_kses( ( 'yearly' === $stats['subscription']['period'] ) ? __( 'processed files this month. <strong>Yearly</strong> subscription.', 'ilovepdf' ) : __( 'processed files this month. <strong>Monthly</strong> subscription.', 'ilovepdf' ), 'ilove_pdf_expanded_alowed_tags' ); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ( $stats['package_files_limit'] ) : ?>
                                        <div class="progress">
                                            <div class="progress__text"><?php esc_html_e( 'Prepaid', 'ilovepdf' ); ?></div>
                                            <div class="progress__total">
                                                <div class="progress__total__percent" style="width: <?php echo esc_html( ilove_pdf_get_percentage( $stats['package_files_used'], $stats['package_files_limit'] ) ); ?>%;"></div>
                                                <div class="progress__total_text"><?php echo esc_html( $stats['package_files_used'] ); ?> / <?php echo esc_html( $stats['package_files_limit'] ); ?> <?php esc_html_e( 'processed files. Prepaid files.', 'ilovepdf' ); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <a href="https://developer.ilovepdf.com/user/account" class="link" target="_blank"><?php esc_html_e( 'Account info', 'ilovepdf' ); ?> (<?php echo esc_attr( get_option( 'ilovepdf_user_email' ) ); ?>) &raquo;</a>
                                    <a href="https://developer.ilovepdf.com/pricing" target="_blank" class="button button-primary"><?php esc_html_e( 'Buy more files', 'ilovepdf' ); ?></a>
                                </div>
                                <div class="col-md-5 col-md-offset-1 panel panel-margin-left">
                                    <h3>Tools</h3>
                                    <a href="?page=ilove-pdf-content-statistics&tab=compress_statistic" class="button button-primary"><?php esc_html_e( 'Go to Compress PDF tab', 'ilovepdf' ); ?></a>
                                    <a href="?page=ilove-pdf-content-statistics&tab=watermark_statistic" class="button button-primary"><?php esc_html_e( 'Go to Watermark tab', 'ilovepdf' ); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    

     			<?php elseif ( 'compress_statistic' === $active_tab ) : ?>
                    <?php
                    if ( isset( $_POST['array_ids'] ) ) {
                        ?>
                        <script type="text/javascript">
                        document.onreadystatechange = function(){
                            if(document.readyState === 'complete'){
                                window.exportedCompressMultiPDF(<?php echo json_encode( wp_unslash( $_POST['array_ids'] ) ); ?>);
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
                                    <h3><?php esc_html_e( 'Total savings', 'ilovepdf' ); ?></h3>
                                    <p style="margin-bottom: 20px;">Here you can check how much space you saved.</p>
                                    <div style="overflow: hidden;">
                                        <div class="c100 p<?php echo esc_html( ilove_pdf_get_percentage_compress( ilove_pdf_get_all_pdf_original_size(), ilove_pdf_get_all_pdf_original_size() - ilove_pdf_get_all_pdf_current_size() ) ); ?> green"> 
                                            <span style="top: -15px"><?php esc_html_e( 'Saved!', 'ilovepdf' ); ?></span>
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
                                                <li><strong id="stats_total_files_compressed"><?php echo esc_html( ( get_option( 'ilovepdf_compressed_files' ) > 0 ) ? get_option( 'ilovepdf_compressed_files' ) : 0 ); ?></strong> <?php esc_html_e( 'PDF files compressed', 'ilovepdf' ); ?></li>                                               
                                                <li><strong id="stats_initial_size"><?php echo esc_html( size_format( ilove_pdf_get_all_pdf_original_size(), 2 ) ); ?></strong> <?php esc_html_e( 'initial size', 'ilovepdf' ); ?></li>
                                                <li><strong id="stats_current_size"><?php echo esc_html( size_format( ilove_pdf_get_all_pdf_current_size(), 2 ) ); ?></strong> <?php esc_html_e( 'current size', 'ilovepdf' ); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="multi-process">
                                            <div class="all-compressing pdf-status">
                                                <!-- <?php esc_html_e( 'Processing', 'ilovepdf' ); ?>... -->
                                                <span></span>
                                                <div class="progress-percent"></div>
                                            </div>
                                            <span class="compress-error pdf-status"><?php esc_html_e( 'Error', 'ilovepdf' ); ?></span>
                                            <span class="compress-success pdf-status"><?php esc_html_e( 'Finished', 'ilovepdf' ); ?></span>
                                            <span class="compress-abort pdf-status"><?php esc_html_e( 'Canceled', 'ilovepdf' ); ?></span>
                                        </div>
                                        <div class="multi-process">
                                            <a href="#" class="button-primary media-ilovepdf-box btn-cancel" id="cancel-compress"><?php esc_html_e( 'Cancel Process', 'ilovepdf' ); ?></a>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-12 panel" id="compress-pdf-list">
                                    <h3><?php esc_html_e( 'PDFs in your library', 'ilovepdf' ); ?></h3>
                                    <p>Compress all non compressed PDF in your library at once.</p>
                                    <?php $files = ilove_pdf_initialize_list_compress_pdf(); ?>
                                    <?php
                                        $paged            = isset( $_GET['paged'] ) ? sanitize_url( wp_unslash( $_GET['paged'] ) ) : 1;
                                        $query_files_args = array(
                                            'post_type'   => 'attachment',
                                            'post_status' => 'inherit',
                                            'post_mime_type' => 'application/pdf',
                                            'posts_per_page' => 150,
                                            'paged'       => $paged,
                                        );

                                        $query_files = new WP_Query( $query_files_args );
										?>

                                    <!-- New Multi Ajax -->
                                    <?php if ( count( $files ) > 0 ) : ?>
                                            <p><a href="#" class="button-primary media-ilovepdf-box btn-compress-all"><?php esc_html_e( 'Compress ' . count( $files ) . ' PDF', 'ilovepdf' ); ?></a></p>
                                    <?php endif; ?>
                                    <!-- End Multi Ajax -->
                                    
                                    <?php if ( $query_files->have_posts() ) : ?>
                                    <table class="wp-list-table widefat optimization-pdf">
                                        <thead>
                                            <tr>
                                                <th class="column-primary"><?php esc_html_e( 'File', 'ilovepdf' ); ?></th>
                                                <th class="column-author"><?php esc_html_e( 'Original Size', 'ilovepdf' ); ?></th>
                                                <th class="column-author"><?php esc_html_e( 'Compressed Size', 'ilovepdf' ); ?></th>
                                                <th class="column-author">&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ( $query_files->have_posts() ) :
												$query_files->the_post();
												?>
                                                <tr id="file-row-<?php echo esc_html( get_the_ID() ); ?>">
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
                                                        <a href="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>?action=ilovepdf_compress&id=<?php echo esc_html( get_the_ID() ); ?>&nonce_ilove_pdf_compress=<?php echo esc_html( wp_create_nonce( 'admin-post' ) ); ?>" class="button-primary media-ilovepdf-box btn-compress"><?php esc_html_e( 'Compress', 'ilovepdf' ); ?></a>
                                                        <span class="compressing pdf-status"><?php esc_html_e( 'Compressing', 'ilovepdf' ); ?>...</span>
                                                        <span class="error pdf-status"><?php esc_html_e( 'Error', 'ilovepdf' ); ?></span>
                                                        <span class="success pdf-status"><?php esc_html_e( 'Completed', 'ilovepdf' ); ?></span>
                                                    <?php else : ?>
                                                            <?php $original_current_file_size = get_post_meta( get_the_ID(), '_wp_attached_original_size', true ); ?>
                                                            <?php $compress_file_size = get_post_meta( get_the_ID(), '_wp_attached_compress_size', true ); ?>
                                                            <?php echo esc_html( ilove_pdf_get_percentage_compress( $original_current_file_size, $original_current_file_size - $compress_file_size ) . '%' ); ?>                                                 
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
                                            <span class="displaying-num"><?php echo count( $files ); ?> <?php esc_html_e( 'PDFs non compressed', 'ilovepdf' ); ?></span>
                                        <?php endif; ?>
                                        <?php
                                        echo wp_kses_post(
                                            paginate_links(
                                                array(
													'base' => 'upload.php%_%',
													'format' => '?paged=%#%',
													'current' => max( 1, isset( $_GET['paged'] ) ? sanitize_url( wp_unslash( $_GET['paged'] ) ) : 1 ),
													'total' => $query_files->max_num_pages,
                                                )
                                            )
                                        );
										?>
                                        </div>
                                        <br class="clear">
                                    </div>
                                    <!-- New Multi Ajax -->
                                        <?php if ( count( $files ) > 0 ) : ?>
                                            <a href="#" class="button-primary media-ilovepdf-box btn-compress-all"><?php esc_html_e( 'Compress ' . count( $files ) . ' PDF', 'ilovepdf' ); ?></a>
                                        <?php endif; ?>
                                    <!-- End Multi Ajax --> 
                                    <?php else : ?>
                                        <span class="files-not-found"><?php esc_html_e( 'No pdf files found', 'ilovepdf' ); ?></span>
                                    <?php endif; ?>
                                    <a href="options-general.php?page=ilove-pdf-content-setting" class="button button-primary"><?php esc_html_e( 'Settings', 'ilovepdf' ); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
     			<?php elseif ( 'watermark_statistic' === $active_tab ) : ?>
                    <?php
                    if ( isset( $_POST['array_ids'] ) ) {
                        ?>
                        <script type="text/javascript">
                        document.onreadystatechange = function(){
                            if(document.readyState === 'complete'){
                                window.exportedWatermarkMultiPDF(<?php echo json_encode( wp_unslash( $_POST['array_ids'] ) ); ?>);
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
                                    <h3><?php esc_html_e( 'Total PDF files stamped', 'ilovepdf' ); ?></h3>
                                    <p style="margin-bottom: 20px;">Here you can check how many PDF files have been stamped.</p>
                                    <h1><strong id="stats_total_files_watermarked"><?php echo esc_html( get_option( 'ilovepdf_watermarked_files' ) ? get_option( 'ilovepdf_watermarked_files' ) : 0 ); ?></strong> <?php esc_html_e( 'PDF files have been stamped!', 'ilovepdf' ); ?></h1>

                                    <div class="row">
                                        <div class="multi-process">
                                            <div class="all-applying-watermark pdf-status">
                                                <!-- <?php esc_html_e( 'Processing', 'ilovepdf' ); ?>... -->
                                                <span></span>
                                                <div class="progress-percent"></div>
                                            </div>
                                            <span class="applied-error pdf-status"><?php esc_html_e( 'Error', 'ilovepdf' ); ?></span>
                                            <span class="applied-success pdf-status"><?php esc_html_e( 'Finished', 'ilovepdf' ); ?></span>
                                            <span class="applied-abort pdf-status"><?php esc_html_e( 'Canceled', 'ilovepdf' ); ?></span>
                                        </div>
                                        <div class="multi-process">
                                            <a href="#" class="button-primary media-ilovepdf-box btn-cancel" id="cancel-watermark"><?php esc_html_e( 'Cancel Process', 'ilovepdf' ); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 panel" id="watermark-pdf-list">
                                    <h3><?php esc_html_e( 'PDFs in your library', 'ilovepdf' ); ?></h3>
                                    <p>Stamp all non stamped PDF in your library at once.</p>
                                    <?php $files = ilove_pdf_initialize_list_watermark_pdf(); ?>
                                    <?php
                                        $paged            = isset( $_GET['paged'] ) ? sanitize_url( wp_unslash( $_GET['paged'] ) ) : 1;
                                        $query_files_args = array(
                                            'post_type'   => 'attachment',
                                            'post_status' => 'inherit',
                                            'post_mime_type' => 'application/pdf',
                                            'posts_per_page' => 150,
                                            'paged'       => $paged,
                                        );

                                        $query_files = new WP_Query( $query_files_args );
										?>

                                    <!-- New Multi Ajax -->
                                    <?php if ( count( $files ) > 0 ) : ?>
                                        <p><a href="#" class="button-primary media-ilovepdf-box btn-watermark-all"><?php esc_html_e( 'Apply watermark in ' . count( $files ) . ' PDF', 'ilovepdf' ); ?></a></p>
                                    <?php endif; ?>
                                    <!-- End Multi Ajax -->


                                    <?php if ( $query_files->have_posts() ) : ?>
                                    <table class="wp-list-table widefat optimization-pdf">
                                        <thead>
                                            <tr>
                                                <th class="column-primary"><?php esc_html_e( 'File', 'ilovepdf' ); ?></th>
                                                <th class="column-author"><?php esc_html_e( 'Size', 'ilovepdf' ); ?></th>
                                                <th class="column-author">&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ( $query_files->have_posts() ) :
												$query_files->the_post();
												?>
                                                <tr id="file-row-<?php echo esc_html( get_the_ID() ); ?>">
                                                    <td><a href="<?php echo esc_url( get_edit_post_link( get_the_ID() ) ); ?>"><?php echo esc_html( get_the_title() ); ?></a></td>
                                                    <td><?php echo esc_html( size_format( filesize( get_attached_file( get_the_ID() ) ), 2 ) ); ?></td>
                                                    <td>
                                                    <?php if ( ! ilove_pdf_is_file_watermarked( get_the_ID() ) ) : ?>
                                                        <a href="<?php echo esc_url( add_query_arg( 'nonce_ilove_pdf_watermark', wp_create_nonce( 'admin-post' ), admin_url( 'admin-post.php' ) . '?action=ilovepdf_watermark&id=' . get_the_ID() ) ); ?>" class="button-primary media-ilovepdf-box btn-watermark"><?php esc_html_e( 'Apply Watermark', 'ilovepdf' ); ?></a>
                                                        <span class="applying-watermark pdf-status"><?php esc_html_e( 'Applying Watermark', 'ilovepdf' ); ?>...</span>
                                                        <span class="error pdf-status"><?php esc_html_e( 'Error', 'ilovepdf' ); ?></span>
                                                        <span class="success pdf-status"><?php esc_html_e( 'Completed', 'ilovepdf' ); ?></span>
                                                    <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                    <div class="tablenav bottom" style="margin-bottom: 20px;">
                                        <div class="tablenav-pages">
                                        <?php if ( count( $files ) > 0 ) : ?>
                                            <span class="displaying-num"><?php echo count( $files ); ?> <?php esc_html_e( 'PDFs without watermark', 'ilovepdf' ); ?></span>
                                        <?php endif; ?>
                                        <?php
                                        echo wp_kses_post(
                                            paginate_links(
                                                array(
													'base' => 'upload.php%_%',
													'format' => '?paged=%#%',
													'current' => max( 1, isset( $_GET['paged'] ) ? sanitize_url( wp_unslash( $_GET['paged'] ) ) : 1 ),
													'total' => $query_files->max_num_pages,
                                                )
                                            )
                                        );
										?>
                                        </div>
                                        <br class="clear">
                                    </div>
                                    <!-- New Multi Ajax -->
                                        <?php if ( count( $files ) > 0 ) : ?>
                                            <a href="#" class="button-primary media-ilovepdf-box btn-watermark-all"><?php esc_html_e( 'Apply watermark in ' . count( $files ) . ' PDF', 'ilovepdf' ); ?></a>
                                        <?php endif; ?>
                                    <!-- End Multi Ajax -->
                                    <?php else : ?>
                                        <span class="files-not-found"><?php esc_html_e( 'No pdf files found', 'ilovepdf' ); ?></span>
                                    <?php endif; ?>
                                    <a href="options-general.php?page=ilove-pdf-content-setting&tab=watermark_options" class="button button-primary"><?php esc_html_e( 'Settings', 'ilovepdf' ); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
     			<?php endif; ?>
     		</div>
        <?php else : ?>
            <div class="col-md-12">
                <div class="panel">            
                    <p><?php esc_html_e( 'You must first login or register to use this plugin', 'ilovepdf' ); ?></p>
                    <a href="<?php echo esc_url( admin_url( 'options-general.php?page=ilove-pdf-content-setting' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Go to Settings', 'ilovepdf' ); ?></a>
                </div>
            </div>
        <?php endif; ?>
    </div>
	<?php
}