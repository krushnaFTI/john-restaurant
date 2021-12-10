<?php

global $post_ID;

$view_object = mprm_toppings_get_view();
$currency_position = mprm_get_option('currency_position', 'before');

$topping_meta = mpto_get_topping_meta($post_ID);
?>

<div class="mprm-topping-type-wrapper">
	<?php wp_nonce_field('mprm-toppings' . '_nonce', 'mprm-toppings' . '_nonce_box'); ?>

	<input class="" type="radio" name="metaboxes[topping-types]" id="mprm-topping-type-checkbox" <?php checked($topping_meta['topping_type'], 'checkbox') ?> value="checkbox">
	<label for="mprm-topping-type-checkbox"><?php _e('Checkbox', 'mprm-toppings') ?></label>
	<br>
	<input class="" type="radio" name="metaboxes[topping-types]" id="mprm-topping-type-radio" <?php checked($topping_meta['topping_type'], 'radio') ?> value="radio">
	<label for="mprm-topping-type-radio"><?php _e('Radio buttons', 'mprm-toppings') ?></label>
	<br>
	<input class="" type="radio" name="metaboxes[topping-types]" id="mprm-topping-type-stepper" <?php checked($topping_meta['topping_type'], 'stepper') ?> value="stepper">
	<label for="mprm-topping-type-stepper"><?php _e('Numeric stepper', 'mprm-toppings') ?></label>

	<div class="mprm-content mprm-topping-type-checkbox">
		<p><strong><?php _e('Price:', 'mprm-toppings'); ?></strong></p>

		<?php
		$price_args = array(
			'name' => 'metaboxes[checkbox][price]',
			'value' => isset($topping_meta['checkbox']['price']) ? esc_attr(mprm_format_amount($topping_meta['checkbox']['price'])) : '',
			'class' => 'mprm-price-field',
			'id' => 'mpto-checkbox-price'
		);
		?>

		<label for="mpto-checkbox-price">
			<?php if ($currency_position == 'before') : ?>
				<?php echo mprm_currency_filter(''); ?>
				<?php $view_object->render_html('../admin/settings/text', $price_args); ?>
			<?php else : ?>
				<?php $view_object->render_html('../admin/settings/text', $price_args); ?>
				<?php echo mprm_currency_filter(''); ?>
			<?php endif; ?>
		</label>

	</div>
	<div class="mprm-content mprm-topping-type-radio">
		<?php $required = isset($topping_meta['radio']['required']) ? $topping_meta['radio']['required'] : false ?>
		<br>
		<p>
			<label for="mprm_variable_required">
				<input type="checkbox" name="metaboxes[radio][required]" <?php checked($required, '1') ?> id="mprm_variable_required" value="1"/>
				<?php echo apply_filters('mprm_toppings_required_radio', __('Required to be selected', 'mprm-toppings')); ?>
			</label>
		</p>
		<p><b><?php _e('Pricing Options:', 'mprm-toppings'); ?></b></p>
		<br>
		<div id="mprm_variable_price_fields" class="mprm_pricing_fields">
			<input type="hidden" name="metaboxes[radio][_variable_pricing]" value="1">

			<div id="mprm_price_fields" class="mprm_meta_table_wrap">
				<table class="widefat mprm_toppings_repeatable_table">
					<tr>
						<th class="mprm-option-actions"><?php _e('Order', 'mprm-toppings'); ?></th>
						<th class="mprm-option-name"><?php _e('Label', 'mprm-toppings'); ?></th>
						<th class="mprm-option-price"><?php _e('Price', 'mprm-toppings'); ?></th>
						<th class="mprm-option-default"><?php _e('Default', 'mprm-toppings'); ?></th>
						<th class="mprm-option-id"><?php _e('ID', 'mprm-toppings'); ?></th>
						<?php do_action('mprm_price_table_head', $post_ID); ?>
						<th class="mprm-option-actions"><?php _e('Actions', 'mprm-toppings'); ?></th>
					</tr>
					<?php
					if (!empty($topping_meta['radio']['variable_prices']) && is_array($topping_meta['radio']['variable_prices'])) :

						foreach ($topping_meta['radio']['variable_prices'] as $key => $value) :
							$name = isset($value['name']) ? $value['name'] : '';
							$amount = isset($value['amount']) ? $value['amount'] : '';
							$index = isset($value['index']) ? $value['index'] : $key;
							$args = apply_filters('mprm_toppings_price_row_args', compact('name', 'amount'), $value);
							?>
							<tr class="mprm_variable_prices_wrapper mprm_toppings_repeatable_row" data-key="<?php echo esc_attr($key); ?>">
								<?php do_action('mprm_toppings_render_price_row', $key, $args, $post_ID, $index); ?>
							</tr>

						<?php endforeach;
					else : ?>
						<tr class="mprm_variable_prices_wrapper mprm_toppings_repeatable_row" data-key=""><?php do_action('mprm_toppings_render_price_row', 1, array(), $post_ID, 1); ?></tr>
					<?php endif; ?>
					<tr>
						<td class="submit" colspan="4">
							<button class="button-secondary mprm_toppings_add_repeatable"><?php _e('Add New Price', 'mprm-toppings'); ?></button>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div class="mprm-content mprm-topping-type-stepper">
		<p><b><?php _e('Stepper Options:', 'mprm-toppings'); ?></b></p>
		<p>
			<label for="mpto-min-stepper">
				<input type="text" id="mpto-min-stepper" class="medium-text" value="<?php echo $topping_meta['stepper']['min'] ?>" name="metaboxes[stepper][min]" placeholder="0">
				<?php _e('Minimum item quantity', 'mprm-toppings') ?>
			</label>
		</p>
		<p>
			<label for="mpto-max-stepper">
				<input type="text" id="mpto-max-stepper" class="medium-text" value="<?php echo $topping_meta['stepper']['max'] ?>" name="metaboxes[stepper][max]" placeholder="99">
				<?php _e('Maximum item quantity', 'mprm-toppings') ?>
			</label>
		</p>
		<p>
			<label for="mpto-stepper-price">
				<?php

				$price_args = array(
					'name' => 'metaboxes[stepper][price]',
					'value' => isset($topping_meta['stepper']['price']) ? esc_attr(mprm_format_amount($topping_meta['stepper']['price'])) : '',
					'class' => 'mprm-price-field',
					'id' => 'mpto-stepper-price'
				);
				$view_object->render_html('../admin/settings/text', $price_args);
				_e('Price for minimum item quantity', 'mprm-toppings');
				?>
			</label>
		</p>
	</div>
</div>