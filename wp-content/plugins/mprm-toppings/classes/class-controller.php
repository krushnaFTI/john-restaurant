<?php
namespace mprm_toppings\classes;

/**
 * Class Controller_toppings
 */
class Controller extends Core {

	protected static $instance;

	/**
	 * @return Controller
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Send json data
	 *
	 * @param array /mixed $data
	 */
	public function send_json($data) {
		if (is_array($data) && isset($data['success']) && !$data['success']) {
			wp_send_json_error($data);
		} else {
			wp_send_json_success($data['data']);
		}
	}
}