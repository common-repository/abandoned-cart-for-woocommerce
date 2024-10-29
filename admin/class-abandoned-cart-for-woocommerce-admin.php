<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/admin
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Abandoned_Cart_For_Woocommerce_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook      The plugin page slug.
	 */
	public function mwb_acfw_admin_enqueue_styles( $hook ) {
		$screen = get_current_screen();
		// multistep form css.
		if ( ! acfw_mwb_standard_check_multistep() ) {
			$style_url        = ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'build/style-index.css';
			wp_enqueue_style(
				'mwb-admin-react-styles',
				$style_url,
				array(),
				time(),
				false
			);
			return;
		}
		if ( isset( $screen->id ) && 'makewebbetter_page_abandoned_cart_for_woocommerce_menu' == $screen->id ) {

			wp_enqueue_style( 'mwb-acfw-select2-css', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/select-2/abandoned-cart-for-woocommerce-select2.css', array(), time(), 'all' );

			wp_enqueue_style( 'mwb-acfw-meterial-css', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'mwb-acfw-meterial-css2', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'mwb-acfw-meterial-lite', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-lite.min.css', array(), time(), 'all' );

			wp_enqueue_style( 'mwb-acfw-meterial-icons-css', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/icon.css', array(), time(), 'all' );

			wp_enqueue_style( $this->plugin_name . '-admin-global', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/scss/mwb-acfw-abandoned-cart-for-woocommerce-admin-global.css', array( 'mwb-acfw-meterial-icons-css' ), time(), 'all' );

			wp_enqueue_style( $this->plugin_name, ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/scss/abandoned-cart-for-woocommerce-admin.scss', array(), $this->version, 'all' );

				wp_enqueue_style( 'wp-jquery-ui-dialog' );
		}
		wp_enqueue_style( 'mwb-abandon-setting-css', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/scss/mwb-afcw-abandoned-cart-for-woocommerce-setting.css', array(), time(), 'all' );

		wp_enqueue_style( 'chartcsss', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/chart.js/dist/Chart.css', array(), time(), 'all' );

		wp_enqueue_style( 'chartmin', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/chart.js/dist/Chart.min.css', array(), time(), 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook      The plugin page slug.
	 */
	public function mwb_acfw_admin_enqueue_scripts( $hook ) {

		if ( ! acfw_mwb_standard_check_multistep() ) {
			// js for the multistep from.
			$script_path      = '../../build/index.js';
			$script_asset_path = ABANDONED_CART_FOR_WOOCOMMERCE_DIR_PATH . 'build/index.asset.php';
			$script_asset      = file_exists( $script_asset_path )
				? require $script_asset_path
				: array(
					'dependencies' => array(
						'wp-hooks',
						'wp-element',
						'wp-i18n',
						'wc-components',
					),
					'version'      => filemtime( $script_path ),
				);
			$script_url        = ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'build/index.js';
			wp_register_script(
				'react-app-block',
				$script_url,
				$script_asset['dependencies'],
				$script_asset['version'],
				true
			);
			wp_enqueue_script( 'react-app-block' );
			wp_localize_script(
				'react-app-block',
				'frontend_ajax_object',
				array(
					'ajaxurl'            => admin_url( 'admin-ajax.php' ),
					'mwb_standard_nonce' => wp_create_nonce( 'ajax-nonce' ),
					'redirect_url' => admin_url( 'admin.php?page=abandoned_cart_for_woocommerce_menu' ),
				)
			);
			return;
		}

		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'makewebbetter_page_abandoned_cart_for_woocommerce_menu' == $screen->id ) {
			wp_enqueue_script( 'mwb-acfw-select2', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/select-2/abandoned-cart-for-woocommerce-select2.js', array( 'jquery' ), time(), false );

			wp_enqueue_script( 'mwb-acfw-metarial-js', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-web.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-acfw-metarial-js2', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-acfw-metarial-lite', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-lite.min.js', array(), time(), false );

			wp_register_script( $this->plugin_name . 'admin-js', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/mwb-acfw-abandoned-cart-for-woocommerce-admin.js', array( 'jquery', 'mwb-acfw-select2', 'mwb-acfw-metarial-js', 'mwb-acfw-metarial-js2', 'mwb-acfw-metarial-lite' ), $this->version, false );

			$tab_check = false;
			if ( 'abandoned-cart-for-woocommerce-analytics' === ( ( isset( $_GET['acfw_tab'] ) ) ? sanitize_text_field( wp_unslash( $_GET['acfw_tab'] ) ) : false ) ) {
				$tab_check = true;
			}
			wp_localize_script(
				$this->plugin_name . 'admin-js',
				'acfw_admin_param',
				array(
					'ajaxurl'             => admin_url( 'admin-ajax.php' ),
					'reloadurl'           => admin_url( 'admin.php?page=abandoned_cart_for_woocommerce_menu' ),
					'acfw_gen_tab_enable' => get_option( 'acfw_radio_switch_demo' ),
					'tab'                 => $tab_check,
				)
			);

			wp_enqueue_script( $this->plugin_name . 'admin-js' );

			$acfw_enable = get_option( 'mwb_enable_acfw' );
			if ( 'on' === $acfw_enable ) {
				wp_register_script( 'demo_js', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/mwb-afcw-custom.js', array( 'jquery' ), $this->version, false );

						wp_localize_script(
							'demo_js',
							'demo_js_ob',
							array(
								'ajaxurl' => admin_url( 'admin-ajax.php' ),
								'nonce'   => ( wp_create_nonce( 'custom' ) ),
							)
						);

					wp_enqueue_script( 'demo_js' );
					wp_enqueue_script( 'jquery-ui-dialog' );

				// Chart.min.js.
				wp_enqueue_script( 'chart', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/chart.js/dist/Chart.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( 'bundle', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/chart.js/dist/Chart.bundle.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( 'bundle-min', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/chart.js/dist/Chart.bundle.min.js', array( 'jquery' ), $this->version, false );
			}
		}
	}

	/**
	 * Adding settings menu for Abandoned Cart for WooCommerce.
	 *
	 * @since    1.0.0
	 */
	public function mwb_acfw_options_page() {
		global $submenu;
		if ( empty( $GLOBALS['admin_page_hooks']['mwb-plugins'] ) ) {
			add_menu_page( 'MakeWebBetter', 'MakeWebBetter', 'read', 'mwb-plugins', array( $this, 'mwb_plugins_listing_page' ), ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/MWB_Grey-01.svg', 15 );
			if ( acfw_mwb_standard_check_multistep() ) {
				add_submenu_page( 'mwb-plugins', 'Home', 'Home', 'manage_options', 'home', array( $this, 'makewebbetter_welcome_callback_function' ) );
			}
			$acfw_menus =
			// desc - filter for trial.
			apply_filters( 'mwb_add_plugins_menus_array', array() );

			if ( is_array( $acfw_menus ) && ! empty( $acfw_menus ) ) {
				foreach ( $acfw_menus as $acfw_key => $acfw_value ) {
					add_submenu_page( 'mwb-plugins', $acfw_value['name'], $acfw_value['name'], 'manage_options', $acfw_value['menu_link'], array( $acfw_value['instance'], $acfw_value['function'] ) );
				}
			}
		} else {
			$is_home = false;
			if ( ! empty( $submenu['mwb-plugins'] ) ) {
				foreach ( $submenu['mwb-plugins'] as $key => $value ) {
					if ( 'Home' === $value[0] ) {
						$is_home = true;
					}
				}
				if ( ! $is_home ) {
					if ( acfw_mwb_standard_check_multistep() ) {
						add_submenu_page( 'mwb-plugins', 'Home', 'Home', 'manage_options', 'home', array( $this, 'makewebbetter_welcome_callback_function' ), 1 );
					}
				}
			}
		}
	}

	/**
	 *
	 * Adding the default menu into the WordPress menu.
	 *
	 * @name makewebbetter_callback_function
	 * @since 1.0.0
	 */
	public function makewebbetter_welcome_callback_function() {
		include ABANDONED_CART_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/abandoned-cart-for-woocommerce-welcome.php';
	}

	/**
	 * Removing default submenu of parent menu in backend dashboard
	 *
	 * @since   1.0.0
	 */
	public function mwb_acfw_remove_default_submenu() {
		global $submenu;
		if ( is_array( $submenu ) && array_key_exists( 'mwb-plugins', $submenu ) ) {
			if ( isset( $submenu['mwb-plugins'][0] ) ) {
				unset( $submenu['mwb-plugins'][0] );
			}
		}
	}


	/**
	 * Abandoned Cart for WooCommerce mwb_acfw_admin_submenu_page.
	 *
	 * @since 1.0.0
	 * @param array $menus Marketplace menus.
	 */
	public function mwb_acfw_admin_submenu_page( $menus = array() ) {
		$menus[] = array(
			'name'      => 'Abandoned Cart for WooCommerce',
			'slug'      => 'abandoned_cart_for_woocommerce_menu',
			'menu_link' => 'abandoned_cart_for_woocommerce_menu',
			'instance'  => $this,
			'function'  => 'mwb_acfw_options_menu_html',
		);
		return $menus;
	}


	/**
	 * Abandoned Cart for WooCommerce mwb_plugins_listing_page.
	 *
	 * @since 1.0.0
	 */
	public function mwb_plugins_listing_page() {
		$active_marketplaces = apply_filters( 'mwb_add_plugins_menus_array', array() );
		if ( is_array( $active_marketplaces ) && ! empty( $active_marketplaces ) ) {
			require ABANDONED_CART_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/welcome.php';
		}
	}

	/**
	 * Abandoned Cart for WooCommerce admin menu page.
	 *
	 * @since    1.0.0
	 */
	public function mwb_acfw_options_menu_html() {

		include_once ABANDONED_CART_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/abandoned-cart-for-woocommerce-admin-dashboard.php';
	}

	/**
	 * Function name mwb_abandon_setting_tabs
	 * this fucntion will used to craete setting tabs for admin dashboard
	 *
	 * @param array $acfw_default_tabs all custom setting tabs.
	 * @return array
	 * @since             1.0.0
	 */
	public function mwb_abandon_setting_tabs( $acfw_default_tabs ) {
		$acfw_default_tabs['abandoned-cart-for-woocommerce-email-workflow'] = array(
			'title' => esc_html__( 'Email Work Flow', 'abandoned-cart-for-woocommerce' ),
			'name'  => 'abandoned-cart-for-woocommerce-email-workflow',
		);
		$acfw_default_tabs['abandoned-cart-for-woocommerce-report'] = array(
			'title' => esc_html__( 'Abandon Cart Reports ', 'abandoned-cart-for-woocommerce' ),
			'name'  => 'abandoned-cart-for-woocommerce-report',
		);
		$acfw_default_tabs['abandoned-cart-for-woocommerce-analytics'] = array(
			'title' => esc_html__( 'Abandon Cart Analytics ', 'abandoned-cart-for-woocommerce' ),
			'name'  => 'abandoned-cart-for-woocommerce-analytics',
		);
		$acfw_default_tabs['abandoned-cart-for-woocommerce-overview'] = array(
			'title' => esc_html__( ' Overview', 'abandoned-cart-for-woocommerce' ),
			'name'  => 'abandoned-cart-for-woocommerce-overview',
		);

		$acfw_default_tabs = apply_filters( 'mwb_acfw_license_tab', $acfw_default_tabs );
		return $acfw_default_tabs;
	}


	/**
	 * Abandoned Cart for WooCommerce admin menu page.
	 *
	 * @since    1.0.0
	 * @param array $acfw_settings_general Settings fields.
	 */
	public function mwb_acfw_admin_general_settings_page( $acfw_settings_general ) {
		$roles = wp_roles();
		$role  = $roles->role_names;

		$acfw_settings_general = array(
			array(
				'title'       => __( 'Enable plugin', 'abandoned-cart-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Enable plugin to start the functionality.', 'abandoned-cart-for-woocommerce' ),
				'id'          => 'mwb_enable_acfw',
				'value'       => get_option( 'mwb_enable_acfw' ),
				'class'       => 'acfw-radio-switch-class',
				'options'     => array(
					'yes' => __( 'YES', 'abandoned-cart-for-woocommerce' ),
					'no'  => __( 'NO', 'abandoned-cart-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Add to Cart Pop-Up', 'abandoned-cart-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Enable this to show pop-up at the add to cart time.', 'abandoned-cart-for-woocommerce' ),
				'id'          => 'mwb_enable_atc_popup',
				'value'       => get_option( 'mwb_enable_atc_popup' ),
				'class'       => 'm-radio-switch-class',
				'options'      => array(
					'yes' => __( 'YES', 'abandoned-cart-for-woocommerce' ),
					'no'  => __( 'NO', 'abandoned-cart-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Add to Cart Pop-Up Title', 'abandoned-cart-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Enter title here to show on add to cart pop-up.', 'abandoned-cart-for-woocommerce' ),
				'id'          => 'mwb_atc_title',
				'value'       => get_option( 'mwb_atc_title' ),
				'class'       => 'acfw-text-class',
				'placeholder' => __( 'Add to Cart title', 'abandoned-cart-for-woocommerce' ),
			),
			array(
				'title'       => __( 'Add to Cart Pop-Up Text', 'abandoned-cart-for-woocommerce' ),
				'type'        => 'textarea',
				'description' => __( 'Enter text here to show on add to cart pop-up.', 'abandoned-cart-for-woocommerce' ),
				'id'          => 'mwb_atc_text',
				'value'       => get_option( 'mwb_atc_text' ),
				'class'       => 'acfw-text-class',
				'placeholder' => __( 'Add to Cart text', 'abandoned-cart-for-woocommerce' ),
			),
			array(
				'title'       => __( 'Cut-off time', 'abandoned-cart-for-woocommerce' ),
				'type'        => 'number',
				'description' => __( 'Enter time in HOURS after which a cart will be treated as abandoned.', 'abandoned-cart-for-woocommerce' ),
				'id'          => 'mwb_acfw_cut_off_time',
				'value'       => get_option( 'mwb_acfw_cut_off_time' ),
				'min'         => 1,
				'class'       => 'm-number-class',
				'placeholder' => __( 'Enter Time', 'abandoned-cart-for-woocommerce' ),
			),
			array(
				'title'       => __( 'Delete abandoned cart history', 'abandoned-cart-for-woocommerce' ),
				'type'        => 'number',
				'description' => __( 'Enter the number of days before which you dont want to keep history of abandoned cart. Remain blank to never delete history automatically.', 'abandoned-cart-for-woocommerce' ),
				'id'          => 'mwb_delete_time_for_ac',
				'value'       => get_option( 'mwb_delete_time_for_ac' ),
				'min'         => 0,
				'class'       => 'm-number-class',
				'placeholder' => __( 'Enter Time', 'abandoned-cart-for-woocommerce' ),
			),
			array(
				'title'       => __( 'User role for tracking ', 'abandoned-cart-for-woocommerce' ),
				'type'        => 'multiselect',
				'description' => __( 'Select user roles for which you want to track abandoned carts(By default only “GUEST USERS” are tracked).', 'abandoned-cart-for-woocommerce' ),
				'id'          => 'mwb_user_roles',
				'value'       => get_option( 'mwb_user_roles' ),
				'class'       => 'm-multiselect-class mwb-defaut-multiselect',
				'placeholder' => '',
				'options'     => $role,
			),
			array(
				'title'       => __( 'Coupon code prefix', 'abandoned-cart-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Add a pattern in which you want the coupons to be generated for Cart Abandoners. Generated coupon will have prefix_<random_5_digit_alphanumeric>.', 'abandoned-cart-for-woocommerce' ),
				'id'          => 'mwb_acfw_coupon_prefix',
				'value'       => get_option( 'mwb_acfw_coupon_prefix' ),
				'class'       => 'm-text-class',
				'placeholder' => __( 'Enter Coupon code', 'abandoned-cart-for-woocommerce' ),
			),
			array(
				'title'       => __( 'Coupon expiry', 'abandoned-cart-for-woocommerce' ),
				'type'        => 'number',
				'description' => __( 'Enter the number of hours after which coupon will be expired if not used. Time will start at the time of coupon send.', 'abandoned-cart-for-woocommerce' ),
				'id'          => 'mwb_acfw_coupon_expiry',
				'value'       => get_option( 'mwb_acfw_coupon_expiry' ),
				'min'         => 0,
				'class'       => 'm-number-class',
				'placeholder' => __( 'Enter Time', 'abandoned-cart-for-woocommerce' ),
			),
			array(
				'title'       => __( 'Coupon Discount', 'abandoned-cart-for-woocommerce' ),
				'type'        => 'number',
				'description' => __( 'Enter the percentage discount (between 1-100) which will apply on abandoned cart.', 'abandoned-cart-for-woocommerce' ),
				'id'          => 'mwb_acfw_coupon_discount',
				'value'       => get_option( 'mwb_acfw_coupon_discount' ),
				'min'         => 0,
				'max'         => 100,
				'class'       => 'm-number-class',
				'placeholder' => __( 'Enter Discount', 'abandoned-cart-for-woocommerce' ),
			),
		);
		$acfw_settings_general   = apply_filters( 'mwb_acfw_general_pro_settings', $acfw_settings_general );
		$acfw_settings_general[] = array(
			'type'        => 'button',
			'id'          => 'save_general',
			'button_text' => __( 'Save Settings', 'abandoned-cart-for-woocommerce' ),
			'class'       => 'm-button-class myclick',
		);

		return $acfw_settings_general;
	}

	/**
	 * Abandoned Cart for WooCommerce save tab settings.
	 *
	 * @since 1.0.0
	 */
	public function mwb_acfw_admin_save_tab_settings() {
		global $error_notice;
		global $result;
		if ( isset( $_POST['save_general'] ) ) {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'general_setting_nonce' ) ) {
				return;
			}
			$mwb_acfw_gen_flag = false;
			$acfw_genaral_settings = apply_filters( 'acfw_general_settings_array', array() );
			$acfw_button_index     = array_search( 'submit', array_column( $acfw_genaral_settings, 'type' ) );
			if ( isset( $acfw_button_index ) && ( null == $acfw_button_index || '' == $acfw_button_index ) ) {
				$acfw_button_index = array_search( 'button', array_column( $acfw_genaral_settings, 'type' ) );
			}
			if ( isset( $acfw_button_index ) && '' !== $acfw_button_index ) {
				unset( $acfw_genaral_settings[ $acfw_button_index ] );
				if ( is_array( $acfw_genaral_settings ) && ! empty( $acfw_genaral_settings ) ) {
					foreach ( $acfw_genaral_settings as $acfw_genaral_setting ) {
						if ( isset( $acfw_genaral_setting['id'] ) && '' !== $acfw_genaral_setting['id'] ) {
							if ( isset( $_POST[ $acfw_genaral_setting['id'] ] ) ) {
								$result = isset( $_POST ) ? map_deep( wp_unslash( $_POST ), 'sanitize_text_field' ) : '';
								update_option( $acfw_genaral_setting['id'], $result[ $acfw_genaral_setting ['id'] ] );
							} else {
								update_option( $acfw_genaral_setting['id'], '' );
							}
						} else {
							$mwb_acfw_gen_flag = true;
						}
					}
				}

				if ( $mwb_acfw_gen_flag ) {
					$mwb_acfw_error_text = esc_html__( 'Id of some field is missing', 'abandoned-cart-for-woocommerce' );
				} else {
					$error_notice        = false;
					$mwb_acfw_error_text = esc_html__( 'Settings saved !', 'abandoned-cart-for-woocommerce' );
				}
			}
		}
	}


	/**
	 * Function mwb_save_email_tab_settings
	 * This function is used to save the email settings.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function mwb_save_email_tab_settings() {
		global $error_notice;
		if ( isset( $_POST['submit_workflow'] ) ) {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'workflow_nonce' ) ) {
				return;
			}
			global $wpdb;
			$new_chekcbox = array();
			$checkbox_arr = array_key_exists( 'checkbox', $_POST ) ? map_deep( wp_unslash( $_POST['checkbox'] ), 'sanitize_text_field' ) : '';
			$time_arr     = array_key_exists( 'time', $_POST ) ? map_deep( wp_unslash( $_POST['time'] ), 'sanitize_text_field' ) : '';
			$email_arr    = array_key_exists( 'email_workflow_content', $_POST ) ? map_deep( wp_unslash( $_POST['email_workflow_content'] ), 'wp_kses_post' ) : '';
			$mail_subject = array_key_exists( 'subject', $_POST ) ? map_deep( wp_unslash( $_POST['subject'] ), 'sanitize_text_field' ) : '';
			$setting      = apply_filters( 'mwb_wacr_mail_send_setting', array() );

			$total_count = count( $mail_subject );

			/**
			 * Register strings for translation.
			 */
			if ( function_exists( 'icl_register_string' ) ) {
				icl_register_string( 'Mail_subject', 'Mail subject - input field', $mail_subject );
			}
			$i = 0;
			while ( $i < $total_count ) {
				if ( ! array_key_exists( $i, $checkbox_arr ) ) {
					$new_chekcbox[ $i ] = 'off';
				} else {
					$new_chekcbox[ $i ] = $checkbox_arr[ $i ];
				}
				$i++;
			}
			foreach ( $new_chekcbox as $key => $value ) {
				$package = array(
					'kind' => 'Layout',
					'name' => 'workflow' . $key,
					'title' => 'workflow_title' . $key,
					'edit_link' => '',
					'view_link' => '',
				);
				$subject_package = array(
					'kind' => 'Layout',
					'name' => 'subject' . $key,
					'title' => 'subject_title' . $key,
					'edit_link' => '',
					'view_link' => '',
				);
				$mwb_email_html_arr  = get_option( 'mwb_email_html_key' );
					$id             = isset( $mwb_email_html_arr[ $key + 1 ] ) ? $mwb_email_html_arr[ $key + 1 ] : false;
					$enable         = $value;
					$time           = $time_arr[ $key ];
					$sub            = $mail_subject[ $key ];
					$email          = $email_arr[ $key ];
				do_action( 'wpml_register_string', $email, 'Email WOrkflow content' . $key, $package, 'workflow' . $key, 'VISUAL' );
				do_action( 'wpml_register_string', $sub, 'Email WOrkflow Subject' . $key, $subject_package, 'subject' . $key, 'VISUAL' );

					$update_array = array(
						'ew_enable'        => $enable,
						'ew_mail_subject' => $sub,
						'ew_content'       => $email,
						'ew_initiate_time' => $time,
					);
					if ( ! empty( $setting ) ) {

						$update_array = apply_filters( 'mwb_wacr_save_condition', $update_array, $setting[ $key ] );
					}
					$db_mail_data = $wpdb->get_results( $wpdb->prepare( ' SELECT * FROM ' . $wpdb->prefix . 'mwb_email_workflow where ew_id = %s', $id ) );
					if ( ! empty( $db_mail_data ) ) {
						$wpdb->update(
							$wpdb->prefix . 'mwb_email_workflow',
							$update_array,
							array(
								'ew_id' => $id,
							)
						);
					} else {
						$update_array = apply_filters( 'mwb_insert_dynamic_workflow', $update_array );
					}
			}
			$error_notice = false;
		}
	}

	/**
	 * Callback function for ajax request handling.
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_abdn_cart_viewing_cart_from_quick_view() {
		global $wpdb;
		check_ajax_referer( 'custom', 'nonce' );
		$cart_id   = sanitize_text_field( wp_unslash( isset( $_POST['cart_id'] ) ? $_POST['cart_id'] : '' ) );
		$cart_data = $wpdb->get_results( $wpdb->prepare( ' SELECT cart FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE id = %d ', $cart_id ) );
		$cart      = json_decode( $cart_data[0]->cart, true );
		$currency = get_option( 'woocommerce_currency' );
		$symbol = get_woocommerce_currency_symbol( $currency );
		require_once ABANDONED_CART_FOR_WOOCOMMERCE_DIR_PATH . 'admin/template/abandoned-cart-for-woocommerce-cart-quick-view.php';
		wp_die();
	}

	/**
	 * Function name mwb_get_exit_location
	 * this function will store details about user from where he left the page.
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_get_exit_location() {
		check_ajax_referer( 'custom', 'nonce' );
		$left_url    = isset( $_POST['cust_url'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_url'] ) ) : '';
		global $wpdb;
		$ip             = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$mwb_abndon_key = isset( $_COOKIE['mwb_cookie_data'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['mwb_cookie_data'] ) ) : '';
		$res = $wpdb->get_results( $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE mwb_abandon_key = %s AND ip_address = %s', $mwb_abndon_key, $ip ) );
		if ( ! empty( $res ) ) {
			$wpdb->update(
				$wpdb->prefix . 'mwb_abandoned_cart',
				array(
					'left_page' => $left_url,
				),
				array(
					'mwb_abandon_key' => $mwb_abndon_key,
					'ip_address'      => $ip,
				)
			);
		}
		wp_die();
	}


	/**
	 * Function to get the data
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_get_data() {
		global $wpdb,$wp_query;
		$data = $wpdb->get_results( 'SELECT monthname(time) as MONTHNAME,count(id) as count  FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE cart_status = 1 group by monthname(time) order by time ASC' );
		echo wp_json_encode( $data );
		wp_die();

	}
	/**
	 *  Function name mwb_save__guest_mail()
	 * This Function is used to save email that has been captured from the checkuot page.
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_save__guest_mail() {
		check_ajax_referer( 'custom', 'nonce' );

		global $wpdb;
		$mwb_abadoned_key = isset( $_COOKIE['mwb_cookie_data'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['mwb_cookie_data'] ) ) : '';
		$mail             = ! empty( $_POST['guest_user_email'] ) ? sanitize_text_field( wp_unslash( $_POST['guest_user_email'] ) ) : '';
		$ip_address       = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$wpdb->update(
			$wpdb->prefix . 'mwb_abandoned_cart',
			array(
				'email' => $mail,
			),
			array(
				'ip_address' => $ip_address,
				'mwb_abandon_key' => $mwb_abadoned_key,
			)
		);
		wp_die();
	}
	/**
	 * Function name mwb_delete_cart_record_callback
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function mwb_delete_cart_record_callback() {

		if ( isset( $_GET['deleted'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			require_once ABANDONED_CART_FOR_WOOCOMMERCE_DIR_PATH . 'admin/template/abandoned-cart-for-woocommerce-cart-record-delete.php';
		}
	}

	/**
	 * This function is used to save tracking values.
	 *
	 * @since 1.0.5
	 * @return void
	 */
	public function mwb_acfw_save_tracking_value() {

		if ( isset( $_POST['mwb_acfw_save_tracking_val'] ) ) {

			if ( ! isset( $_POST['acfw_tracking_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['acfw_tracking_nonce'] ) ), 'acfw_tracking_nonce' ) ) {
				return;
			}
			$acfw_enable_tracking = isset( $_POST['acfw_enable_tracking'] ) ? sanitize_text_field( wp_unslash( $_POST['acfw_enable_tracking'] ) ) : '';
			update_option( 'acfw_enable_tracking', $acfw_enable_tracking );
		}
	}

}
