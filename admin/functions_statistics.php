<?php

function ilove_pdf_get_statistics() {
	$response = wp_remote_get(
        ILOVEPDF_USER_URL . '/' . get_option( 'ilovepdf_user_id', true ),
		array(
			'headers' => array( 'Authorization' => 'Bearer ' . get_option( 'ilovepdf_user_token', true ) ),
		)
	);

	if ( isset( $response['response']['code'] ) && $response['response']['code'] === 200 ) {
		return json_decode( $response['body'], true );
	} else {
		return;
	}
}

function ilove_pdf_get_percentage( $used, $limit ) {
	if ( $limit === 0 ) {
		return 0;
	}

	$percentage = $used * 100 / $limit;

	return ( $percentage > 100 ) ? 100 : $percentage;
}

function ilove_pdf_get_percentage_compress( $original, $compressed ) {
	if ( $original === 0 ) {
		return 0;
	}

	$percentage = $compressed * 100 / $original;

	return round( ( $percentage > 100 ) ? 100 : $percentage );
}

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
