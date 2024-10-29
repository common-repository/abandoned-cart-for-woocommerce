<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link  https://makewebbetter.com/
 * @since 1.0.0
 *
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}
global $acfw_mwb_acfw_obj;
$acfw_default_tabs = $acfw_mwb_acfw_obj->mwb_acfw_plug_default_tabs();
$acfw_tab_key      = '';
?>
<header>
	<?php
	// desc - This hook is used for trial.
	do_action( 'mwb_acfw_settings_saved_notice' );
	?>
	<div class="mwb-header-container mwb-bg-white mwb-r-8">
		<h1 class="mwb-header-title"><?php echo esc_attr( 'MakeWebBetter' ); ?></h1>
	</div>
</header>
<main class="mwb-main mwb-bg-white mwb-r-8">
	<section class="mwb-section">
		<div>
			<?php
				// desc - This hook is used for trial.
			do_action( 'mwb_acfw_before_common_settings_form' );
				// if submenu is directly clicked on woocommerce.
			$acfw_genaral_settings = apply_filters(
				'acfw_home_settings_array',
				array(
					array(
						'title' => __( 'Enable Tracking', 'abandoned-cart-for-woocommerce' ),
						'type'  => 'radio-switch',
						'id'    => 'acfw_enable_tracking',
						'value' => get_option( 'acfw_enable_tracking' ),
						'class' => 'acfw-radio-switch-class',
						'options' => array(
							'yes' => __( 'YES', 'abandoned-cart-for-woocommerce' ),
							'no' => __( 'NO', 'abandoned-cart-for-woocommerce' ),
						),
					),
					array(
						'type'  => 'button',
						'id'    => 'mwb_acfw_save_tracking_val',
						'button_text' => __( 'Save', 'abandoned-cart-for-woocommerce' ),
						'class' => 'acfw-button-class',
					),
				)
			);
			?>
			<form action="" method="POST" class="mwb-home-section-form">
				<div class="acfw-secion-wrap">
					<?php
					$acfw_general_html = $acfw_mwb_acfw_obj->mwb_acfw_plug_generate_html( $acfw_genaral_settings );
					echo esc_html( $acfw_general_html );
					?>
				<input type="hidden" name="acfw_tracking_nonce" value="<?php echo esc_html( wp_create_nonce( 'acfw_tracking_nonce' ) ); ?>">

				</div>
			</form>
			<?php
			do_action( 'mwb_acfw_before_common_settings_form' );
			$all_plugins = get_plugins();
			?>
		</div>
	</section>
	<style type="text/css">
		.cards {
			display: flex;
			flex-wrap: wrap;
			padding: 20px 40px;
		}
		.card {
			flex: 1 0 518px;
			box-sizing: border-box;
			margin: 1rem 3.25em;
			text-align: center;
		}

	</style>
	<div class="centered">
		<section class="cards">
			<?php foreach ( get_plugins() as $key => $value ) : ?>
				<?php if ( 'MakeWebBetter' === $value['Author'] ) : ?>
					<article class="card">
						<div class="container">
							<h4><b><?php echo esc_html( $value['Name'] ); ?></b></h4> 
							<p><?php echo esc_html( $value['Version'] ); ?></p> 
							<p><?php echo esc_html( $value['Description'] ); ?></p>
						</div>
					</article>
				<?php endif; ?>
			<?php endforeach; ?>
		</section>
	</div>
