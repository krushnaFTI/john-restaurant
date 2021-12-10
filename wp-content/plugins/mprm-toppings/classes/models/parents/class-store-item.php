<?php
namespace mprm_toppings\classes\models\parents;

use mprm_toppings\classes\View;

/**
 * Class Store_item
 */
class Store_item extends \mp_restaurant_menu\classes\models\parents\Store_item {

	/** Return instance View object
	 * @return mixed
	 */
	public function get_view() {
		return View::get_instance();
	}
}