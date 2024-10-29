<?php
/**
 * Provide woocommerce reports of abandoned carts.
 *
 * This file is used to show reports ad details of the abandoned carts.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit(); // Exit if accessed directly.
}

if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}


	/**
	 * Class Name Mwb_List_Table
	 * This class will show the details of abandoned carts by extending WP_List_Table.
	 */
class Abandoned_Cart_For_Woocommerce_Report extends WP_List_Table {


	/**
	 * Function name mwb_abandon_cart_data().
	 *
	 * This Function is used to fetch data from the database.
	 *
	 * @param string $orderby sorting order by column.
	 * @param string $order sorting order.
	 * @param string $search_item item to search.
	 * @return array
	 */
	public function mwb_abandon_cart_data( $orderby, $order, $search_item ) {
		global $wpdb, $mwb_wacr_activated;
		$data_arr = array();

		if ( ! empty( $search_item ) ) {
			$result  = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE cart_status != 0 AND ( email LIKE %s )', '%' . $search_item . '%' ) );
		} elseif ( isset( $_GET['orderby'] ) && isset( $_GET['order'] ) ) {
			$order_show = sanitize_text_field( wp_unslash( $_GET['order'] ) );
			$order_by = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
			if ( 'email' === $order_by && 'asc' === $order_show ) {
				$result = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE cart_status != 0 ORDER BY email asc ' );
			} elseif ( 'email' === $order_by && 'desc' === $order_show ) {
				$result = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE cart_status != 0 ORDER BY email desc ' );
			} elseif ( 'total' === $order_by && 'asc' === $order_show ) {
				$result = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE cart_status != 0 ORDER BY total asc ' );
			} elseif ( 'total' === $order_by && 'desc' === $order_show ) {
				$result = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE cart_status != 0 ORDER BY total desc ' );
			} elseif ( 'cart_status' === $order_by && 'asc' === $order_show ) {
				$result = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE cart_status != 0 ORDER BY cart_status asc ' );
			} elseif ( 'cart_status' === $order_by && 'desc' === $order_show ) {
				$result = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE cart_status != 0 ORDER BY cart_status desc ' );
			}
		} else {
			$result  = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_abandoned_cart WHERE cart_status != 0 ' );

		}
		if ( count( $result ) > 0 ) {
			foreach ( $result as $key => $value ) {

				$status = $value->cart_status;
				if ( '1' === $status ) {
					$status_new = __( 'Abandoned', 'abandoned-cart-for-woocommerce' );
				} elseif ( '2' === $status ) {
					$status_new = __( 'Recovered', 'abandoned-cart-for-woocommerce' );
				}
				$data = array(
					'id'     => $value->id,
					'email'     => $value->email,
					'left_page'   => $value->left_page,
					'cart_status' => $status_new,
					'total'       => $value->total,
				);
				$data_arr[] = apply_filters( 'mwb_wacr_coupon_status_show', $data, $value );
			}
		}

		return $data_arr;

	}


	/**
	 * Function name get_hidden_columns.
	 * this function will be used for getting hidden coloumns
	 *
	 * @return array
	 * @since             1.0.0
	 */
	public function get_hidden_columns() {
		return array( 'id' );

	}
	/**
	 * Function name get_sortable_columns
	 * This function is used to craete columns as sortable.
	 *
	 * @return array
	 * @since             1.0.0
	 */
	public function get_sortable_columns() {
			return array(
				'email'       => array( 'email', true ),
				'cart_status' => array( 'cart_status', true ),
				'total'       => array( 'total', true ),
			);
	}

