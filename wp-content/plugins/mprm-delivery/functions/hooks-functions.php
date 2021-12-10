<?php use mprm_delivery\classes\models\Settings;
use mprm_delivery\classes\View;

/**
 *  Render Delivery form
 */
function mprm_render_delivery_form() {
	$settings = Settings::get_instance()->get_settings_data();
	View::get_instance()->get_template('delivery-form', $settings);
}