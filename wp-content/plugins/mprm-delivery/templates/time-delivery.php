<?php $time_mode = empty($time_mode) ? 'asap' : $time_mode ?>
<div class="row">
    <div class="col-12">
<label><?php _e('When is it for?', 'mprm-delivery'); ?></label>
</div>

<div class="col-6">
<input class="mprm-delivery-time mprm-delivery-time-asap" id="<?php echo 'mprm-' . $name . '-asap' ?>" type="radio" name="<?php echo $name . '[time-mode]' ?>" <?php checked($time_mode, 'asap'); ?> value="asap"/>



<label for="<?php echo 'mprm-' . $name . '-asap' ?>"><?php echo __('ASAP, approx.', 'mprm-delivery') . ' ' . mpde_get_ASAP_time() ?></label>
</div>
<div class="col-6">
<input class="mprm-delivery-time <?php echo 'mprm-' . $name . '-time-later' ?>" id="<?php echo 'mprm-' . $name . '-later' ?>" type="radio" name="<?php echo $name . '[time-mode]' ?>" <?php checked($time_mode, 'later'); ?> value="later"/>



<label for="<?php echo 'mprm-' . $name . '-later' ?>"><?php _e('Later (At a set time)', 'mprm-delivery'); ?></label>

<span class="mprm-content mprm-time-wrapper <?php echo 'mprm-' . $name . '-time-later' ?> ">

	<select class="mprm-time-hours" name="<?php echo $name . '[order-hours]' ?>">

		<?php mpde_get_available_hours(); ?>

	</select>

	<select class="mprm-time-minutes" name="<?php echo $name . '[order-minutes]' ?>">

		<option value="00" <?php selected('00') ?>>00</option>

		<option value="15" <?php selected('15') ?>>15</option>

		<option value="30" <?php selected('30') ?>>30</option>

		<option value="45" <?php selected('45') ?>>45</option>

	</select>

</span>
</div>
</div>