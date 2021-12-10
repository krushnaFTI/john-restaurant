<?php

$delivery_order_meta = get_post_meta($order->ID, 'mpde_delivery', true);
list($delivery_order_meta, $gate, $street, $apartment, $notes, $address_type, $time_mode, $time) = mpde_normalize_order_delivery_meta($delivery_order_meta);

if ($delivery_order_meta) {
	?><tr>
		<td><strong><?php _e('Delivery Method') ?></strong></td>
		<td>
			<?php if ($delivery_order_meta['delivery_mode'] === 'collection') { ?>
				<?php _e('Pickup', 'mprm-delivery'); ?><br/>
				<?php printf( __('At %s', 'mprm-delivery'),  $time); ?>
			<?php } elseif ($delivery_order_meta['delivery_mode'] === 'delivery') { ?>
				<?php _e('Delivery', 'mprm-delivery'); ?><?php echo ucfirst($address_type) . $street . $apartment . $gate . $notes ?><br/>
				<?php if ($time_mode == 'asap') { ?>
					<?php echo __('ASAP', 'mprm-delivery'); ?>
				<?php } else {?>
					<?php printf( __('At %s', 'mprm-delivery'),  $time); ?>
				<?php }
			} ?>
		</td>
	</tr><?php
}
