<input type="password" name="mprm_settings[<?php echo $data['id'] ?>]" id="<?php echo $data['id'] ?>" value="<?php echo $license_key ?>" placeholder="" class="regular-text" autocomplete="">
<?php if (!empty($license_key)) { ?>
	<i style="display:block;"><?php echo str_repeat("&#8226;", 20) . substr($license_key, -7); ?></i>
<?php } ?>