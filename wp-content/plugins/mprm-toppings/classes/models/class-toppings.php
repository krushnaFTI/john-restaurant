<?php
namespace mprm_toppings\classes\models;

use mp_restaurant_menu\classes\models\Order;
use mprm_toppings\classes;

/**
 * Class Toppings
 */
class Toppings extends classes\models\parents\Store_item {

	/**
	 * @return Toppings
	 */
	private static $_instance;

	/**
	 * Toppings constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->post_types['topping'] = 'mprm_toppings';
	}

	/**
	 * @return Toppings
	 */
	public static function get_instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Add metabox toppings
	 */
	public function add_meta_boxes() {

		add_meta_box(
			'topping_settings',
			__('Visual element', 'mprm-toppings'),
			array($this, 'render_meta_box_content'),
			$this->get_post_type('topping'),
			'advanced',
			'high', array('mprm-type' => 'toppings')
		);

		add_meta_box(
			'mprmt-toppings',
			__('Toppings', 'mprm-toppings'),
			array($this, 'render_meta_box_content'),
			$this->get_post_type('menu_item'),
			'advanced',
			'default', array('mprm-type' => 'menu_item')
		);
	}

	/**
	 * Get topping post type
	 *
	 * * @param $value
	 *
	 * @return string
	 */
	public function get_post_type($value = 'topping') {
		return $this->post_types[$value];
	}

	/**
	 * Render meta-box content
	 *
	 * @param $post
	 *
	 * @param $meta_box
	 */
	public function render_meta_box_content($post, $meta_box) {
		$data = array();
		switch ($meta_box['args']['mprm-type']) {
			case 'toppings':
				$this->get_view()->render_html('../admin/metaboxes/topping_settings', $data);
				break;
			case 'menu_item':
				$data['items'] = $this->get_items(array('orderby' => 'title'));
				$data['selected_items'] = $this->get_selected_toppings($post->ID);
				$this->get_view()->render_html('../admin/metaboxes/check-box-list', $data);
				break;
			default:
				break;
		}
	}

	/**
	 * Get posts topping
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_items(array $args = array()) {
		$params = array(
			'post_type' => $this->get_post_type('topping'),
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'tax_query' => array()
		);
		$params = array_merge($params, $args);
		$posts = get_posts($params);

		return $posts;
	}

	/**
	 * Get selected toppings
	 *
	 * @param $id
	 *
	 * @return array|mixed
	 */
	public function get_selected_toppings($id) {
		$selected_toppings = get_post_meta($id, '_mprm_toppings', true);
		if (!$selected_toppings) {
			return array();
		} else {
			if (is_string($selected_toppings)) {
				$selected_toppings = explode(",", $selected_toppings);
			}
			return $selected_toppings;
		}
	}

	/**
	 * Add toppings to menu item
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function add_to_menu_item(array $data) {
		$cart_object = $this->get('cart');

		$index_in_cart = $cart_object->add_to_cart($data['menuID'], array('quantity' => 1, 'price_id' => 0));

		$cart_items = $cart_object->get_cart_contents();

		if (!empty($cart_items) && !empty($data['menuID'])) {
			if (empty($cart_items[$index_in_cart]['toppings'])) {
				unset($data['menuID']);
				$cart_items[$index_in_cart]['toppings'] = $data;
			}
		}
		$this->get('session')->set('mprm_cart', $cart_items);

		return (isset($index_in_cart)) ? true : false;
	}

	/**
	 * Update topping associated menu item
	 *
	 * @param $old_data
	 * @param $new_data
	 *
	 * @return mixed
	 */
	public function update_toppings_menu_item($old_data, $new_data) {
		$data = $old_data;

		foreach ($old_data as $key => $topping) {
			if (!empty($new_data[$key])) {
				if ($new_data[$key]['type'] === $data[$key]['type']) {
					$data[$key]['quantity'] = empty($data[$key]['quantity']) ? 1 : $data[$key]['quantity'];
					$data[$key]['quantity'] += (empty($new_data[$key]['quantity']) ? 1 : $new_data[$key]['quantity']);
				}
			}
		}

		foreach ($new_data as $new_data_key => $item) {
			if (empty($data[$new_data_key])) {
				$data[$new_data_key] = $item;
			}
		}
		return $data;
	}

