<?php
namespace mprm_delivery\classes\controllers;


use mprm_delivery\classes\Controller;

/**
 * Class Controller_Delivery
 *
 * @package mprm_delivery\classes\controllers
 */
class Controller_Delivery extends Controller {

	protected static $instance;

	/**
	 * @return Controller_Delivery
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function action_get_delivery_cost() {
		$request = $_REQUEST;
		$data = array('data' => array());

		if (!isset($request['data']['nonce']) || !wp_verify_nonce($request['data']['nonce'], 'mpde_delivery_checkout_form')) {
			$this->send_json($data);
			exit;
		} else {

			$delivery_type = empty($request['data']['delivery_type']) ? false : esc_html($request['data']['delivery_type']);

			$this->get('delivery')->set_delivery_type($delivery_type);

			$cart_tax = mprm_get_cart_tax();
			$cart_total = mprm_get_cart_total();
			$subtotal = mprm_get_cart_subtotal();

			$delivery_cost = $this->get('delivery')->get_delivery_cost($subtotal, false);

			$data['data'] = array(
				'taxes' => html_entity_decode(mprm_currency_filter(mprm_format_amount($cart_tax)), ENT_COMPAT, 'UTF-8'),
				'total' => html_entity_decode(mprm_currency_filter(mprm_format_amount($cart_total)), ENT_COMPAT, 'UTF-8'),
				'delivery_cost' => html_entity_decode(mprm_currency_filter(mprm_format_amount($delivery_cost)), ENT_COMPAT, 'UTF-8')
			);

			$this->send_json($data);
		}
	}
}