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

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Abandoned_Cart_For_Woocommerce_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @param  boolean $network_wide either network activated or not.
	 * @since  1.0.0
	 */
	public static function abandoned_cart_for_woocommerce_activate( $network_wide ) {

		global $wpdb;
		// check if the plugin has been activated on the network.
		if ( is_multisite() && $network_wide ) {
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				// code to be executed.
				self::mwb_acfw_insert_default_data();

				restore_current_blog();
			}
		} else {
			// code to be executed.
			self::mwb_acfw_insert_default_data();

		}
	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public static function mwb_acfw_insert_default_data() {

		global $wpdb;
		update_option( 'mwb_acfw_cut_off_time', 1 );
		update_option( 'mwb_enable_acfw', 'on' );
		$charset_collate = $wpdb->get_charset_collate();
		$char2 = $wpdb->get_charset_collate();
		$char3 = $wpdb->get_charset_collate();
		$char4 = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql = 'CREATE TABLE ' . $wpdb->prefix . "mwb_abandoned_cart (
		id INT NOT NULL AUTO_INCREMENT,
		u_id INT,
		email varchar(50),
		cart text,
		time datetime NOT NULL,
		total varchar(50),
		cart_status INT(9) NOT NULL,
		workflow_sent varchar(50),
		cron_status boolean,
		mail_count INT,
		ip_address varchar(300),
		mwb_abandon_key varchar(100),
		coupon_code varchar(100),
		left_page varchar(100),
		PRIMARY KEY  (id)
		) $charset_collate;";

		$sql1 = 'CREATE TABLE ' . $wpdb->prefix . "mwb_email_workflow (
			ew_id INT(9) NOT NULL AUTO_INCREMENT,
			ew_enable varchar(10),
			ew_mail_subject varchar(200),
			ew_content varchar(50000),
			ew_initiate_time varchar(5),
			PRIMARY KEY  (ew_id)
		) $char2;";

		$sql2 = 'CREATE TABLE ' . $wpdb->prefix . "mwb_cart_recovery (
			cr_id INT(9) NOT NULL AUTO_INCREMENT,
			ac_id INT,
			ew_id INT,
			time datetime,
			PRIMARY KEY  (cr_id)
		) $char3;";

		$sql4 = 'CREATE TABLE ' . $wpdb->prefix . "mwb_newsletter (
			id INT(9) NOT NULL AUTO_INCREMENT,
			email varchar(100),
			mdmail varchar(100),
			subscription varchar(50),
			PRIMARY KEY  (id)
		) $char4;";
		if ( $wpdb->get_var( 'SHOW TABLES LIKE"' . $wpdb->prefix . 'mwb_abandoned_cart"' ) != $wpdb->prefix . 'mwb_abandoned_cart' ) {
			dbDelta( $sql );
		}
		if ( $wpdb->get_var( 'SHOW TABLES LIKE"' . $wpdb->prefix . 'mwb_email_workflow"' ) != $wpdb->prefix . 'mwb_email_workflow' ) {
			dbDelta( $sql1 );

			if ( dbDelta( $sql1 ) ) {
				$content1 = '<h1>Your order is still waiting in your cart!! </h1>
				<h3>Dear customer,	Thank you for visiting………………………….. !! We notice that you added a product in your cart, but didnt continue to checkout.Grab your order.
				Would you like to complete your order</h3><br>
				{cart} <br> {coupon} <br><br> {checkout}';

				$content2 = '<h1>ARE YOU LOOKING FOR THE DISCOUNT? </h1><h3> Get SPECIAL DISCOUNT   ON YOUR Order</h3><br> 
				{cart} <br> {coupon} <br><br> {checkout}';

				$content3 = '<h1>Your Coupon is SAD...Did You Forget?</h1>
				<h3>HURRY UP!! Use Your Coupon Now Dear customer,
					Greetings</h3>
					<h3>Hurry-up, use the coupon code before it expires and snag your most awaited deal, Now!!</h3><br>
					{cart} <br> {coupon} <br> <br> {checkout}';
				$result  = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_email_workflow' );
				if ( empty( $result ) ) {

					$wpdb->insert(
						$wpdb->prefix . 'mwb_email_workflow',
						array(
							'ew_enable'           => 'on',
							'ew_mail_subject'     => 'Psst! You left something in your cart',
							'ew_content'          => $content1,
							'ew_initiate_time'    => 1,
						)
					);
					$wpdb->insert(
						$wpdb->prefix . 'mwb_email_workflow',
						array(
							'ew_enable'           => 'on',
							'ew_mail_subject'     => 'Avail Flat Discount on your cart!!',
							'ew_content'          => $content2,
							'ew_initiate_time'    => 2,
						)
					);
					$wpdb->insert(
						$wpdb->prefix . 'mwb_email_workflow',
						array(
							'ew_enable'           => 'on',
							'ew_mail_subject'     => 'Hurry Up! Your coupon will expire soon...',
							'ew_content'          => $content3,
							'ew_initiate_time'    => 3,
						)
					);
				}
			}
		}
		if ( $wpdb->get_var( 'SHOW TABLES LIKE"' . $wpdb->prefix . 'mwb_cart_recovery"' ) != $wpdb->prefix . 'mwb_cart_recovery' ) {
			dbDelta( $sql2 );
		}
		if ( $wpdb->get_var( 'SHOW TABLES LIKE"' . $wpdb->prefix . 'mwb_newsletter"' ) != $wpdb->prefix . 'mwb_newsletter' ) {
			dbDelta( $sql4 );
		}

	}

}
