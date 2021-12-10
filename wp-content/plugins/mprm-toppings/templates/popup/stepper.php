<?php $topping_meta = mpto_get_topping_meta($topping->ID, true);
$min = empty($topping_meta['min']) ? '0' : $topping_meta['min'];
$max = empty($topping_meta['max']) ? '99' : $topping_meta['max'];
?>
<li class="mprm-topping_<?php echo $topping->ID ?>">
	<div class="mprm-item">
		<span class="mprm-topping-row mprm-topping-title">
			<?php echo wp_get_attachment_image(get_post_thumbnail_id($topping->ID), 'thumbnail', false, array('class' => "mprm-gallery-image")); ?>
			<?php echo get_the_title($topping->ID); ?>
		</span>
		<span class="mprm-topping-row mprm-topping-option">
			<input type="number" min="<?php echo $min ?>" max="<?php echo $max ?>" class="mprm-spinner" value="<?php echo $min ?>" data-id="<?php echo $topping->ID ?>">
		</span>
		<span class="mprm-topping-row mprm-topping-price"><?php echo mprm_currency_filter(mprm_format_amount(mpto_get_price($topping->ID))) ?></span>
	</div>
</li>