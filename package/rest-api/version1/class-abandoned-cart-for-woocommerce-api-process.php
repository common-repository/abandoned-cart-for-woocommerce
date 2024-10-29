<?php
/**
 * Fired during plugin activation
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Abandoned_Cart_For_Woocommerce_Api_Process' ) ) {

	/**
	 * The plugin API class.
	 *
	 * This is used to define the functions and data manipulation for custom endpoints.
	 *
	 * @since      1.0.0
	 * @package    Hydroshop_Api_Management
	 * @subpackage Hydroshop_Api_Management/includes
	 * @author     MakeWebBetter <makewebbetter.com>
	 */
	class Abandoned_Cart_For_Woocommerce_Api_Process {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {

		}

		/**
		 * Define the function to process data for custom endpoint.
		 *
		 * @since    1.0.0
		 * @param   Array $acfw_request  data of requesting headers and other information.
		 * @return  Array $mwb_acfw_rest_response    returns processed data and status of operations.
		 */
		public function mwb_acfw_default_process( $acfw_request ) {
			$mwb_acfw_rest_response = array();

			// Write your custom code here.

			$mwb_acfw_rest_response['status'] = 200;
			$mwb_acfw_rest_response['data'] = $acfw_request->get_headers();
			return $mwb_acfw_rest_response;
		}
	}
}
