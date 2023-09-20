<?php
/**
 * Función que pinta la página de estadísticas
 */
function ilove_pdf_content_page_statistics() {
    if ( isset( $_POST['file'] ) ) {
        if ( strcmp( $_GET['tab'], 'compress_statistic' ) === 0 ) {
            ilove_pdf_compress_pdf( $_POST['file'] );
        } elseif ( strcmp( $_GET['tab'], 'watermark_statistic' ) === 0 ) {
            ilove_pdf_watermark_pdf( $_POST['file'] );
        }
    } elseif ( isset( $_POST['multi'] ) ) {
        if ( strcmp( $_GET['tab'], 'compress_statistic' ) === 0 ) {
            ilove_pdf_compress_pdf( null );
        } elseif ( strcmp( $_GET['tab'], 'watermark_statistic' ) === 0 ) {
            ilove_pdf_watermark_pdf( null );
        }
    }

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

	$stats = ilove_pdf_get_statistics();
	?>
    <div class="wrap">
        <h2 class="plugin-logo-full"><?php echo $logo_svg; ?></h2>
        <?php if ( get_option( 'ilovepdf_user_id' ) ) : ?>         
            <?php
                $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'statistic_statistic';
            ?>
             
            <h2 class="nav-tab-wrapper">
                <a href="?page=ilove-pdf-content-statistics&tab=statistic_statistic" class="nav-tab <?php echo 'statistic_statistic' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php echo __( 'Overview', 'ilovepdf' ); ?></a>
                <a href="?page=ilove-pdf-content-statistics&tab=compress_statistic" class="nav-tab <?php echo 'compress_statistic' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php echo __( 'Compress PDF', 'ilovepdf' ); ?></a>
                <a href="?page=ilove-pdf-content-statistics&tab=watermark_statistic" class="nav-tab <?php echo 'watermark_statistic' === $active_tab ? 'nav-tab-active tab-ilovepdf' : ''; ?>"><?php echo __( 'Watermark', 'ilovepdf' ); ?></a>
            </h2>
             	        
             
          		<?php if ( 'statistic_statistic' === $active_tab ) : ?>
                    <div class="wrap">
                        <div class="container no-center">
                            <div class="row">
                                <div class="col-md-6 panel">
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
                                    <a href="https://developer.ilovepdf.com/user/account" class="link" target="_blank"><?php echo __( 'Account info', 'ilovepdf' ); ?> (<?php echo get_option( 'ilovepdf_user_email' ); ?>) &raquo;</a>
                                    <a href="https://developer.ilovepdf.com/pricing" target="_blank" class="button button-primary"><?php echo __( 'Buy more files', 'ilovepdf' ); ?></a>
                                </div>
                                <div class="col-md-5 col-md-offset-1 panel panel-margin-left">
                                    <h3>Tools</h3>
                                    <a href="?page=ilove-pdf-content-statistics&tab=compress_statistic" class="button button-primary"><?php echo __( 'Go to Compress PDF tab', 'ilovepdf' ); ?></a>
                                    <a href="?page=ilove-pdf-content-statistics&tab=watermark_statistic" class="button button-primary"><?php echo __( 'Go to Watermark tab', 'ilovepdf' ); ?></a>
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
                                window.exportedCompressMultiPDF(<?php echo json_encode( $_POST['array_ids'] ); ?>);
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
                                    <h3><?php echo __( 'Total savings', 'ilovepdf' ); ?></h3>
                                    <p style="margin-bottom: 20px;">Here you can check how much space you saved.</p>
                                    <div style="overflow: hidden;">
                                        <div class="c100 p<?php echo ilove_pdf_get_percentage_compress( ilove_pdf_get_all_pdf_original_size(), ilove_pdf_get_all_pdf_original_size() - ilove_pdf_get_all_pdf_current_size() ); ?> green"> 
                                            <span style="top: -15px"><?php echo __( 'Saved!', 'ilovepdf' ); ?></span>
                                            <?php
                                            $percent = ilove_pdf_get_percentage_compress( ilove_pdf_get_all_pdf_original_size(), ilove_pdf_get_all_pdf_original_size() - ilove_pdf_get_all_pdf_current_size() );
                                            ?>
                                            <span id="stats_total_percentage"><?php echo ( $percent > 0 ) ? $percent : 0; ?>%</span>
                                            <div class="slice">
                                                <div class="bar"></div>
                                                <div class="fill"></div>
                                            </div>
                                        </div>
                                        <div class="status-c100">
                                            <ul>
                                                <li><strong id="stats_total_files_compressed"><?php echo ( get_option( 'ilovepdf_compressed_files' ) > 0 ) ? get_option( 'ilovepdf_compressed_files' ) : 0; ?></strong> <?php echo __( 'PDF files compressed', 'ilovepdf' ); ?></li>                                               
                                                <li><strong id="stats_initial_size"><?php echo size_format( ilove_pdf_get_all_pdf_original_size(), 2 ); ?></strong> <?php echo __( 'initial size', 'ilovepdf' ); ?></li>
                                                <li><strong id="stats_current_size"><?php echo size_format( ilove_pdf_get_all_pdf_current_size(), 2 ); ?></strong> <?php echo __( 'current size', 'ilovepdf' ); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="multi-process">
                                            <div class="all-compressing pdf-status">
                                                <!-- <?php echo __( 'Processing', 'ilovepdf' ); ?>... -->
                                                <span></span>
                                                <div class="progress-percent"></div>
                                            </div>
                                            <span class="compress-error pdf-status"><?php echo __( 'Error', 'ilovepdf' ); ?></span>
                                            <span class="compress-success pdf-status"><?php echo __( 'Finished', 'ilovepdf' ); ?></span>
                                            <span class="compress-abort pdf-status"><?php echo __( 'Canceled', 'ilovepdf' ); ?></span>
                                        </div>
                                        <div class="multi-process">
                                            <a href="#" class="button-primary media-ilovepdf-box btn-cancel" id="cancel-compress"><?php echo __( 'Cancel Process', 'ilovepdf' ); ?></a>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-12 panel" id="compress-pdf-list">
                                    <h3><?php echo __( 'PDFs in your library', 'ilovepdf' ); ?></h3>
                                    <p>Compress all non compressed PDF in your library at once.</p>
                                    <?php $files = ilove_pdf_initialize_list_compress_pdf(); ?>
                                    <?php
                                        $paged            = ( $_GET['paged'] ) ? $_GET['paged'] : 1;
                                        $query_files_args = array(
                                            'post_type'   => 'attachment',
                                            'post_status' => 'inherit',
                                            'post_mime_type' => 'application/pdf',
                                            'posts_per_page' => 250,
                                            'paged'       => $paged,
                                        );

                                        $query_files = new WP_Query( $query_files_args );
										?>

                                    <!-- New Multi Ajax -->
                                    <?php if ( count( $files ) > 0 ) : ?>
                                            <p><a href="#" class="button-primary media-ilovepdf-box btn-compress-all"><?php echo __( 'Compress ' . count( $files ) . ' PDF', 'ilovepdf' ); ?></a></p>
                                    <?php endif; ?>
                                    <!-- End Multi Ajax -->
                                    
                                    <?php if ( $query_files->have_posts() ) : ?>
                                    <table class="wp-list-table widefat optimization-pdf">
                                        <thead>
                                            <tr>
                                                <th class="column-primary"><?php echo __( 'File', 'ilovepdf' ); ?></th>
                                                <th class="column-author"><?php echo __( 'Original Size', 'ilovepdf' ); ?></th>
                                                <th class="column-author"><?php echo __( 'Compressed Size', 'ilovepdf' ); ?></th>
                                                <th class="column-author">&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ( $query_files->have_posts() ) :
												$query_files->the_post();
												?>
                                                <tr id="file-row-<?php echo get_the_ID(); ?>">
                                                    <td><a href="<?php echo get_edit_post_link( get_the_ID() ); ?>"><?php echo the_title(); ?></a></td>
                                                    <?php if ( ! ilove_pdf_is_file_compressed( get_the_ID() ) ) : ?>
                                                        <td><?php echo size_format( filesize( get_attached_file( get_the_ID() ) ), 2 ); ?></td>
                                                        <td></td>
                                                    <?php else : ?>
                                                        <td><?php echo size_format( get_post_meta( get_the_ID(), '_wp_attached_original_size', true ), 2 ); ?></td>
                                                        <td><?php echo size_format( get_post_meta( get_the_ID(), '_wp_attached_compress_size', true ), 2 ); ?></td>
                                                    <?php endif; ?>
                                                    <td>
                                                    <?php if ( ! ilove_pdf_is_file_compressed( get_the_ID() ) ) : ?>
                                                        <a href="<?php echo admin_url( 'admin-post.php' ); ?>?action=ilovepdf_compress&id=<?php echo get_the_ID(); ?>&nonce_ilove_pdf_compress=<?php echo wp_create_nonce( 'admin-post' ); ?>" class="button-primary media-ilovepdf-box btn-compress"><?php echo __( 'Compress', 'ilovepdf' ); ?></a>
                                                        <span class="compressing pdf-status"><?php echo __( 'Compressing', 'ilovepdf' ); ?>...</span>
                                                        <span class="error pdf-status"><?php echo __( 'Error', 'ilovepdf' ); ?></span>
                                                        <span class="success pdf-status"><?php echo __( 'Completed', 'ilovepdf' ); ?></span>
                                                    <?php else : ?>
                                                            <?php $original_current_file_size = get_post_meta( get_the_ID(), '_wp_attached_original_size', true ); ?>
                                                            <?php $compress_file_size = get_post_meta( get_the_ID(), '_wp_attached_compress_size', true ); ?>
                                                            <?php echo ilove_pdf_get_percentage_compress( $original_current_file_size, $original_current_file_size - $compress_file_size ) . '%'; ?>                                                 
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
                                            <span class="displaying-num"><?php echo count( $files ); ?> <?php echo __( 'PDFs non compressed', 'ilovepdf' ); ?></span>
                                        <?php endif; ?>
                                        <?php
                                        echo paginate_links(
                                            array(
												'base'    => 'upload.php%_%',
												'format'  => '?paged=%#%',
												'current' => max( 1, $_GET['paged'] ),
												'total'   => $query_files->max_num_pages,
                                            )
                                        );
										?>
                                        </div>
                                        <br class="clear">
                                    </div>
                                    <!-- New Multi Ajax -->
                                        <?php if ( count( $files ) > 0 ) : ?>
                                            <a href="#" class="button-primary media-ilovepdf-box btn-compress-all"><?php echo __( 'Compress ' . count( $files ) . ' PDF', 'ilovepdf' ); ?></a>
                                        <?php endif; ?>
                                    <!-- End Multi Ajax --> 
                                    <?php else : ?>
                                        <span class="files-not-found"><?php echo __( 'No pdf files found', 'ilovepdf' ); ?></span>
                                    <?php endif; ?>
                                    <a href="options-general.php?page=ilove-pdf-content-setting" class="button button-primary"><?php echo __( 'Settings', 'ilovepdf' ); ?></a>
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
                                window.exportedWatermarkMultiPDF(<?php echo json_encode( $_POST['array_ids'] ); ?>);
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
                                    <h3><?php echo __( 'Total PDF files stamped', 'ilovepdf' ); ?></h3>
                                    <p style="margin-bottom: 20px;">Here you can check how many PDF files have been stamped.</p>
                                    <h1><strong id="stats_total_files_watermarked"><?php echo ( get_option( 'ilovepdf_watermarked_files' ) ? get_option( 'ilovepdf_watermarked_files' ) : 0 ); ?></strong> <?php echo __( 'PDF files have been stamped!', 'ilovepdf' ); ?></h1>

                                    <div class="row">
                                        <div class="multi-process">
                                            <div class="all-applying-watermark pdf-status">
                                                <!-- <?php echo __( 'Processing', 'ilovepdf' ); ?>... -->
                                                <span></span>
                                                <div class="progress-percent"></div>
                                            </div>
                                            <span class="applied-error pdf-status"><?php echo __( 'Error', 'ilovepdf' ); ?></span>
                                            <span class="applied-success pdf-status"><?php echo __( 'Finished', 'ilovepdf' ); ?></span>
                                            <span class="applied-abort pdf-status"><?php echo __( 'Canceled', 'ilovepdf' ); ?></span>
                                        </div>
                                        <div class="multi-process">
                                            <a href="#" class="button-primary media-ilovepdf-box btn-cancel" id="cancel-watermark"><?php echo __( 'Cancel Process', 'ilovepdf' ); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 panel" id="watermark-pdf-list">
                                    <h3><?php echo __( 'PDFs in your library', 'ilovepdf' ); ?></h3>
                                    <p>Stamp all non stamped PDF in your library at once.</p>
                                    <?php $files = ilove_pdf_initialize_list_watermark_pdf(); ?>
                                    <?php
                                        $paged            = ( $_GET['paged'] ) ? $_GET['paged'] : 1;
                                        $query_files_args = array(
                                            'post_type'   => 'attachment',
                                            'post_status' => 'inherit',
                                            'post_mime_type' => 'application/pdf',
                                            'posts_per_page' => 250,
                                            'paged'       => $paged,
                                        );

                                        $query_files = new WP_Query( $query_files_args );
										?>

                                    <!-- New Multi Ajax -->
                                    <?php if ( count( $files ) > 0 ) : ?>
                                        <p><a href="#" class="button-primary media-ilovepdf-box btn-watermark-all"><?php echo __( 'Apply watermark in ' . count( $files ) . ' PDF', 'ilovepdf' ); ?></a></p>
                                    <?php endif; ?>
                                    <!-- End Multi Ajax -->


                                    <?php if ( $query_files->have_posts() ) : ?>
                                    <table class="wp-list-table widefat optimization-pdf">
                                        <thead>
                                            <tr>
                                                <th class="column-primary"><?php echo __( 'File', 'ilovepdf' ); ?></th>
                                                <th class="column-author"><?php echo __( 'Size', 'ilovepdf' ); ?></th>
                                                <th class="column-author">&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ( $query_files->have_posts() ) :
												$query_files->the_post();
												?>
                                                <tr id="file-row-<?php echo get_the_ID(); ?>">
                                                    <td><a href="<?php echo get_edit_post_link( get_the_ID() ); ?>"><?php echo the_title(); ?></a></td>
                                                    <td><?php echo size_format( filesize( get_attached_file( get_the_ID() ) ), 2 ); ?></td>
                                                    <td>
                                                    <?php if ( ! ilove_pdf_is_file_watermarked( get_the_ID() ) ) : ?>
                                                        <a href="<?php echo add_query_arg( 'nonce_ilove_pdf_watermark', wp_create_nonce( 'admin-post' ), admin_url( 'admin-post.php' ) . '?action=ilovepdf_watermark&id=' . get_the_ID() ); ?>" class="button-primary media-ilovepdf-box btn-watermark"><?php echo __( 'Apply Watermark', 'ilovepdf' ); ?></a>
                                                        <span class="applying-watermark pdf-status"><?php echo __( 'Applying Watermark', 'ilovepdf' ); ?>...</span>
                                                        <span class="error pdf-status"><?php echo __( 'Error', 'ilovepdf' ); ?></span>
                                                        <span class="success pdf-status"><?php echo __( 'Completed', 'ilovepdf' ); ?></span>
                                                    <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                    <div class="tablenav bottom" style="margin-bottom: 20px;">
                                        <div class="tablenav-pages">
                                        <?php if ( count( $files ) > 0 ) : ?>
                                            <span class="displaying-num"><?php echo count( $files ); ?> <?php echo __( 'PDFs without watermark', 'ilovepdf' ); ?></span>
                                        <?php endif; ?>
                                        <?php
                                        echo paginate_links(
                                            array(
												'base'    => 'upload.php%_%',
												'format'  => '?paged=%#%',
												'current' => max( 1, $_GET['paged'] ),
												'total'   => $query_files->max_num_pages,
                                            )
                                        );
										?>
                                        </div>
                                        <br class="clear">
                                    </div>
                                    <!-- New Multi Ajax -->
                                        <?php if ( count( $files ) > 0 ) : ?>
                                            <a href="#" class="button-primary media-ilovepdf-box btn-watermark-all"><?php echo __( 'Apply watermark in ' . count( $files ) . ' PDF', 'ilovepdf' ); ?></a>
                                        <?php endif; ?>
                                    <!-- End Multi Ajax -->
                                    <?php else : ?>
                                        <span class="files-not-found"><?php echo __( 'No pdf files found', 'ilovepdf' ); ?></span>
                                    <?php endif; ?>
                                    <a href="options-general.php?page=ilove-pdf-content-setting&tab=watermark_options" class="button button-primary"><?php echo __( 'Settings', 'ilovepdf' ); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
     			<?php endif; ?>
     		</div>
        <?php else : ?>
            <div class="col-md-12">
                <div class="panel">            
                    <p><?php echo __( 'You must first login or register to use this plugin', 'ilovepdf' ); ?></p>
                    <a href="<?php echo admin_url( 'options-general.php?page=ilove-pdf-content-setting' ); ?>" class="button button-primary"><?php echo __( 'Go to Settings', 'ilovepdf' ); ?></a>
                </div>
            </div>
        <?php endif; ?>
    </div>
	<?php
}