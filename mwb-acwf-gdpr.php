<?php
/**
 * Exit if accessed directly
 *
 * @package    abandoned-cart-for-woocommerce
 * @since             1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Return the default suggested privacy policy content.
 *
 * @since             1.0.0
 * @return string The default policy content.
 * @name mwb_acfw_suggested_privacy_content
 * @author MakeWebBetter
 * @link https://www.makewebbetter.com/
 */
function mwb_acfw_suggested_privacy_content() {
	return '<h2>' . __( 'Stored Customer Details for Tracking Abandoned cart', 'abandoned-cart-for-woocommerce' ) . '</h2>' .
	'<p>' . __( "We store customer's email address, customer's Ip Address so that we can send them email reagarding their abandoned cart to cover them", 'abandoned-cart-for-woocommerce' ) . '</p>';
}

/**
 * Add the suggested privacy policy text to the policy postbox.
 *
 * @name mwb_acfw_privacy_content
 * @author MakeWebBetter
 * @link https://www.makewebbetter.com/
 */
function mwb_acfw_privacy_content() {
	$content = mwb_acfw_suggested_privacy_content();
	wp_add_privacy_policy_content( __( 'Abandoned Cart For WooCommerce', 'abandoned-cart-for-woocommerce' ), $content );
}

add_action( 'admin_init', 'mwb_acfw_privacy_content', 20 );

// Export Personal Data.

/**
 * Register exporter for Plugin user data.
 *
 * @param array $exporters Details of all the exporters.
 * @return array
 * @name mwb_acfw_exporters
 * @author MakeWebBetter
 * @link https://www.makewebbetter.com/
 */
function mwb_acfw_exporters( $exporters ) {
	$exporters[] = array(
		'exporter_friendly_name' => __( 'Recipient Details', 'abandoned-cart-for-woocommerce' ),
		'callback'               => 'mwb_acfw_callback_data_exporter',
	);
	return $exporters;
}

add_filter( 'wp_privacy_personal_data_exporters', 'mwb_acfw_exporters' );


/**
 * Exporter for Plugin user data.
 *
 * @param string $email_address Contains Email Addresss.
 * @param int    $page contains page.
 * @return array
 * @name mwb_acfw_callback_data_exporter
 * @author MakeWebBetter
 * @link https://www.makewebbetter.com/
 */
function mwb_acfw_callback_data_exporter( $email_address, $page = 1 ) {
	$export_items = array();
	global $wpdb;
	$mail = $email_address;
	$data = array();
	$user_data = $wpdb->get_results( $wpdb->prepare( ' SELECT email, ip_address FROM ' . $wpdb->prefix . 'mwb_abandoned_cart where email = %s', $mail ) );
	foreach ( $user_data as $key => $val ) {
		if ( $mail === $val->email ) {
			$data[] = array(
				'name' => 'Email address',
				'value' => $val->email,
			);
			$data[] = array(
				'name' => 'Ip Address',
				'value' => $val->ip_address,
			);
		}
	}
	if ( array_search( $mail, array_column( $user_data, 'email' ) ) !== false ) {
		$export_items[] = array(
			'group_id'    => 'Abandoned_cart_data',
			'group_label' => 'Abandoned Cart Stored Data',
			'item_id'     => 'abandoned_cart_data' . $mail,
			'data'        => $data,
		);
	}

		// Returns an array of exported items for this pass, but also a boolean whether this exporter is finished.
		// If not it will be called again with $page increased by 1.
	return array(
		'data' => $export_items,
		'done' => true,
	);
}

// Delete Personal Data.

/**
 * Register eraser for Plugin user data.
 *
 * @param array $erasers contains erased data.
 * @return array
 * @name mwb_acfw_plugin_register_erasers
 * @author MakeWebBetter
 * @link https://www.makewebbetter.com/
 */
function mwb_acfw_plugin_register_erasers( $erasers = array() ) {
	$erasers[] = array(
		'eraser_friendly_name' => __( 'Recipient Details', 'abandoned-cart-for-woocommerce' ),
		'callback'               => 'mwb_acfw_plugin_user_data_eraser',
	);
	return $erasers;
}


add_filter( 'wp_privacy_personal_data_erasers', 'mwb_acfw_plugin_register_erasers' );

/**
 * Eraser for Plugin user data.
 *
 * @since             1.0.0
 * @param string $email_address contains email address.
 * @param int    $page conains page.
 * @return array
 * @name mwb_acfw_plugin_user_data_eraser
 * @author MakeWebBetter
 * @link https://www.makewebbetter.com/
 */
function mwb_acfw_plugin_user_data_eraser( $email_address, $page = 1 ) {
	global $wpdb;
	if ( empty( $email_address ) ) {
		return array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}
	$mail = $email_address;
	$messages = array();
	$items_removed  = false;
	$items_retained = false;
	$user_data = $wpdb->get_results( $wpdb->prepare( ' SELECT email, ip_address FROM ' . $wpdb->prefix . 'mwb_abandoned_cart where email = %s', $mail ) );

	if ( array_search( $mail, array_column( $user_data, 'email' ) ) !== false ) {
		$status = $wpdb->delete(
			$wpdb->prefix . 'mwb_abandoned_cart',
			array(
				'email' => $mail,
			)
		);
		if ( $status ) {
			$items_removed = true;
			$messages[] = __( 'Removed data of "Abandoned Cart"', 'abandoned-cart-for-woocommerce' );
		} else {
			$items_removed = true;
		}
	}
	// Returns an array of exported items for this pass, but also a boolean whether this exporter is finished.
	// If not it will be called again with $page increased by 1.
	return array(
		'items_removed'  => $items_removed,
		'items_retained' => $items_retained,
		'messages'       => $messages,
		'done'           => true,
	);
}
