<?php
/**
 * Functions Statistics
 *
 * @link       https://ilovepdf.com/
 * @since      1.0.0
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/admin
 */

/**
 * Get Statistics.
 *
 * @since    1.0.0
 */
function ilove_pdf_get_statistics() {
	$response = wp_remote_get(
        ILOVE_PDF_USER_URL . '/' . get_option( 'ilovepdf_user_id', true ),
		array(
			'headers' => array( 'Authorization' => 'Bearer ' . get_option( 'ilovepdf_user_token', true ) ),
		)
	);

	if ( isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		return json_decode( $response['body'], true );
	} else {
		return;
	}
}

/**
 * Get Percentage.
 *
 * @since    1.0.0
 * @param    int $used    Used.
 * @param    int $limit   Limit.
 */
function ilove_pdf_get_percentage( $used, $limit ) {
	if ( 0 === $limit ) {
		return 0;
	}

	$percentage = $used * 100 / $limit;

	return ( $percentage > 100 ) ? 100 : $percentage;
}

/**
 * Get Percentage Compress.
 *
 * @since    1.0.0
 * @param    int $original    original.
 * @param    int $compressed   Compressed.
 */
function ilove_pdf_get_percentage_compress( $original, $compressed ) {
	if ( 0 === $original ) {
		return 0;
	}

	$percentage = $compressed * 100 / $original;

	return round( ( $percentage > 100 ) ? 100 : $percentage );
}

/**
 * Get All PDF Files current size.
 *
 * @since    1.0.0
 */
function ilove_pdf_get_all_pdf_current_size() {

	$query_files_args = array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'post_mime_type' => 'application/pdf',
        'posts_per_page' => - 1,
    );

    $query_files = new WP_Query( $query_files_args );

    $total_current_size = 0;
    foreach ( $query_files->posts as $file ) {
    	if ( metadata_exists( 'post', $file->ID, '_wp_attached_compress_size' ) ) {
    		$total_current_size = $total_current_size + get_post_meta( $file->ID, '_wp_attached_compress_size', true );
    	} else {
        	$total_current_size = $total_current_size + filesize( get_attached_file( $file->ID ) );
        }
	}

    return $total_current_size;
}

/**
 * Get All PDF Files original size.
 *
 * @since    1.0.0
 */
function ilove_pdf_get_all_pdf_original_size() {

	$query_files_args = array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'post_mime_type' => 'application/pdf',
        'posts_per_page' => - 1,
    );

    $query_files = new WP_Query( $query_files_args );

    $total_original_size = 0;
    foreach ( $query_files->posts as $file ) {
    	if ( metadata_exists( 'post', $file->ID, '_wp_attached_original_size' ) ) {
    		$total_original_size = $total_original_size + get_post_meta( $file->ID, '_wp_attached_original_size', true );
    	} else {
        	$total_original_size = $total_original_size + filesize( get_attached_file( $file->ID ) );
        }
	}

    return $total_original_size;
}
