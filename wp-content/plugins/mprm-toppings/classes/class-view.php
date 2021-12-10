<?php
namespace mprm_toppings\classes;
/**
 * View class
 */
class View extends \mp_restaurant_menu\classes\View {

	protected static $instance;

	public function __construct() {
		$this->template_path = MP_TO_TEMPLATE_PATH;
		$this->templates_path = MP_TO_TEMPLATES_PATH;
		$this->prefix = 'mpto';
	}

	/**
	 * @return View
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Add custom taxonomy columns
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public function init_menu_columns($columns) {

		$columns = array_slice($columns, 0, 1, true) + array('mprm-thumb' => __('Image', 'mprm-toppings')) + array_slice($columns, 1, count($columns) - 1, true);
		$columns = array_slice($columns, 0, 3, true) + array('mprm-type' => __('Type', 'mprm-toppings')) + array_slice($columns, 3, count($columns) - 1, true);
		$columns = array_slice($columns, 0, 3, true) + array('mprm-price' => __('Price', 'mprm-toppings')) + array_slice($columns, 3, count($columns) - 1, true);

		return $columns;
	}

	/**
	 * Add content to custom column
	 *
	 * @param $column
	 * @param $post_ID
	 */
	public function show_menu_columns($column, $post_ID) {

		$topping_meta = mpto_get_topping_meta($post_ID);
		$topping_type = isset( $topping_meta['topping_type'] ) ? $topping_meta['topping_type'] : '&ndash;';

		switch ($column) {
			case 'mprm-thumb':
				echo '<a href="' . get_edit_post_link($post_ID) . '">' . get_the_post_thumbnail($post_ID, 'thumbnail', array('width' => 50, 'height' => 50)) . '</a>' . '<div class=mprm-clear></div>';
				break;
			case 'mprm-price':
				if ( mpto_has_variable_prices($post_ID) ) {
					$prices = mprm_toppings_get_variable_prices($post_ID);
					if ( !empty($prices) ) {
						$labels = [];
						foreach($prices as $price) {
							$labels[] = sprintf( '%s: %s', $price['name'], mprm_currency_filter(mprm_format_amount($price['amount'])) );
						}
						echo esc_html( implode( ' / ', $labels ) );
					}
				} else {
					echo esc_html( mprm_currency_filter(mprm_format_amount(mprm_toppings_get_price($post_ID))) );
				}
				break;
			case 'mprm-type':
				echo esc_html( $topping_type );
				break;
		}
	}
}