	/**
	 * Function name get_columns
	 * This function is used get all columns from data.
	 *
	 * @return $columns
	 * @since             1.0.0
	 */
	public function get_columns() {
		$currency = get_option( 'woocommerce_currency' );
		$symbol = get_woocommerce_currency_symbol( $currency );

		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'id'          => 'ID',
			'email'       => __( 'Email', 'abandoned-cart-for-woocommerce' ),
			'left_page'   => __( 'Left Page From ', 'abandoned-cart-for-woocommerce' ),
			'total'       => __( 'Total', 'abandoned-cart-for-woocommerce' ) . ' ' . $symbol,
			'cart_status' => __( 'Status', 'abandoned-cart-for-woocommerce' ),
		);
		$columns = apply_filters( 'mwb_acfw_add_column_to_view_coupon_status', $columns );
		return $columns;

	}
	/**
	 * Function name column_cb
	 * this function is used to show chekbox
	 *
	 * @param array $item contains columns.
	 * @return array
	 * @since             1.0.0
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />',
			$item['id']
		);
	}


	/**
	 * Function name column_default.
	 * this function is used to find the data of the columns
	 *
	 * @param array  $item contains item.
	 * @param string $column_name contains column name.
	 * @return array
	 * @since             1.0.0
	 */
	public function column_default( $item, $column_name ) {
		global $mwb_wacr_activated;
		switch ( $column_name ) {
			case 'id':
			case 'email':
			case 'left_page':
			case 'cart_status':
			case 'total':
			case 'action':
			case 'coupon_code':
			case 'coupon_status':
				return $item[ $column_name ];
			default:
				return 'No Value';
		}

	}

	/**
	 * Function name column_email
	 * this function will show email columns
	 *
	 * @param array $item contains item.
	 * @return array
	 * @since             1.0.0
	 */
	public function column_email( $item ) {

		$action = array(
			'view' => '<a href="javascript:void(0)" id="view_data" data-id="' . $item['id'] . '">View</a>',
		);
		return sprintf( '%1$s %2$s', $item['email'], $this->row_actions( $action ) );
	}




	/**
	 * Function name get_bulk_actions.
	 * This Function is used to get the bulk action
	 *
	 * @return array
	 * @since             1.0.0
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete'    => 'Delete',
		);
		return $actions;
	}

	/**
	 * Function name delete_cart
	 * this function is used to delete cart data .
	 *
	 * @param int $id stores the id.
	 * @return void
	 * @since             1.0.0
	 */
	public static function delete_cart( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'mwb_abandoned_cart';

		$wpdb->delete(
			"$table_name",
			array( 'id' => $id ),
			array( '%d' )
		);

	}

	/**
	 * Function name process_bulk_action
	 * this function is used to process bulk action
	 *
	 * @return void
	 * @since             1.0.0
	 */
	public function process_bulk_action() {

		if ( ( isset( $_POST['action'] ) && 'bulk-delete' === $_POST['action'] ) || ( isset( $_POST['action2'] ) && 'bulk-delete' === $_POST['action2'] ) ) {

			if ( isset( $_POST['prepare_nonce_mwb'] ) ? wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['prepare_nonce_mwb'] ) ), 'prepare_nonce_mwb' ) : '' ) {
				$delete_ids = isset( $_POST['bulk-delete'] ) ? map_deep( wp_unslash( $_POST['bulk-delete'] ), 'sanitize_text_field' ) : '';

				// loop over the array of record IDs and delete them.
				foreach ( $delete_ids as $id ) {
					self::delete_cart( $id );

				}
				wp_safe_redirect(
					add_query_arg(
						array(
							'page'     => 'abandoned_cart_for_woocommerce_menu',
							'acfw_tab' => 'abandoned-cart-for-woocommerce-report',
							'deleted'  => true,
						),
						admin_url( 'admin.php' )
					)
				);
				exit;
			} else {
				esc_html_e( 'Nonce Not Verified', 'abandoned-cart-for-woocommerce' );
			}
		}
	}

		/**
		 * Function to prepare items
		 *
		 * @return void
		 * @since             1.0.0
		 */
	public function prepare_items() {
		$search_item = '';
		if ( isset( $_POST['search_nonce_mwb'] ) ? wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['search_nonce_mwb'] ) ), 'search_nonce_mwb' ) : '' ) {
			$search_item = isset( $_POST['s'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['s'] ) ) ) : '';
		}
		$orderby     = isset( $_GET['orderby'] ) ? trim( sanitize_key( wp_unslash( $_GET['orderby'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$order       = isset( $_GET['order'] ) ? trim( sanitize_key( wp_unslash( $_GET['order'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$mwb_all_data = $this->mwb_abandon_cart_data( $orderby, $order, $search_item );
		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$total_data   = count( $mwb_all_data );
		$this->set_pagination_args(
			array(
				'total_items' => $total_data,
				'per_page' => $per_page,
			)
		);
		$this->items = array_slice( $mwb_all_data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		// callback to get columns.
		$columns = $this->get_columns();
		// callback to get hidden columns.
		$hidden = $this->get_hidden_columns();
		// callback to get sortable columns.
		$sortable = $this->get_sortable_columns();
		$this->process_bulk_action();
		// all callback called to the header.
		$this->_column_headers = array( $columns, $hidden, $sortable );

	}


}
?>
	<div id="view" title="<?php esc_html_e( 'Full Details Of Cart', 'abandoned-cart-for-woocommerce' ); ?>">
	<p id="show_table"></p>
	</div>
	<?php
