<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
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

global $acfw_mwb_acfw_obj, $error_notice;
$acfw_active_tab   = isset( $_GET['acfw_tab'] ) ? sanitize_key( $_GET['acfw_tab'] ) : 'abandoned-cart-for-woocommerce-general';
$acfw_default_tabs = $acfw_mwb_acfw_obj->mwb_acfw_plug_default_tabs();
if ( ! acfw_mwb_standard_check_multistep() ) {
	?>
	<div id="react-app"></div>
	<?php
	return;
}
?>
<header>
<?php do_action( 'mwb_acfw_pro_activat_notice' ); ?>
	<div class="mwb-header-container mwb-bg-white mwb-r-8">
		<h1 class="mwb-header-title"><?php echo esc_attr( strtoupper( str_replace( '-', ' ', $acfw_mwb_acfw_obj->mwb_acfw_get_plugin_name() ) ) ); ?></h1>
		<a href="https://docs.makewebbetter.com/abandoned-cart-for-woocommerce/" target="_blank" class="mwb-link"><?php esc_html_e( 'Documentation', 'abandoned-cart-for-woocommerce' ); ?></a>
		<span>|</span>
		<a href="https://makewebbetter.com/contact-us/" class="mwb-link"><?php esc_html_e( 'Support', 'abandoned-cart-for-woocommerce' ); ?></a>

	</div>
</header>
<?php
if ( ! $error_notice ) {
	$saved_msg = esc_html__( 'Settings saved !', 'abandoned-cart-for-woocommerce' );
	$acfw_mwb_acfw_obj->mwb_acfw_plug_admin_notice( $saved_msg, 'success' );
}

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
					} elseif ( ! empty( $acfw_active_tab ) && in_array( $acfw_active_tab, array( 'abandoned-cart-for-woocommerce-report', 'abandoned-cart-for-woocommerce-product-report' ), true ) ) {
						if ( 'abandoned-cart-for-woocommerce-report' === $acfw_tab_key ) {
							$acfw_tab_classes .= 'active';
						}
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

	<section class="mwb-section">
		<div>
			<?php
				do_action( 'mwb_acfw_before_general_settings_form' );
						// if submenu is directly clicked on woocommerce.
			if ( empty( $acfw_active_tab ) ) {
				$acfw_active_tab = 'mwb_acfw_plug_general';
			}

						// look for the path based on the tab id in the admin templates.
				$acfw_tab_content_path = 'admin/partials/' . $acfw_active_tab . '.php';

				$acfw_mwb_acfw_obj->mwb_acfw_plug_load_template( $acfw_tab_content_path );

				do_action( 'mwb_acfw_after_general_settings_form' );
			?>
		</div>
	</section>
