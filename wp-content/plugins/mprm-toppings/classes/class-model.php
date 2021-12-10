<?php
namespace mprm_toppings\classes;

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