	/**
	 * Update topping quantity
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function update_topping_quantity($data) {
		$cart_items = $this->get("cart")->get_cart_contents();
		$update = false;

		foreach ($cart_items as $key => $cart_item) {
			if ($cart_item['id'] === $data['menuID']) {
				if (empty($cart_item['toppings'])) {
					continue;
				}

				foreach ($cart_item['toppings'] as $key_topping => $topping) {
					if (!empty($data[$key_topping])) {
						$update = true;
						$topping['quantity'] = (int)$data[$key_topping];
						$cart_item['toppings'][$key_topping] = $topping;
						$cart_items[$key] = $cart_item;
					}
				}
			}
		}

		if ($update) {
			$this->get('session')->set('mprm_cart', $cart_items);
		}

		return $update;
	}

	/**
	 * Init action
	 */
	public function init_action() {
		add_action('mprm_success_page_cart_item', 'mprm_success_page_cart_topping_item', 10, 2);
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
		add_action('save_post', array($this, 'save_toppings'), 10, 2);
		add_action('mprm_toppings_render_price_row', 'mprm_toppings_render_price_row', 5, 4);
		add_action('mprm_cart_item_after', array($this, 'render_cart_toppings'), 5, 2);
		add_action('mprm_purchase_link_form_after', array($this, 'add_purchase_link'), 5, 2);
		add_action('mprm_email_menu_item_after', array($this, 'email_menu_item_child'), 10, 2);

		add_filter('mprm_cart_content_details', array($this, 'modified_cart_content_details'), 5, 1);
		add_filter('mprm_insert_payment', array($this, 'insert_payment'), 5, 2);
		add_filter('mprm_purchase_data_before_gateway', array($this, 'purchase_data_before_gateway'), 5, 2);
		add_filter('mprm-cart-item-data-after', array($this, 'render_widget_toppings'), 5, 1);
		add_filter('mprm_email_price_name', array($this, 'add_email_price_name'), 10, 3);
		add_filter('mprm_tag_menu_item_cart_items', array($this, 'change_email_cart_items'), 10, 1);

		//add_filter('mprm_in_cart_position', array($this, 'cart_item_data'), 10, 2);
	}

