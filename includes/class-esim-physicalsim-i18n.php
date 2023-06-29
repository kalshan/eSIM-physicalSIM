<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://codeies.com
 * @since      1.0.0
 *
 * @package    Esim_Physicalsim
 * @subpackage Esim_Physicalsim/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Esim_Physicalsim
 * @subpackage Esim_Physicalsim/includes
 * @author     Codeies Pvt Ltd <contact@codeies.com>
 */
class Esim_Physicalsim_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'esim-physicalsim',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
