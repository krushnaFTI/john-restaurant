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
		<h3>Check out</h3>
	<div class="left-content">
		<div class="cart-main">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="cart-table">
				<tbody>
					<tr>
						<th width="20%">Product</th>
						<th width="35%"></th>
						<th width="15%">Price</th>
						<th width="15%">Quantity</th>
						<th width="15%">Actions</th>
					</tr>
				</tbody>
				<tbody>
					<tr>
						<td class="cart-product-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/product-img.jpg" alt="" /></td>
						<td class="cart-product-content"><h4>Spicy potato</h4>
							<p>Pellentesque habitant morbi tris senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, </p></td>
						<td class="cart-product-price">$12.50</td>
						<td class="cart-product-qty product-qty">
							<div class="qty-minus"><a class="qty-button" href="#">-</a></div>
							<div class="qty-input"><input type="text" class="quntity-input" value="1" /></div>
							<div class="qty-plus"> <a class="qty-button" href="#">+</a></div>
						</td>
						<td class="cart-product-actions"><a href="#"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/cart-delete.png" alt="" /></a></td>
					</tr>
					<tr>
						<td class="cart-product-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/product-img.jpg" alt="" /></td>
						<td class="cart-product-content"><h4>Spicy potato</h4>
							<p>Pellentesque habitant morbi tris senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, </p></td>
						<td class="cart-product-price">$12.50</td>
						<td class="cart-product-qty product-qty">
							<div class="qty-minus"><a class="qty-button" href="#">-</a></div>
							<div class="qty-input"><input type="text" class="quntity-input" value="1" /></div>
							<div class="qty-plus"> <a class="qty-button" href="#">+</a></div>
						</td>
						<td class="cart-product-actions"><a href="#"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/cart-delete.png" alt="" /></a></td>
					</tr>
					<tr>
						<td class="cart-product-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/product-img.jpg" alt="" /></td>
						<td class="cart-product-content"><h4>Spicy potato</h4>
							<p>Pellentesque habitant morbi tris senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, </p></td>
						<td class="cart-product-price">$12.50</td>
						<td class="cart-product-qty product-qty">
							<div class="qty-minus"><a class="qty-button" href="#">-</a></div>
							<div class="qty-input"><input type="text" class="quntity-input" value="1" /></div>
							<div class="qty-plus"> <a class="qty-button" href="#">+</a></div>
						</td>
						<td class="cart-product-actions"><a href="#"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/cart-delete.png" alt="" /></a></td>
					</tr>
				</tbody>
			</table>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="cart-price-table">
				<tr>
					<td>Subtotal <span>$39</span></td>
					<td style="text-align: center">Tax <span>$4</span></td>
					<td style="text-align: right">Total <span>$43</span></td>
				</tr>
			</table>
		</div>
		
		<div class="delivery-main">
			<div class="title-bg">I would like my order</div>
			<div id="parentHorizontalTab" class="parentHorizontalTab">
            <ul class="resp-tabs-list hor_1">
                <li>Delivery</li>
                <li>Pickup</li>
               
            </ul>
            <div class="resp-tabs-container hor_1">
              <div>
                    <p>You can pick up your order at the following address:<br>
					84 Friar Street, CLIFFE, ME3 1ES</p>

					<p>We're open Mon-Sa 8am - 9pm</p>

					<p>Reach us at 760-796-3011 phone number if needed</p>

				<p>When is it for?</p>
				<p>
					  <label>
					    <input type="radio" name="RadioGroup1" value="radio" id="RadioGroup1_0">
					    ASAP, approx. 10:03 am</label>
					<label>
					    <input type="radio" name="RadioGroup1" value="radio" id="RadioGroup1_1">
					    Later (At a set time)</label>
			    </p>
				  <textarea placeholder="Order notes to resturant"></textarea>
                </div>
                 <div>
                    <p>You can pick up your order at the following address:<br>
					84 Friar Street, CLIFFE, ME3 1ES</p>

					<p>We're open Mon-Sa 8am - 9pm</p>

					<p>Reach us at 760-796-3011 phone number if needed</p>

				<p>When is it for?</p>
				<p>
					  <label>
					    <input type="radio" name="RadioGroup1" value="radio" id="RadioGroup1_0">
					    ASAP, approx. 10:03 am</label>
					<label>
					    <input type="radio" name="RadioGroup1" value="radio" id="RadioGroup1_1">
					    Later (At a set time)</label>
			    </p>
				  <textarea placeholder="Order notes to resturant"></textarea>
                </div>
               
            </div>
        </div>
		</div>
		<div class="payment-method-main">
			<div class="title-bg">Select Payment Method</div>
			<div id="parentHorizontalTab" class="parentHorizontalTab">
            <ul class="resp-tabs-list hor_1">
                <li>Cash on delivery</li>
                <li>Pay via PayPal</li>
                <li>credit card or debit card</li>
               
            </ul>
            <div class="resp-tabs-container hor_1">
              <div>
                   Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sed blandit orci. Praesent eu magna ut dui tempor consectetur ac at sem. Praesent lobortis leo nec gravida ultrices. 
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
               
            </div>
        </div>
		</div>
		
		
	</div>
	<div class="right-content">
		
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
<script>
	jQuery(document).ready(function(){
		jQuery(".qty-button").on("click", function () {
			var $button = jQuery(this);
			var oldValue = $button.closest('.product-qty').find("input.quntity-input").val();

			if ($button.text() == "+") {
				var newVal = parseFloat(oldValue) + 1;
			} else {
				// Don't allow decrementing below zero
				if (oldValue > 0) {
					var newVal = parseFloat(oldValue) - 1;
				} else {
					newVal = 0;
				}
			}

			$button.closest('.product-qty').find("input.quntity-input").val(newVal);

		});
	});
</script>
<?php get_footer(); ?>
