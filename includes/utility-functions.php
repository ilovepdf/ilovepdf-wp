<?php
/**
 * Utility Functions
 *
 * @link       https://ilovepdf.com/
 * @since      1.2.3
 *
 * @package    Ilove_Pdf
 * @subpackage Ilove_Pdf/includes
 */

/**
 * Expand Alowed HTMl Tags. Use for wp_kses.
 */
function ilove_pdf_expanded_alowed_tags() {
	// style
	$my_allowed = wp_kses_allowed_html( 'post' );

	// form tag - input
	$my_allowed['form'] = array(
		'id'             => true,
		'class'          => true,
		'action'         => true,
		'accept'         => true,
		'accept-charset' => true,
		'enctype'        => true,
		'method'         => true,
		'name'           => true,
		'target'         => true,
	);

	// form fields - input
	$my_allowed['input'] = array(
		'class'    => array(),
		'id'       => array(),
		'name'     => array(),
		'value'    => array(),
		'min'      => array(),
		'max'      => array(),
		'type'     => array(),
		'checked'  => array(),
		'selected' => array(),
		'disabled' => array(),
	);
	// select
	$my_allowed['select'] = array(
		'class' => array(),
		'id'    => array(),
		'name'  => array(),
		'value' => array(),
		'type'  => array(),
	);
	// select options
	$my_allowed['option'] = array(
		'value'    => array(),
		'selected' => array(),
	);
	// style
	$my_allowed['style'] = array(
		'types' => array(),
	);

	// script
	$my_allowed['script'] = array();

	return $my_allowed;
}

/**
 * Recursive sanitation for an array
 *
 * @param Array $data_array  Array to sanitize.
 */
function ilove_pdf_array_sanitize_text_field( $data_array ) {

    foreach ( $data_array as $value ) {
		$value = sanitize_text_field( $value );
    }

    return $data_array;
}

/**
 * Regenerate attachment metadata
 *
 * @since      2.0.6
 * @param int $attachment_id File ID.
 */
function ilove_pdf_regenerate_attachment_data( $attachment_id ) {

	if ( ! $attachment_id ) {
		return;
	}

	$filename     = get_attached_file( $attachment_id ); // Get Filename of attachment
	$metadata_old = wp_get_attachment_metadata( $attachment_id ); // Old attachment metadata
	$metadata     = wp_generate_attachment_metadata( $attachment_id, $filename ); // Regenerate attachment metadata

	// Delete old attachment metadata
	if ( isset( $metadata_old['sizes'] ) ) {

		foreach ( $metadata_old['sizes'] as $size => $data ) {
			$thumb_file = pathinfo( $filename )['dirname'] . '/' . $data['file'];

			if ( file_exists( $thumb_file ) ) {
				wp_delete_file( $thumb_file );
			}
		}
	}

	wp_update_attachment_metadata( $attachment_id, $metadata ); // Update new attachment metadata
}
