<?php
global $post;

list($delivery_order_meta, $gate, $street, $apartment, $notes, $address_type, $time_mode, $time) = mpde_normalize_order_delivery_meta($delivery_order_meta);
$_address_type = empty($delivery_order_meta['address_type']) ? '' : $delivery_order_meta['address_type'];

if ($delivery_order_meta['delivery_mode'] === 'collection') {
?>
	<span class="mpde-delivery_mode"><?php _e('Pickup', 'mprm-delivery'); ?></span>, <span><?php
	if ( $time_mode == 'asap' ) {
		printf( __('ASAP: %s', 'mprm-delivery'), $time );
	} else {
		printf( __('Later: %s', 'mprm-delivery'), $time );
	}
	?></span>
<?php } elseif ($delivery_order_meta['delivery_mode'] === 'delivery') {
?>
	<span class="mpde-delivery_mode"><?php _e('Delivery', 'mprm-delivery'); ?></span>, <span><?php
	if ( $time_mode == 'asap' ) {
		printf( __('ASAP: %s', 'mprm-delivery'), $time );
	} else {
		printf( __('Later: %s', 'mprm-delivery'), $time );
	}
	?></span>
	<br/>
	<span style="color:#999"><?php echo ucfirst($_address_type) . $street . $apartment . $gate . $notes ?></span>
<?php
}