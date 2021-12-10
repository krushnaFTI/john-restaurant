<?php
use mprm_toppings\classes\View;

foreach ($toppings as $topping_key => $topping):

	$topping_post = get_post($topping['id']);

	if (!is_object($topping_post) || !is_a($topping_post, 'WP_Post')) {
		continue;
	}

	$topping_type = get_post_meta($topping_post->ID, 'topping-types', true);

	View::get_instance()->get_template("checkout/{$topping_type}", array('topping' => $topping, 'item' => $item, 'topping_post' => $topping_post, 'index' => $index));

endforeach;