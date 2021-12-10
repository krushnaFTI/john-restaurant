<div class="<?php echo apply_filters('mprm-widget-cart-toppings', 'mprm-widget-cart-toppings-list') ?>">
	<?php foreach ($toppings as $topping_key => $topping):
		$topping_post = get_post($topping['id']);
		$price_name = ($topping['type'] == "radio") ? ' - ' . $topping['value'] : '';
		if (!is_object($topping_post) && !is_a($topping_post, 'WP_Post')) {
			continue;
		}
		$price = mprm_currency_filter(mprm_format_amount($topping['item_price']));
		?>
		<div class="mprm-widget-cart-topping">
			<?php if ($topping['quantity'] > 1) { ?>
				<span class="widget-cart-topping-title"><?php echo $topping_post->post_title ?></span>&ensp;-&ensp;<span class="widget-cart-topping-quantity"><?php echo $topping['quantity'] ?>&ensp;&times;&ensp;</span><span class="widget-cart-topping-price"><?php echo $price ?></span>
			<?php } else { ?>
				<span class="widget-cart-topping-title"><?php echo $topping_post->post_title . $price_name ?></span>&ensp;-&ensp;<span class="widget-cart-topping-price"><?php echo $price ?></span>
			<?php } ?>
		</div>
	<?php endforeach; ?>
</div>
