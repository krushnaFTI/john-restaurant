<?php
use mp_restaurant_menu\classes\models\Order;
use mprm_delivery\classes\View;

/**
 * Delivery part
 *
 * @param array $params
 */
function mprm_get_delivery_part(array $params) {
	$default_values = array(
		'template' => '',
		'data' => array(),
		'output' => true,
		'checked' => false
	);

	$params = array_merge($default_values, $params);

	if ($params['output']) {
		echo View::get_instance()->get_template_html($params['template'], $params['data']);
	} else {
		View::get_instance()->get_template($params['template'], $params['data']);
	}
}

/**
 * ASAP time (Minimum time interval from the time of purchase)
 *
 * @param string $time_type
 *
 * @return string
 */
function mpde_get_ASAP_time($time_type = 'timestamp') {


	$min_time_interval = (int)mprm_get_option('delivery_min_time_interval', 0);
	if ($time_type === 'timestamp') {
		$time = strtotime("+{$min_time_interval} minutes", current_time('timestamp'));
	} elseif ($time_type === 'order') {
		$order_time = get_post_time();
		$time = strtotime("+{$min_time_interval} minutes", $order_time);;
	}

	//round up
	//$time = ceil( $time / (5 * 60)) * (5 * 60);

	$asap_time = date_i18n(get_option('time_format'), $time);

	return $asap_time;
}

/**
 * Generate hours DropDown
 *
 * @param int $start
 * @param int $end
 */
function mpde_get_available_hours($start = 0, $end = 24) {
	$min_time_interval = (int)mprm_get_option('delivery_min_time_interval', 0);
	$time = strtotime("+{$min_time_interval} minutes", current_time('timestamp'));

	//round up
	$time = ceil( $time / (60 * 60)) * (60 * 60);
	$current_hour = date('H', $time);
	$current_minutes = date('i', current_time('timestamp'));

	/*$minutes = $min_time_interval % 60;
	if (($current_minutes + $minutes) >= 60) {
		$current_hour++;
	}*/

	$time_format = str_replace( ':i', '', get_option('time_format') );
	for ($i = $start; $i < $end; $i++):
		if ($current_hour > $i) {
			continue;
		}
		$formatting_i = "$i:00";
		?>
		<option value="<?php echo $i; ?>"><?php echo date( $time_format, strtotime($formatting_i)); ?></option>
	<?php endfor;
}

/**
 * Email tag {delivery} callback
 *
 * @param $order_id
 *
 * @return mixed|string|void
 */
function mpde_email_tag_delivery($order_id) {
	$order = new Order($order_id);
	if (is_object($order)) {
		$delivery_data = get_post_meta($order->ID, 'mpde_delivery', true);
		if (isset($delivery_data['delivery_cost'])) {
			$delivery_cost = is_numeric($delivery_data['delivery_cost']) ?
				html_entity_decode(mprm_currency_filter(mprm_format_amount($delivery_data['delivery_cost']))) :
				$delivery_data['delivery_cost'];
		}
		return $delivery_cost;
	}
}

/**
 * Email tag {delivery_information} callback
 *
 * @param $order_id
 *
 * @return mixed|string|void
 */
function mpde_email_tag_delivery_information($order_id) {
	$order = new Order($order_id);

	if (is_object($order)) {
		$delivery_data = get_post_meta($order->ID, 'mpde_delivery', true);
		if (!empty($delivery_data)) {
			return View::get_instance()->render_html('../admin/email/delivery-information', array('delivery_order_meta' => $delivery_data), false);
		}
	}
}

/**
 * Delivery information
 *
 * @param $delivery_data
 *
 * @return array
 */
function mpde_normalize_order_delivery_meta($delivery_data) {

	$gate = !empty($delivery_data['delivery_gate_code']) ? ', ' . __('gate:', 'mprm-delivery') . $delivery_data['delivery_gate_code'] : '';
	$street = !empty($delivery_data['delivery_street']) ? ', ' . $delivery_data['delivery_street'] : '';
	$apartment = !empty($delivery_data['delivery_apartment']) ? ', ' . $delivery_data['delivery_apartment'] : '';
	$notes = !empty($delivery_data['delivery_notes']) ? ', ' . $delivery_data['delivery_notes'] : '';
	$address_type = !empty($delivery_data['address_type']) ? ', ' . $delivery_data['address_type'] : '';
	$time_mode = !empty($delivery_data['time-mode']) ? $delivery_data['time-mode'] : '';
	$time = !empty($delivery_data['order-hours']) ? date(get_option('time_format'), mktime($delivery_data['order-hours'], $delivery_data['order-minutes'], 0, 0, 0, 0)) : '';

	return array($delivery_data, $gate, $street, $apartment, $notes, $address_type, $time_mode, $time);
}
