<?php
namespace mprm_toppings\classes;

use mprm_toppings\classes\models\Settings;
use mprm_toppings\classes\models\Toppings;

/**
 * Class Core
 * @package mprm_toppings\classes
 */
class Core extends \mp_restaurant_menu\classes\Core {
	protected static $instance;
	/**
	 * Current state
	 */
	private $state;

	private $version;

	/**
	 * Core constructor.
	 */

	public function __construct() {
		$this->state = new State_Factory(dirname(__NAMESPACE__));
		$this->init_plugin_version();
	}

	/**
	 *  Get plugin version
	 */
	public function init_plugin_version() {
		$filePath = MP_TO_PLUGIN_PATH . 'mprm-toppings.php';
		if (!$this->version) {
			$pluginObject = get_plugin_data($filePath);
			$this->version = $pluginObject['Version'];
		}
	}

	/**
	 * @param $name
	 */

	public function init_plugin($name) {
		Core::include_all(MP_TO_PLUGIN_PATH . 'functions/');

		Toppings::get_instance()->init_action();
		Core::get_instance()->hooks();
		MPRM_post_types::get_instance()->init();
		$this->update_global_menu_item_settings();
	}

	/**
	 * @return Core
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hooks
	 */
	public function hooks() {
		add_action('init', array($this, 'wp_ajax_route_url'), 0);
		add_action('init', array(Settings::get_instance(), 'init_action'), 5);
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		add_action('admin_menu', array(MPRM_post_types::get_instance(), 'add_custom_menu_order'), 90);
		add_action('wp_enqueue_scripts', array($this, 'add_theme_style'));
		add_filter('mprm-script-mp-restaurant-menu', array($this, 'add_theme_script'), 10, 1);

		add_action('admin_init', array(Settings::get_instance(), 'save_settings'));
		add_action('admin_init', array(Settings::get_instance(), 'update_plugin_custom'),9);
		add_action('admin_init', array($this, 'admin_init') );
	}

	public function admin_init() {
		add_filter("manage_mprm_toppings_posts_columns", array( $this->get_view(), 'init_menu_columns' ), 10);
		add_action("manage_mprm_toppings_posts_custom_column", array( $this->get_view(), 'show_menu_columns' ), 10, 2);
	}

	/**
	 * Update menu item settings
	 */
	public function update_global_menu_item_settings() {
		$settings = get_option('mprm_settings');

		$update = false;

		$default_settings = array(
			'toppings_title' => __('Add toppings', 'mprm-toppings'),
			'toppings_popup_title' => __('You can order optional toppings', 'mprm-toppings')
		);

		// first init setup settings
		if (empty($settings)) {
			update_option('mprm_settings_extensions', $default_settings);
		} else {
			$array_default_keys = array_keys($default_settings);

			foreach ($array_default_keys as $key) {
				if (!array_key_exists($key, $settings)) {
					$update = true;
				}
			}

			if ($update) {
				update_option('mprm_settings', $settings);
			}
		}


	}

	/**
	 * Ajax route URL
	 */
	public function wp_ajax_route_url() {
		$controller = isset($_REQUEST['mpto_controller']) ? $_REQUEST['mpto_controller'] : null;
		$action = isset($_REQUEST['mprm_action']) ? $_REQUEST['mprm_action'] : null;
		if (!empty($action) && !empty($controller)) {
			// call controller
			$controller = $this->get_controller($controller);
			if (is_object($controller)) {
				$action = 'action_' . $action;
				$controller->$action();
			}
			die();
		}
	}

	/**
	 * @param null $type
	 *
	 * @return model
	 */
	public function get_controller($type = null) {
		return $this->get_state()->get_controller($type);
	}

	/**
	 * @return bool|State_Factory
	 */
	public function get_state() {
		if ($this->state) {
			return $this->state;
		} else {
			return false;
		}
	}

	/**
	 * Get view
	 *
	 * @return object
	 */
	public function get_view() {
		return View::get_instance();
	}

	/**
	 * Get model instance
	 *
	 * @param bool|false $type
	 *
	 * @return bool|mixed
	 */
	public function get($type = false) {
		$state = false;
		if ($type) {
			$state = $this->get_model($type);
		}
		return $state;
	}

	/**
	 * Check and return current state
	 *
	 * @param string $type
	 *
	 * @return boolean
	 */
	public function get_model($type = null) {
		return $this->get_state()->get_model($type);
	}

	/**
	 * add theme script only load {mp-restaurant-menu.js}
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public function add_theme_script($name) {
		$prefix = $this->get_prefix();

		if ($name === 'mp-restaurant-menu') {
			$version = $this->get_version();
			wp_enqueue_script('mprm-topping', MP_TO_ASSETS_URL . "js/mprm-toppings{$prefix}.js", array('mp-restaurant-menu'), $version, true);
		}
		return $name;
	}

	/**
	 * @return string
	 */
	public function get_prefix() {
		$prefix = !MP_TO_DEBUG ? '.min' : '';
		return $prefix;
	}

	public function get_version() {
		return $this->version;
	}

	public function add_theme_style() {
		wp_enqueue_style('mprm-topping', MP_TO_ASSETS_URL . "css/style-toppings.css", array(), $this->get_version());
	}

	/**
	 *
	 */
	public function admin_enqueue_scripts() {
		global $current_screen;
		$this->current_screen($current_screen);
	}

	/**
	 * Current screen
	 *
	 * @param \WP_Screen $current_screen
	 */
	public function current_screen(\WP_Screen $current_screen) {
		$prefix = $this->get_prefix();
		if (is_admin()) {
			wp_enqueue_style('mprm-topping', MP_TO_ASSETS_URL . "css/style-toppings.css", array(), $this->get_version());
		}
		if (!empty($current_screen)) {

			switch ($current_screen->id) {
				case 'mprm_toppings':
					wp_enqueue_script('mprm-topping', MP_TO_ASSETS_URL . "js/mprm-toppings{$prefix}.js", array('mp-restaurant-menu'), $this->get_version(), true);
					wp_enqueue_style('mprm-topping', MP_TO_ASSETS_URL . "css/style-toppings.css", array(), $this->get_version());
					wp_enqueue_media();
					break;
				case 'mp_menu_item':
					wp_enqueue_script('mprm-topping', MP_TO_ASSETS_URL . "js/mprm-toppings{$prefix}.js", array(), $this->get_version(), true);
					wp_enqueue_script('mprm-select2', MP_TO_ASSETS_URL . "js/lib/select2.full{$prefix}.js", array(), '4.0.3', true);

					wp_enqueue_style('mprm-topping', MP_TO_ASSETS_URL . "css/style-toppings.css", array(), $this->get_version());
					wp_enqueue_style('mprm-select2', MP_TO_ASSETS_URL . "css/lib/select2{$prefix}.css", array(), '4.0.3');
					break;
				default:
					break;
			}
		}
	}
}