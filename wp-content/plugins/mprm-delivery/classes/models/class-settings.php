<?php
namespace mprm_delivery\classes\models;

use mprm_delivery\classes\misc\Plugin_updater;
use mprm_delivery\classes\Model;
use mprm_delivery\classes\View;

/**
 * Class delivery Settings
 *
 * @package mprm_delivery\classes\models
 */
class Settings extends Model {
	/**
	 * @return Settings
	 */
	private static $_instance;

	/**
	 * @return Settings
	 */
	public static function get_instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Init settings
	 */
	public function init_action() {
		add_filter('mprm_settings_tabs', array($this, 'add_settings_tabs'), 10, 1);

		add_filter('mprm_settings_extensions', array($this, 'add_settings_data'), 10, 1);
		add_filter('mprm_settings_licenses', array($this, 'add_license_data'), 10, 1);

		add_filter('mprm_settings_sections_extensions', array($this, 'add_settings_sections_extensions'), 10, 1);
		add_filter('mprm_settings_sections_licenses', array($this, 'add_settings_sections_licenses'), 10, 1);

		add_action('mprm_delivery_license', array($this, 'render_view_license'), 10, 1);
		add_action('mprm_delivery_status', array($this, 'render_view_license'), 10, 1);
		add_action('mprm_delivery_action', array($this, 'render_view_license'), 10, 1);

	}

	/**
	 * Save settings
	 */
	public function save_settings() {
		if (isset($_POST['mprm_delivery_nonce']) && wp_verify_nonce($_POST['mprm_delivery_nonce'], 'mprm_delivery_nonce')) {

			if (isset($_POST['mprm_settings']['delivery_license'])) {
				if (empty($_POST['mprm_settings']['delivery_license'])) {
					delete_option('delivery_license');
				} else {
					update_option('delivery_license', trim($_POST['mprm_settings']['delivery_license']));
				}
				$prefix = License::get_instance()->get_prefix();
				wp_cache_delete($prefix, "{$prefix}_restaurant");
			}

			if (!empty($_POST['mprm_settings']['mpde_license_activate']) && !empty($_POST['mprm_settings']['delivery_license'])) {
				License::get_instance()->activate_license();
			} elseif (!empty($_POST['mprm_settings']['mpde_license_deactivate']) && !empty($_POST['mprm_settings']['delivery_license'])) {
				License::get_instance()->deactivate_license();
			}
		}
	}

	/**
	 * Render settings view license
	 *
	 * @param $args
	 *
	 * @return void
	 */
	public function render_view_license($args) {

		switch ($args['id']) {
			case 'delivery_license':
				$license_key = License::get_instance()->get_license_key();
				View::get_instance()->render_html('../admin/settings/license-key', array('data' => $args, 'license_key' => $license_key));
				break;
			case'delivery_status':
				$license_status = License::get_instance()->get_license_status();
				View::get_instance()->render_html('../admin/settings/license-status', array('data' => $args, 'license_status' => $license_status));
				break;
			case'delivery_action':
				View::get_instance()->render_html('../admin/settings/license-action', array('data' => $args));
				break;
			default:
				break;
		}

	}

	/**
	 * Add settings sections license
	 *
	 * @param $sections
	 *
	 * @return mixed
	 */
	public function add_settings_sections_licenses($sections) {
		if (empty($sections)) {
			$sections['main'] = __('Main', 'mprm-delivery');
		}

		return $sections;
	}

	/**
	 * Add settings sections
	 *
	 * @param $sections
	 *
	 * @return mixed
	 */
	public function add_settings_sections_extensions($sections) {
		return $sections;
	}

	/**
	 * Add settings tabs
	 *
	 * @param $tabs
	 *
	 * @return array
	 */
	public function add_settings_tabs($tabs) {
		if (!isset($tabs['licenses']) && is_array($tabs)) {
			$tabs['licenses'] = __('Licenses', 'mp-restaurant-menu');
		}
		return $tabs;
	}

