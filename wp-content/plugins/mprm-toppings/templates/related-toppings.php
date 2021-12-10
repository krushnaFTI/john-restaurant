<?php
use mprm_toppings\classes\View;

if (empty($args['toppings'])) {
	return;
}
$toppings_box_title = mprm_get_option('toppings_popup_title', __('You can order optional toppings', 'mprm-toppings'));
$pro_price = mpto_get_price($post_id);

?>
<div id="toppings-wrapper-<?php echo $post_id ?>" class="mprm-section" data-menu_id="<?php echo $post_id ?>" data-price_id="<?php echo $pro_price; ?>">
 
	<div class="mprm-hidden <?php echo apply_filters('mprm-cart-toppings-wrapper', 'mprm-cart-toppings-wrapper') ?>">
		
		<div class="mprm-title"><?php echo $toppings_box_title ?></div>
		<div class="mprm-close mprm-topping-popup-close"></div>
		<input type="hidden" name="menu_item_id" value="<?php echo $post_id ?>">

		<div class="mprm-list-wrapper">
			<ul class="mprm-list">
				<?php foreach ($args['toppings'] as $topping) {
					$topping_type = get_post_meta($topping->ID, 'topping-types', true);
					View::get_instance()->get_template("popup/{$topping_type}", array('topping' => $topping));
				}
				?>
			</ul>
		</div>

		<?php
			if ( function_exists('mprm_get_add_to_cart_notice') ) {
				mprm_get_add_to_cart_notice();
			} else {
				mprm_get_view()->get_template( 'common/notice', array('menu_item_id' => get_the_ID()) );
			}
		 ?>
		<div class="mprm-topping-footer mprm-display-inline mprm_purchase_submit_wrapper" style="position: relative;">
			<?php mprm_get_preloader('small-preloader mprm-hidden'); ?>
			<a href="javascript:void(0)" class=" mprm-submit mprm-display-inline mprm-topping-add-to-cart <?php echo $args['style'] . ' ' . $args['color'] . ' ' . $args['padding'] ?> "><?php echo mprm_get_option('add_to_cart_text', __('Add to Cart', 'mprm-toppings')) ?></a> 
		</div>
	</div>
</div>