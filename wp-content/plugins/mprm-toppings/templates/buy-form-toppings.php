<?php $class = '';

if (\mp_restaurant_menu\classes\models\Settings::get_instance()->is_ajax_disabled()) {
	$class = 'mprm-no-js';
}
$toppings = mprm_get_selected_toppings($ID);
$args['color'] = mprm_get_option('checkout_color', 'mprm-btn blue');
$args['padding'] = mprm_get_option('checkout_padding');
$args['style'] = mprm_get_option('button_style', 'button');
$args['toppings'] = $toppings;
$toppings_button = mprm_get_option('toppings_title', __('View Item', 'mprm-toppings'));
if (!empty($toppings)) {
	?>
	<div class="<?php echo apply_filters('mpto_topping_buy_button', 'mpto-topping-buy-button') ?> mprm-display-inline">
		<!--<div class="mprm-text mprm-add-topping mprm-content-container mprm_purchase_submit_wrapper">
			<a rel="nofollow" href="" class="mprm-submit <?php echo $args['style'] . ' ' . $args['color'] . ' ' . $args['padding'] ?> mprm-topping-popup-open mprm-open mprm-display-inline">
				<span class="mprm-text"><?php echo $toppings_button ?></span>
			</a>
		</div>-->
		<div class="edit-item-topping-main mprm-text  mprm-content-container mprm_purchase_submit_wrapper">
			<a id="edit_item_topping" data-id="<?php echo esc_attr($ID);?>" 
			class="edit-item-topping mprm-submit button inherit mprm-inherit mprm-open mprm-display-inline"><span class="mprm-text">Select Item</span></a>
		</div>
		<!--<form id="mprm_toppings_form-<?php echo $ID ?>" class="mprm_form  mprm_purchase_form <?php echo $class ?>" method="POST">
			<?php //get_related_toppings_html($ID, $args); ?>
		</form>-->
	</div>
<?php }