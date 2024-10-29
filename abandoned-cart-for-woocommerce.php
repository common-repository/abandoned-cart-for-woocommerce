<?php
/**
 * This Plugin will track abandoned carts for woocommerce.
 *
 * @link              https://makewebbetter.com/
 * @since             1.0.0
 * @package           Abandoned_Cart_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Abandoned Cart for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/abandoned-cart-for-woocommerce/
 * Description:       This Plugin will track abandoned carts of WooCommerce shop's for both guest and registered user's and it will help them to successfully conversion of the abandoned cart.
 * Version:           1.0.5
 * Author:            MakeWebBetter
 * Author URI:        https://makewebbetter.com/
 * Text Domain:       abandoned-cart-for-woocommerce
 * Domain Path:       /languages
 *
 * Requires at least: 4.6
 * Tested up to:      5.8.2
 *
 * WC requires at least: 4.0.0
 * WC tested up to:    5.9.0
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
$mwb_abn_cart_activated = false;

/**
 * Checking for activation of Woocommerce
 */

$active_plugins = array_merge( get_option( 'active_plugins', array() ), get_site_option( 'active_sitewide_plugins', array() ) );
if ( array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) || in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {

	require_once plugin_dir_path( __FILE__ ) . 'mwb-acwf-gdpr.php';

	/**
	 * Define plugin constants.
	 *
	 * @since             1.0.0
	 */
	function define_abandoned_cart_for_woocommerce_constants() {

		abandoned_cart_for_woocommerce_constants( 'ABANDONED_CART_FOR_WOOCOMMERCE_VERSION', '1.0.5' );
		abandoned_cart_for_woocommerce_constants( 'ABANDONED_CART_FOR_WOOCOMMERCE_DIR_PATH', plugin_dir_path( __FILE__ ) );
		abandoned_cart_for_woocommerce_constants( 'ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL', plugin_dir_url( __FILE__ ) );
		abandoned_cart_for_woocommerce_constants( 'ABANDONED_CART_FOR_WOOCOMMERCE_SERVER_URL', 'https://makewebbetter.com' );
		abandoned_cart_for_woocommerce_constants( 'ABANDONED_CART_FOR_WOOCOMMERCE_ITEM_REFERENCE', 'Abandoned Cart for WooCommerce' );
	}


	/**
	 * Callable function for defining plugin constants.
	 *
	 * @param   String $key    Key for contant.
	 * @param   String $value   value for contant.
	 * @since             1.0.0
	 */
	function abandoned_cart_for_woocommerce_constants( $key, $value ) {

		if ( ! defined( $key ) ) {

			define( $key, $value );
		}
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-abandoned-cart-for-woocommerce-activator.php
	 *
	 * @param boolean $network_wide network_wide.
	 * @return void
	 */
	function activate_abandoned_cart_for_woocommerce( $network_wide ) {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-abandoned-cart-for-woocommerce-activator.php';
		Abandoned_Cart_For_Woocommerce_Activator::abandoned_cart_for_woocommerce_activate( $network_wide );
		$mwb_acfw_active_plugin = get_option( 'mwb_all_plugins_active', false );
		if ( is_array( $mwb_acfw_active_plugin ) && ! empty( $mwb_acfw_active_plugin ) ) {
			$mwb_acfw_active_plugin['abandoned-cart-for-woocommerce'] = array(
				'plugin_name' => 'Abandoned Cart for WooCommerce',
				'active' => '1',
			);
		} else {
			$mwb_acfw_active_plugin = array();
			$mwb_acfw_active_plugin['abandoned-cart-for-woocommerce'] = array(
				'plugin_name' => 'Abandoned Cart for WooCommerce',
				'active' => '1',
			);
		}
		update_option( 'mwb_all_plugins_active', $mwb_acfw_active_plugin );
	}

	/**
	 * This function is used to create blog for multiple sites.
	 *
	 * @param object $new_site new site.
	 * @return void
	 */
	function mwb_acfw_create_blog( $new_site ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}
			// check if the plugin has been activated on the network.
		if ( is_plugin_active_for_network( 'abandoned-cart-for-woocommerce/abandoned-cart-for-woocommerce.php' ) ) {
					$blog_id = $new_site->blog_id;
			// get option value for license activation.
			// switch to newly created site.

			switch_to_blog( $blog_id );

			// update license option value to newly created site.
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-abandoned-cart-for-woocommerce-activator.php';
			Abandoned_Cart_For_Woocommerce_Activator::mwb_acfw_insert_default_data();

			// code to be executed when site is created, call any function from activation file.
			restore_current_blog();
		}
	}

	add_action( 'wp_initialize_site', 'mwb_acfw_create_blog', 900 );

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-abandoned-cart-for-woocommerce-deactivator.php
	 */
	function deactivate_abandoned_cart_for_woocommerce() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-abandoned-cart-for-woocommerce-deactivator.php';
		Abandoned_Cart_For_Woocommerce_Deactivator::abandoned_cart_for_woocommerce_deactivate();
		$mwb_acfw_deactive_plugin = get_option( 'mwb_all_plugins_active', false );
		if ( is_array( $mwb_acfw_deactive_plugin ) && ! empty( $mwb_acfw_deactive_plugin ) ) {
			foreach ( $mwb_acfw_deactive_plugin as $mwb_acfw_deactive_key => $mwb_acfw_deactive ) {
				if ( 'abandoned-cart-for-woocommerce' === $mwb_acfw_deactive_key ) {
					$mwb_acfw_deactive_plugin[ $mwb_acfw_deactive_key ]['active'] = '0';
				}
			}
		}
		update_option( 'mwb_all_plugins_active', $mwb_acfw_deactive_plugin );
	}

	register_activation_hook( __FILE__, 'activate_abandoned_cart_for_woocommerce' );
	register_deactivation_hook( __FILE__, 'deactivate_abandoned_cart_for_woocommerce' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-abandoned-cart-for-woocommerce.php';


	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_abandoned_cart_for_woocommerce() {
		define_abandoned_cart_for_woocommerce_constants();

		$acfw_plugin_standard = new Abandoned_Cart_For_Woocommerce();
		$acfw_plugin_standard->mwb_acfw_run();
		$GLOBALS['acfw_mwb_acfw_obj'] = $acfw_plugin_standard;
		$GLOBALS['error_notice']      = true;
		$GLOBALS['mwb_wacr_activated'] = false;

	}
	run_abandoned_cart_for_woocommerce();


	// Add settings link on plugin page.
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'abandoned_cart_for_woocommerce_settings_link' );


	if ( ! function_exists( 'acfw_mwb_standard_check_multistep' ) ) {
		/**
		 * Function name acfw_mwb_standard_check_multistep.
		 * this function is used to check multistep done.
		 *
		 * @since 1.0.5
		 * @return boolean
		 */
		function acfw_mwb_standard_check_multistep() {
			$bool = false;
			$mwb_standard_check = get_option( 'acfw_acfw_plugin_standard_multistep_done', false );
			if ( ! empty( $mwb_standard_check ) ) {
				$bool = true;
			}
			$bool = apply_filters( 'mwb_standard_multistep_done', $bool );
			return $bool;
		}
	}


	/**
	 * Settings link.
	 *
	 * @since    1.0.0
	 * @param   Array $links    Settings link array.
	 */
	function abandoned_cart_for_woocommerce_settings_link( $links ) {

		$my_link = array(
			'<a href="' . admin_url( 'admin.php?page=abandoned_cart_for_woocommerce_menu' ) . '">' . __( 'Settings', 'abandoned-cart-for-woocommerce' ) . '</a>',
		);

		if ( ! in_array( 'woocommerce-abandoned-cart-recovery/woocommerce-abandoned-cart-recovery.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
			$my_link_2 = array(
				'<a id="mwb_acfw_go_pro" href="https://makewebbetter.com/product/woocommerce-abandoned-cart-recovery/?utm_source=MWB-wooabandoned-&utm_medium=MWB-site&utm_campaign=MWB-wooabandoned-site">' . __( 'Go Pro', 'abandoned-cart-for-woocommerce' ) . '</a>',
			);
			return array_merge( $my_link, $my_link_2, $links );
		}
		return array_merge( $my_link, $links );
	}

	if ( ! function_exists( 'mwb_acfw_custom_settings_plugin_tab' ) ) {
		/**
		 * Adding custom setting links at the plugin activation list.
		 *
		 * @param array  $links_array array containing the links to plugin.
		 * @param string $plugin_file_name plugin file name.
		 * @return array
		 */
		function mwb_acfw_custom_settings_plugin_tab( $links_array, $plugin_file_name ) {
			if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
				$links_array[] = '<a href="https://demo.makewebbetter.com/abandoned-cart-for-woocommerce" target="_blank"><img src="' . ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/Demo.svg" class="mwb_acfw_plugin_extra_custom_tab"></i>Demo</a>';
				$links_array[] = '<a href="https://docs.makewebbetter.com/abandoned-cart-for-woocommerce/" target="_blank"><img src="' . ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/Documentation.svg" class="mwb_acfw_plugin_extra_custom_tab"></i>Documentation</a>';
				$links_array[] = '<a href="https://makewebbetter.com/submit-query/" target="_blank"><img src="' . ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/Support.svg" class="mwb_acfw_plugin_extra_custom_tab"></i>Support</a>';
			}
				return $links_array;
		}
	}
	add_filter( 'plugin_row_meta', 'mwb_acfw_custom_settings_plugin_tab', 10, 2 );

} else {
		// Add admin error notice.
	if ( is_multisite() ) {
		add_action( 'network_admin_notices', 'mwb_abn_cart_plugin_error_notice' );
	} else {
		add_action( 'admin_notices', 'mwb_abn_cart_plugin_error_notice' );

	}

	add_action( 'admin_init', 'mwb_abn_cart_plugin_deactivate' );


	// Checking the existance of the same name function in this file.
	if ( ! function_exists( 'mwb_abn_cart_plugin_error_notice' ) ) {
		/**
		 * Function name  mwb_abn_cart_plugin_error_notice
		 * This function will show notice while deactivating without woocommerce
		 *
		 * @return void
		 * @since             1.0.0
		 */
		function mwb_abn_cart_plugin_error_notice() {

			require_once plugin_dir_path( __FILE__ ) . 'common/partials/abandoned-cart-for-woocommerce-deactivation-notice.php';
		}
	}

	// Checking the Existance of the same name funciton in the file.
	if ( ! function_exists( 'mwb_abn_cart_plugin_deactivate' ) ) {
		/**
		 * Function Name : mwb_abn_cart_plugin_deactivate.
		 * This Function will Be called at the deactivation time.
		 *
		 * @return void
		 * @since             1.0.0
		 */
		function mwb_abn_cart_plugin_deactivate() {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			unset( $_GET['activate'] );
		}
	}
}

