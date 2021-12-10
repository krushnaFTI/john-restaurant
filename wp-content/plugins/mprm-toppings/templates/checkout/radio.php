<?php
$topping_meta = mpto_get_topping_meta($topping_post->ID, true);
$price_name = ($topping['type'] === "radio") ? ' - ' . $topping['value'] : '';
$required = empty($topping_meta['required']) ? false : true;

if ($topping['item_price'] <= 0) {
	$item_price = '';
} else {
	$item_price = mprm_currency_filter(mprm_format_amount($topping['item_price']));
}
$is_quantities_enabled = \mp_restaurant_menu\classes\models\Cart::get_instance()->item_quantities_enabled();

?>

<tr class="mprm-cart-topping" data-menu-id="<?php echo $item['id'] ?>" data-topping-id="<?php echo $topping_post->ID; ?>">
	<div class="topping-item">
	<td class="mprm-cart-topping-item-name"><?php echo ' - ' . $topping_post->post_title . $price_name; ?> </td>
	<td class="mprm-cart-topping-item-price"><span class="custom-topping-price"><?php echo $item_price; ?></span></td>
	<?php if ($is_quantities_enabled) { ?>
		<td class="mprm-cart-topping-quantities" colspan="<?php echo $required ? '2' : '' ?>"></td>
	<?php } ?>
	<?php if (!$required) { ?>
		<td class="mprm-cart-topping-actions">
			<a class="mprm-cart-topping-remove-item-btn" data-menu_ID="<?php echo $item['id']; ?>" href="<?php echo esc_url(mprm_toppings_remove_url($topping_post, $index)); ?>"><?php _e('Remove', 'mprm-toppings') ?></a>
		</td>
	<?php } ?>
	</div>
</tr>
