<?php
namespace mprm_menu_cart\classes;

/**
 * Model class
 */
class Model extends Core {

	protected static $instance;

	/**
	 * @return Model
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}
