<?php
namespace mprm_menu_cart\classes;
/**
 * View class
 */
class View extends \mp_restaurant_menu\classes\View {

	protected static $instance;

	public function __construct() {
		$this->template_path = MP_MENU_TEMPLATE_PATH;
		$this->templates_path = MP_MENU_TEMPLATES_PATH;
		$this->prefix = 'mp_menu';
	}

	/**
	 * @return View
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
