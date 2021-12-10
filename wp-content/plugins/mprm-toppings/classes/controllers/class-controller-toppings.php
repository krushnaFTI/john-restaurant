<?php
namespace mprm_toppings\classes\controllers;

use mp_restaurant_menu\classes\View;
use mprm_toppings\classes\Controller;
use mprm_toppings\classes\models\Toppings;

/**
 * Class Controller_toppings
 */
class Controller_Toppings extends Controller {

	protected static $instance;

	/**
	 * @return Controller_toppings
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Add topping to cart
	 */
	public function action_add_to_cart() {
		$request = $_REQUEST;
		$data = array('data' => array(), 'success' => FALSE);
		if (!empty($request['data']['menuID'])) {

			add_filter('mprm_item_quantities_cart', array(Toppings::get_instance(), 'change_item_quantities_cart'), 11, 1);
			$data['success'] = $this->get('toppings')->add_to_menu_item($request['data']);
			remove_filter('mprm_item_quantities_cart', array(Toppings::get_instance(), 'change_item_quantities_cart'), 11);

			$data['data']['cart'] = View::get_instance()->get_template_html('widgets/cart/index');
			$this->send_json($data);
		}
	}

	/**
	 * Update cart topping
	 */
	public function action_update_cart_topping() {
		$request = $_REQUEST;

		if (!empty($request['data']['menuID'])) {

			if (mprm_item_in_cart($request['data']['menuID'], array())) {

				$update = $this->get('toppings')->update_topping_quantity($request['data']);

				if ($update) {
					$this->send_json($this->get('toppings')->get_cart_details());
				}
			}
		}
	}

	/**
	 * Remove topping from menu item in cart
	 */
	
	public function action_remove() {
		$request = $_REQUEST;
		if (!empty($request['topping_ID'])) {
			$this->get('toppings')->remove_from_cart($request['topping_ID'], $request['cart_item']);
			if (wp_get_referer()) {
				wp_safe_redirect(wp_get_referer());
			} else {
				$checkout_uri = mprm_get_checkout_uri();
				wp_safe_redirect($checkout_uri);
			}
		}
	}
}