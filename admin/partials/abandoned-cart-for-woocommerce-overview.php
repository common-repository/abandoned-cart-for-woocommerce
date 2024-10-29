<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Get_a_quote
 * @subpackage Get_a_quote/admin/partials
 */

// Overview Content Here.
?>
<div class="acfw-overview__wrapper">
	<div class="acfw-overview__banner">
		<img src="<?php echo esc_html( ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/Abandoned-Cart-banner.jpg' ); ?>" alt="Overview banner image">
	</div>
	<div class="acfw-overview__content">
		<div class="acfw-overview__content-description">
			<h2><?php echo esc_html_e( 'What Is Abandoned Cart For WooCommerce?', 'abandoned-cart-for-woocommerce' ); ?></h2>
			<p>

				<?php
				esc_html_e(
					'Abandoned Cart For WooCommerce is an all-in-one solution to beat an online seller’s biggest nightmare, i.e, abandoned carts. With the help of this plugin, 
					you can very easily and efficiently win back your lost WooCommerce customers and skyrocket your conversion rate.',
					'abandoned-cart-for-woocommerce'
				);
				?>
			</p>
			<h3><?php esc_html_e( 'As a store owner, you get to:', 'abandoned-cart-for-woocommerce' ); ?></h3>
			<div class="acfw-overview__features-wrapper">
				<ul class="acfw-overview__features">
					<li><?php esc_html_e( 'Analyze deep insights into your abandoned carts and products.', 'abandoned-cart-for-woocommerce' ); ?></li>
					<li><?php esc_html_e( 'Capture customer emails on the checkout and cart page.', 'abandoned-cart-for-woocommerce' ); ?></li>
					<li><?php esc_html_e( 'Send automated emails to your lost customers', 'abandoned-cart-for-woocommerce' ); ?></li>
					<li><?php esc_html_e( 'Win-back even the unregistered customers', 'abandoned-cart-for-woocommerce' ); ?></li>
					<li><?php esc_html_e( 'See a complete list of abandoned carts', 'abandoned-cart-for-woocommerce' ); ?></li>
					<li><?php esc_html_e( 'Customizable abandoned cart workflows', 'abandoned-cart-for-woocommerce' ); ?></li>
					<li><?php esc_html_e( 'Disable tracking for selected user roles', 'abandoned-cart-for-woocommerce' ); ?></li>
					<li><?php esc_html_e( 'Create an abandoned cart recovery strategy for even the variable products', 'abandoned-cart-for-woocommerce' ); ?></li>
				</ul>
				<div class="acfw-overview__video--url">
					<?php $mwb_acfw_youtube_link = 'https://www.youtube.com/embed/54Z-mkcit60'; ?>
					<iframe width="560" height="315" src=<?php echo esc_url( apply_filters( 'mwb_acfw_overview_video_link', $mwb_acfw_youtube_link ) ); ?> title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
			</div>
		</div>
		<h1> <?php esc_html_e( 'The Free Plugin Benefits', 'abandoned-cart-for-woocommerce' ); ?></h1>
		<div class="acfw-overview__keywords">
			<div class="acfw-overview__keywords-item">
				<div class="acfw-overview__keywords-card">
					<div class="acfw-overview__keywords-image">
						<img src="<?php echo esc_html( ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/Advanced-report.png' ); ?>" alt="Advanced-report image">
					</div>
					<div class="acfw-overview__keywords-text">
						<h3 class="acfw-overview__keywords-heading"><?php echo esc_html_e( ' Advanced Reports For Abandoned Products', 'abandoned-cart-for-woocommerce' ); ?></h3>
						<p class="acfw-overview__keywords-description">
							<?php
							esc_html_e(
								'The plugin comes with advanced reports for the abandoned carts made in your store. Thus, allowing you to 
								make an informed abandoned cart recovery strategy.',
								'abandoned-cart-for-woocommerce'
							);
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="acfw-overview__keywords-item">
				<div class="acfw-overview__keywords-card">
					<div class="acfw-overview__keywords-image">
						<img src="<?php echo esc_html( ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/Workflow.png' ); ?>" alt="Workflow image">
					</div>
					<div class="acfw-overview__keywords-text">
						<h3 class="acfw-overview__keywords-heading"><?php echo esc_html_e( 'Workflow For Abandoned Carts', 'abandoned-cart-for-woocommerce' ); ?></h3>
						<p class="acfw-overview__keywords-description"><?php echo esc_html_e( 'Abandoned Cart For WooCommerce comes with a customizable workflow that lets you send emails and win back your lost customers.', 'abandoned-cart-for-woocommerce' ); ?></p>
					</div>
				</div>
			</div>
			<div class="acfw-overview__keywords-item">
				<div class="acfw-overview__keywords-card">
					<div class="acfw-overview__keywords-image">
						<img src="<?php echo esc_html( ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/Variable-product.png' ); ?>" alt="Variable product image">
					</div>
					<div class="acfw-overview__keywords-text">
						<h3 class="acfw-overview__keywords-heading"><?php echo esc_html_e( 'Support Variable Product', 'abandoned-cart-for-woocommerce' ); ?></h3>
						<p class="acfw-overview__keywords-description">

							<?php
							echo esc_html_e(
								'The plugin works very well with and supports variable products on your WooCommerce store.',
								'abandoned-cart-for-woocommerce'
							);
							?>

						</p>
					</div>
				</div>
			</div>
			<div class="acfw-overview__keywords-item">
				<div class="acfw-overview__keywords-card">
					<div class="acfw-overview__keywords-image">
						<img src="<?php echo esc_html( ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/List-of-abandoned-users.png' ); ?>" alt="List-of-abandoned-users image">
					</div>
					<div class="acfw-overview__keywords-text">
						<h3 class="acfw-overview__keywords-heading"><?php echo esc_html_e( 'Complete List Of Abandoned Users', 'abandoned-cart-for-woocommerce' ); ?></h3>
						<p class="acfw-overview__keywords-description">

							<?php
							echo esc_html_e(
								'The plugin gives you a complete list of shoppers that left carts abandoned on your WooCommerce store.',
								'abandoned-cart-for-woocommerce'
							);
							?>

						</p>
					</div>
				</div>
			</div>
			<div class="acfw-overview__keywords-item">

				<div class="acfw-overview__keywords-card mwb-card-support">

					<div class="acfw-overview__keywords-image">
						<img src="<?php echo esc_html( ABANDONED_CART_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/Support.png' ); ?>" alt="Support image">
					</div>
					<div class="acfw-overview__keywords-text">
						<h3 class="acfw-overview__keywords-heading"><?php echo esc_html_e( 'Support', 'abandoned-cart-for-woocommerce' ); ?></h3>
						<p class="acfw-overview__keywords-description">
							<?php
							esc_html_e(
								"Phone, Email & Skype support. Our Support is ready to assist you regarding any query, issue, or feature request and if that doesn't help our Technical team will connect with you personally and have your query
								resolved.",
								'abandoned-cart-for-woocommerce'
							);
							?>
						</p>
					</div>
					<a href="https://makewebbetter.com/contact-us/" title=""></a>

				</div>
			</div>
		</div>
	</div>
</div>
