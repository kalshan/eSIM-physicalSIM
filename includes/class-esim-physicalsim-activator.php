<?php

/**
 * Fired during plugin activation
 *
 * @link       https://codeies.com
 * @since      1.0.0
 *
 * @package    Esim_Physicalsim
 * @subpackage Esim_Physicalsim/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Esim_Physicalsim
 * @subpackage Esim_Physicalsim/includes
 * @author     Codeies Pvt Ltd <contact@codeies.com>
 */
class Esim_Physicalsim_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
            global $wpdb;
     $table_name = $wpdb->prefix . "codeies_esim_physicalsim";
    $charset_collate = $wpdb->get_charset_collate();
    if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name ) {
        $sql = "CREATE TABLE $table_name (
            ID mediumint(9) NOT NULL AUTO_INCREMENT,
            type varchar(100) NOT NULL,
            sku varchar(100) NOT NULL,
            sim_or_serial_num varchar(100) NOT NULL,
            pin_or_confirmation varchar(50) NULL,
            activation_url varchar(512) NULL,
            name varchar(200) NOT NULL,
            order_id varchar(100) NOT NULL,
            short_description varchar(500) NOT NULL,
            description TEXT NOT NULL,
            deliveryDatetime datetime NOT NULL,
            enabled varchar(2) NOT NULL,
            trashed varchar(2) NOT NULL DEFAULT '0',
            PRIMARY KEY (ID)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
/*   UNIQUE KEY (activation_url) ,
     UNIQUE KEY (pin_or_confirmation),*/
	}

}
