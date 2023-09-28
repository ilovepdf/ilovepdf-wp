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

	// script - input
	$my_allowed['script'] = array();

	return $my_allowed;
}
