<?php
/**
 * 
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
	<div class="left-content">
		<div class="product-details-top">
			<div class="product-details-top-left">
				<div class="product-details-main-img">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/product-main-img.jpg" alt="" />
				</div>
				<div class="product-thumbnail-img">
					<ul>
						<li><a href="#"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/thumbnail-img.jpg" alt="" /></a></li>
						<li><a href="#"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/thumbnail-img.jpg" alt="" /></a></li>
						<li><a href="#"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/thumbnail-img.jpg" alt="" /></a></li>
					</ul>
				</div>
			</div>
			<div class="product-details-top-right">
				<div class="product-details-name">Spicy potato</div>
				<div class="product-details-des">When compared to steak-cut chips (UK), fries (US & Global), roasted potatoes or crinkle-cut chips (UK), a wedge could be defined as having distinct corners when viewed as a cross-section perpendicular to the normal- a centreline running along the length of the cut potato form. This can be viewed as a triangular section, should there be 4 corners it would commonly be referred to as just a chip/fries.</div>
				<div class="product-details-price">Price: $12</div>
				<div class="product-details-cart-button"><a href="#" class="button">add to cart</a></div>
				<div class="order-via">
					<div class="order-via-title">order VIa</div>
					<ul>
						<li><a href="#"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/swiggy.png" alt="" /></a></li>
						<li><a href="#"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/uber-eats.png" alt="" /></a></li>
						<li><a href="#"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/zomato.png" alt="" /></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="product-tabs">
			<div id="parentHorizontalTab" class="parentHorizontalTab">
            <ul class="resp-tabs-list hor_1">
                <li>Ingredients</li>
                <li>Portion Size</li>
                <li>Nutritional</li>
            </ul>
            <div class="resp-tabs-container hor_1">
                <div>
                    <ul>
						<li>Calories: 350</li>
						<li>Cholesterol: 6 g</li>
						<li>Fiber: 45 g</li>
						<li>Sodium: 14 g</li>
						<li>Carbohydrates: 5 g</li>
						<li>Fat: 5 g</li>
						<li>Protein: 16 g</li>
					</ul>
                </div>
                <div>
                    <ul>
						<li>Cholesterol: 6 g</li>
						<li>Fiber: 45 g</li>
						<li>Sodium: 14 g</li>
						<li>Carbohydrates: 5 g</li>
						<li>Fat: 5 g</li>
						<li>Protein: 16 g</li>
					</ul>
                </div>
                <div>
                    <ul>
						<li>Fiber: 45 g</li>
						<li>Sodium: 14 g</li>
						<li>Carbohydrates: 5 g</li>
						<li>Fat: 5 g</li>
						<li>Protein: 16 g</li>
					</ul>
                </div>
            </div>
        </div>
		</div>
		<div class="related-products">
			<h3>You might also like</h3>
			<div class="row">
				<div class="col-6 item">
					<div class="related-products-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/product-img.jpg" alt=""></div>
					<div class="related-products-content">
						<div class="related-products-title">
							<a href="#"><span class="name">Spicy potato</span><span class="price">$12.50</span></a>
						</div>
						<div class="related-products-des">
							Pellentesque habitant morbi tris senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, 
						</div>
					</div>
				</div>
				<div class="col-6 item">
					<div class="related-products-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/product-img.jpg" alt=""></div>
					<div class="related-products-content">
						<div class="related-products-title">
							<a href="#"><span class="name">Spicy potato</span><span class="price">$12.50</span></a>
						</div>
						<div class="related-products-des">
							Pellentesque habitant morbi tris senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, 
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-6 item">
					<div class="related-products-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/product-img.jpg" alt=""></div>
					<div class="related-products-content">
						<div class="related-products-title">
							<a href="#"><span class="name">Spicy potato</span><span class="price">$12.50</span></a>
						</div>
						<div class="related-products-des">
							Pellentesque habitant morbi tris senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, 
						</div>
					</div>
				</div>
				<div class="col-6 item">
					<div class="related-products-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/product-img.jpg" alt=""></div>
					<div class="related-products-content">
						<div class="related-products-title">
							<a href="#"><span class="name">Spicy potato</span><span class="price">$12.50</span></a>
						</div>
						<div class="related-products-des">
							Pellentesque habitant morbi tris senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, 
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</div>
	<div class="right-content">
		<div class="sidebar-box">
			<h3>Cart</h3>
			<div class="sidebar-content">
				<div class="cart-items">
					<div class="cart-item">
						<span class="cart-name"><a href="#">Spicy potato</a></span> <span class="cart-qty">- 2</span><span class="cart-delete"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/delete-icon.png" alt="" /></span> <span class="cart-price">$12.50</span>
					</div>
					<div class="cart-item">
						<span class="cart-name"><a href="#">Pizza</a></span> <span class="cart-qty"> - 2</span><span class="cart-delete"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/delete-icon.png" alt="" /></span> <span class="cart-price">$12.50</span>
					</div>
					<div class="cart-item">
						<span class="cart-name"><a href="#">Puff</a></span> <span class="cart-qty"> - 2</span><span class="cart-delete"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/delete-icon.png" alt="" /></span> <span class="cart-price">$12.50</span>
					</div>
				</div>
				<div class="cart-sub-total">
						Subtotal <span class="cart-subtotal">$39.00</span>
					</div>
				<div class="cart-button"><a href="#" class="button">Checkout</a></div>
			</div>
		</div>
		<div class="sidebar-box">
			<h3>Recommended by Our Chef</h3>
			<div class="sidebar-content">
				<div class="recommended-items">
					<div class="recommended-item">
						<div class="recommended-item-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/recommended-img.png" alt="" /></div>
						<div class="recommended-item-right">
							<h4>Spicy potato</h4>
							<p>with lemon, honey and various herbs</p>
						</div>
					</div>
					<div class="recommended-item">
						<div class="recommended-item-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/recommended-img.png" alt="" /></div>
						<div class="recommended-item-right">
							<h4>Spicy potato</h4>
							<p>with lemon, honey and various herbs</p>
						</div>
					</div>
					<div class="recommended-item">
						<div class="recommended-item-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/recommended-img.png" alt="" /></div>
						<div class="recommended-item-right">
							<h4>Spicy potato</h4>
							<p>with lemon, honey and various herbs</p>
						</div>
					</div>
					<div class="recommended-item">
						<div class="recommended-item-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/recommended-img.png" alt="" /></div>
						<div class="recommended-item-right">
							<h4>Spicy potato</h4>
							<p>with lemon, honey and various herbs</p>
						</div>
					</div>
					<div class="recommended-item">
						<div class="recommended-item-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/recommended-img.png" alt="" /></div>
						<div class="recommended-item-right">
							<h4>Spicy potato</h4>
							<p>with lemon, honey and various herbs</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>	 
</div>

<?php get_footer(); ?>
