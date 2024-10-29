<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to show atc pop-up.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/public/partials
 */

?>

<div class="pop_atc" id="mwb_atc_dialog" style="display: none;">
		<div class="mwb-dialog">
		<div class="mwb-dialog__img">
		<img src="<?php echo esc_html( ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL ) . 'public/src/images/cart.svg'; ?>" alt="">
		</div>
		<div class="mwb-dialog__text">
			<?php
			$mwb_acfw_atc_text = get_option( 'mwb_atc_text' );
			?>
		<p>
			<?php
			if ( $mwb_acfw_atc_text ) {
					echo esc_html( $mwb_acfw_atc_text );
			} else {
				esc_html_e( 'Do You Want to Buy', 'abandoned-cart-for-woocommerce' );
			}
			?>
		</p>
		</div>
		</div>
		<form action="" method="get" accept-charset="utf-8" class="mwb-dialog__form">
		<input type="email" id="email_atc" placeholder="<?php esc_html_e( 'Please Enter Your Email Here', 'abandoned-cart-for-woocommerce' ); ?>" required> <br>
		<span id = "e9"></span>
		<input type="button" id="subs" class="submit" value="<?php esc_html_e( 'Add To Cart', 'abandoned-cart-for-woocommerce' ); ?>" class="button button-danger">
		</form>
</div>
