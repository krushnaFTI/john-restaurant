<?php
/**
 * Plugin Name: Restaurant Menu Toppings
 * Plugin URI: https://motopress.com
 * Description: This extension allows you to add optional toppings available for additional purchase along with each menu item of your MotoPress Restaurant menu.
 * Version: 1.1.3
 * Author: MotoPress
 * Author URI: https://motopress.com
 * License: GPLv2 or later
 * Text Domain: mprm-toppings
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
	exit;
}
// Exit if accessed directly


define('MP_TO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MP_TO_ASSETS_URL', plugins_url(plugin_basename(__DIR__) . '/assets/'));
define('MP_TO_TEMPLATES_PATH', MP_TO_PLUGIN_PATH . 'templates/');
define('MP_TO_PLUGIN_NAME', str_replace('-', '_', dirname(plugin_basename(__FILE__))));
define('MP_TO_PLUGIN_ID', 407377);
define('MP_TO_DEBUG', FALSE);

register_activation_hook(__FILE__, array(MPRM_toppings::get_instance(), 'init'));
add_action('plugins_loaded', array('MPRM_toppings', 'get_instance'));

use mprm_toppings\classes\Core;

/**
 * Class MPRM_toppings
 */
class MPRM_toppings {

	private static $_instance;

	/**
	 * MPRM_toppings constructor.
	 */
	private function __construct() {

		$this->include_all();
		MPRM_Loader::get_instance()->init_loader();

		if (class_exists('\mp_restaurant_menu\classes\Core')) {
			Core::get_instance()->init_plugin(MP_TO_PLUGIN_NAME);
		}
		if (!defined('MP_TO_TEMPLATE_PATH')) {
			define('MP_TO_TEMPLATE_PATH', $this->template_path());
		}
	}

	/**
	 * Include all
	 */
	public function include_all() {
		require_once $this->get_plugin_path() . 'classes/misc/class-loader.php';
	}

	/**
	 * Get plugin path
	 */
	public function get_plugin_path() {
		return plugin_dir_path(__FILE__);
	}

	/**
	 * Get the template path.
	 * @return string
	 */
	public function template_path() {
		return apply_filters('mpto_template_path', 'mprm-toppings/');
	}

	/**
	 * @return MPRM_toppings
	 */
	public static function get_instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Plugin data
	 *
	 * @param bool $key
	 *
	 * @return array
	 */
	public function get_plugin_data($key = false) {
		$plugin_data = get_plugin_data($this->get_plugin_file());
		if ($key) {
			return $plugin_data[$key];
		} else {
			return $plugin_data;
		}
	}

	/**
	 * Get plugin file
	 *
	 * @return string
	 */
	public function get_plugin_file() {
		global $wp_version, $network_plugin;
		if (version_compare($wp_version, '3.9', '<') && isset($network_plugin)) {
			$pluginFile = $network_plugin;
		} else {
			$pluginFile = __FILE__;
		}
		return $pluginFile;
	}

	public function init() {

	}

	private function __clone() {
	}
}
