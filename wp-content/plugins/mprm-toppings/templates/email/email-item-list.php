<ul class="" style="list-style: none"><?php
	foreach ($items as $item) {
		$cart_item_post = get_post($item['id']);
		if ($cart_item_post->post_type !== mpto_get_post_type()) {
			continue;
		}
		$quantity = mprm_item_quantities_enabled() ? $item['quantity'] : '';
		$price_id = mprm_get_cart_item_price_id($item);
		$item_title = get_the_title($item['id']) . ' ' . apply_filters('mprm_email_topping_price_name', mpto_get_price_option_name($item['id'], $price_id, $order), $item);
		if (!empty($quantity) && $quantity > 1) {
			$price = $quantity . ' x ' . mprm_currency_filter(mprm_format_amount($item['item_price']));
		} else {
			$price = mprm_currency_filter(mprm_format_amount($item['item_price']));
		}
		?><li style="margin-top: 0.25em;margin-bottom: 0.25em;"><span><?php echo $item_title ?></span><span>&nbsp;–&nbsp;<?php echo $price ?></span></li>
<?php } ?>
</ul>