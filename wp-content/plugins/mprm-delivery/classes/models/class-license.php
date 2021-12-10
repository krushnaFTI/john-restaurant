<?php
/**
 * Toolbar
 *
 * This class handles outputting our front-end theme switcher, toolbar
 */
namespace mprm_delivery\classes\models;

use mprm_delivery\classes\Model;

/**
 * Class License
 *
 * @version 1.0.1
 *
 * @package mprm_delivery\classes\models
 */
class License extends Model {

	protected static $instance;

	protected $pluginData;

	protected $prefix;

	public function __construct() {
		$this->prefix = 'delivery';
		$name = '\MPRM_' . $this->prefix;
		$this->pluginData = $name::get_instance()->get_plugin_data();
	}

	/**
	 * @return License
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get prefix
	 * @return string
	 */
	public function get_prefix() {
		return $this->prefix;
	}

	/**
	 * License install
	 */
	public function license_install() {
		$autoLicenseKey = apply_filters("mprm_{$this->prefix}_auto_license_key", false);

		if ($autoLicenseKey) {
			$this->set_and_activate_license_key($autoLicenseKey);
		}
	}

	/**
	 * Set and activate
	 *
	 * @param $licenseKey
	 */
	public function set_and_activate_license_key($licenseKey) {
		$this->set_license_key($licenseKey);
		$this->activate_license();
	}

	/**
	 * Set license key
	 *
	 * @param $licenseKey
	 */
	private function set_license_key($licenseKey) {
		$oldLicenseKey = $this->get_license_key();

		if ($oldLicenseKey && $oldLicenseKey !== $licenseKey) {
			delete_option("{$this->prefix}_status"); // new license has been entered, so must reactivate
		}

	}

	/**
	 * Get license key
	 *
	 * @return mixed|void
	 */
	public function get_license_key() {
		$license = mprm_get_option("{$this->prefix}_license", '');
		return $license;
	}

	/**
	 * Activate license
	 *
	 * @return array|bool|mixed|object
	 */
	public function activate_license() {
		$licenseKey = $this->get_license_key();

		// data to send in our API request
		$apiParams = array(
			'edd_action' => 'activate_license',
			'license'    => $licenseKey,
			'item_id'    => MP_DE_PLUGIN_ID,
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_get(add_query_arg($apiParams, $this->pluginData['PluginURI']), array('timeout' => 15, 'sslverify' => false));
		// make sure the response came back okay

		if (is_wp_error($response)) {
			return false;
		}

		// decode the license data
		$licenseData = json_decode(wp_remote_retrieve_body($response));
		// $licenseData->license will be either "active" or "inactive"
		update_option("{$this->prefix}_status", ucfirst($licenseData->license) . __('until', 'mprm-delivery') . ' ' . $licenseData->expires);

		return $licenseData;
	}

	/**
	 * Get license status
	 *
	 * @return bool
	 */
	public function get_license_status() {
		global $licenseData;

		$licenseData = $this->check_license($this->get_license_key());

		$message = '';

		switch ($licenseData->license) {
			case 'inactive':
				$message = __('Inactive', 'mprm-delivery');
				break;
			case 'site_inactive':
				$message = __('Inactive', 'mprm-delivery');
				break;
			case 'disabled' :
				$message = __('Disabled', 'mprm-delivery');
				break;
			case 'valid':
				if ($licenseData->expires !== 'lifetime') {
					$date = ($licenseData->expires) ? new \DateTime($licenseData->expires) : false;
					$expires = ($date) ? ' ' . $date->format('d.m.Y') : '';
					$message = __('Valid until', 'mprm-delivery') . $expires;
				} else {
					$message = __('Valid (Lifetime)', 'mprm-delivery');
				}
				break;

			case 'expired' :
				$message = __('Expired', 'mprm-delivery');
				break;
			case 'invalid' :
				$message = __('Invalid', 'mprm-delivery');
				break;
			case 'invalid_item_id' :
				$message = __('Product ID is not valid', 'mprm-delivery');
				break;
		}

		return $message;
	}

	/**
	 * Check License
	 *
	 * @param $license
	 *
	 * @return array|bool|mixed|object
	 */
	private function check_license($license) {

		$apiParams = array(
			'edd_action' => 'check_license',
			'license'    => $license,
			'item_id'    => MP_DE_PLUGIN_ID,
			'url'        => home_url(),
		);

		$pluginUri = $this->pluginData['PluginURI'];

		if (!$licenseData = wp_cache_get($this->prefix, "{$this->prefix}_restaurant")) {
			// Call the custom API.
			$response = wp_remote_get(add_query_arg($apiParams, $pluginUri), array('timeout' => 15, 'sslverify' => false));

			if (is_wp_error($response)) {
				return false;
			}
			$licenseData = json_decode(wp_remote_retrieve_body($response));
			wp_cache_add($this->prefix, $licenseData, $this->prefix . '_restaurant', 3600);
		}

		return $licenseData;
	}

	/**
	 * Deactivate license
	 *
	 * @return array|bool|mixed|object
	 */
	public function deactivate_license() {
		$licenseKey = $this->get_license_key();

		// data to send in our API request
		$apiParams = array(
			'edd_action' => 'deactivate_license',
			'license'    => $licenseKey,
			'item_id'    => MP_DE_PLUGIN_ID,
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_get(add_query_arg($apiParams, $this->pluginData['PluginURI']), array('timeout' => 15, 'sslverify' => false));

		// make sure the response came back okay
		if (is_wp_error($response)) {
			return false;
		}

		// decode the license data
		$licenseData = json_decode(wp_remote_retrieve_body($response));

		// $license_data->license will be either "deactivated" or "failed"

		if ($licenseData->license == 'deactivated') {
			delete_option("{$this->prefix}_status");
		}

		return $licenseData;
	}
}