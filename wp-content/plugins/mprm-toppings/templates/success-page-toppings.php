<?php
if (empty($toppings)) {
	return;
}
foreach ($toppings as $key => $topping) {
	$toppingObject = get_post($key);
	?>
	<tr>
		<td>
			<div class="mprm_purchase_receipt_product_name mprm-post-<?php echo $toppingObject->post_type ?>">
				<?php echo ' - ' . $toppingObject->post_title ?>
				<?php if (mprm_toppings_has_variable_prices($key) && !is_null($topping['index'])) : ?>
					<span class="mprm_purchase_receipt_price_name">&nbsp;&ndash;&nbsp;<?php echo $topping['value'] ?></span>
				<?php endif; ?>
			</div>
		</td>
		<td> <?php echo empty($topping['quantity']) ? 1 : $topping['quantity']; ?> </td>
		<td> <?php echo mprm_currency_filter(mprm_format_amount($topping['price'])); ?> </td>
	</tr>
<?php } ?>
