<?php

namespace mprm_delivery\classes\models;



use mp_restaurant_menu\classes\Core;

use mp_restaurant_menu\classes\models\Order;

use mp_restaurant_menu\classes\models\Session;

use mp_restaurant_menu\classes\models\Taxes;

use mprm_delivery\classes\Model;

use mprm_delivery\classes\View;



/**

 * Class Delivery

 */

class Delivery extends Model {



	private static $_instance;



	/**

	 * @return Delivery

	 */

	public static function get_instance() {

		if (empty(self::$_instance)) {

			self::$_instance = new self();

		}

		return self::$_instance;

	}



	/**

	 * Init Actions/filters

	 */

	public function init_action() {

		if ($this->get('settings')->get_available_mode()) {



			remove_action('mprm_checkout_additional_information', 'mprm_checkout_delivery_address', '5');



			add_action('mprm_checkout_form_top', 'mprm_render_delivery_form', 10);

			add_action('mprm_checkout_table_tax_before', array($this, 'render_checkout_delivery_cost'), 10, 1);

			add_action('mprm_insert_payment', array($this, 'mprm_updated_edited_purchase'), 10, 1);

			add_action('mprm_update_edited_purchase', array($this, 'mprm_updated_edited_purchase'), 10, 1);

			add_action('mprm_success_page_subtotal_after', array($this, 'success_page_delivery'), 10, 1);

			add_action('mprm_cart_total_widget_before', array($this, 'widget_delivery_cost'), 10, 1);

			add_action('add_meta_boxes', array($this, 'add_meta_boxes'), 11);

			add_action('mprm_success_page_subtotal_before', array($this, 'add_delivery_information'), 10, 1);



			//add_filter('mprm_send_admin_notice', array($this, 'send_admin_notice'), 10, 2);

			add_filter('mprm_get_cart_tax', array($this, 'change_cart_tax'), 10, 1);

			add_filter('mprm_order_delivery_cost', array($this, 'edit_order_delivery_cost'), 10, 1);

			add_filter('mprm_purchase_data_before_gateway', array($this, 'purchase_data_before_gateway'), 10, 2);

			add_filter('mprm_get_cart_total', array($this, 'change_cart_total'), 10, 1);

			add_filter('mprm_ajax_cart_item_quantity_response', array($this, 'add_delivery_response'), 10, 1);

			add_filter('mprm_email_tags', array($this, 'add_email_tags'), 10, 1);

			add_filter('mprm_orders_list_delivery', array($this, 'render_admin_table_row'), 10, 1);

		}

	}



	/**

	 * @param $admin_notice

	 * @param $payment_id

	 *

	 * @return bool

	 */

	/*public function send_admin_notice($admin_notice, $payment_id) {

		$order = New Order($payment_id);

		if (empty($order) && !is_object($order)) {

			return $admin_notice;

		}

		if ($order->gateway == 'manual' && $order->status == 'publish') {

			$admin_notice = false;

		}



		return $admin_notice;

	}*/



	/**

	 * Success page delivery information

	 *

	 * @param $order

	 */

	public function add_delivery_information($order) {

		if (!empty($order) && is_object($order)) {

			View::get_instance()->render_html('../admin/success-page/delivery-information', array('order' => $order));

		}

	}



	/**

	 * Add delivery MetaBox for order

	 */

	public function add_meta_boxes() {

		$order_post_type = mprm_get_post_type('order');



		add_meta_box(

			'order-delivery',

			__('Delivery details', 'mprm-delivery'),

			array($this, 'order_meta_box'),

			$order_post_type,

			'advanced',

			'low',

			array('post_type' => $order_post_type)

		);

	}



	/**

	 * Edit order delivery_cost

	 *

	 * @param $delivery_cost

	 *

	 * @return mixed|string|void

	 */

	public function edit_order_delivery_cost($delivery_cost) {

		global $post;



		$order_data = get_post_meta($post->ID, 'mpde_delivery', true);



		if (!empty($order_data)) {

			if (isset($order_data['delivery_cost'])) {

				$delivery_cost = $order_data['delivery_cost'];

			}

		}

		return $delivery_cost;

	}



	/**

	 * Render order MetaBox

	 */

