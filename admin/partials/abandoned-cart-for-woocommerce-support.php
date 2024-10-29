<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $acfw_mwb_acfw_obj;
$acfw_support_settings = apply_filters( 'acfw_supprot_tab_settings_array', array() );
?>
<!--  template file for admin settings. -->
<div class="acfw-section-wrap">
	<?php if ( is_array( $acfw_support_settings ) && ! empty( $acfw_support_settings ) ) { ?>
		<?php foreach ( $acfw_support_settings as $acfw_support_setting ) { ?>
		<div class="mwb-col-wrap">
			<div class="mwb-shadow-panel">
				<div class="content-wrap">
					<div class="content">
						<h3><?php echo esc_html( $acfw_support_setting['title'] ); ?></h3>
						<p><?php echo esc_html( $acfw_support_setting['description'] ); ?></p>
					</div>
					<div class="mdc-button mdc-button--raised mwb-cta-btn"><span class="mdc-button__ripple"></span>
						<a href="#" class="mwb-btn mwb-btn-primary"><?php echo esc_html( $acfw_support_setting['link-text'] ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	<?php } ?>
</div>
