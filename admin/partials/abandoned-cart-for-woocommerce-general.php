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
global $acfw_mwb_acfw_obj, $error_notice;
$acfw_genaral_settings = apply_filters( 'acfw_general_settings_array', array() );

?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="mwb-acfw-gen-section-form">
	<input type="hidden" name="nonce" value="<?php echo esc_html( wp_create_nonce( 'general_setting_nonce' ) ); ?>">
	<div class="acfw-secion-wrap">
		<?php
		$acfw_general_html = $acfw_mwb_acfw_obj->mwb_acfw_plug_generate_html( $acfw_genaral_settings );
		echo esc_html( $acfw_general_html );
		?>
	</div>
</form>
