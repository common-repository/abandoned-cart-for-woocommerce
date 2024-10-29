<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to show quick view of the cart.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/admin/template
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit(); // Exit if accessed directly.
}
?>

<table>
			<tr>
				<th>
					<?php esc_html_e( 'Product Id', 'abandoned-cart-for-woocommerce' ); ?>
				</th>
				<th>
					<?php esc_html_e( 'Product Name', 'abandoned-cart-for-woocommerce' ); ?>
				</th>
				<th>
					<?php esc_html_e( 'Quantity', 'abandoned-cart-for-woocommerce' ); ?>
				</th>
				<th>
					<?php echo esc_html__( 'Total ', 'abandoned-cart-for-woocommerce' ) . esc_html( '(' . $symbol . ')' ); ?>
				</th>
			</tr>
	<?php
	foreach ( $cart as $key => $value ) {
		$product_id = $value['product_id'];
		$quantity   = $value['quantity'];
		$total      = $value['line_total'];
		?>
			<tr>
				<td>
					<?php echo esc_html( $product_id ); ?>
				</td>
				<td>
					<?php
						$product = wc_get_product( $product_id );

							echo esc_html( $product->get_title() );
					?>
				</td>
				<td>
					<?php echo esc_html( $quantity ); ?>
				</td>
				<td>
					<?php echo esc_html( $total ); ?>
				</td>

			</tr>
	<?php } ?>
	</table>
