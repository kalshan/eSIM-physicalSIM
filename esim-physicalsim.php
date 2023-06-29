<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://codeies.com
 * @since             1.0.0
 * @package           Esim_Physicalsim
 *
 * @wordpress-plugin
 * Plugin Name:       eSIM & PhysicalSIM
 * Plugin URI:        https://codeies.com
 * Description:       ESIM & Physical-Sim Woocommerce Plugin
 * Version:           1.0.0
 * Author:            Codeies Pvt Ltd
 * Author URI:        https://codeies.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       esim-physicalsim
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
global $wpdb;
define( 'ESIM_PHYSICALSIM_VERSION', '1.0.0' );
define( 'ESIM_PHYSICALSIM_DIR', plugin_dir_path( __FILE__ ) );
define( 'ESIMPHYSICALSIM_TABLE', $wpdb->prefix . 'codeies_esim_physicalsim');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-esim-physicalsim-activator.php
 */
function activate_esim_physicalsim() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-esim-physicalsim-activator.php';
	Esim_Physicalsim_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-esim-physicalsim-deactivator.php
 */
function deactivate_esim_physicalsim() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-esim-physicalsim-deactivator.php';
	Esim_Physicalsim_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_esim_physicalsim' );
register_deactivation_hook( __FILE__, 'deactivate_esim_physicalsim' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-esim-physicalsim.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_esim_physicalsim() {

	$plugin = new Esim_Physicalsim();
	$plugin->run();

}
run_esim_physicalsim();
