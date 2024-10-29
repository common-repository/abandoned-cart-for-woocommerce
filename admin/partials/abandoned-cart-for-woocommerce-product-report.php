<?php
/**
 * Provide woocommerce reports of abandoned carts product-wise.
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

global $acfw_mwb_acfw_obj;
$acfw_active_tab   = isset( $_GET['acfw_tab'] ) ? sanitize_key( $_GET['acfw_tab'] ) : 'abandoned-cart-for-woocommerce-general';
$acfw_default_tabs = $acfw_mwb_acfw_obj->mwb_acfw_plug_default_sub_tabs();
?>

<main class="mwb-main mwb-bg-white mwb-r-8">
	<nav class="mwb-navbar">
		<ul class="mwb-navbar__items">
			<?php
			if ( is_array( $acfw_default_tabs ) && ! empty( $acfw_default_tabs ) ) {

				foreach ( $acfw_default_tabs as $acfw_tab_key => $acfw_default_tabs ) {

					$acfw_tab_classes = 'mwb-link ';

					if ( ! empty( $acfw_active_tab ) && $acfw_active_tab === $acfw_tab_key ) {
						$acfw_tab_classes .= 'active';
					}
					?>
					<li>
						<a id="<?php echo esc_attr( $acfw_tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=abandoned_cart_for_woocommerce_menu' ) . '&acfw_tab=' . esc_attr( $acfw_tab_key ) ); ?>" class="<?php echo esc_attr( $acfw_tab_classes ); ?>"><?php echo esc_html( $acfw_default_tabs['title'] ); ?></a>
					</li>
					<?php
				}
			}
			?>
		</ul>
	</nav>
</main>
<div class='product_tab'>
<div class="m-section-note">

<?php esc_html_e( 'Report as per Abandoned Products', 'abandoned-cart-for-woocommerce' ); ?>
</div>
	<?php

	global $wpdb;
	$arr = array();
	$result  = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'mwb_abandoned_cart ' );
	foreach ( $result as $k => $value ) {
		$cart = $value->cart;
		$decode_cart = json_decode( $cart );
		foreach ( $decode_cart as $key => $val ) {
			$id_mwb = $val->product_id;
			if ( array_key_exists( $id_mwb, $arr ) ) {
				$arr[ $id_mwb ] = $arr[ $id_mwb ] + 1;
			} else {
				$arr[ $id_mwb ] = 1;
			}
		}
	}
	?>
	<table>
		<tr>
			<th>
				<h6><?php esc_html_e( 'Product Name', 'abandoned-cart-for-woocommerce' ); ?></h6>
			</th>
			<th>
				<h6><?php esc_html_e( 'Abandoned time', 'abandoned-cart-for-woocommerce' ); ?></h6>
			</th>
		</tr>
		<?php
		foreach ( $arr as $key => $val ) {
			$product = wc_get_product( $key );
			?>
			<tr>
				<td>
					<?php echo esc_html( $product->get_title() ); ?>
				</td>
				<td>
					<?php echo esc_html( $val ); ?>
				</td>
			</tr>

			<?php
		}
		?>

	</table>
</div>
<?php
