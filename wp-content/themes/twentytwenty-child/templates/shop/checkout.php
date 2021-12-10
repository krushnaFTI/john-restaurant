<div class="container">
	<div id="mprm_checkout_wrap" class="<?php echo mprm_get_option('disable_styles') ? 'mprm-no-styles' : 'mprm-plugin-styles' ?>">
	    <h3>Check out</h3>
		<?php if ( ($cart_contents || $cart_has_fees) && mprm_can_checkout() ) : ?>
			<?php
			do_action('mprm_purchase_form_cart_items_before');
			?>
		<?php /* <p class="mprm-required"><small><?php _e('Required fields are followed by', 'mp-restaurant-menu'); ?></small></p> 
		<?php
			mprm_get_checkout_cart_template();
			do_action('mprm_purchase_form_cart_items_after');   */
			?>
			<div id="mprm_checkout_form_wrap" class="mprm-clear">
				<?php do_action('mprm_before_purchase_form'); ?>
				<form id="mprm_purchase_form" class="mprm-clear" action="<?php echo $form_action; ?>" method="POST">
					<?php
					/**
					 * Hooks in at the top of the checkout form
					 *
					 * @since 1.0
					 */
					do_action('mprm_checkout_form_top');

					if (mprm_show_gateways()) {
						do_action('mprm_payment_mode_select');
					} else {
						do_action('mprm_purchase_form');
					}

					/**
					 * Hooks in at the bottom of the checkout form
					 *
					 * @since 1.0
					 */
					do_action('mprm_checkout_form_bottom'); ?>
					<input type="hidden" class="webpushr_id_value" name="webpushr_id_value">
					
				</form>
				<?php 
				
				// $get_cart = unserialize($_SESSION['mprm']['mprm_cart']);
				// $get_delivery_type = $_SESSION['mprm']['mpde_delivery_type'];
				// $get_purchase_details = unserialize($_SESSION['mprm']['mprm_purchase']);

				$get_product = $_SESSION['cart_details'][0]['name'];
				$get_user_email = $_SESSION['user_email'];
				$get_total_price = $_SESSION['price'];
				
				?>

				<form action="<?php echo site_url(); ?>/stripe-checkout" method="POST" class="stripe-checkout-form">

					<input type="hidden" name="stripe-address_type" id="stripe-address_type" value="">
					<input type="hidden" name="stripe-delivery_street" id="stripe-delivery_street" value="">
					<input type="hidden" name="stripe-delivery_apartment" id="stripe-delivery_apartment" value="">
					<input type="hidden" name="stripe-delivery_gate_code" id="stripe-delivery_gate_code" value="">
					<input type="hidden" name="stripe-delivery_notes" id="stripe-delivery_notes" value="">
					<input type="hidden" name="stripe-time-mode" id="stripe-time-mode" value="">
					<input type="hidden" name="stripe-order-hours" id="stripe-order-hours" value="">
					<input type="hidden" name="stripe-order-minutes" id="stripe-order-minutes" value="">
					<input type="hidden" class="webpushr_id_value" name="webpushr_id_value">

					<input type="hidden" name="stripe-user-fname" id="stripe-user-fname" value="">
					<input type="hidden" name="stripe-user-lname" id="stripe-user-lname" value="">
					<input type="hidden" name="stripe-user-phone" id="stripe-user-phone" value="">
					<input type="hidden" name="stripe-payment-method" id="stripe-payment-method" value="">
					<input type="hidden" name="stripe-delivery-amount" id="stripe-delivery-amount" value="">
					<input type="hidden" name="stripe-tax" id="stripe-tax" value="">
					<input type="hidden" name="stripe-subtotal" id="stripe-subtotal" value="">
					
					<input type="hidden" name="stripe-total-amount" id="stripe-total-amount" value="<?php echo $get_total_price; ?>">
					<input type="hidden" name="stripe-checkout-email" id="stripe-checkout-email" value="<?php echo $get_user_email; ?>">
					<input type="hidden" name="stripe-item-name" id="stripe-item-name" value="<?php echo $get_product; ?>">
					<input type="hidden" name="stripe-item-description" id="stripe-item-description" value="John Restaurant">
					
					<input type="submit" class="mprm-stripe-submit" id="mprm-stripe-purchase-button">
				</form>
				<?php //echo do_shortcode('[wp_stripe_checkout item_name="T-Shirt" description="Short-sleeve" amount="1.00" label="Pay Now"]');
				
				 do_action('mprm_after_purchase_form'); ?>
			</div>
			<?php
		else:
			/**
			 * Fires off when there is nothing in the cart
			 *
			 * @since 1.0
			 */
			do_action('mprm_cart_empty');
		endif; ?>
	</div>
	
	<div class="right-content checkout_sidebar">
		<div class="sidebar-box">
			<div class="sidebar-content">
	            <?php mprm_get_checkout_cart_template();
		            do_action('mprm_purchase_form_cart_items_after'); 
				?>
		    </div>
		</div>
	</div>
	
	
<?php /*
	<div class="right-content checkout_sidebar">
		<div class="sidebar-box">
			<h3>Recommended by Our Chef</h3>
			<div class="sidebar-content">
				<div class="recommended-items">
					<?php
					$args = array(  
						'post_type' => 'mp_menu_item',
						'post_status' => 'publish',
						'posts_per_page' => 4, 
						'orderby' => 'date', 
						'order' => 'DESC', 
					);
					$loop = new WP_Query( $args ); 
					if ( $loop->have_posts() ) {
						while ( $loop->have_posts() ) : $loop->the_post();
							$product_item_id = get_the_ID();
							$product_price = get_post_meta($product_item_id, 'price');
							$product_in_stock = get_post_meta($product_item_id, 'stock_manage_field_custom');
							if($product_in_stock[0] == "InStock"){ ?>
							
								<div class="recommended-item">
									<div class="recommended-item-img"><a href="<?php echo get_the_permalink($product_item_id); ?>"><img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="" /></a></div>
									<div class="recommended-item-right">
										<h4><a href="<?php echo get_the_permalink($product_item_id); ?>"><?php echo get_the_title(); ?></a></h4>
										<?php $get_excerpt = get_the_excerpt(); ?>
										<p><?php echo substr($get_excerpt, 0, 40); ?>...</p>
									</div>
								</div>
							
							<?php }
						endwhile;
					} ?>
				</div>
			</div>
		</div>
	</div> */ ?>
</div>