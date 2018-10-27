<?php

/**
 * Fired during plugin activation
 *
 * @link       https://prodev.lt
 * @since      1.0.0
 *
 * @package    Wp_C_Import
 * @subpackage Wp_C_Import/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_C_Import
 * @subpackage Wp_C_Import/includes
 * @author     Romualdas D. <romualdas@prodev.lt>
 */
class Wp_C_Import_Activator {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Plugin activation

	 * @since    1.0.0
	 */
	public static function activate() {

		if ( ! wp_next_scheduled ( 'wpci_product_import' ) ) {

			wp_schedule_event(time(), 'daily', 'wpci_product_import');

		}

	}

}
