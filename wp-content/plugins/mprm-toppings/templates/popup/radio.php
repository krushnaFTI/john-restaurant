<?php
$topping_meta = mpto_get_topping_meta($topping->ID, true);
$default_price_id = get_post_meta($topping->ID, '_mprm_default_price_id', true);
?>
	<li class="mprm-topping_<?php echo $topping->ID ?>">
		<div class="mprm-item">
			<span class="mprm-topping-row mprm-topping-title">
				<?php echo wp_get_attachment_image(get_post_thumbnail_id($topping->ID), 'thumbnail', false, array('class' => "mprm-gallery-image")); ?>
				<?php echo get_the_title($topping->ID); ?>
			</span>
		</div>
	</li>
<?php

if (!empty($topping_meta['variable_prices'])) {
	foreach ($topping_meta['variable_prices'] as $key => $price) {

		if ($price['amount'] <= 0) {
			$price['amount'] = '';
		} else {
			$price['amount'] = mprm_currency_filter(mprm_format_amount($price['amount']));
		}

		?>
		<li class="mprm-topping_<?php echo $topping->ID . '-' . $key ?> mprm-option-select">
			<div class="mprm-item">
				<label class="mprm-topping-row mprm-topping-title" for="mprm-price-option-<?php echo $key ?>"><?php echo $price['name'] ?></label>
				<span class="mprm-topping-row mprm-topping-option">
					<input class="mprm-radio-input" type="radio" <?php checked($default_price_id, $key) ?> value="<?php echo $price['name'] ?>" id="mprm-price-option-<?php echo $key ?>" name="price-index-<?php echo $topping->ID ?>" data-price-name="<?php echo $price['name'] ?>" data-price-index="<?php echo $price['index'] ?>" data-id="<?php echo $topping->ID ?>">
				</span>
				<span class="mprm-topping-row mprm-topping-price"><?php echo $price['amount'] ?></span>
			</div>
		</li>
	<?php }
} ?>