	public function order_meta_box() {

		global $post;

		$order_data = get_post_meta($post->ID, 'mpde_delivery', true);

		$shipping_address = get_post_meta($post->ID, '_mprm_order_shipping_address', true);



		$default_data = array(

			'address_type' => '',

			'delivery_apartment' => '',

			'delivery_gate_code' => '',

			'delivery_notes' => '',

			'delivery_street' => '',

			'time-mode' => 'asap',

			'order-minutes' => '00',

			'order-hours' => '',

			'delivery_mode' => 'delivery'

		);



		$order_data = empty($order_data) ? array() : $order_data;



		$order_data = array_merge($default_data, $order_data);



		View::get_instance()->render_html('../admin/metaboxes/delivery-details', array('order_data' => $order_data, 'shipping_address' => $shipping_address, 'post' => $post));

	}



	/**

	 * Render delivery data

	 *

	 * @param $shipping_address

	 *

	 * @return mixed

	 */

	public function render_admin_table_row($shipping_address) {

		global $post;



		$delivery_order_meta = get_post_meta($post->ID, 'mpde_delivery', true);



		if (empty($delivery_order_meta)) {

			return $shipping_address;

		} else {

			return View::get_instance()->render_html('../admin/admin-table-row', array('delivery_order_meta' => $delivery_order_meta), false);

		}

	}



	/**

	 * Add macros {delivery}

	 *

	 * @param $email_tags

	 *

	 * @return array

	 */

	public function add_email_tags($email_tags) {

		if (is_array($email_tags) && !empty($email_tags)) {

			$email_tags[] = array(

				'tag' => 'delivery',

				'description' => __('Delivery cost.', 'mprm-delivery'),

				'function' => 'mpde_email_tag_delivery'

			);

			$email_tags[] = array(

				'tag' => 'delivery_information',

				'description' => __('Delivery information.', 'mprm-delivery'),

				'function' => 'mpde_email_tag_delivery_information'

			);

		}

		return $email_tags;

	}



	/**

	 *  Add to order delivery

	 *

	 * @param $purchase_data

	 *

	 * @return mixed

	 */

	public function purchase_data_before_gateway($purchase_data) {

		$delivery_cost = $this->get_delivery_cost($purchase_data['price']);
		
		if (mprm_get_option('enable_delivery', false)) {

			$purchase_data['shipping_cost'] = $delivery_cost;

			$purchase_data['no_shipping'] = '0';

			$purchase_data['shipping'] = true;

		}

		return $purchase_data;

	}



	/**

	 * Get delivery cost by settings

	 *

	 * @return mixed|string|void

	 *

	 * @param $sub_total

	 * @param $type

	 *

	 * @param bool $formatting

	 **/

