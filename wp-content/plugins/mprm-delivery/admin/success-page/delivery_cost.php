<?php

$delivery_cost = is_numeric($data['delivery_cost']) ?
	html_entity_decode(mprm_currency_filter(mprm_format_amount($data['delivery_cost']))) :
	$data['delivery_cost'];

?>
<tr>
	<td><strong><?php _e('Delivery', 'mprm-delivery'); ?></strong></td>
	<td><?php echo esc_html( $delivery_cost ); ?></td>
</tr>