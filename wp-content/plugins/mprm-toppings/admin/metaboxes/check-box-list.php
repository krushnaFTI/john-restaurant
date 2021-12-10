<?php wp_nonce_field('mprm-toppings' . '_nonce', 'mprm-toppings' . '_nonce_box'); ?>

<select id="mprm-toppings-data" multiple="multiple" style="width: 100%;display: none; ">
	<?php foreach ($items as $item): ?>
		<option value="<?php echo $item->ID ?>" data-id="<?php echo $item->ID ?>"><?php echo $item->post_title ?></option>
	<?php endforeach; ?>
</select>
<input id="toppings-input-hidden" multiple="multiple" name="mprm-toppings" type="hidden" style="width: 100%" value="<?php echo is_array($selected_items) ? implode(",", $selected_items) : $selected_items ?>">
<p class="description"><?php _e('Drag and drop to set an order. Type to search.', 'mprm-toppings') ?></p>




