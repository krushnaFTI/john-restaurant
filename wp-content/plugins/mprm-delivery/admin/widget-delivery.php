<?php $cart_items = mprm_get_cart_items(); ?>
<li class="mprm-cart-item mprm-cart-meta mprm_delivery <?php echo empty($cart_items) ? 'mprm-hidden' : '' ?>"><?php _e('Delivery:', 'mprm-delivery'); ?>
	<span class="mprm_cart_delivery_amount cart-delivery-cost" data-cost="" data-free="<?php _e('Free', 'mprm-delivery') ?>"><?php echo $data['delivery_cost']; ?></span>
</li>