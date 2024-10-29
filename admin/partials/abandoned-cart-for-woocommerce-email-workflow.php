<?php
/**
 * Provide Email workflows
 *
 * This file is used to show workflows to the merchat for sending in email.
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

?>
<div class="m-section-wrap">
<div class="m-section-note">

<img src="<?php echo esc_html( ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/src/images/note.svg'; ?>" alt="">

<?php esc_html_e( 'Use Placeholders', 'abandoned-cart-for-woocommerce' ); ?> <span><?php echo esc_html( '{coupon}' ); ?></span> <?php esc_html_e( 'to apply a coupon on the cart', 'abandoned-cart-for-woocommerce' ); ?><span> <?php echo esc_html( ' {cart} ' ); ?></span> <?php esc_html_e( 'for displaying the cart in the email', 'abandoned-cart-for-woocommerce' ); ?>
<span> <?php echo esc_html( '{checkout}' ); ?></span> <?php esc_html_e( ' for checkout page', 'abandoned-cart-for-woocommerce' ); ?>
</div>
<form action="" method="POST" class="mwb-m-gen-section-form">
<?php
global $wpdb;
	$settings_val = array();
	$result  = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_email_workflow' );
	$mwb_email_key_step  = array();
foreach ( $result as $key => $data ) {
			$ew_id        = $data->ew_id;
			$enable_value = $data->ew_enable;
			$content      = $data->ew_content;
			$time         = $data->ew_initiate_time;
			$subject      = $data->ew_mail_subject;
			$step         = $key + 1;
			$count = $ew_id - 1;
			$mwb_email_key_step[ $step ] = $ew_id;
	?>
						<input type="hidden" name="nonce" value="<?php echo esc_html( wp_create_nonce( 'workflow_nonce' ) ); ?>">
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label class="mwb-form-label mwb-form-group step_label_class"><?php echo esc_attr( 'Step' . $step ); ?></label>
							</div>
						</div>

					<div class="mwb-form-group">
						<div class="mwb-form-group__label">
							<label for="<?php echo 'enable_email-workflow'; ?>" class="mwb-form-label"><?php esc_html_e( 'Enable The workflow', 'abandoned-cart-for-woocommerce' ); ?></label>
						</div>
						<div class="mwb-form-group__control mwb-pl-4">
							<div class="mdc-form-field">
								<div class="mdc-checkbox">
								<input name="checkbox[<?php echo esc_attr( $key ); ?>]" id="<?php echo 'enable_email-workflow_' . esc_html( $step ); ?>" type="checkbox" value="on" class="mdc-checkbox__native-control m-checkbox-class" <?php checked( 'on', $enable_value ); //phpcs:ignore ?>	/>
									<div class="mdc-checkbox__background">
										<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
											<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
										</svg>
										<div class="mdc-checkbox__mixedmark"></div>
									</div>
									<div class="mdc-checkbox__ripple"></div>
								</div>
							</div>
						</div>
					</div>
						<?php apply_filters( 'mwb_wacr_add_condition', $ew_id, $data ); ?>
					<div class="mwb-form-group" id=<?php echo esc_attr( 'time_parent' . $step ); ?>>
						<div class="mwb-form-group__label">
							<label for="<?php echo esc_attr( 'initiate_time' . $step ); ?>" class="mwb-form-label"><?php esc_html_e( 'Initiate Time', 'abandoned-cart-for-woocommerce' ); // WPCS: XSS ok. ?></label>
						</div>
						<div class="mwb-form-group__control">
							<label class="mdc-text-field mdc-text-field--outlined">
								<span class="mdc-notched-outline">
									<span class="mdc-notched-outline__leading"></span>
									<span class="mdc-notched-outline__notch">
									</span>
									<span class="mdc-notched-outline__trailing"></span>
								</span>
								<input class="mdc-text-field__input m-number-class" name="time[]" id="<?php echo esc_attr( 'initiate_time' . $step ); ?>" type="number" value="<?php echo esc_html( $time ); ?>" placeholder="<?php esc_attr_e( 'Enter time', 'abandoned-cart-for-woocommerce' ); ?>" min="1" >
							</label>
							<div class="mdc-text-field-helper-line">
							<?php
							if ( 1 === $step ) {
								?>
								<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php esc_attr_e( 'Enter time In Hours', 'abandoned-cart-for-woocommerce' ); ?></div>
								<?php
							} else {
								?>
								<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php esc_attr_e( 'Enter time In Days', 'abandoned-cart-for-woocommerce' ); ?></div>
							<?php	} ?>
							</div>
						</div>
					</div>
					<div class="mwb-form-group" id=<?php echo esc_attr( 'subject_parent' . $step ); ?>>
						<div class="mwb-form-group__label">
							<label for="<?php echo esc_attr( 'subject' . $step ); ?>" class="mwb-form-label"><?php esc_html_e( 'Mail Subject', 'abandoned-cart-for-woocommerce' ); // WPCS: XSS ok. ?></label>
						</div>
						<div class="mwb-form-group__control">
							<label class="mdc-text-field mdc-text-field--outlined">
								<span class="mdc-notched-outline">
									<span class="mdc-notched-outline__leading"></span>
									<span class="mdc-notched-outline__notch">
									</span>
									<span class="mdc-notched-outline__trailing"></span>
								</span>
								<input class="mdc-text-field__input m-number-class" name="subject[]" id="<?php echo esc_attr( 'subject' . $step ); ?>" type="text" value="<?php echo esc_html( $subject ); ?>" placeholder="<?php esc_attr_e( 'Enter Mail Subject', 'abandoned-cart-for-woocommerce' ); ?>" >
							</label>
							<div class="mdc-text-field-helper-line">
								<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php esc_attr_e( 'Enter Subject', 'abandoned-cart-for-woocommerce' ); ?></div>
							</div>
						</div>
					</div>
					<div class="mwb-form-group" id=<?php echo esc_attr( 'content_parent' . $step ); ?>>
						<div class="mwb-form-group__label">
							<label class="mwb-form-label" for="<?php echo esc_attr( 'email_content' . $step ); ?>"><?php esc_attr_e( 'Content', 'abandoned-cart-for-woocommerce' ); ?></label>
						</div>
						<div class="mwb-form-group__control">

						<?php $settings = array( 'textarea_name' => 'email_workflow_content[]' ); ?>
						<?php wp_editor( $content, "email_workflow_content_$step", $settings ); ?>

						</div>
					</div>

					<?php do_action( 'mwb_wacr_delete_workflow', $ew_id ); ?>
					<br>
	<?php
}

	update_option( 'mwb_email_html_key', $mwb_email_key_step );
?>
<div class="mwb-form-group">
<br>
<div class="mwb-form-group__control">
	<?php apply_filters( 'mwb_wacr_add_more_button', array() ); ?>

	</div>
	</div>


<?php
		apply_filters( 'mwb_custom_email_settings_array', array() );

?>
		<div class="mwb-form-group">
				<div class="mwb-form-group__control">
					<input type="submit" class="mdc-button mdc-button--raised mdc-ripple-upgraded" name="submit_workflow" value="<?php esc_html_e( 'Save Workflow', 'abandoned-cart-for-woocommerce' ); ?>">
				</div>
		</div>
</form>
</div>

