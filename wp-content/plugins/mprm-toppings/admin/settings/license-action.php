<?php
global $licenseData;

if (isset($licenseData->license)) {

	if ($licenseData->license === 'inactive' || $licenseData->license === 'site_inactive') {
		wp_nonce_field('mprm_topping_nonce', 'mprm_topping_nonce'); ?>
		<input type="submit" class="button-secondary" name="mprm_settings[mpto_license_activate]" value="<?php _e('Activate License') ?>"/>
		<?php
	} elseif ($licenseData->license === 'valid') {
		wp_nonce_field('mprm_topping_nonce', 'mprm_topping_nonce'); ?>
		<input type="submit" class="button-secondary" name="mprm_settings[mpto_license_deactivate]" value="<?php _e('Deactivate License') ?>"/>
		<?php
	} elseif ($licenseData->license === 'expired') { ?>
		<a href="<?php echo \MPRM_toppings::get_instance()->get_plugin_data('PluginURI'); ?>" class="button-secondary" target="_blank"><?php _e('Renew License') ?></a>
		<?php
	}

}
?>