	/**
	 * Add delivery settings
	 *
	 * @param array $settings_data
	 *
	 * @return array
	 */
	public function add_settings_data($settings_data) {
		$delivery_settings = array(
			'delivery_header' => array(
				'id' => 'delivery_header',
				'name' => '<h3>' . __('Delivery Settings', 'mprm-delivery') . '</h3>',
				'desc' => '',
				'type' => 'header',
			),
			'enable_delivery' => array(
				'id' => 'enable_delivery',
				'name' => __('Enable Delivery', 'mprm-delivery'),
				'desc' => __('Allow customers to choose Delivery method on the checkout page.', 'mprm-delivery'),
				'type' => 'checkbox',
			),
			'delivery_cost' => array(
				'id' => 'delivery_cost',
				'name' => __('Price of delivery, set 0 for free', 'mprm-delivery'),
				'type' => 'text',
				'placeholder' => '0'
			),
			'delivery_taxable' => array(
				'id' => 'delivery_taxable',
				'name' => __('Tax status', 'mprm-delivery'),
				'desc' => __('Delivery is taxable', 'mprm-delivery'),
				'type' => 'checkbox',
			),
			'delivery_min_cost_free' => array(
				'id' => 'delivery_min_cost_free',
				'name' => __('Minimum order amount for free delivery (without currency symbols)', 'mprm-delivery'),
				'type' => 'text',
				'placeholder' => '0'
			),
			'enable_collection_delivery' => array(
				'id' => 'enable_collection_delivery',
				'name' => __('Enable Pickup', 'mprm-delivery'),
				'desc' => __('Allow customers to choose Pickup method on the checkout page.', 'mprm-delivery'),
				'type' => 'checkbox',
			),
			'delivery_pickup_address' => array(
				'id' => 'delivery_pickup_address',
				'name' => __('Address to pick up from, working hours and any other information to be shown to customer.', 'mprm-delivery'),
				'type' => 'textarea',
			),
			'enable_time_delivery' => array(
				'id' => 'enable_time_delivery',
				'name' => __('Time of Delivery/Pickup', 'mprm-delivery'),
				'desc' => __('Allow customers to choose time of delivery/pickup.', 'mprm-delivery'),
				'type' => 'checkbox',
			),
			'delivery_min_time_interval' => array(
				'id' => 'delivery_min_time_interval',
				'name' => __('Minimum time interval from the time of purchase (in minutes)', 'mprm-delivery'),
				'type' => 'text',
				'placeholder' => 'minutes'
			)
		);

		if (empty($settings_data)) {
			$settings_data = array('main' => $delivery_settings);
		} else {
			// add settings to end array
			$settings_data['main'] = array_merge_recursive($settings_data['main'], $delivery_settings);
		}

		return $settings_data;
	}

	/**
	 * Add license field
	 *
	 * @param $settings_data
	 *
	 * @return array
	 */
	public function add_license_data($settings_data) {
		$license_settings = array(
			'delivery_license' => array(
				'id' => 'delivery_license',
				'name' => __('Delivery Addon License', 'mprm-delivery'),
				'type' => 'hook',
				'placeholder' => ''
			)
		);

		if (mprm_get_option('delivery_license', false)) {

			$license_settings['delivery_status'] = array(
				'id' => 'delivery_status',
				'name' => __('Status', 'mprm-delivery'),
				'type' => 'hook',
				'placeholder' => ''
			);
			$license_settings['delivery_action'] = array(
				'id' => 'delivery_action',
				'name' => __('Action', 'mprm-delivery'),
				'type' => 'hook',
				'placeholder' => ''
			);

		}

		if (empty($settings_data)) {
			$settings_data = array('main' => $license_settings);
		} else {
			// add settings to end array
			$settings_data['main'] = array_merge_recursive($settings_data['main'], $license_settings);
		}

		return $settings_data;
	}

	/**
	 * Get delivery settings
	 *
	 * @return array
	 */
	public function get_settings_data() {
		$delivery_settings = array();

		$delivery_mode = array(
			'delivery' => (bool)mprm_get_option('enable_delivery', false),
			'collection' => (bool)mprm_get_option('enable_collection_delivery', false)
		);

		$delivery_settings['delivery_mode'] = $delivery_mode;
		$delivery_settings['time_delivery'] = mprm_get_option('enable_time_delivery', false);
		$delivery_settings['min_time_interval'] = mprm_get_option('delivery_min_time_interval', false);
		$delivery_settings['pickup_address'] = mprm_get_option('delivery_pickup_address', '');
		$delivery_settings['delivery_cost'] = mprm_get_option('delivery_cost', '');
		$delivery_settings['delivery_taxable'] = mprm_get_option('delivery_taxable', '1');
		$delivery_settings['delivery_min_cost_free'] = mprm_get_option('delivery_min_cost_free', '');

		return $delivery_settings;
	}

	/**-
	 * Available delivery mode
	 *
	 * @return bool|string
	 */
	public function get_available_mode() {
		$default = false;

		$delivery = (bool)mprm_get_option('enable_delivery', false);
		$collection = (bool)mprm_get_option('enable_collection_delivery', false);

		if ($delivery) {
			$default = 'delivery';
		} elseif ($collection) {
			$default = 'collection';
		}

		return $default;
	}

	public function update_plugin_custom() {
		$isDisableUpdater = apply_filters('mpde_disable_updater', false);

		if (!$isDisableUpdater) {
			$pluginData = \MPRM_delivery::get_instance()->get_plugin_data();
			new  Plugin_updater(
				$pluginData['PluginURI'],
				\MPRM_delivery::get_instance()->get_plugin_file(),
				array(
					'version' => $pluginData['Version'],                  // current version number
					'license' => mprm_get_option('delivery_license', ''), // license key (used get_option above to retrieve from DB)
					'item_id' => MP_DE_PLUGIN_ID,                         // id of this plugin
					'author'  => $pluginData['Author']                    // author of this plugin
				)
			);
		}
	}
}