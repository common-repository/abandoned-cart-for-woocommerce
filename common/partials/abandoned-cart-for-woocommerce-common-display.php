<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Abandoned_Cart_For_Woocommerce
 * @subpackage Abandoned_Cart_For_Woocommerce/common/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<h1> <?php esc_html_e( 'You have successfully unsubscribed !!!!!! Go back to', 'abandoned-cart-for-woocommerce' ); ?><a href="<?php echo wp_kses_post( get_site_url() ); ?>">   <?php esc_html_e( 'Site', 'abandoned-cart-for-woocommerce' ); ?></a></h1>
<?php
exit();
?>
