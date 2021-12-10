<?php
$price = mpto_get_price($topping->ID);

if ($price <= 0) {
	$price = '';
} else {
	$price = mprm_currency_filter(mprm_format_amount($price));
} ?>


<li class="mprm-topping_<?php echo $topping->ID ?>">
	<div class="mprm-item">
		<span class="mprm-topping-row mprm-topping-title">
			<?php echo wp_get_attachment_image(get_post_thumbnail_id($topping->ID), 'thumbnail', false, array('class' => "mprm-gallery-image")); ?>
			<?php echo get_the_title($topping->ID); ?>
		</span>
		<span class="mprm-topping-row mprm-topping-option">
			<input type="checkbox" class="mprm-checkbox" value="<?php echo mpto_get_price($topping->ID) ?>" data-id="<?php echo $topping->ID ?>">
		</span>
		<span class="mprm-topping-row mprm-topping-price"><?php echo $price ?></span>
	</div>
</li>