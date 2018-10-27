<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://prodev.lt
 * @since      1.0.0
 *
 * @package    Wp_C_Import
 * @subpackage Wp_C_Import/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_C_Import
 * @subpackage Wp_C_Import/admin
 * @author     Romualdas D. <romualdas@prodev.lt>
 */
class Wp_C_Import_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-c-import-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-c-import-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Notice about missing Woocommerce plugin activation
	 */
	public function no_wc() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/no-wc-notice.php';
	}

	public function product_import() {

		self::update_products();

		exit;
	}

	/**
	 * Update products
	 */
	public static function update_products() {

		global $wp_c_import, $wpdb;

		$xmlurl = $wp_c_import['opt-xmlurl'] ?? '';
		$xmlpath = $wp_c_import['opt-xmlpath'] ?? '';

		if ( ! empty($xmlurl) ) {

			$remote_xml_content = wp_remote_get( $xmlurl, [ 'sslverify' => false ] )['body'];
			$xml_products = simplexml_load_string( $remote_xml_content ) or die('Bad XML, check response from: ' . $xmlurl );

		} elseif ( ! empty($xmlpath) ) {

			$xml_products = simplexml_load_file( get_home_path() . DIRECTORY_SEPARATOR . $xmlpath ) or die('Bad XML, check response from: ' . $xmlpath );

		}

		if ( ! empty($xml_products) ) {
			foreach( $xml_products as $xml_product ) {

				$product_name = (string)$xml_product->name;
				$sku = (string)$xml_product->catalogue_number;
				$stock = (int)$xml_product->storehouse_counter;
				$in_stock = ( $stock > 0 ? 'instock' : 'outofstock' );
				$price = (double)$xml_product->price;
				$barcode = (string)$xml_product->barcode;

				$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );

				if ( $product_id ) {
					// $product = new WC_Product( $product_id );
					update_post_meta( $product_id, '_regular_price', $price );
					update_post_meta( $product_id, '_price', $price );
					update_post_meta( $product_id, '_stock_status', $in_stock );
					update_post_meta( $product_id, '_stock', $stock );
					wp_update_post([
						'ID' => $product_id,
						'post_title' => $product_name,
					]);

					wc_delete_product_transients( $product_id );
				}
			}
		}

	}

	/**
	 * Callback after Redux settings saved
	 */
	public function settings_saved() {

		global $wp_c_import;

		$cron = $wp_c_import['opt-crontime'] ?? '';

		if ( ! empty($cron) ) {
			wp_clear_scheduled_hook('wpci_product_import');

			if ( ! wp_next_scheduled ( 'wpci_product_import' ) ) {
				wp_schedule_event(time(), $cron, 'wpci_product_import');
			}
		}

	}

}
