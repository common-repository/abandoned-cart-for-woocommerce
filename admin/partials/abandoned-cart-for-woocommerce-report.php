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
?>


<?php
global $acfw_mwb_acfw_obj, $mwb_wacr_activated;
$acfw_active_tab   = isset( $_GET['acfw_tab'] ) ? sanitize_key( $_GET['acfw_tab'] ) : 'abandoned-cart-for-woocommerce-general';
$acfw_default_tabs = $acfw_mwb_acfw_obj->mwb_acfw_plug_default_sub_tabs();

/** Checking The activation of pro-plugin */
if ( in_array( 'woocommerce-abandoned-cart-recovery/woocommerce-abandoned-cart-recovery.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

	$mwb_wacr_activated = true;
}
require_once ABANDONED_CART_FOR_WOOCOMMERCE_DIR_PATH . 'includes/class-abandoned-cart-for-woocommerce-report.php';


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
		<div class="wrap">
					<div id="poststuff">
						<div id="post-body" class="metabox-holder columns-4">
							<div id="post-body-content">
								<div class="meta-box-sortables ui-sortable">
								<form method="POST">
										<?php
										do_action( 'mwb_delete_cart_record' );
										$obj = new Abandoned_Cart_For_Woocommerce_Report();
										$obj->prepare_items();
										$data_search = isset( $_SERVER['PHP_SELF'] ) ? trim( sanitize_key( wp_unslash( $_SERVER['PHP_SELF'] ) ) ) : '';
										echo "<form method='post' name='mwb_search_post' action='" . esc_html( $data_search ) . "?page=abandoned_cart_for_woocommerce_menu&acfw_tab=class-abandoned-cart-for-woocommerce-report'>";
										echo '<input type="hidden" name="search_nonce_mwb" value=' . esc_html( wp_create_nonce( 'search_nonce_mwb' ) ) . '>';
										$obj->search_box( 'Search by email', 'mwb_search_data_id' );
										echo '</form>';
										echo '<form method="POST">';
										echo '<input type="hidden" name="prepare_nonce_mwb" value=' . esc_html( wp_create_nonce( 'prepare_nonce_mwb' ) ) . '>';
										$obj->display();
										echo '</form>';
										?>
									</form>
								</div>
							</div>
						</div>
						<br class="clear">
					</div>
				</div>
				
<?php
