<?php
namespace mprm_toppings\classes\models;

use mprm_toppings\classes\misc\Plugin_updater;
use mprm_toppings\classes\Model;
use mprm_toppings\classes\View;

/**
 * Class Settings
 * @package mprm_toppings\classes\models
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
		add_filter('mprm_settings_extensions', array($this, 'add_settings_data'));
		add_filter('mprm_settings_sections_extensions', array($this, 'add_settings_sections_extensions'), 10, 1);
		add_filter('mprm_settings_sections_licenses', array($this, 'add_settings_sections_licenses'), 10, 1);
		add_filter('mprm_settings_licenses', array($this, 'add_license_data'), 10, 1);
		add_action('mprm_toppings_license', array($this, 'render_view_license'), 10, 1);
		add_action('mprm_toppings_status', array($this, 'render_view_license'), 10, 1);
		add_action('mprm_toppings_action', array($this, 'render_view_license'), 10, 1);
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
	 * Save settings
	 */
	public function save_settings() {
		if (isset($_POST['mprm_topping_nonce']) && wp_verify_nonce($_POST['mprm_topping_nonce'], 'mprm_topping_nonce')) {

			if (isset($_POST['mprm_settings']['toppings_license'])) {
				if (empty($_POST['mprm_settings']['toppings_license'])) {
					delete_option('toppings_license');

				} else {
					update_option('toppings_license', trim($_POST['mprm_settings']['toppings_license']));
				}
				$prefix = License::get_instance()->get_prefix();
				wp_cache_delete($prefix, "{$prefix}_restaurant");
			}

			if (!empty($_POST['mprm_settings']['mpto_license_activate']) && !empty($_POST['mprm_settings']['toppings_license'])) {
				License::get_instance()->activate_license();
			} elseif (!empty($_POST['mprm_settings']['mpto_license_deactivate']) && !empty($_POST['mprm_settings']['toppings_license'])) {
				License::get_instance()->deactivate_license();
			}
		}
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
			'toppings_license' => array(
				'id' => 'toppings_license',
				'name' => __('Toppings Addon License', 'mprm-toppings'),
				'type' => 'hook',
				'placeholder' => ''
			)
		);

		if (mprm_get_option('toppings_license', false)) {

			$license_settings['toppings_status'] = array(
				'id' => 'toppings_status',
				'name' => __('Status', 'mprm-toppings'),
				'type' => 'hook',
				'placeholder' => ''
			);
			$license_settings['toppings_action'] = array(
				'id' => 'toppings_action',
				'name' => __('Action', 'mprm-toppings'),
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
	 * Render settings view license
	 *
	 * @param $args
	 *
	 * @return void
	 */
	public function render_view_license($args) {

		switch ($args['id']) {
			case 'toppings_license':
				$license_key = License::get_instance()->get_license_key();
				View::get_instance()->render_html('../admin/settings/license-key', array('data' => $args, 'license_key' => $license_key));
				break;
			case'toppings_status':
				$license_status = License::get_instance()->get_license_status();
				View::get_instance()->render_html('../admin/settings/license-status', array('data' => $args, 'license_status' => $license_status));
				break;
			case'toppings_action':
				View::get_instance()->render_html('../admin/settings/license-action', array('data' => $args));
				break;
			default:
				break;
		}

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
	 *  Add toppings settings
	 *
	 * @param array $settings_data
	 *
	 * @return array
	 */
	public function add_settings_data($settings_data) {
		$toppings_settings = array(
			'toppings_header' => array(
				'id' => 'toppings_header',
				'name' => '<h3>' . __('Toppings Settings', 'mprm-toppings') . '</h3>',
				'desc' => '',
				'type' => 'header',
			),
			'toppings_title' => array(
				'id' => 'toppings_title',
				'name' => __('"Add a topping" button label', 'mprm-toppings'),
				'type' => 'text',
				'size' => 'regular',
			),
			'toppings_popup_title' => array(
				'id' => 'toppings_popup_title',
				'name' => __('"You can order optional toppings" text', 'mprm-toppings'),
				'type' => 'text',
			)
		);

		if (empty($settings_data)) {
			$settings_data = array('main' => $toppings_settings);
		} else {
			// add settings to end array
			$settings_data['main'] = array_merge_recursive($settings_data['main'], $toppings_settings);
		}
		return $settings_data;
	}

	/**
	 * Update plugin custom url
	 */
	public function update_plugin_custom() {
		$isDisableUpdater = apply_filters('mpde_disable_updater', false);

		if (!$isDisableUpdater) {
			$pluginData = \MPRM_toppings::get_instance()->get_plugin_data();
			new  Plugin_updater(
				$pluginData['PluginURI'],
				\MPRM_toppings::get_instance()->get_plugin_file(),
				array(
					'version' => $pluginData['Version'],                  // current version number
					'license' => mprm_get_option('toppings_license', ''), // license key (used get_option above to retrieve from DB)
					'item_id' => MP_TO_PLUGIN_ID,                         // id of this plugin
					'author'  => $pluginData['Author'],                   // author of this plugin
				)
			);
		}
	}
}