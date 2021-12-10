<?php
namespace mprm_menu_cart\classes;

/**
 * Singleton factory
 *
 * @version 1.0.0
 */
class State_Factory {

	protected static $instance;

	/**
	 * @return State_Factory
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get register instance object
	 *
	 * @param string $value
	 *
	 * @return model
	 */
	public function get_model($value = null) {
		$model = false;
		if ('model' == $value) {
			$model = Model::get_instance();
		} else {
			$class = __NAMESPACE__ . "\\models\\" . ucfirst($value);

			if (class_exists($class)) {
				$model = $class::get_instance();
			}
		}
		return $model;
	}

	/**
	 * Get controller instance object
	 *
	 * @param string $value
	 *
	 * @return bool
	 *
	 */
	public function get_controller($value = null) {
		$controller = false;
		$class = __NAMESPACE__ . "\\controllers\\Controller_" . ucfirst($value);

		if (class_exists($class)) {
			$controller = $class::get_instance();
		}
		return $controller;
	}
}
