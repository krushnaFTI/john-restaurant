<?php
namespace mprm_delivery\classes;
/**
 * View class
 */
class View extends \mp_restaurant_menu\classes\View {

	protected static $instance;

	/**
	 * @return View
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		$this->template_path = MP_DE_TEMPLATE_PATH;
		$this->templates_path = MP_DE_TEMPLATES_PATH;
		$this->prefix = 'mpde';
	}
}
