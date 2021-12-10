<?php
use mprm_toppings\classes\models\Toppings as Toppings;
use mprm_toppings\classes\View as View;

/**
 * @param $post_id
 *
 * @return string
 */
function mprm_toppings_get_price($post_id) {
	return Toppings::get_instance()->get_price($post_id);
}

/**
 * @param $post_id
 *
 * @return bool
 */
function mprm_toppings_single_price_option_mode($post_id) {
	return Toppings::get_instance()->is_single_price_mode($post_id);
}

/**
 * @param $post_id
 *
 * @return bool
 */
function mprm_toppings_has_variable_prices($post_id) {
	return Toppings::get_instance()->has_variable_prices($post_id);
}

/**
 * @param $post_id
 *
 * @return bool|mixed|void
 */
function mprm_toppings_get_variable_prices($post_id) {
	return Toppings::get_instance()->get_variable_prices($post_id);
}

/**
 * @return \mprm_toppings\classes\View
 */
function mprm_toppings_get_view() {
	return View::get_instance();
}

/**
 *
 * @param $post_id
 *
 * @return bool|void
 */
function mprm_toppings_disabled_checkout($post_id) {
	return Toppings::get_instance()->get_disabled_checkout($post_id);
}

/**
 * @param $post_id
 *
 * @return array
 */
function mprm_get_selected_toppings($post_id) {

	$toppings = array();

	if (empty($post_id)) {
		return array();
	} else {

		$topping_IDS = Toppings::get_instance()->get_selected_toppings($post_id);

		if (empty($topping_IDS)) {
			return array();
		}

		foreach ($topping_IDS as $key => $ID) {

			$ID = apply_filters( 'wpml_object_id', $ID, 'post', true );
			if ( function_exists('icl_object_id') ) {
				$ID = icl_object_id( $ID, 'post', true );
			}

			$topping = get_post($ID);

			if (empty($topping)) {
				continue;
			}

			if ($topping->post_status == 'publish' && (mpto_get_post_type('topping') === $topping->post_type)) {
				$toppings[$key] = $topping;
			}
		}

		return $toppings;
	}
}

/**
 * @param $post_id
 * @param $args
 *
 * @return mixed
 */
function get_related_toppings_html($post_id, $args) {
	return mprm_toppings_get_view()->get_template('related-toppings', array('args' => $args, 'post_id' => $post_id));
}

/**
 * @param $topping_post
 * @param $cart_menu_item_index
 *
 * @return string/bool
 */
function mprm_toppings_remove_url($topping_post, $cart_menu_item_index) {
	return Toppings::get_instance()->get_remove_url($topping_post, $cart_menu_item_index);
}

/**
 * Get topping meta
 *
 * @param $post
 * @param bool $current
 *
 * @return array/bool
 */
function mpto_get_topping_meta($post, $current = false) {

	$data = array();
	$topping_type = mprm_get_post_meta($post, 'topping-types', true, 'checkbox');
	$data['topping_type'] = $topping_type;

	$data['checkbox'] = mprm_get_post_meta($post, 'checkbox', true, array('price' => ''));
	$data['radio'] = mprm_get_post_meta($post, 'radio', true, array('required' => 0, 'variable_prices' => false));
	$data['stepper'] = mprm_get_post_meta($post, 'stepper', true, array('min' => '', 'max' => '', 'price' => ''));

	if ($current) {
		return $data[$topping_type];
	} else {
		return $data;
	}
}

/**
 * @param $post_id
 *
 * @return string
 */
function mpto_get_price($post_id) {
	return Toppings::get_instance()->get_price($post_id);
}

/**
 * Get topping post type
 *
 * @param $type
 *
 * @return string
 */
function mpto_get_post_type($type = 'topping') {
	return Toppings::get_instance()->get_post_type($type);
}

/**
 * Variable prices
 *
 * @param $id
 *
 * @return bool
 */
function mpto_has_variable_prices($id) {
	return Toppings::get_instance()->has_variable_prices($id);
}

/**
 * GEt price option name
 *
 * @param $item
 * @param $price_id
 * @param $order
 *
 * @return mixed|void
 */
function mpto_get_price_option_name($item_id, $price_id, $order) {
	return Toppings::get_instance()->get_price_option_name($item_id, $price_id, $order);
}
