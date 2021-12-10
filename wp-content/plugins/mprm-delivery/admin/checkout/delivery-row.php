<?php do_action('mprm_checkout_table_delivery_before'); ?>

	<tr class="mprm_cart_footer_row mprm_cart_delivery_row">

		<td class="mprm_cart_delivery"><span><?php _e('Delivery', 'mprm-delivery'); ?></span></td>

		<td><span class="mprm_cart_delivery_amount" data-cost="" data-free="<?php _e('Free', 'mprm-delivery') ?>"><?php echo $data['delivery_cost'] ?></span></td>

	</tr>

<?php do_action('mprm_checkout_table_delivery_after'); ?>