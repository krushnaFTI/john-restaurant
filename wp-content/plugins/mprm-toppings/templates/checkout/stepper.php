<?php $topping_meta = mpto_get_topping_meta($topping_post->ID, true);
$min = $topping_meta['min'] <= 0 ? 1 : $topping_meta['min'];
?>

<tr class="mprm-cart-topping" data-menu-id="<?php echo $item['id'] ?>" data-topping-id="<?php echo $topping_post->ID ?>">
	<div class="topping-item">
	<td class="mprm-cart-topping-item-name"><?php echo ' - ' . $topping_post->post_title; ?></td>
	<td class="mprm-cart-topping-item-price"><span class="custom-topping-price"><?php echo mprm_currency_filter(mprm_format_amount($topping['item_price'])); ?></span></td>
	<?php if (\mp_restaurant_menu\classes\models\Cart::get_instance()->item_quantities_enabled() ) { ?>
		<td class="mprm-cart-topping-quantities">
			<input type="number" min="<?php echo $min; ?>" step="<?php $topping_meta['max'] ?>" name="mprm-cart-topping-<?php echo $topping_post->ID ?>" class="mprm-input mprm-item-topping-quantity" value="<?php echo $topping['quantity'] ?>">
		</td>
	<?php } ?>
	<td class="mprm-cart-topping-actions"><a class="mprm-cart-topping-remove-item-btn mprm-cart-topping-remove-item-checkout" data-mprm_action="remove" data-mpto_controller="toppings" data-menu_ID="<?php echo $item['id']; ?>" data-topping_ID="<?php echo $topping_post->ID ?>" data-cart_item="<?php echo esc_attr($index); ?>" href="<?php echo esc_url(mprm_toppings_remove_url($topping_post, $index)); ?>"><?php _e('Remove', 'mprm-toppings') ?></a></td>
	</div>
</tr>