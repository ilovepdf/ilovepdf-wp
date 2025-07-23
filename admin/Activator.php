<?php

namespace Ilove_Pdf_WP;

use Ilove_Pdf_WP\File_System;
use Ilove_Pdf_WP\Helpers\DB_Handler;
use Ilove_Pdf_WP\Account\User_Account;
use Ilove_Pdf_WP\Tools\General\Settings as General_Settings;
use Ilove_Pdf_WP\Tools\Compress\Settings as Compress_Settings;
use Ilove_Pdf_WP\Tools\Watermark\Settings as Watermark_Settings;

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
		File_System::migrate_legacy_directories();

		User_Account::create_wordpress_id();

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

		if ( ! isset( $settings['ipdf_option_watermark_active'] ) ) {
			$settings['ipdf_option_watermark_active'] = 'on';
        }

		if ( ! isset( $settings['ipdf_option_mode_watermark'] ) ) {
			$settings['ipdf_option_mode_watermark'] = 'text';
        }

		if ( ! isset( $settings['ipdf_option_position'] ) ) {
			$settings['ipdf_option_position'] = 'center middle';
        }

		if ( ! isset( $settings['ipdf_option_mode_text'] ) ) {
			$settings['ipdf_option_mode_text'] = get_bloginfo( 'name' ) ?: 'iLovePDF';
        }

		if ( ! isset( $settings['ipdf_option_font_size'] ) ) {
			$settings['ipdf_option_font_size'] = 33;
		}

		if ( ! isset( $settings['ipdf_option_font_family'] ) ) {
			$settings['ipdf_option_font_family'] = 'Arial Unicode MS';
		}

		if ( ! isset( $settings['ipdf_option_font_color'] ) ) {
			$settings['ipdf_option_font_color'] = '#dd3333';
		}

		if ( ! isset( $settings['ipdf_option_transparency'] ) ) {
			$settings['ipdf_option_transparency'] = 100;
		}

		if ( ! isset( $settings['ipdf_option_rotation'] ) ) {
			$settings['ipdf_option_rotation'] = 0;
		}

		if ( ! isset( $settings['ipdf_option_layer'] ) ) {
			$settings['ipdf_option_layer'] = 'above';
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

		if ( ! isset( $get_options['ipdf_option_backup'] ) ) {
			$get_options['ipdf_option_backup'] = 'on';
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

		if ( ! isset( $get_options['ipdf_option_compress_active'] ) ) {
			$get_options['ipdf_option_compress_active'] = 'on';
		}

		if ( ! isset( $get_options['ipdf_option_compression_level'] ) ) {
			$get_options['ipdf_option_compression_level'] = 'recommended';
		}

		DB_Handler::update_option( Compress_Settings::get_db_key_compress_settings(), $get_options );
	}
}