	public function get_delivery_cost($sub_total, $formatting = false, $type = false) {



		if (empty($type)) {

			$type = $this->get_delivery_type();

		}

		$delivery_cost_settings = mprm_get_option('delivery_cost', 0);

		$delivery_min_cost_free = mprm_get_option('delivery_min_cost_free', false);



		switch ($type) {

			case 'collection':

				$delivery_cost = 0;

				break;

			case 'delivery':



				if ($delivery_cost_settings === 0) {
					if($_SESSION['postmates_delivery_id']){
						$delivery_cost = $_SESSION['postmates_delivery_id'];
					}else{
						$delivery_cost = 0;
					}
				} elseif (!empty($delivery_min_cost_free) && ($sub_total >= (float)$delivery_min_cost_free)) {
					if($_SESSION['postmates_delivery_id']){
						$delivery_cost = $_SESSION['postmates_delivery_id'];
					}else{
						$delivery_cost = 0;
					}
				} else {
					// $get_settings_option = get_option('mprm_settings');
					// $get_postmates_key = $get_settings_option['postmates_sandbox_key_text'];
					// $get_postmates_customer_id = $get_settings_option['postmates_customer_id_text'];
					
					// if(!empty($get_postmates_key) && !empty($get_postmates_customer_id)){
					
					// 	// $admin_user_id = '1';
					// 	// $get_admin_phone_number = get_user_meta( $admin_user_id, 'admin_phone_number');
					// 	// $get_admin_business_address = get_user_meta( $admin_user_id, 'admin_business_address');
					// 	// $get_admin_nickname = get_user_meta( $admin_user_id, 'nickname');

					// 	$url1 = "https://api.postmates.com/v1/customers/".$get_postmates_customer_id."/delivery_quotes";
					// 	$uname = $get_postmates_key;
					// 	$pwd = "";

					// 	$create_delivery_data1 = "dropoff_address=20 McAllister St, San Francisco, CA 94102&pickup_address=101 Market St, San Francisco, CA 94105";

					// 	$ch_url = curl_init($url1);
					// 	curl_setopt($ch_url, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Accept: application/json'));
					// 	curl_setopt($ch_url, CURLOPT_USERPWD, $uname . ":" . $pwd);
					// 	curl_setopt($ch_url, CURLOPT_TIMEOUT, 30);
					// 	curl_setopt($ch_url, CURLOPT_POST, 1);
					// 	curl_setopt($ch_url, CURLOPT_POSTFIELDS, $create_delivery_data1);
					// 	curl_setopt($ch_url, CURLOPT_RETURNTRANSFER, TRUE);
					// 	curl_setopt($ch_url, CURLOPT_SSL_VERIFYPEER, false);
					// 	$create_del_return1 = curl_exec($ch_url);
					// 	curl_close($ch_url);
						
					// 	$created_delivery_data1 = json_decode($create_del_return1);
					// 	$postmates_delivery_price = $created_delivery_data1->fee;
					// 	$postmates_delivery_duration = $created_delivery_data1->duration;
					// 	$postmates_delivery_currency = $created_delivery_data1->currency;
					// 	if($postmates_delivery_currency == "USD"){
					// 		$delivery_cost = $postmates_delivery_price/100;
					// 	}
					
					//}else{
						if($_SESSION['postmates_delivery_id']){
							$delivery_cost = $_SESSION['postmates_delivery_id'];
						}else{
							$delivery_cost = $delivery_cost_settings;
						}
					//}
					
				}

				break;

			default:

				$delivery_cost = 0;
				break;
		}





		if ($formatting) {
			$delivery_cost = is_numeric($delivery_cost) ? mprm_currency_filter(mprm_format_amount($delivery_cost)) : $delivery_cost;

		}



		return apply_filters('mpde_delivery_cost', $delivery_cost);

	}



	/**

	 * Get saved delivery_type

	 *

	 * @return bool|mixed|string

	 */

	public function get_delivery_type() {

		$delivery_type = Session::get_instance()->get_session_by_key('mpde_delivery_type');

		$available_mode = $this->get('settings')->get_available_mode();

		return empty($delivery_type) ? $available_mode : $delivery_type;

	}



	/**

	 * Add checkout delivery row

	 */

	public function render_checkout_delivery_cost() {

		$cart_total = mprm_get_cart_subtotal();

		$this->set_delivery_type($this->get('settings')->get_available_mode());



		$delivery_cost = $this->get_delivery_cost($cart_total, true);



		View::get_instance()->render_html('../admin/checkout/delivery-row', array('delivery_cost' => $delivery_cost));

	}



	/**

	 * Save curent delivery type

	 *

	 * @param $type

	 */

	public function set_delivery_type($type) {

		Session::get_instance()->set('mpde_delivery_type', $type);

	}



	/**

	 * Delivery cost in success page

	 *

	 * @param $order

	 */

	public function success_page_delivery($order) {

		$order_data = get_post_meta($order->ID, 'mpde_delivery', true);

		$delivery_cost = 0.00;

		if (!empty($order_data)) {



			if (isset ($order_data['delivery_cost'])) {

				$delivery_cost = is_numeric($order_data['delivery_cost']) ? html_entity_decode(mprm_currency_filter(mprm_format_amount($order_data['delivery_cost']))) : $order_data['delivery_cost'];

			} else {

				$delivery_cost = __('Free', 'mprm-delivery');

			}

		}

		View::get_instance()->render_html('../admin/success-page/delivery_cost', array('delivery_cost' => $delivery_cost));

	}



	/**

	 * Widget delivery cost

	 *

	 * @param $subtotal

	 */