	public function cart_item_data($post_id, $cart_item) {
		if (isset($cart_item['toppings']) && !empty($cart_item['toppings'])) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Output toppings email tag {menu_item_list}
	 *
	 * @param $cart_item
	 * @param $order_id
	 */
	public function email_menu_item_child($cart_item, $order_id) {
		if (!empty($cart_item['child_items'])) {
			$this->get_view()->get_template('email/email-item-list', array('items' => $cart_item['child_items'], 'order' => $order_id));
		}
	}

	/**
	 * Add child items
	 *
	 * @param $menu_item_list
	 *
	 * @return array
	 */
	public function change_email_cart_items($menu_item_list) {
		if (!empty($menu_item_list)) {
			foreach ($menu_item_list as $key => $item) {
				if (empty($item['parent'])) {
					$menu_item_list[$key]['child_items'] = $this->findChildItems($item, $menu_item_list);
				}
			}
		}

		return $menu_item_list;
	}

	/**
	 * Find child cart items
	 *
	 * @param $cart_item
	 *
	 * @param $menu_item_list
	 *
	 * @return array
	 */
	public function findChildItems($cart_item, $menu_item_list) {
		$childItems = array();
		if (!empty($cart_item['toppings'])) {
			$topping_keys = array_keys($cart_item['toppings']);
		} else {
			return array();
		}

		foreach ($menu_item_list as $key => $item) {
			if (!empty($item['parent']) && ($cart_item['id'] === $item['parent']) && in_array($item['id'], $topping_keys) && $item['cart_key'] == $cart_item['cart_key'] ) {
				$childItems[$key] = $item;
			}
		}
		return $childItems;
	}

	/**
	 * Add to email topping price name
	 *
	 * @param $price_name
	 *
	 * @param $cart_item
	 * @param $order_id
	 *
	 * @return mixed
	 */
	public function add_email_price_name($price_name, $cart_item, $order_id) {
		$topping_post = get_post($cart_item['id']);

		if (empty($topping_post) && $topping_post->post_type !== $this->get_post_type()) {
			return $price_name;
		}

		if ($this->has_variable_prices($topping_post->ID) && !empty($cart_item['item_number']['options'])) {
			$price_id = $cart_item['item_number']['options']['price_id'];
			$price_name = ' - ' . $this->get_price_option_name($topping_post->ID, $price_id, $order_id);
		}

		return $price_name;
	}

	/**
	 * Add more one menu items in cart
	 *
	 *
	 * @return bool
	 */
	public function change_item_quantities_cart() {
		return FALSE;
	}

	/**
	 * Add toppings to checkout cart
	 *
	 * @param $item
	 */
	public function add_toppings_checkout_cart($item) {
		if (!empty($item)) { ?>
			<div class="mprm-icon-container"><span class="mprm-plus-icon"></span></div>
		<?php }
	}

	/**
	 * Add purchase link
	 *
	 * @param $ID
	 * @param $args
	 */
	public function add_purchase_link($ID, $args) {
		if (!empty($ID)) {
			$this->get_view()->get_template('buy-form-toppings', array('ID' => $ID, 'args' => $args));
		}
	}

	/**
	 * Render widget toppings
	 *
	 * @param $item
	 *
	 * @return void|mixed
	 */
	public function render_widget_toppings($item) {
		$toppings = empty($item['toppings']) ? array() : $item['toppings'];
		if (empty($toppings)) {
			return false;
		}
		$toppings = $this->add_topping_price($toppings);
		$toppings = $this->sort_array_by_order($toppings);

		return $this->get_view()->get_template('widget-cart-toppings', array('toppings' => $toppings, 'item' => $item));
	}

	/**
	 * Add topping price
	 *
	 * @param $toppings
	 * @param $tax_rate
	 *
	 * @return mixed
	 */
	public function add_topping_price($toppings, $tax_rate = false) {
		if (!empty($toppings)) {
			foreach ($toppings as $ID => $topping) {

				switch ($topping['type']) {
					case 'checkbox':
					case 'number':
						$toppings[$ID]['item_price'] = $this->get_price($ID);
						$toppings[$ID]['subtotal'] = $this->get_price($ID) * $toppings[$ID]['quantity'];
						$toppings[$ID]['tax'] = $toppings[$ID]['subtotal'] * (!$tax_rate ? 0 : $tax_rate);
						$toppings[$ID]['price'] = ($toppings[$ID]['subtotal'] + $toppings[$ID]['tax']);
						break;
					case 'radio':
						if ($this->has_variable_prices($ID)) {
							$prices = $this->get_variable_prices($ID);

							foreach ($prices as $price) {
								if ($price['name'] == $topping['value']) {
									$toppings[$ID]['quantity'] = (int)(empty($topping['quantity']) ? 1 : $topping['quantity']);
									$toppings[$ID]['item_price'] = $price['amount'];
									$toppings[$ID]['subtotal'] = $price['amount'] * $toppings[$ID]['quantity'];
									$toppings[$ID]['tax'] = $toppings[$ID]['subtotal'] * (!$tax_rate ? 1 : $tax_rate);
									$toppings[$ID]['price'] = ($toppings[$ID]['subtotal'] + $toppings[$ID]['tax']);
								}
							}
						}
						break;
					default:
						break;
				}
			}
		}
		return $toppings;
	}

	/**
	 * Sort array by order
	 *
	 * @param $toppings
	 *
	 * @return mixed
	 */
	public function sort_array_by_order($toppings) {
		usort($toppings, function ($a, $b) {
			if ($a['order'] == $b['order']) {
				return 0;
			}
			return ($a['order'] < $b['order']) ? -1 : 1;
		});

		return $toppings;
	}

	/**
	 * Data before gateway
	 *
	 * @param $purchase_data
	 *
	 * @return mixed
	 */
	public function purchase_data_before_gateway($purchase_data) {

		$cart_details = $purchase_data['cart_details'];
		$purchase_data['cart_details'] = array();
		$tax_rate = $this->get('taxes')->get_tax_rate();

		// add purchase_data toppings
		foreach ($cart_details as $cart_key => $cart_item) {

			$cart_item = array_merge($purchase_data['menu_items'][$cart_key], $cart_item);

			$cart_item['subtotal'] = $cart_item['item_price'] * $cart_item['quantity'];
			$cart_item['tax'] = $cart_item['subtotal'] * $tax_rate;
			$cart_item['price'] = $cart_item['subtotal'] + $cart_item['tax'];

			$cart_item['cart_key'] = $cart_key;
			$purchase_data['cart_details'][] = $cart_item;
			if (!empty($purchase_data['menu_items'][$cart_key]['toppings'])) {

				$toppings = $purchase_data['menu_items'][$cart_key]['toppings'];
				$toppings = $this->add_topping_price($toppings);
				$toppings = $this->sort_array_by_order($toppings);

				foreach ($toppings as $key_id => $topping) {
					$subtotal = $topping['item_price'] * ($topping['quantity'] * $cart_item['quantity']);
					$tax = round($subtotal * $tax_rate);

					$topping_cart_item = array(
						'id' => $topping['id'],
						'options' => array(),
						'quantity' => $topping['quantity'],
						'is_topping' => true,
						'parent_quantity' => $cart_item['quantity'],
						'parent' => $cart_item['id'],
						'cart_key' => $cart_key,
						'name' => get_post($topping['id'])->post_title,
						'item_number' =>
							array(
								'id' => $topping['id'],
								'quantity' => $topping['quantity'],
								'options' =>
									array(
										'quantity' => $topping['quantity'],
										'price_id' => empty($topping['index']) ? 0 : $topping['index'],
									),
							),
						'item_price' => $topping['item_price'],
						'discount' => 0,
						'subtotal' => $subtotal,
						'tax' => $tax,
						'fees' => array(),
						'price' => $tax + $subtotal,
					);
					$purchase_data['cart_details'][] = $topping_cart_item;

				}
			} else {
				continue;
			}
		}

		return $purchase_data;
	}

	/**
	 * Insert payment with toppings
	 *
	 * @param $paymentID
	 * @param $payment_data
	 *
	 * @return mixed
	 */
	public function insert_payment($paymentID, $payment_data) {
		$payment = $this->get('order');
		$payment->setup_payment($paymentID);
		$meta = $payment->get_meta();
		$tax_rate = $this->get('taxes')->get_tax_rate();
		$sub_total = 0;
		$tax = 0;

		//add menu items toppings
		foreach ($meta['menu_items'] as $key => $item) {
			$item = array_merge($payment_data['menu_items'][$key], $item);
			$meta['menu_items'][$key] = $item;
		}

		//add order toppings
		foreach ($payment_data['cart_details'] as $cart_key => $cart_item) {
			if (!empty($meta['menu_items'][$cart_key]['toppings'])) {
				$toppings = $meta['menu_items'][$cart_key]['toppings'];

				foreach ($toppings as $key => $topping) {
					if (isset($cart_item['is_topping']) && $cart_item['is_topping']) {
						$topping['quantity'] = (empty($topping['quantity']) ? 1 : $topping['quantity']) * $cart_item['parent_quantity'];
					} else {
						$topping['quantity'] = (empty($topping['quantity']) ? 1 : $topping['quantity']);
					}
					$toppings[$key] = $topping;
				}

				$sub_total += $this->get_subtotal($toppings);
			}
			if (isset($cart_item['is_topping']) && $cart_item['is_topping']) {
				$cart_item['subtotal'] = $cart_item['item_price'] * ($cart_item['quantity'] * $cart_item['parent_quantity']);
			} else {
				$cart_item['subtotal'] = $cart_item['item_price'] * $cart_item['quantity'];

			}

			$cart_item['tax'] = $cart_item['subtotal'] * $tax_rate;
			$cart_item['price'] = $cart_item['subtotal'] + $cart_item['tax'];

			$meta['cart_details'][$cart_key] = $cart_item;
		}

		// recalculation tax
		foreach ($meta['cart_details'] as $key => $item) {
			$tax += $item['tax'];
		}

		$this->get('order')->update_meta('_mprm_order_meta', $meta);
		$this->get('order')->update_meta('_mprm_order_tax', $tax);
		$this->get('order')->update_meta('_mprm_order_total', $payment_data['price']);

		return $payment_data;
	}

	/**
	 * Get subtotal
	 *
	 * @param $toppings
	 *
	 * @return number
	 */
	public function get_subtotal($toppings) {
		$toppings = $this->add_topping_price($toppings);

		$prices = wp_list_pluck($toppings, 'subtotal');
		if (is_array($prices)) {
			$toppings_subtotal = array_sum($prices);
		} else {
			$toppings_subtotal = 0.00;
		}
		if ($toppings_subtotal < 0) {
			$toppings_subtotal = 0.00;
		}
		return $toppings_subtotal;
	}

	/**
	 * Add topping to cart
	 *
	 * @param $cart_items
	 *
	 * @return mixed
	 */
	public function modified_cart_content_details($cart_items) {
		foreach ($cart_items as $key => $item) {
			if (!empty($item['item_number']['toppings'])) {
				$toppings = $item['item_number']['toppings'];
				foreach ($toppings as $topping_key => $topping) {
					$topping['quantity'] = (empty($topping['quantity']) ? 1 : $topping['quantity']) * $item['quantity'];
					$toppings[$topping_key] = $topping;
				}

				$toppings_subtotal = $this->get_subtotal($toppings);

				$item['subtotal'] = $item['subtotal'] + $toppings_subtotal;

				$item = $this->get_tax_with_toppings($item);

				$item['price'] = $item['subtotal'] + $item['tax'];
				$cart_items[$key] = $item;
			}
		}
		return $cart_items;
	}

	/**
	 * Calculate topping tax
	 *
	 * @param $item
	 *
	 * @return mixed
	 */
	public function get_tax_with_toppings($item) {
		$tax_rate = $this->get('taxes')->get_tax_rate();
		$item['tax'] = ($item['subtotal'] * $tax_rate);;
		return $item;
	}

	/**
	 * Get cart subtotal with toppings subtotal
	 *
	 * @param $items
	 *
	 * @return float|int|number|void
	 */
	public function get_toppings_subtotal($items) {
		$toppings_subtotal = 0.00;
		if (empty($items)) {
			return false;
		}
		foreach ($items as $key => $item) {
			if (!empty($item['item_number']['toppings'])) {
				$toppings_subtotal = $this->get_subtotal($item['item_number']['toppings']);
			}
		}
		return $toppings_subtotal;
	}

	/**
	 * Render cart toppings
	 *
	 * @param $item
	 * @param (int) $index in cart
	 *
	 * @return mixed
	 */
	public function render_cart_toppings($item, $index) {
		$toppings = empty($item['toppings']) ? array() : $item['toppings'];

		if (empty($toppings)) {
			return false;
		}
		$toppings = $this->add_topping_price($toppings);

		$toppings = $this->sort_array_by_order($toppings);

		return $this->get_view()->get_template('cart-toppings', array('toppings' => $toppings, 'item' => $item, 'index' => $index));
	}

	/**
	 * Save toppings
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return mixed
	 */
	public function save_toppings($post_id, $post) {
		$request = $_REQUEST;

		if (!current_user_can('edit_post', $post->ID)) {
			return $post->ID;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post->ID;
		}

		if (!empty($_REQUEST['post_type']) && !in_array($_REQUEST['post_type'], array_values($this->post_types))) {
			return $post->ID;
		}

		if (!empty($request['mprm-toppings_nonce_box']) && !wp_verify_nonce($request['mprm-toppings_nonce_box'], 'mprm-toppings_nonce')) {
			return $post->ID;
		}

		if (!empty($request['metaboxes'])) {

			delete_post_meta($post_id, '_mprm_default_price_id');
			delete_post_meta($post_id, '_variable_pricing');

			foreach ($request['metaboxes'] as $key => $value) {
				switch ($key) {
					case'radio':
						if ($key == $request['metaboxes']['topping-types']) {

							update_post_meta($post_id, $key, $value);

							if (isset($request['_mprm_default_price_id'])) {
								update_post_meta($post_id, '_mprm_default_price_id', $request['_mprm_default_price_id']);
							}

							if (isset($request['metaboxes']['radio']['variable_prices']) && is_array($request['metaboxes']['radio']['variable_prices'])) {
								if ($request['metaboxes']['topping-types'] == 'radio') {
									$variable_prices = array();
									// reindex
									foreach ($request['metaboxes']['radio']['variable_prices'] as $key_price => $price) {
										$price['index'] = $key_price;
										$variable_prices[$key_price] = $price;
									}
									$request['metaboxes']['radio']['variable_prices'] = $variable_prices;

									update_post_meta($post_id, 'radio', $request['metaboxes']['radio']);
									update_post_meta($post_id, 'mprm_variable_prices', $request['metaboxes']['radio']['variable_prices']);
									update_post_meta($post_id, '_variable_pricing', 1);
								}
							}

						} else {
							delete_post_meta($post_id, $key);
						}
						break;

					case'stepper':
					case'checkbox':

						if ($key == $request['metaboxes']['topping-types']) {

							update_post_meta($post_id, $key, $value);
							update_post_meta($post_id, 'price', $value['price']);
						} else {
							delete_post_meta($post_id, $key);
						}
						break;
					default:
						if (empty($value)) {
							delete_post_meta($post_id, $key);
						} else {
							update_post_meta($post_id, $key, $value);
						}
						break;
				}
			}
		}
		if (!empty($request['mprm-toppings'])) {
			update_post_meta($post_id, '_mprm_toppings', $request['mprm-toppings']);
		}

		if (isset($request['mprm-order-removed']) && ($request['post_type'] == $this->post_types['order'])) {
			$this->update_order_toppings($request);
		}

		return $post->ID;
	}

	/**
	 * Update order toppings
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	private function update_order_toppings(array $data) {
		$deleted_toppings = json_decode(stripcslashes($data['mprm-order-removed']), true);
		if (empty($deleted_toppings)) {
			return false;
		}
		$payment_id = absint($data['post_ID']);
		$payment = new Order($payment_id);

		if (empty($payment_id)) {
			return false;
		}

		foreach ($deleted_toppings as $deleted_topping) {
			$deleted_topping = $deleted_topping[0];

			if (empty ($deleted_topping['id'])) {
				continue;
			}

			$price_id = empty($deleted_topping['price_id']) ? 0 : (int)$deleted_topping['price_id'];

			$args = array(
				'quantity' => (int)$deleted_topping['quantity'],
				'price_id' => (int)$price_id,
				'item_price' => (float)$deleted_topping['amount'],
				'cart_index' => !isset($deleted_topping['cart_index']) ? false : $deleted_topping['cart_index']
			);

			$this->remove_topping_from_order($deleted_topping['id'], $args, $payment);

			do_action('mprm_remove_topping_from_payment', $payment_id, $deleted_topping['id']);
		}

		$updated = $payment->save();

		return $updated;
	}

	/**
	 * Remove topping from order
	 *
	 * @param $topping_ID
	 * @param $args
	 * @param Order $payment
	 *
	 * @return bool
	 */
	private function remove_topping_from_order($topping_ID, $args, Order $payment) {
		// Set some defaults
		$defaults = array(
			'quantity' => 1,
			'item_price' => false,
			'price_id' => false,
			'cart_index' => false,
		);

		$args = wp_parse_args($args, $defaults);

		if (get_post_type($topping_ID) != $this->get_post_type('topping')) {
			return false;
		}
		$payment->check_remove_args($topping_ID, $args);

		if (false === $args['cart_index']) {
			$found_cart_key = $payment->search_cart_key($topping_ID, $args);
		} else {
			$found_cart_key = $payment->check_cart_index(absint($args['cart_index']), $topping_ID);
		}
		// exit if cart key false
		if ($found_cart_key === false) {
			return false;
		}

		$cart_index = 0;

		$cart_details = $payment->cart_details;

		foreach ($cart_details as $key => $cart_item) {
			$remove_topping_key = false;

			if (!empty($cart_item['toppings'])) {

				foreach ($cart_item['toppings'] as $topping_key => $topping) {
					$cart_index++;
					if ($found_cart_key == $cart_index) {
						$remove_topping_key = $topping_key;
					}
				}
			}

			$cart_index++;
			if ($remove_topping_key) {
				unset($cart_details[$key]['toppings'][$remove_topping_key]);
			}
		}

		$payment->apply_remove_args($topping_ID, $args, $found_cart_key);

		unset($cart_details[$found_cart_key]);
		$payment->cart_details = $cart_details;

		return true;
	}

	/**
	 * Get updated cart details
	 *
	 * @return array
	 */
	public function get_cart_details() {
		$date = array();
		$total = $this->get('cart')->get_cart_total();

		$date['data'] = array(
			'taxes' => html_entity_decode($this->get('cart')->cart_tax(), ENT_COMPAT, 'UTF-8'),
			'subtotal' => html_entity_decode($this->get('menu_item')->currency_filter($this->get('formatting')->format_amount($this->get('cart')->get_cart_subtotal())), ENT_COMPAT, 'UTF-8'),
			'total' => html_entity_decode($this->get('menu_item')->currency_filter($this->get('formatting')->format_amount($total)), ENT_COMPAT, 'UTF-8')
		);

		$date['success'] = true;
		$date['data'] = apply_filters('mprm_ajax_cart_topping_item_quantity_response', $date['data']);

		return $date;
	}

	/**
	 * Remove Url
	 *
	 * @param $topping
	 * @param $cart_key
	 *
	 * @return mixed|void
	 */
	public function get_remove_url($topping, $cart_key) {

		if (defined('DOING_AJAX')) {
			$current_page = mprm_get_checkout_uri();
		} else {
			$current_page = $this->get('misc')->get_current_page_url();
		}

		$remove_url = $this->get('misc')->add_cache_busting(add_query_arg(array('cart_item' => $cart_key, 'topping_ID' => $topping->ID, 'mprm_action' => 'remove', 'mpto_controller' => 'toppings'), $current_page));

		return apply_filters('mprm_topping_item_url', $remove_url);
	}

	/**
	 * Remove topping from cart by index
	 *
	 * @param $topping_ID
	 * @param $cart_index
	 *
	 * @return bool
	 */
	public function remove_from_cart($topping_ID, $cart_index) {
		$removed = FALSE;
		$cart_items = $this->get("cart")->get_cart_contents();
		foreach ($cart_items as $index => $cart_item) {
			if (empty($cart_item['toppings'])) {
				continue;
			}

			if ($index == $cart_index) {
				foreach ($cart_item['toppings'] as $key_topping => $topping) {

					if ($key_topping == $topping_ID) {
						$removed = TRUE;
						unset($cart_item['toppings'][$topping_ID]);
						$cart_items[$index] = $cart_item;
					}
				}
			}
		}

		if ($removed) {
			$this->get('session')->set('mprm_cart', $cart_items);
		}

		return $removed;
	}
}