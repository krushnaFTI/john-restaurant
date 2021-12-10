<?php list($delivery_order_meta, $gate, $street, $apartment, $notes, $address_type, $time_mode, $time) = mpde_normalize_order_delivery_meta($delivery_order_meta); ?>
<?php if ($delivery_order_meta['delivery_mode'] === 'collection') { ?>
	<span><?php _e('Pickup', 'mprm-delivery'); ?>,</span><span><?php echo $time ?></span>
<?php } elseif ($delivery_order_meta['delivery_mode'] === 'delivery') { ?>
	<span><?php _e('Delivery', 'mprm-delivery'); ?></span><span><?php echo ucfirst($address_type) . $street . $apartment . $gate . $notes ?></span><span><?php echo ', ' . $time_mode ?> </span>
	<?php if ($time_mode !== 'asap') { ?>
		<span><?php echo $time ?></span>
	<?php }
} ?>