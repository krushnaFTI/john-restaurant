<div class="delivery-tabs" id="mprm-delivery-tabs">

	<input class="mprm-type-delivery" type="radio" id="mpde-tab-delivery" name="delivery-mode" value="delivery" <?php checked($order_data['delivery_mode'], 'delivery') ?>>

	<label class="mprm-tab-label" for="mpde-tab-delivery"><?php _e('Delivered', 'mprm-delivery'); ?></label>

	<br>

	<input class="mprm-type-collection" type="radio" id="mpde-tab-collection" value="collection" name="delivery-mode" <?php checked($order_data['delivery_mode'], 'collection') ?>>

	<label class="mprm-tab-label" for="mpde-tab-collection"><?php _e('For pickup', 'mprm-delivery'); ?></label>



	<div class="mprm-content mprm-type-delivery mprm-delivery-mode-wrapper">

		<p>

			<select class="widefat" data-placeholder="<?php _e('Address Type', 'mprm-delivery') ?>" name="delivery[address_type]">

				<option value="" disabled <?php selected($order_data['address_type'], '') ?> class="mprm-hidden"><?php _e('Address Type', 'mprm-delivery') ?></option>

				<option value="school" <?php selected($order_data['address_type'], 'school') ?> > <?php _e('School', 'mprm-delivery') ?></option>

				<option value="business" <?php selected($order_data['address_type'], 'business') ?> ><?php _e('Business', 'mprm-delivery') ?></option>

				<option value="apartment" <?php selected($order_data['address_type'], 'apartment') ?> ><?php _e('Apartment', 'mprm-delivery') ?></option>

				<option value="house" <?php selected($order_data['address_type'], 'house') ?> ><?php _e('House', 'mprm-delivery') ?></option>

				<option value="other" <?php selected($order_data['address_type'], 'other') ?> ><?php _e('Other', 'mprm-delivery') ?></option>

			</select>

		</p>

		<p><input class="widefat" type="text" name="delivery[delivery_street]" value="<?php echo $order_data['delivery_street'] ?>" placeholder=" <?php _e('Street', 'mprm-delivery') ?>"></p>

		<p><input class="widefat" type="text" name="delivery[delivery_apartment]" value="<?php echo $order_data['delivery_apartment'] ?>" placeholder=" <?php _e('Apt/Ste/Rm', 'mprm-delivery') ?>"></p>

		<p><input class="widefat" type="text" name="delivery[delivery_gate_code]" value="<?php echo $order_data['delivery_gate_code'] ?>" placeholder=" <?php _e('Gate Code', 'mprm-delivery') ?>"></p>



		<p>

			<textarea class="widefat" name="delivery[delivery_notes]" placeholder=" <?php _e('Notes', 'mprm-delivery') ?>"><?php echo !empty($shipping_address) ? (empty($order_data['delivery_notes']) ? $shipping_address : '') : $order_data['delivery_notes'] ?></textarea>

		</p>



		<p><label><?php _e('When is it for?', 'mprm-delivery'); ?></label></p>



		<input class="mprm-delivery-time mprm-delivery-time-asap" id="mprm-delivery-asap" type="radio" name="delivery[time-mode]" <?php checked($order_data['time-mode'], 'asap'); ?> value="asap"/>

		<label for="mprm-delivery-asap"><?php echo __('ASAP, approx.', 'mprm-delivery') . ' ' . mpde_get_ASAP_time('order') ?></label>

		<br>

		<input class="mprm-delivery-time mprm-delivery-time-later" id="mprm-delivery-later" type="radio" name="delivery[time-mode]" <?php checked($order_data['time-mode'], 'later'); ?> value="later"/>

		<label for="mprm-delivery-later"><?php _e('Later (At a set time)', 'mprm-delivery'); ?></label>



		<div class="mprm-content mprm-time-wrapper mprm-delivery-time-later">

			<br>

			<select class="mprm-time-hours" name="delivery[order-hours]">

				<?php mpde_get_available_hours($order_data['order-hours']); ?>

			</select>

			<select class="mprm-time-minutes" name="delivery[order-minutes]">

				<option value="00" <?php selected('00', $order_data['order-minutes']) ?>>00</option>

				<option value="15" <?php selected('15', $order_data['order-minutes']) ?>>15</option>

				<option value="30" <?php selected('30', $order_data['order-minutes']) ?>>30</option>

				<option value="45" <?php selected('45', $order_data['order-minutes']) ?>>45</option>

			</select>

		</div>



	</div>

	<div class="mprm-content mprm-type-collection mprm-delivery-mode-wrapper">



		<?php if (mprm_get_option('enable_time_delivery', false)) { ?>



			<p><label><?php _e('When is it for?', 'mprm-delivery'); ?></label></p>

			<input class="mprm-delivery-time mprm-collection-time-asap" id="mprm-collection-asap" type="radio" name="collection[time-mode]" <?php checked($order_data['time-mode'], 'asap'); ?> value="asap"/>

			<label for="mprm-collection-asap"><?php echo __('ASAP, approx.', 'mprm-delivery') . ' ' . mpde_get_ASAP_time('order') ?></label>

			<br>



			<input class="mprm-delivery-time mprm-collection-time-later" id="mprm-collection-alter" type="radio" name="collection[time-mode]" <?php checked($order_data['time-mode'], 'later'); ?> value="later"/>

			<label for="mprm-collection-alter"><?php _e('Later (At a set time)', 'mprm-delivery'); ?></label>



			<div class="mprm-content mprm-time-wrapper mprm-collection-time-later">

				<br>

				<select class="mprm-time-hours" name="collection[order-hours]">

					<?php mpde_get_available_hours($order_data['order-hours']); ?>

				</select>

				<select class="mprm-time-minutes" name="collection[order-minutes]">

					<option value="00" <?php selected('00', $order_data['order-minutes']) ?>>00</option>

					<option value="15" <?php selected('15', $order_data['order-minutes']) ?>>15</option>

					<option value="30" <?php selected('30', $order_data['order-minutes']) ?>>30</option>

					<option value="45" <?php selected('45', $order_data['order-minutes']) ?>>45</option>

				</select>

			</div>

		<?php } ?>

	</div>

</div>