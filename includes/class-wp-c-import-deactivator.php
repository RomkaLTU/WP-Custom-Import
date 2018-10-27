<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://prodev.lt
 * @since      1.0.0
 *
 * @package    Wp_C_Import
 * @subpackage Wp_C_Import/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wp_C_Import
 * @subpackage Wp_C_Import/includes
 * @author     Romualdas D. <romualdas@prodev.lt>
 */
class Wp_C_Import_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook('wpci_product_import');
	}

}
