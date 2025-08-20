<?php

namespace Ilove_Pdf_WP;

use Ilove_Pdf_WP\Account\User_Data;
use Ilove_Pdf_WP\Helpers\DB_Handler;
use Ilove_Pdf_WP\Helpers\File_System;
use Ilove_Pdf_WP\Tools\General\Settings as General_Settings;
use Ilove_Pdf_WP\Tools\Compress\Settings as Compress_Settings;
use Ilove_Pdf_WP\Tools\Watermark\Settings as Watermark_Settings;
use Ilove_Pdf_WP\Tools\Compress\Statistics as Compress_Statistics;
use Ilove_Pdf_WP\Tools\Watermark\Statistics as Watermark_Statistics;

/**
 * Fired during plugin activation.
 *
 * @since   1.0.0
 * @package Ilove_Pdf_WP
 */
class Activator {
	/**
	 * Activate plugin
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		File_System::create_ilovepdf_directories();

		Compress_Statistics::reset_statistics();
		Watermark_Statistics::reset_statistics();

		User_Data::create_wordpress_id();

		self::set_default_values_watermark_settings();
		self::set_default_values_compress_settings();
		self::set_default_values_general_settings();
	}

	/**
	 * Watermark Settings Value.
	 *
	 * Set default values.
	 *
	 * @since 3.0.0 New fields are used, which are stored in 'ilovepdf_watermark_settings'.
	 * @since 1.2.4
	 */
	public static function set_default_values_watermark_settings() {

		$settings = get_option( Watermark_Settings::get_db_key_settings(), array() );

		if ( ! isset( $settings[ Watermark_Settings::get_field_watermark_active() ] ) ) {
			$settings[ Watermark_Settings::get_field_watermark_active() ] = 'on';
        }

		if ( ! isset( $settings[ Watermark_Settings::get_field_mode() ] ) ) {
			$settings[ Watermark_Settings::get_field_mode() ] = 'text';
        }

		if ( ! isset( $settings[ Watermark_Settings::get_field_position() ] ) ) {
			$settings[ Watermark_Settings::get_field_position() ] = 'center middle';
        }

		if ( ! isset( $settings[ Watermark_Settings::get_field_text_mode() ] ) ) {
			$settings[ Watermark_Settings::get_field_text_mode() ] = ! empty( get_bloginfo( 'name' ) ) ? get_bloginfo( 'name' ) : 'iLovePDF';
        }

		if ( ! isset( $settings[ Watermark_Settings::get_field_font_size() ] ) ) {
			$settings[ Watermark_Settings::get_field_font_size() ] = 33;
		}

		if ( ! isset( $settings[ Watermark_Settings::get_field_font_style() ] ) ) {
			$settings[ Watermark_Settings::get_field_font_style() ] = null;
		}

		if ( ! isset( $settings[ Watermark_Settings::get_field_font_family() ] ) ) {
			$settings[ Watermark_Settings::get_field_font_family() ] = 'Arial Unicode MS';
		}

		if ( ! isset( $settings[ Watermark_Settings::get_field_font_color() ] ) ) {
			$settings[ Watermark_Settings::get_field_font_color() ] = '#dd3333';
		}

		if ( ! isset( $settings[ Watermark_Settings::get_field_transparency() ] ) ) {
			$settings[ Watermark_Settings::get_field_transparency() ] = 100;
		}

		if ( ! isset( $settings[ Watermark_Settings::get_field_rotation() ] ) ) {
			$settings[ Watermark_Settings::get_field_rotation() ] = 0;
		}

		if ( ! isset( $settings[ Watermark_Settings::get_field_layer() ] ) ) {
			$settings[ Watermark_Settings::get_field_layer() ] = 'above';
		}

		DB_Handler::update_option( Watermark_Settings::get_db_key_settings(), $settings );
	}

	/**
	 * General Settings Value.
	 *
	 * Set default values.
	 *
	 * @since 3.0.0 New fields are used, which are stored in 'ilovepdf_general_settings'.
	 * @since 2.1.0
	 */
	public static function set_default_values_general_settings() {

		$get_options = get_option( General_Settings::get_db_key_general_settings(), array() );

		if ( ! isset( $get_options[ General_Settings::get_field_backup() ] ) ) {
			$get_options[ General_Settings::get_field_backup() ] = 'on';
		}

		DB_Handler::update_option( General_Settings::get_db_key_general_settings(), $get_options );
	}

	/**
	 * Compress Settings Value.
	 *
	 * Set default values.
	 *
	 * @since 3.0.0 New fields are used, which are stored in 'ilovepdf_compress_settings'.
	 * @since 2.1.1
	 */
	public static function set_default_values_compress_settings() {

		$get_options = get_option( Compress_Settings::get_db_key_compress_settings(), array() );

		if ( ! isset( $get_options[ Compress_Settings::get_field_compress_active() ] ) ) {
			$get_options[ Compress_Settings::get_field_compress_active() ] = 'on';
		}

		if ( ! isset( $get_options[ Compress_Settings::get_field_compression_level() ] ) ) {
			$get_options[ Compress_Settings::get_field_compression_level() ] = 'recommended';
		}

		DB_Handler::update_option( Compress_Settings::get_db_key_compress_settings(), $get_options );
	}
}