	public function widget_delivery_cost($subtotal) {



		if (empty($subtotal)) {

			$subtotal = mprm_get_cart_subtotal();

		}

		$delivery_cost = $this->get_delivery_cost($subtotal, true, $this->get('settings')->get_available_mode());



		View::get_instance()->render_html('../admin/widget-delivery', array('delivery_cost' => $delivery_cost));

	}



	/**

	 * Add delivery info in order

	 *

	 * @param $payment_id

	 *

	 * @return bool|int

	 */

	public function mprm_updated_edited_purchase($payment_id) {

		global $action;

		if (empty($payment_id)) {

			return false;

		}

		$post = get_post($payment_id);



		if ($post->post_type === Core::get_instance()->post_types['order']) {

			$order = new Order($payment_id);

			$delivery_mode = esc_attr($_POST['delivery-mode']);

			$order_meta = $_POST[$delivery_mode];



			if ($action !== 'editpost') {

				

				if ($order_meta['time-mode'] == 'asap') {

					$min_time_interval = (int)mprm_get_option('delivery_min_time_interval', 0);

					$post_time = strtotime("+{$min_time_interval} minutes", get_post_time( 'U', false, $post) );

					$order_meta['order-hours'] = date('H', $post_time);

					$order_meta['order-minutes'] = date('i', $post_time);

				}

				

				if ($delivery_mode !== 'collection') {

					$order_meta['delivery_cost'] = $this->get_delivery_cost($order->subtotal);

					if (is_numeric($order_meta['delivery_cost'])) {

						$delivery_tax = $this->get_delivery_tax();

						$order->tax = Taxes::get_instance()->calculate_tax($order->subtotal) + $delivery_tax;

					}



				} else {

					$order_meta['delivery_cost'] = 0;

					$order->tax = Taxes::get_instance()->calculate_tax($order->subtotal);

				}



				$order->total = $order->subtotal + $order_meta['delivery_cost'] + $order->tax;



				$order->update_meta('_mprm_order_total', $order->total);

				$order->update_meta('_mprm_order_tax', $order->tax);

				$order->save();



			} else {

				$order_meta['delivery_cost'] = esc_html($_POST['mprm-order-delivery-cost']);

			}



			$order_meta['delivery_mode'] = $delivery_mode;



			return update_post_meta($payment_id, 'mpde_delivery', $order_meta);

		} else {

			return false;

		}

	}



	/**

	 * Get Delivery tax

	 *

	 * @return int

	 */

	public function get_delivery_tax() {

		$delivery_tax = 0;



		if (Taxes::get_instance()->use_taxes() && mprm_get_option('delivery_taxable', false)) {

			$subtotal = mprm_get_cart_subtotal();

			$delivery_cost = $this->get_delivery_cost($subtotal);



			$delivery_tax = Taxes::get_instance()->calculate_tax($delivery_cost);

		}



		return $delivery_tax;

	}



	/**

	 * Add response for change quantity items in cart

	 *

	 * @param $data

	 *

	 * @return array

	 */

	public function add_delivery_response($data) {

		$subtotal = mprm_get_cart_subtotal();

		if (is_array($data)) {

			$delivery_cost = $this->get_delivery_cost($subtotal);

			$data['delivery_cost'] = is_numeric($delivery_cost) ? html_entity_decode(mprm_currency_filter(mprm_format_amount($delivery_cost))) : $delivery_cost;

		}

		return $data;

	}



	/**

	 * Cart total with delivery cost

	 *

	 * @param $cart_total

	 *

	 * @return int|string

	 */

	public function change_cart_total($cart_total) {



		$subtotal = mprm_get_cart_subtotal();

		$delivery_cost = $this->get_delivery_cost($subtotal);

		if (is_numeric($delivery_cost)) {

			if ($cart_total > 0) {

				$cart_total += $delivery_cost;

			}

		}

		return $cart_total;

	}



	/**

	 * Change cart tax

	 *

	 * @param $tax

	 *

	 * @return int|string

	 */

	public function change_cart_tax($tax) {

		$delivery_tax = $this->get_delivery_tax();

		$action = empty($_POST['action']) ? false : $_POST['action'];



		if (is_numeric($delivery_tax) && ($action !== 'editpost')) {

			if ($tax > 0) {

				$tax += $delivery_tax;

			}

		}

		return $tax;

	}

}