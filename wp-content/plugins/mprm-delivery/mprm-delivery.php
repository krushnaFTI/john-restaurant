<?php
/**
 * Plugin Name: Restaurant Menu Delivery
 * Plugin URI: https://motopress.com/products/restaurant-menu-delivery/
 * Description: This extension enables online sales with delivery services for your MotoPress Restaurant Menu offering delivery and pickup choices.
 * Version: 1.1.4
 * Author: MotoPress
 * Author URI: https://motopress.com
 * License: GPLv2 or later
 * Text Domain: mprm-delivery
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly
define('MP_DE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MP_DE_TEMPLATES_PATH', MP_DE_PLUGIN_PATH . 'templates/');
define('MP_DE_ASSETS_URL', plugins_url(plugin_basename(__DIR__) . '/assets/'));
define('MP_DE_PLUGIN_NAME', str_replace('-', '_', dirname(plugin_basename(__FILE__))));
define('MP_DE_PLUGIN_ID', 407382);

if ( ! defined( 'MP_DE_DEBUG' ) ) {
	define('MP_DE_DEBUG', FALSE);
}

register_activation_hook(__FILE__, array(MPRM_delivery::get_instance(), 'init'));
add_action('plugins_loaded', array('MPRM_delivery', 'get_instance'));

use mprm_delivery\classes\Core;
use mprm_delivery\classes\misc\MPRM_Loader;
use mprm_delivery\classes\models\License;

/**
 * Class MPRM_delivery
 */
class MPRM_delivery {

	private static $_instance;

	/**
	 * MPRM_delivery constructor.
	 */
	private function __construct() {

		$this->include_all();

		MPRM_Loader::get_instance()->init_loader();

		if (class_exists('\mp_restaurant_menu\classes\Core')) {
			Core::get_instance()->init_plugin(MP_DE_PLUGIN_NAME);
		}
		if ($this->has_license() && class_exists('mprm_delivery\classes\models\License')) {
			$autoLicenseKey = apply_filters('mp_demo_auto_license_key', false);
			if ($autoLicenseKey) {
				License::get_instance()->set_and_activate_license_key($autoLicenseKey);
			}
		}

		if (!defined('MP_DE_TEMPLATE_PATH')) {
			define('MP_DE_TEMPLATE_PATH', $this->template_path());
		}
	}

	/**
	 * Include files
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
	 * Has license
	 * @return bool
	 */
	public function has_license() {
		return true;
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters('mpde_template_path', 'mprm-delivery/');
	}

	/**
	 * @return MPRM_delivery
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
