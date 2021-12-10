<?php
use mp_restaurant_menu\classes\models\Cart;
use mp_restaurant_menu\classes\models\Misc;

/**
 * Topping render price row
 *
 * @param $key
 * @param array $args
 * @param $post_id
 * @param $index
 */
function mprm_toppings_render_price_row($key, $args = array(), $post_id, $index) {

	$view_object = mprm_toppings_get_view();

	$defaults = array(
		'name' => null,
		'amount' => null
	);

	$args = wp_parse_args($args, $defaults);

	$default_price_id = mprm_get_post_meta($post_id, '_mprm_default_price_id', true, '1');
	$currency_position = mprm_get_option('currency_position', 'before');

	?>
	<td>
		<span class="mprm_toppings_drag-handle"></span>
		<input type="hidden" name="metaboxes[radio][variable_prices][<?php echo $key; ?>][index]" class="mprm_toppings_repeatable_index" value="<?php echo $index; ?>"/>
	</td>

	<td>
		<?php echo $view_object->render_html('../admin/settings/text', array(
			'name' => 'metaboxes[radio][variable_prices][' . $key . '][name]',
			'value' => esc_attr($args['name']),
			'placeholder' => __('Option Name', 'mprm-toppings'),
			'class' => 'mprm_toppings_variable_prices_name large-text'
		)); ?>
	</td>

	<td>
		<?php $price_args = array(
			'name' => 'metaboxes[radio][variable_prices][' . $key . '][amount]',
			'value' => $args['amount'],
			'placeholder' => mprm_format_amount(9.99),
			'class' => 'mprm-toppings-price-field'
		);
		?>

		<?php if ($currency_position == 'before') : ?>
			<span><?php echo mprm_currency_filter(''); ?></span>
			<?php echo $view_object->render_html('../admin/settings/text', $price_args); ?>
		<?php else : ?>
			<?php echo $view_object->render_html('../admin/settings/text', $price_args); ?>
			<?php echo mprm_currency_filter(''); ?>
		<?php endif; ?>
	</td>

	<td class="mprm_toppings_repeatable_default_wrapper">
		<label class="mprm-toppings-default-price">
			<input type="radio" <?php checked($default_price_id, $key, true); ?> class="mprm_toppings_repeatable_default_input" name="_mprm_default_price_id" value="<?php echo $key; ?>"/>
			<span class="screen-reader-text"><?php printf(__('Set ID %s as default price', 'mprm-toppings'), $key); ?></span>
		</label>
	</td>

	<td><span class="mprm_toppings_price_id"><?php echo $key; ?></span></td>

	<?php do_action('mprm_toppings_price_table_row', $post_id, $key, $args); ?>

	<td>
		<button class="mprm_toppings_remove_repeatable" data-type="price" style="background: url(<?php echo admin_url('/images/xit.gif'); ?>) no-repeat;">
			<span class="screen-reader-text"><?php printf(__('Remove price option %s', 'mprm-toppings'), $key); ?></span>
			<span aria-hidden="true">&times;</span>
		</button>
	</td>
	<?php
}

/**
 * Success topping item in cart
 *
 * @param $item
 * @param $order
 *
 * @return bool
 */
function mprm_success_page_cart_topping_item($item, $order) {
	$menu_item_notes = mprm_get_menu_item_notes($item['id']);
	$post_type = get_post_type($item['id']);

	if ($post_type != mpto_get_post_type()) {
		return true;
	}

	$price_id = Cart::get_instance()->get_cart_item_price_id($item);
	$has_variable_prices = mpto_has_variable_prices($item['id']);
	$price_option_name = mpto_get_price_option_name($item['id'], $price_id, $order->ID);

	?>

	<tr>
		<td>
			<div class="mprm_purchase_receipt_product_name mprm-post-<?php echo $post_type ?>">
				<?php echo esc_html($item['name']); ?>
				<?php if ($has_variable_prices && !is_null($price_id)) : ?>
					<span class="mprm_purchase_receipt_price_name">&nbsp;&ndash;&nbsp;<?php echo $price_option_name; ?></span>
				<?php endif; ?>
			</div>

			<?php if (!empty($menu_item_notes)) : ?>
				<div class="mprm_purchase_receipt_product_notes"><?php echo wpautop($menu_item_notes); ?></div>
			<?php endif; ?>

		</td>
		<?php if (Misc::get_instance()->use_skus()) : ?>
			<td></td>
		<?php endif; ?>

		<?php if (Cart::get_instance()->item_quantities_enabled()) { ?>
			<td class="mprm-success-page-quantity"><?php echo $item['quantity']; ?></td>
		<?php } ?>
		<td>
			<?php if (empty($item['in_bundle'])) : ?>
				<?php echo mprm_currency_filter(mprm_format_amount($item['item_price'])); ?>
			<?php endif; ?>
		</td>
	</tr>

<?php }