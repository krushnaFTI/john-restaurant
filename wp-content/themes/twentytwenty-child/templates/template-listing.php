<?php
/**
 * Template Name: Listing
 * Template Post Type: post, page
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */
get_header();

// get_template_part( 'singular' ); ?>
<main id="site-content" role="main">

	<?php

	if ( have_posts() ) {

		while ( have_posts() ) {
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );
		}
	}

	?>

</main><!-- #site-content -->



	


<div class="main-content">
	<div class="container">
		<div class="menu-items-list left-content"><?php echo do_shortcode('[mprm_items view="list" categ="" tags_list="" item_ids="" col="2" categ_name="with_img" show_attributes="1" feat_img="1" excerpt="1" price="1" tags="1" ingredients="1" buy="1" link_item="1" desc_length=""]'); ?></div>
		<?php /* <div class="right-content">
		    <?php dynamic_sidebar('item_listing_page_sidebar'); ?>
		    <?php /* <fieldset id="mprm_payment_summary_table">
        		<?php do_action('mprm_checkout_summary_table', 'mprm_checkout_summary_table'); ?>
        	</fieldset> 
		</div> */ ?>
		<?php /*?><div class="left-content"><?php */?>
			<?php /* ?>
			<div class="listing-items">
				<?php
				$args = array(  
					'post_type' => 'mp_menu_item',
					'post_status' => 'publish',
					'posts_per_page' => -1, 
					'orderby' => 'date', 
					'order' => 'DESC', 
				);
				$loop = new WP_Query( $args ); 
				if ( $loop->have_posts() ) {
					while ( $loop->have_posts() ) : $loop->the_post();
						$product_item_id = get_the_ID();
						$product_price = get_post_meta($product_item_id, 'price');
						$product_in_stock = get_post_meta($product_item_id, 'stock_manage_field_custom');
						if($product_in_stock[0] == "InStock"){
							?>
							<div class="listing-item">
								<div class="listing-img"><img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="" /></div>
								<div class="listing-right-content">
									<div class="listing-title">
										<a href="<?php echo get_the_permalink(); ?>"><span class="name"><?php echo get_the_title(); ?></span><?php if($product_price[0]){ ?><span class="price">$<?php echo $product_price[0]; ?></span><?php } ?></a> 
										<!-- <span class="name"><?php // echo get_the_title(); ?></span>-->
										<?php // if($product_price[0]){ ?><!-- <span class="price">$<?php // echo $product_price[0]; ?></span> --><?php // } ?>
									</div>
									<div class="listing-des"><?php echo get_the_excerpt(); ?></div>
									<div class="custom_btn">
									  <a href="<?php echo get_the_permalink(); ?>" class="button moreinfo">More Info</a>
									  <!--a href="<?php //echo get_the_permalink(); ?>" class="button addtocart">Add To Cart</a-->
									  
									  <!--a href="#" class="button addtocart" data-action="mprm_add_to_cart" data-menu-item-id="<?php //echo esc_attr($post->ID) ?>" <?php //echo $data_variable . ' ' . $type . ' ' . $data_price; ?>>Add To Cart</a-->
									</div>
									<!--<div>-->
									    
									    <?php //echo do_shortcode('[mprm_items view="grid" categ="" tags_list="" item_ids="" col="1" categ_name="only_text" show_attributes="" feat_img="1" excerpt="" price="1" tags="" ingredients="" buy="1" link_item="" desc_length="200"]'); ?>
									<!--</div>-->
								</div>
							</div>
						<?php } 
					endwhile;
				}else{ ?>
					<div class="listing-item">
						<div class="listing-des">No Product Found.</div>
					</div>
					<?php }
				wp_reset_postdata(); ?>
			</div> <?php */ ?>
		<?php /*?></div><?php */?>
		<?php /*?><div class="right-content">
			<div class="sidebar-box">
				<h3>Cart</h3>
				<div class="sidebar-content">
					<div class="cart-items">
						<?php
						$session_cart = unserialize($_SESSION['mprm']['mprm_cart']);
						if(!empty($_SESSION) && !empty($session_cart) ){
							$cart_data = unserialize($_SESSION['mprm']['mprm_cart']); 
							$total_cart_items = count($cart_data);
							for($i=0; $i<$total_cart_items; $i++){ 
								$item_id = $cart_data[$i]['id'];
								$item_title = get_the_title($cart_data[$i]['id']);
								$item_price = get_post_meta($item_id, 'price');
								$item_quantity = $cart_data[$i]['quantity'];
								$item_total_price = $item_price[0] * $item_quantity; ?>
								<div class="cart-item">
									<span class="cart-name"><a href="<?php echo get_the_permalink($item_id); ?>"><?php echo $item_title; ?></a></span>
									<span class="cart-qty">- <?php echo $item_quantity; ?></span>
									<span class="cart-delete"><a href="<?php echo site_url(); ?>/checkout/?cart_item=<?php echo $i; ?>&mprm_action=remove&controller=cart"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/delete-icon.png" alt="" /></a></span>
									<span class="cart-price">$<? echo $item_total_price; ?></span>
								</div>
							<?php } ?>
							<div class="cart-sub-total">
								Subtotal <span class="cart-subtotal"><?php echo mprm_currency_filter(mprm_format_amount(mprm_get_cart_subtotal())); ?></span>
							</div>
							<div class="cart-button"><a href="<?php echo site_url(); ?>/checkout/" class="button">Checkout</a></div>
						<?php }else{ ?>
							<p>Your cart is empty.</p>
						<?php } ?>
					</div>
					
				</div>
			</div>
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
										<div class="recommended-item-img"><a href="<?php echo get_the_permalink(); ?>"><img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="" /></a></div>
										<div class="recommended-item-right">
											<h4><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h4>
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
		</div><?php */?>
	</div>	
</div>

<?php get_footer(); ?>