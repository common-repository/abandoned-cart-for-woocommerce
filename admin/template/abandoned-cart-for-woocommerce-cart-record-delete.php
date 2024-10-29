<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to show  vdeletion msg o fthe record
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
<div class="notice notice-success is-dismissible">
		<p>
			<?php esc_html_e( 'Record deleted successfully', 'abandoned-cart-for-woocommerce' ); ?>
		</p>
	</div>
<br/>
