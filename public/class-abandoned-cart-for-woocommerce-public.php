<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * namespace abandoned_cart_for_woocommerce_public.
 *
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/public
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Abandoned_Cart_For_Woocommerce_Public {

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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function mwb_acfw_public_enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'public/src/scss/mwb-acfw-abandoned-cart-for-woocommerce-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		wp_enqueue_style( 'mwb_acfw_custom', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'public/src/scss/mwb-acfw_custom_css.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function mwb_acfw_public_enqueue_scripts() {

		$acfw_enable = get_option( 'mwb_enable_acfw' );
		if ( 'on' === $acfw_enable ) {
			$mwb_db_title = get_option( 'mwb_atc_title' );
			if ( $mwb_db_title ) {
				$title = $mwb_db_title;
			} else {
				$title = __( 'Enter Your Email Here', 'abandoned-cart-for-woocommerce' );
			}
			wp_register_script( $this->plugin_name, ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'public/src/js/mwb-acfw-abandoned-cart-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );
			wp_localize_script(
				$this->plugin_name,
				'acfw_public_param',
				array(
					'ajaxurl'          => admin_url( 'admin-ajax.php' ),
					'nonce'            => ( wp_create_nonce( 'custom' ) ),
					'atc_check'        => get_option( 'mwb_enable_atc_popup' ),
					'check_login_user' => is_user_logged_in(),
					'title'            => $title,
				)
			);
			wp_enqueue_script( $this->plugin_name );
			wp_enqueue_script( 'jquery-ui-dialog' );
		}
	}

	/**
	 * Function mwb_get_session_cart
	 * Function to get Cart Data from Session.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function mwb_insert_add_to_cart() {

		$mwb_prevent_ip = get_option( 'mwb_wacr_blocked_ip_address', false );
		if ( $mwb_prevent_ip ) {
			do_action( 'mwb_wacr_prevent_dumping', $mwb_prevent_ip );
		} else {
			$this->mwb_insert_data_to_cart_wacr();
		}
	}
	/**
	 * Function name mwb_insert_data_to_cart_wacr
	 * this function will insert the data into the cart
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function mwb_insert_data_to_cart_wacr() {
		global $wpdb;
		$session_cart = WC()->session->cart;
		if ( ! empty( $session_cart ) ) {
			$atcemail    = isset( $_COOKIE['mwb_atc_email'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['mwb_atc_email'] ) ) : '';
			$mwb_abndon_key = isset( $_COOKIE['mwb_cookie_data'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['mwb_cookie_data'] ) ) : '';
			if ( $mwb_abndon_key ) {
				$time   = gmdate( 'Y-m-d H:i:s' );
				$total  = WC()->session->cart_totals['total'];

				$ip              = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
				$encoded_data    = wp_json_encode( $session_cart );
				$guest_cart_data = $encoded_data;
				$mwb_data_result = $wpdb->get_results( $wpdb->prepare( 'SELECT `cart` FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE `mwb_abandon_key` =  %s  AND `ip_address` = %s', $mwb_abndon_key, $ip ) );

				if ( empty( $mwb_data_result ) ) {
					$insert_array = array(
						'u_id'          => 0,
						'email'         => $atcemail,
						'cart'          => $guest_cart_data,
						'time'          => $time,
						'total'         => $total,
						'cart_status'   => 0,
						'workflow_sent' => 0,
						'cron_status'   => 0,
						'mail_count'    => 0,
						'ip_address'    => $ip,
						'mwb_abandon_key' => $mwb_abndon_key,
					);
					$wpdb->insert(
						$wpdb->prefix . 'mwb_abandoned_cart',
						$insert_array
					);
					$mwb_newsletter_result = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_newsletter WHERE `email` =  %s', $atcemail ) );
					if ( empty( $mwb_newsletter_result ) ) {
						if ( ! empty( $atcemail ) ) {
							$wpdb->insert(
								$wpdb->prefix . 'mwb_newsletter',
								array(
									'email'        => $atcemail,
									'subscription' => '1',
									'mdmail'       => md5( $atcemail ),
								)
							);
						}
					}
				} else {
					$wpdb->update(
						$wpdb->prefix . 'mwb_abandoned_cart',
						array(
							'cart' => $guest_cart_data,
							'time' => $time,
							'total' => $total,
						),
						array(
							'mwb_abandon_key' => $mwb_abndon_key,
							'ip_address'      => $ip,
						)
					);
				}
				if ( is_user_logged_in() ) {

					$session_cart       = WC()->session->cart;
					$role               = wp_get_current_user();
					$current_user_role  = $role->roles[0];
					$mwb_selected_roles = get_option( 'mwb_user_roles' );
					if ( $mwb_selected_roles ) {

						if ( in_array( $current_user_role, $mwb_selected_roles, true ) ) {

							$session_cart = WC()->session->cart;
							$cus          = WC()->session->customer;
							$uid          = $cus['id'];
							$uemail       = $cus['email'];
							$time         = gmdate( 'Y-m-d H:i:s' );
							$total        = WC()->session->cart_totals['total'];
							$encoded_data = wp_json_encode( $session_cart );
							$cart_data = $encoded_data;

							$wpdb->update(
								$wpdb->prefix . 'mwb_abandoned_cart',
								array(
									'u_id' => $uid,
									'email' => $uemail,
									'cart' => $cart_data,
									'time' => $time,
									'total' => $total,
								),
								array(
									'ip_address' => $ip,
									'mwb_abandon_key' => $mwb_abndon_key,
								)
							);
						} else {
							$wpdb->delete(
								$wpdb->prefix . 'mwb_abandoned_cart',
								array(
									'ip_address' => $ip,
									'mwb_abandon_key' => $mwb_abndon_key,
								)
							);
						}
					}
				}
			}
		}

	}
	/**
	 * Function to show exit-intent popup to user while abandon the cart
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_add_tocart_popup() {
		$mwb_check_status_of_atc = get_option( 'mwb_enable_atc_popup' );
		if ( ( ! is_user_logged_in() && ( $mwb_check_status_of_atc ) ) && ( ( ! isset( $_COOKIE['mwb_atc_email'] ) ) && ( ! wp_doing_ajax() ) ) ) {
			require_once ABANDONED_CART_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/abandoned-cart-for-woocommerce-public-atc-popup.php';
		}
	}
	/**
	 * Function name mwb_generate_random_cookie
	 * this function will generate random cookie
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_generate_random_cookie() {

		if ( wp_doing_ajax() ) {
			return;
		}
		if ( ! isset( $_COOKIE['mwb_cookie_data'] ) ) {
			$random_cookie = substr( md5( microtime() ), wp_rand( 0, 26 ), 15 );
			setcookie( 'mwb_cookie_data', $random_cookie, time() + ( 86400 * 10 ), '/' );
		}

	}
	/**
	 * Fucntion to update data while login
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_update_cart_while_login() {

		global $wpdb;
		if ( is_user_logged_in() ) {
			$mwb_update_ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
			$mwb_abndon_key = isset( $_COOKIE['mwb_cookie_data'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['mwb_cookie_data'] ) ) : '';

				$role               = wp_get_current_user();
				$current_user_role  = $role->roles[0];
				$mwb_selected_roles = get_option( 'mwb_user_roles' );
			if ( $mwb_selected_roles ) {

				if ( in_array( $current_user_role, $mwb_selected_roles, true ) ) {

					$session_cart = WC()->session->cart;
					$cus          = WC()->session->customer;
					$uid          = $cus['id'];
					$uemail       = $cus['email'];
					$time         = gmdate( 'Y-m-d H:i:s' );
					$total        = WC()->session->cart_totals['total'];
					$encoded_data = wp_json_encode( $session_cart );
					$cart_data = $encoded_data;

					$wpdb->update(
						$wpdb->prefix . 'mwb_abandoned_cart',
						array(
							'u_id' => $uid,
							'email' => $uemail,
							'cart' => $cart_data,
							'time' => $time,
							'total' => $total,
						),
						array(
							'ip_address' => $mwb_update_ip,
							'mwb_abandon_key' => $mwb_abndon_key,
						)
					);
				} else {
					$wpdb->delete(
						$wpdb->prefix . 'mwb_abandoned_cart',
						array(
							'ip_address' => $mwb_update_ip,
							'mwb_abandon_key' => $mwb_abndon_key,
						)
					);
				}
			}
		}

	}

	/**
	 * Fucntion name mwb_check_cart
	 * This function is used to check cart data.
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_check_cart() {
		if ( isset( $_GET['ac_id'] ) ) {
			global $wpdb;
			$id = isset( $_GET['ac_id'] ) ? sanitize_text_field( wp_unslash( $_GET['ac_id'] ) ) : '';
			$ew_id = isset( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : '';
			apply_filters( 'mwb_mail_clicked_track', $id, $ew_id );
			$mwb_data_result = $wpdb->get_results( $wpdb->prepare( ' SELECT cart FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE id = %d ', $id ) );
			if ( ! empty( $mwb_data_result ) ) {
				$cartdata = json_decode( $mwb_data_result[0]->cart, true );
				WC()->session->set( 'cart', $cartdata );
				$check_status = $id;
				WC()->session->set( 'track_recovery', $check_status );
					wp_safe_redirect( wc_get_checkout_url() );
				exit;
			}
		}
	}
	/**
	 * Function name mwb_ac_conversion.
	 * this fucntion wil convert abanconed cart to recvered cart
	 *
	 * @param int $order_id current order id.
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_ac_conversion( $order_id ) {
		$mwb_update_ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$mwb_abndon_key = isset( $_COOKIE['mwb_cookie_data'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['mwb_cookie_data'] ) ) : '';

		global $wpdb;
		if ( isset( WC()->session->track_recovery ) ) {
			$id = WC()->session->track_recovery;
			do_action( 'mwb_wacr_check_coupon_status', $id );
			$order   = wc_get_order( $order_id );
			$orderid = $order_id;
			$crr = $order->get_currency();
			$total = $crr . ' ' . $order->get_total();
			$subject = __( 'Recovered Abandoned Cart', 'abandoned-cart-for-woocommerce' );
			$blogusers   = get_users( 'role=Administrator' );
			$admin_email = $blogusers[0]->data->user_email;
			$admin_name = $blogusers[0]->data->display_name;
			$content = '<h1>' . esc_html__( 'Hello', 'abandoned-cart-for-woocommerce' ) . ' ' . $admin_name . '</h1> <br><h3>' . esc_html__( 'Good News', 'abandoned-cart-for-woocommerce' ) . '</h3> <h3> ' . esc_html__( 'An Abandoned Cart has been Recovered with Order No', 'abandoned-cart-for-woocommerce' ) . ':<a href= "' . admin_url( 'post.php?post=' . $orderid . '&action=edit' ) . '"  >' . $orderid . '</a><br> ' . esc_html__( 'Total Amount', 'abandoned-cart-for-woocommerce' ) . ' : ' . $total . '</h3><br>   <h2>' . esc_html__( 'Thank You', 'abandoned-cart-for-woocommerce' ) . '</h2>';
			$status_mail = $wpdb->update(
				$wpdb->prefix . 'mwb_abandoned_cart',
				array(
					'cart_status' => 2,
				),
				array(
					'id' => $id,
				)
			);
			if ( $status_mail ) {
				wp_mail( $admin_email, $subject, $content );
			}
			WC()->session->__unset( 'track_recovery' );
		} else {
			$wpdb->delete(
				$wpdb->prefix . 'mwb_abandoned_cart',
				array(
					'ip_address'      => $mwb_update_ip,
					'mwb_abandon_key' => $mwb_abndon_key,
					'mail_count'      => 0,
				)
			);
		}

	}
	/**
	 * Function name mwb_get_mail_from_checkout
	 * this function will be used for capturing email form the checkout page.
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_get_mail_from_checkout() {
		if ( ! is_user_logged_in() ) {
			wp_register_script( 'mwb_ck_mail', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'public/src/js/mwb-acfw-custom.js', array( 'jquery' ), 'v1.0.0' . time(), false );

			wp_localize_script(
				'mwb_ck_mail',
				'mwb_ck_mail_ob',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'custom' ),
				)
			);
			wp_enqueue_script( 'mwb_ck_mail' );
		}

	}

	/**
	 * Function name mwb_customer_login_approval
	 * this function is used to recover the cart of logged in user
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function mwb_customer_login_approval_cart() {
		global $wpdb;

		if ( is_user_logged_in() ) {
			$mwb_update_ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
			$mwb_abndon_key = isset( $_COOKIE['mwb_cookie_data'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['mwb_cookie_data'] ) ) : '';
			$mwb_data_result = $wpdb->get_results( $wpdb->prepare( 'SELECT `cart_status` FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE `mwb_abandon_key` =  %s  AND `ip_address` = %s', $mwb_abndon_key, $mwb_update_ip ) );
			if ( ! empty( $mwb_data_result ) ) {
				$updated = $wpdb->update(
					$wpdb->prefix . 'mwb_abandoned_cart',
					array(
						'cart_status' => 2,
					),
					array(
						'mwb_abandon_key' => $mwb_abndon_key,
						'ip_address'      => $mwb_update_ip,
					)
				);
				if ( $updated ) {
					unset( $_COOKIE['mwb_cookie_data'] );
				}
			}
		}

	}

}
