<?php
/**
 * The common-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/common
 */

/**
 * The common-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the common-specific stylesheet and JavaScript.
 *
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/common
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Abandoned_Cart_For_Woocommerce_Common {
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
	public function mwb_acfw_common_enqueue_styles( $hook ) {

		wp_enqueue_style( $this->plugin_name, ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'common/src/scss/abandoned-cart-for-woocommerce-common.scss', array(), $this->version, 'all' );
		wp_enqueue_style( 'common-custom-css', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'common/src/scss/abandoned-cart-common-css.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook      The plugin page slug.
	 */
	public function mwb_acfw_common_enqueue_scripts( $hook ) {
		wp_register_script( $this->plugin_name . 'common-js', ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'common/src/js/abandoned-cart-for-woocommerce-common.js', array( 'jquery', 'mwb-acfw-select2', 'mwb-acfw-metarial-js', 'mwb-acfw-metarial-js2', 'mwb-acfw-metarial-lite' ), $this->version, false );
	}

	/**
	 * Function name mwb_schedule_check_cart_status.
	 * this function will used to schedule first cron to check cart status.
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_schedule_check_cart_status() {
		if ( isset( $_POST['save_general'] ) ) {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'general_setting_nonce' ) ) {
				return;
			}
			$sch = wp_next_scheduled( 'mwb_schedule_first_cron' );
			if ( $sch ) {
				wp_unschedule_event( $sch, 'mwb_schedule_first_cron' );
			}
			wp_schedule_event( time(), 'mwb_custom_time', 'mwb_schedule_first_cron' );
			$this->mwb_delete_ac_history_limited_time();

		}
	}
	/**
	 * Function name mwb_add_cron_interval
	 *
	 * @param array $schedules array.
	 * @return array
	 * @since             1.0.0
	 */
	public function mwb_add_cron_interval( $schedules ) {

		$time                         = get_option( 'mwb_acfw_cut_off_time', '1' );
		$del_time                     = get_option( 'mwb_delete_time_for_ac' );
		$schedules['mwb_custom_time'] = array(
			'interval' => (int) $time * 60 * 60,
			'display'  => esc_html__( 'Every custom time', 'abandoned-cart-for-woocommerce' ),
		);
		if ( $del_time ) {
			$schedules['mwb_del_ac_time'] = array(
				'interval' => $del_time * 86400,
				'display'  => esc_html__( 'Delete custom time', 'abandoned-cart-for-woocommerce' ),
			);
		}

			return $schedules;

	}

	/**
	 * Set mail type to html
	 *
	 * @return array
	 * @since             1.0.0
	 */
	public function mwb_set_type_wp_mail() {
		return 'text/html';

	}

	/**
	 * Function name mwb_check_status.
	 * This function will Used to check the status of cart
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_check_status() {
		global $wpdb;
		$result          = $wpdb->get_results( 'SELECT id,time FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE  cart_status = 0' );
		$mwb_cutoff_time = get_option( 'mwb_acfw_cut_off_time' );
		$mwb_converted_cut_off_time = $mwb_cutoff_time * 60 * 60;
		foreach ( $result as $k => $val ) {
			$mwb_db_time    = $val->time;
			$ac_id          = $val->id;
			$current_time   = time();
			$diffrence_time = $current_time - strtotime( $mwb_db_time );
			if ( $diffrence_time > $mwb_converted_cut_off_time ) {
				$get_status = $wpdb->update(
					$wpdb->prefix . 'mwb_abandoned_cart',
					array(
						'cart_status'  => 1,
					),
					array(
						'id' => $ac_id,
					)
				);
				if ( $get_status ) {
					do_action( 'mwb_acfw_abandon_admin_sent_mail', $ac_id );
				}
			}
		}
		$this->mwb_schedule_first_timer_cron();
	}


	/**
	 * Function name mwb_schedule_first_timer_cron
	 * Funticon TO set timer.
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_schedule_first_timer_cron() {
		update_option( 'mwb_abandon_timer', 1 );
		global $wpdb;
		$result1  = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_email_workflow WHERE ew_id = 1' );
		$check_enable           = $result1[0]->ew_enable;
		$fetch_time             = $result1[0]->ew_initiate_time;
		$converted_time_seconds = $fetch_time * 60 * 60;
		if ( 'on' === $check_enable ) {

			$result = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE cart_status = 1 AND workflow_sent = 0' );

			foreach ( $result as $k => $value ) {
				$abandon_time = $value->time;
				$email        = $value->email;
				$ac_id        = $value->id;
				$cron_status  = $value->cron_status;
				$sending_time = gmdate( 'Y-m-d H:i:s', strtotime( $abandon_time ) + $converted_time_seconds );
				$this->mwb_first_mail_sending( $sending_time, $cron_status, $email, $ac_id );
			}
		}
	}
	/**
	 * Function name mwb_first_mail_sending
	 * this fucntion will sechdule the first as
	 *
	 * @param int    $sending_time sending time.
	 * @param int    $cron_status cron status.
	 * @param string $email email.
	 * @param int    $ac_id ac_id.
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_first_mail_sending( $sending_time, $cron_status, $email, $ac_id ) {
		if ( '0' === $cron_status ) {
			if ( '' !== $email ) {
				as_schedule_single_action( $sending_time, 'send_email_hook', array( $email, $ac_id ) );
			}
		}

	}
	/**
	 * Function name mwb_mail_sent
	 * this function is used to send first mail
	 *
	 * @param string $email get the email address.
	 * @param int    $ac_id ac_id.
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_mail_sent( $email, $ac_id ) {
		$mwb_acfw_coupon_discount = get_option( 'mwb_acfw_coupon_discount' );
		if ( $mwb_acfw_coupon_discount ) {
			$amount        = $mwb_acfw_coupon_discount; // Amount.
		}
		$check = false;
		global $wpdb;
		$ew_id       = 1;
		$result1     = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_email_workflow WHERE ew_id = 1' );
		$content     = $result1[0]->ew_content;
		$ew_id       = $result1[0]->ew_id;
		$subject     = $result1[0]->ew_mail_subject;
		$email       = is_array( $email ) ? array_shift( $email ) : $email;
		$ac_id       = is_array( $ac_id ) ? array_shift( $ac_id ) : $ac_id;

		$mwb_acfw_coupon_discount = get_option( 'mwb_acfw_coupon_discount' );
		$mwb_acfw_coupon_expiry   = get_option( 'mwb_acfw_coupon_expiry' );
		$mwb_acfw_coupon_prefix   = get_option( 'mwb_acfw_coupon_prefix' );

		$content = apply_filters( 'mwb_wacr_mail_tracking', $content, $ew_id, $ac_id );
		$url     = wc_get_checkout_url();
		$new_url = add_query_arg(
			array(
				'id'    => (string) trim( $ew_id ),
				'ac_id' => (string) trim( $ac_id ),
			),
			$url
		);
		$checkout_url       = '<a href = "' . $new_url . '" style="background-color: #2199f5;	padding: 7px 14px; font-size: 16px;	color: #f1f1f1;	font-weight: 600; text-decoration: none; border-radius: 4px; box-shadow: 0 4px 10px #999;" >Checkout Now</a><br>';
		$time          = gmdate( 'Y-m-d H:i:s' );
		$coupon_result = $wpdb->get_results( $wpdb->prepare( ' SELECT coupon_code, cart FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE id = %d ', $ac_id ) );
		$mwb_db_coupon = $coupon_result[0]->coupon_code;
		$mwb_cart = json_decode( $coupon_result[0]->cart, true );
		if ( strpos( $content, '{checkout}' ) ) {
			$sending_content = str_replace( '{checkout}', $checkout_url, $content );
		} else {
			$sending_content = $content;
		}
		if ( strpos( $sending_content, '{cart}' ) ) {
			$cart_data  = $wpdb->get_results( $wpdb->prepare( 'SELECT cart FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE id = %d ', $ac_id ) );
			$dbcart = $cart_data[0]->cart;
			$decoded_cart = json_decode( $dbcart, true );
			$table_content = '<h2>Your Cart</h2><br><table style=" border-collapse: collapse; width: 50%; table-layout: fixed;"><tr> <th style="  background: #e5f4fe; border: 1px solid #000000; text-align: center;	padding: 10px 0;">Product Name</th><th style="background: #e5f4fe; border: 1px solid #000000; text-align: center;	padding: 10px 0;">Quantity</th></tr>';
			foreach ( $decoded_cart as $k => $val ) {
				$pid = $val['product_id'];
				$product = wc_get_product( $pid );
				$pname = $product->get_title();
				$quantity = $val['quantity'];
				$table_content .= '<tr><td style=" border: 1px solid #000000; text-align: center; padding: 10px 0;">' . esc_html( $pname ) . '</td> <td style="border: 1px solid #000000; text-align: center; padding: 10px 0;">' . esc_html( $quantity ) . '</td> </tr>';

			}
			$table_content .= '</table><br>';
			$final_content = str_replace( '{cart}', $table_content, $sending_content );
		} else {
			$final_content = $sending_content;
		}
		if ( $mwb_acfw_coupon_discount ) {

			if ( null === $mwb_db_coupon ) {
				if ( strpos( $final_content, '{coupon}' ) ) {
						$rand = substr( md5( microtime() ), wp_rand( 0, 26 ), 5 );
						$coupon_expiry_time = time() + ( $mwb_acfw_coupon_expiry * 60 * 60 );
						$mwb_coupon_name = $mwb_acfw_coupon_prefix . $rand;

						/**
						* Create a coupon for sending in email.
						*/
						$coupon_code   = $mwb_coupon_name; // Code.
						$amount        = $mwb_acfw_coupon_discount; // Amount.
						$discount_type = 'percent'; // Type: percent.

						$coupon = array(
							'post_title'   => $coupon_code,
							'post_content' => '',
							'post_status'  => 'publish',
							'post_author'  => 1,
							'post_type'    => 'shop_coupon',
						);

						$new_coupon_id = wp_insert_post( $coupon );

						update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
						update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
						update_post_meta( $new_coupon_id, 'usage_limit', 1 );
						update_post_meta( $new_coupon_id, 'individual_use', 'no' );
						$arr_id = array();
						foreach ( $mwb_cart as $key => $mwb_value ) {

							$id_s = $mwb_value['product_id'];
							$arr_id[] = $id_s;
						}
						$main_arr_id = implode( ',', $arr_id );
						update_post_meta( $new_coupon_id, 'product_ids', $main_arr_id );
						update_post_meta( $new_coupon_id, 'expiry_date', $coupon_expiry_time );
						update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
						update_post_meta( $new_coupon_id, 'free_shipping', 'no' );

						$db_code_coupon_mwb   = wc_get_coupon_code_by_id( $new_coupon_id );
						$final_sending_coupon = '<h6 style="font-size: 16px; margin: 20px 0 0; color: red; border: 1px solid red; width: fit-content; padding: 7px;"> Your Coupon Code: ' . $db_code_coupon_mwb . ' <br> Discount : ' . $amount . '% </h6><br><br>';
						$final_content = str_replace( '{coupon}', $final_sending_coupon, $final_content );
							$wpdb->update(
								$wpdb->prefix . 'mwb_abandoned_cart',
								array(
									'coupon_code' => $db_code_coupon_mwb,
								),
								array(
									'id' => $ac_id,
								)
							);

				}
			} else {

				$final_sending_coupon_mwb_db = '<h6 style="font-size: 16px; margin: 20px 0 0; color: red; border: 1px solid red; width: fit-content; padding: 7px;"> Your Coupon Code: ' . $mwb_db_coupon . '<br> Discount : ' . $amount . '% </h6><br><br>';
				$final_content = str_replace( '{coupon}', $final_sending_coupon_mwb_db, $final_content );
			}
		}
		$mail_subscription_check  = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_newsletter WHERE email = %s AND subscription = 0 ', $email ) );
		if ( empty( $mail_subscription_check ) ) {
			$url = get_site_url();
			$new_url = add_query_arg(
				array(
					'pq'    => md5( trim( $email ) ),
					'nonce' => wp_create_nonce( 'my_nonce' ),
				),
				$url
			);
			$unsubscribe = __( 'If you don\'t want to receive email click here:-', 'abandoned-cart-for-woocommerce' ) . '<a href = "' . $new_url . '" >' . esc_html__( 'Unsubscribe Now', 'abandoned-cart-for-woocommerce' ) . '</a><br>';

			$final_content = $final_content . '<br/>' . $unsubscribe;
			if ( '' !== $email ) {
				$package = array(
					'kind'      => 'Layout',
					'name'      => 'workflow0',
					'title'     => 'workflow_title0',
					'edit_link' => '',
					'view_link' => '',
				);
				$subject_package = array(
					'kind'      => 'Layout',
					'name'      => 'subject0',
					'title'     => 'subject_title0',
					'edit_link' => '',
					'view_link' => '',
				);

				$subject       = apply_filters( 'wpml_translate_string', $subject, 'Email WOrkflow Subject0', $subject_package );
				$final_content = apply_filters( 'wpml_translate_string', $final_content, 'Email WOrkflow content0', $package );
				$check         = wp_mail( $email, $subject, $final_content );
				if ( true === $check ) {
					$wpdb->update(
						$wpdb->prefix . 'mwb_abandoned_cart',
						array(
							'cron_status'   => 1,
							'mail_count'    => 1,
						),
						array(
							'id' => $ac_id,
						)
					);
					$update_recovery = array(
						'ac_id' => $ac_id,
						'ew_id' => $ew_id,
						'time'  => $time,
					);
					$update_recovery = apply_filters( 'mwb_wacr_track_mai_not_opened', $update_recovery );
					$wpdb->insert(
						$wpdb->prefix . 'mwb_cart_recovery',
						$update_recovery
					);
				}
			}
		}
	}

	/**
	 * Function name mwb_third_abdn_daily_cart_cron_schedule.
	 * this fucntion will schedule second cron for sending second mail daily.
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_third_abdn_daily_cart_cron_schedule() {
		$cur_stamp = wp_next_scheduled( 'mwb_acfw_dynamic_mail_cron' );
		if ( ! $cur_stamp ) {
			wp_schedule_event( time(), 'daily', 'mwb_acfw_dynamic_mail_cron' );
		}
	}
	/**
	 * Function name mwb_third_abdn_cron_callback_daily.
	 * this fucntion is call back of second cron
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_third_abdn_cron_callback_daily() {
		$this->mwb_send_dynamic_mail();
	}
	/**
	 * Fuction to send Third mail
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_send_dynamic_mail() {
		global $wpdb;
		$mwb_email_html_arr     = get_option( 'mwb_email_html_key' );
		$mwb_count_mail_option  = (int) count( $mwb_email_html_arr );
		$abandoned_pro_is_active = false;
		$active_plugins = array_merge( get_option( 'active_plugins', array() ), get_site_option( 'active_sitewide_plugins', array() ) );
		if ( array_key_exists( 'woocommerce-abandoned-cart-recovery/woocommerce-abandoned-cart-recovery.php', $active_plugins ) || in_array( 'woocommerce-abandoned-cart-recovery/woocommerce-abandoned-cart-recovery.php', $active_plugins, true ) ) {
			$abandoned_pro_is_active = true;
		}
		for ( $i = 2; $i <= $mwb_count_mail_option; $i++ ) {
			$email_html = $mwb_email_html_arr[ $i ];
			$result1  = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_email_workflow WHERE ew_id =%s', $email_html ) );

			$check_enable = $result1[0]->ew_enable;
			$fetch_time = $result1[0]->ew_initiate_time;
			$converted_time_seconds = $fetch_time * 86400;
			if ( 'on' === $check_enable ) {

				$result  = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE cart_status = 1 AND mail_count =%s', ( $i - 1 ) ) );
				foreach ( $result as $key => $value ) {
					$is_last = false;
					$email = $value->email;
					$ac_id = $value->id;
					if ( (int) count( $mwb_email_html_arr ) === $i ) {
						$is_last = true;
					}
					if ( $abandoned_pro_is_active ) {
						$prev_mail_status = $wpdb->get_results( $wpdb->prepare( 'SELECT `time`, mail_status FROM ' . $wpdb->prefix . 'mwb_cart_recovery WHERE ac_id = %s AND ew_id = %s', $ac_id, ( $i - 1 ) ) );
						if ( isset( $result1[0]->ew_setting ) && $result1[0]->ew_setting == $prev_mail_status[0]->mail_status ) {
							$sending_time = gmdate( 'Y-m-d H:i:s', strtotime( $prev_mail_status[0]->time ) + $converted_time_seconds );
							$this->mwb_schedule_third( $sending_time, $email, $ac_id, $i, $is_last );
						}
					} else {
						$prev_mail_time = $wpdb->get_results( $wpdb->prepare( 'SELECT `time` FROM ' . $wpdb->prefix . 'mwb_cart_recovery WHERE ac_id = %s AND ew_id = %s', $ac_id, ( $i - 1 ) ) );
						$sending_time = gmdate( 'Y-m-d H:i:s', strtotime( $prev_mail_time[0]->time ) + $converted_time_seconds );
						$this->mwb_schedule_third( $sending_time, $email, $ac_id, $i, $is_last );
					}
				}
			}
		}

	}
	/**
	 * Function to send the third mail
	 *
	 * @param int    $sending_time sending time.
	 * @param string $email email.
	 * @param int    $ac_id ac_id.
	 * @param mixed  $i contains html.
	 * @param mixed  $is_last contains last workflow.
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_schedule_third( $sending_time, $email, $ac_id, $i, $is_last ) {
		if ( '' !== $email ) {

			as_schedule_single_action( $sending_time, 'mwb_wacr_sent_dynamic_forward_mail', array( $email, $ac_id, $i, $is_last ) );
		}

	}

	/**
	 * Function name mwb_mail_sent_dynamic.
	 * this function is used to send the third mail
	 *
	 * @param string  $email email.
	 * @param int     $ac_id ac id.
	 * @param int     $i check the last email.
	 * @param boolean $is_last check the last email.
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_mail_sent_dynamic( $email, $ac_id, $i, $is_last ) {
		$check = false;
		global $wpdb;
		$id       = is_array( $i ) ? array_shift( $i ) : $i;
		$mwb_email_html_arr     = get_option( 'mwb_email_html_key' );
		$mwb_val = $mwb_email_html_arr[ $i ];
		$result1 = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_email_workflow WHERE ew_id =%s', $mwb_val ) );
		$content = $result1[0]->ew_content;
		$subject = $result1[0]->ew_mail_subject;

		$mwb_acfw_coupon_discount = get_option( 'mwb_acfw_coupon_discount' );
		$mwb_acfw_coupon_expiry   = get_option( 'mwb_acfw_coupon_expiry' );
		$mwb_acfw_coupon_prefix   = get_option( 'mwb_acfw_coupon_prefix' );

		$email = is_array( $email ) ? array_shift( $email ) : $email;
		$ac_id = is_array( $ac_id ) ? array_shift( $ac_id ) : $ac_id;
		$time = gmdate( 'Y-m-d H:i:s' );
		$coupon_result = $wpdb->get_results( $wpdb->prepare( ' SELECT coupon_code, cart FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE id = %d ', $ac_id ) );
		$mwb_db_coupon = $coupon_result[0]->coupon_code;
		$mwb_cart = json_decode( $coupon_result[0]->cart, true );

		$content     = apply_filters( 'mwb_wacr_mail_tracking', $content, $id, $ac_id );
		$url = wc_get_checkout_url();
		$new_url = add_query_arg(
			array(
				'id'    => (string) trim( $id ),
				'ac_id'   => (string) trim( $ac_id ),
			),
			$url
		);
		$checkout_url       = '<a href = "' . $new_url . '" style="background-color: #2199f5;	padding: 7px 14px; font-size: 16px;	color: #f1f1f1;	font-weight: 600; text-decoration: none; border-radius: 4px; box-shadow: 0 4px 10px #999;" >Checkout Now</a><br>';
		$time = gmdate( 'Y-m-d H:i:s' );
		if ( strpos( $content, '{checkout}' ) ) {
			$sending_content = str_replace( '{checkout}', $checkout_url, $content );
		} else {
			$sending_content = $content;
		}
		if ( strpos( $sending_content, '{cart}' ) ) {
			$cart_data  = $wpdb->get_results( $wpdb->prepare( 'SELECT cart FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE id = %d ', $ac_id ) );
			$dbcart = $cart_data[0]->cart;
			$decoded_cart = json_decode( $dbcart, true );
			$table_content = '<h2>Your Cart</h2><br><table style=" border-collapse: collapse; width: 50%; table-layout: fixed;"><tr> <th style="  background: #e5f4fe; border: 1px solid #000000; text-align: center;	padding: 10px 0;">Product Name</th><th style="background: #e5f4fe; border: 1px solid #000000; text-align: center;	padding: 10px 0;">Quantity</th></tr>';
			foreach ( $decoded_cart as $k => $val ) {
				$pid            = $val['product_id'];
				$product        = wc_get_product( $pid );
				$pname          = $product->get_title();
				$quantity       = $val['quantity'];
				$table_content .= '<tr><td style=" border: 1px solid #000000; text-align: center; padding: 10px 0;">' . esc_html( $pname ) . '</td> <td style="border: 1px solid #000000; text-align: center; padding: 10px 0;">' . esc_html( $quantity ) . '</td> </tr>';

			}
			$table_content .= '</table><br>';
			$final_content = str_replace( '{cart}', $table_content, $sending_content );
		} else {
			$final_content = $sending_content;
		}
		if ( $mwb_acfw_coupon_discount ) {

			if ( null === $mwb_db_coupon ) {
				if ( strpos( $final_content, '{coupon}' ) ) {

					$rand = substr( md5( microtime() ), wp_rand( 0, 26 ), 5 );
					$coupon_expiry_time = time() + ( $mwb_acfw_coupon_expiry * 60 * 60 );
					$mwb_coupon_name = $mwb_acfw_coupon_prefix . $rand;

					/**
					* Create a coupon for sending in email.
					*/
					$coupon_code   = $mwb_coupon_name; // Code.
					$amount        = $mwb_acfw_coupon_discount; // Amount.
					$discount_type = 'percent'; // Type: percent.

					$coupon = array(
						'post_title'   => $coupon_code,
						'post_content' => '',
						'post_status'  => 'publish',
						'post_author'  => 1,
						'post_type'    => 'shop_coupon',
					);

					$new_coupon_id = wp_insert_post( $coupon );

					update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
					update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
					update_post_meta( $new_coupon_id, 'usage_limit', 1 );
					update_post_meta( $new_coupon_id, 'individual_use', 'no' );
					$arr_id = array();
					foreach ( $mwb_cart as $key => $mwb_value ) {

						$id_s = $mwb_value['product_id'];
						$arr_id[] = $id_s;
					}
					$main_arr_id = implode( ',', $arr_id );
					update_post_meta( $new_coupon_id, 'product_ids', $main_arr_id );
					update_post_meta( $new_coupon_id, 'expiry_date', $coupon_expiry_time );
					update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
					update_post_meta( $new_coupon_id, 'free_shipping', 'no' );

					$db_code_coupon_mwb           = wc_get_coupon_code_by_id( $new_coupon_id );
					$final_sending_coupon         = '<h6 style="font-size: 16px; margin: 20px 0 0; color: red; border: 1px solid red; width: fit-content; padding: 7px;"> Your Coupon Code: ' . $db_code_coupon_mwb . ' <br> Discount : ' . $amount . '% </h6><br><br>';
								$final_content = str_replace( '{coupon}', $final_sending_coupon, $final_content );
						$wpdb->update(
							$wpdb->prefix . 'mwb_abandoned_cart',
							array(
								'coupon_code' => $db_code_coupon_mwb,
							),
							array(
								'id' => $ac_id,
							)
						);
				} else {
					$final_content = $final_content;
				}
			} else {
				$final_sending_coupon_mwb_db = '<h6 style="font-size: 16px; margin: 20px 0 0; color: red; border: 1px solid red; width: fit-content; padding: 7px;"> Your Coupon Code: ' . $mwb_db_coupon . ' <br> Discount : ' . $mwb_acfw_coupon_discount . '% </h6><br><br>';
				$final_content = str_replace( '{coupon}', $final_sending_coupon_mwb_db, $final_content );
			}
		}
		$mail_subscription_check  = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_newsletter WHERE email = %s AND subscription = 0 ', $email ) );

		if ( empty( $mail_subscription_check ) ) {
			$url = get_site_url();
			$new_url = add_query_arg(
				array(
					'pq'    => trim( $email ),
					'nonce' => wp_create_nonce( 'my_nonce' ),
				),
				$url
			);
			$unsubscribe = __( 'If you dont want to receive email click here:-', 'abandoned-cart-for-woocommerce' ) . '<a href = "' . $new_url . '" >' . esc_html__( 'Unsubscribe Now', 'abandoned-cart-for-woocommerce' ) . '</a><br/>';

			$final_content = $final_content . '<br/>' . $unsubscribe;
			if ( '' !== $email ) {
				$package = array(
					'kind'      => 'Layout',
					'name'      => 'workflow' . $id - 1,
					'title'     => 'workflow_title' . $id - 1,
					'edit_link' => '',
					'view_link' => '',
				);
				$subject_package = array(
					'kind'      => 'Layout',
					'name'      => 'subject' . $id - 1,
					'title'     => 'subject_title' . $id - 1,
					'edit_link' => '',
					'view_link' => '',
				);

				$subject       = apply_filters( 'wpml_translate_string', $subject, 'Email WOrkflow Subject' . $id - 1, $subject_package );
				$final_content = apply_filters( 'wpml_translate_string', $final_content, 'Email WOrkflow content' . $id - 1, $package );

				$check = wp_mail( $email, $subject, $final_content );
				if ( true === $check ) {
					$update_data = array(
						'mail_count' => $id,
					);
					if ( $is_last ) {
						$update_data['workflow_sent'] = 1;
					}
					$wpdb->update(
						$wpdb->prefix . 'mwb_abandoned_cart',
						$update_data,
						array(
							'id' => $ac_id,
						)
					);
					$update_recovery = array(
						'ac_id' => $ac_id,
						'ew_id' => $id,
						'time'  => $time,
					);
					$update_recovery = apply_filters( 'mwb_wacr_track_mai_not_opened', $update_recovery );
					$wpdb->insert(
						$wpdb->prefix . 'mwb_cart_recovery',
						$update_recovery
					);

				}
			}
		}

	}

	/**
	 * Function name mwb_delete_ac_history_limited_time
	 * this function is used to delete abandoned cart history after a given time by admin
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_delete_ac_history_limited_time() {
		$del_time = get_option( 'mwb_delete_time_for_ac' );
		if ( $del_time ) {
			$sch_del = wp_next_scheduled( 'mwb_schedule_del_cron' );
			if ( $sch_del ) {
				wp_unschedule_event( $sch_del, 'mwb_schedule_del_cron' );
			}
			wp_schedule_event( time(), 'mwb_del_ac_time', 'mwb_schedule_del_cron' );
		}

	}

	/**
	 * Function name mwb_del_data_of_ac
	 * this function is callback of del cron.
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function mwb_del_data_of_ac() {
		global $wpdb;
		$time = get_option( 'mwb_delete_time_for_ac' );
		if ( $time ) {
			$wpdb->query(
				'TRUNCATE TABLE ' . $wpdb->prefix . 'mwb_abandoned_cart'
			);
			$wpdb->query(
				'TRUNCATE TABLE ' . $wpdb->prefix . 'mwb_cart_recovery'
			);
		}
	}
	/**
	 * Function name check_mail_subs
	 * this function will be used to unsubscribe emails
	 *
	 * @since 1.0.1
	 * @return void
	 */
	public function check_mail_subs() {
		global $wpdb;
		$check_load        = isset( $_GET['pq'] ) ? sanitize_text_field( wp_unslash( $_GET['pq'] ) ) : '';
		if ( '' !== $check_load ) {
					$wpdb->update(
						$wpdb->prefix . 'mwb_newsletter',
						array(
							'subscription'  => 0,
						),
						array(
							'mdmail' => $check_load,
						)
					);
			require_once ABANDONED_CART_FOR_WOOCOMMERCE_DIR_PATH . '/common/partials/abandoned-cart-for-woocommerce-common-display.php';
		}
	}


	/**
	 * Function is used for the sending the track data.
	 *
	 * @name acfw_makewebbetter_tracker_send_event.
	 * @param boolean $override override.
	 * @since 1.0.5
	 */
	public function acfw_makewebbetter_tracker_send_event( $override = false ) {
		require WC()->plugin_path() . '/includes/class-wc-tracker.php';

		$last_send = get_option( 'makewebbetter_tracker_last_send' );
		if ( ! apply_filters( 'makewebbetter_tracker_send_override', $override ) ) {
			// Send a maximum of once per week by default.
			$last_send = $this->mwb_acfw_last_send_time();
			if ( $last_send && $last_send > apply_filters( 'makewebbetter_tracker_last_send_interval', strtotime( '-1 week' ) ) ) {
				return;
			}
		} else {
			// Make sure there is at least a 1 hour delay between override sends, we don't want duplicate calls due to double clicking links.
			$last_send = $this->mwb_acfw_last_send_time();
			if ( $last_send && $last_send > strtotime( '-1 hours' ) ) {
				return;
			}
		}
		// Update time first before sending to ensure it is set.
		update_option( 'makewebbetter_tracker_last_send', time() );
		$params = WC_Tracker::get_tracking_data();
		$params = apply_filters( 'makewebbetter_tracker_params', $params );
		$api_url = 'https://tracking.makewebbetter.com/wp-json/acfw-route/v1/acfw-testing-data/';
		$sucess = wp_safe_remote_post(
			$api_url,
			array(
				'method'      => 'POST',
				'body'        => wp_json_encode( $params ),
			)
		);
	}

	/**
	 * Get the updated time.
	 *
	 * @name mwb_acfw_last_send_time
	 *
	 * @since 1.0.5
	 */
	public function mwb_acfw_last_send_time() {
		return apply_filters( 'makewebbetter_tracker_last_send_time', get_option( 'makewebbetter_tracker_last_send', false ) );
	}

	/**
	 * Update the option for settings from the multistep form.
	 *
	 * @name acfw_mwb_standard_save_settings_filter
	 * @since 1.0.5
	 */
	public function acfw_mwb_standard_save_settings_filter() {
		check_ajax_referer( 'ajax-nonce', 'nonce' );

		$term_accpted = ! empty( $_POST['consetCheck'] ) ? sanitize_text_field( wp_unslash( $_POST['consetCheck'] ) ) : ' ';
		if ( ! empty( $term_accpted ) && 'yes' == $term_accpted ) {
			update_option( 'acfw_enable_tracking', 'on' );
		}
		// settings fields.

		$mwb_acfw_cut_off_time = ! empty( $_POST['mwb_acfw_cut_off_time'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_acfw_cut_off_time'] ) ) : '';
		update_option( 'mwb_acfw_cut_off_time', $mwb_acfw_cut_off_time );

		$mwb_atc_text = ! empty( $_POST['mwb_atc_text'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_atc_text'] ) ) : '';
		update_option( 'mwb_atc_text', $mwb_atc_text );

		$mwb_atc_title = ! empty( $_POST['mwb_atc_title'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_atc_title'] ) ) : '';
		update_option( 'mwb_atc_title', $mwb_atc_title );

		$checked_first_switch = ! empty( $_POST['mwb_enable_acfw'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_enable_acfw'] ) ) : '';
		if ( ! empty( $checked_first_switch ) && $checked_first_switch ) {
			update_option( 'mwb_enable_acfw', 'on' );
		}

		$checked_second_switch = ! empty( $_POST['mwb_enable_atc_popup'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_enable_atc_popup'] ) ) : '';
		if ( ! empty( $checked_second_switch ) && $checked_second_switch ) {
			update_option( 'mwb_enable_atc_popup', 'on' );
		}
		update_option( 'acfw_acfw_plugin_standard_multistep_done', 'yes' );

		wp_send_json( 'yes' );
	}
}
