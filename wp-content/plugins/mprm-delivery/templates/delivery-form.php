<?php

if (!$delivery_mode['delivery'] && !$delivery_mode['collection']) {

	return '';

}

?>

<fieldset id="mprm_delivery_mode_select">

	<?php wp_nonce_field('mpde_delivery_checkout_form', 'delivery_checkout_form_nonce'); ?>



	<span class="mprm-payment-details-label"><legend><?php _e('I would like my order', 'mprm-delivery') ?></legend></span>

	<div id="mprm_delivery_mode_select-wrapper">

		<?php mprm_get_delivery_part(array('template' => 'deliveries-mode', 'data' => $delivery_mode)) ?>



		<div class="mprm-content  mprm-type-delivery mprm-delivery-mode-wrapper">

			<div class="row">
			<div class="col-6">

				<label class="mprm-required"><?php _e('Address Type', 'mprm-delivery') ?></label>

				

				<select class="mprm-required" data-required="" required name="delivery[address_type]">

					<option value=""></option>

					<option value="apartment"><?php _e('Apartment', 'mprm-delivery') ?></option>

					<option value="house"><?php _e('House', 'mprm-delivery') ?></option>

					<option value="business"><?php _e('Business', 'mprm-delivery') ?></option>

					<option value="school"><?php _e('School', 'mprm-delivery') ?></option>

					<option value="other"><?php _e('Other', 'mprm-delivery') ?></option>

				</select>

			</div>



			<div class="col-6">

			<label class="mprm-required"><?php _e('Address', 'mprm-delivery') ?></label>
				
				<input type="text" class="mprm-required delivery_street_field" data-required="" required name="delivery[delivery_street]" placeholder=" <?php _e('Address like below', 'mprm-delivery') ?>">
				<lable class="demo_address">20 McAllister St</label>

			</div>

			<div class="col-6">

				<label class="mprm-required"><?php _e('Apt/Ste/Rm', 'mprm-delivery') ?></label>

				

				<input type="text" class="mprm-required" data-required="" required name="delivery[delivery_apartment]" placeholder=" <?php _e('Apt/Ste/Rm', 'mprm-delivery') ?>">

			</div>

			<div class="col-6">

				<label><?php _e('Gate Code', 'mprm-delivery') ?></label>

				

				<input type="text" name="delivery[delivery_gate_code]" placeholder=" <?php _e('Gate Code', 'mprm-delivery') ?>">

			</div>

			<div class="col-6">

				<label><?php _e('Notes', 'mprm-delivery') ?></label>

				

				<input type="text" name="delivery[delivery_notes]" placeholder=" <?php _e('Notes', 'mprm-delivery') ?>">

			</div>


<div class="col-12">
			<?php if ($time_delivery) {

				mprm_get_delivery_part(array('template' => 'time-delivery', 'data' => array('name' => 'delivery')));

			} ?>
	</div>
</div>

		</div>

		<div class="mprm-content mprm-type-collection mprm-delivery-mode-wrapper">

			<p><?php echo $pickup_address ?></p>

			<?php if ($time_delivery) {

				mprm_get_delivery_part(array('template' => 'time-delivery', 'data' => array('name' => 'collection')));

			} ?>

</div>
	</div>

</fieldset>





