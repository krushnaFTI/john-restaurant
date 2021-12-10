<?php
namespace mp_restaurant_menu\classes\models;
use mp_restaurant_menu\classes\Model;
use mp_restaurant_menu\classes\View;
/**
 * Class Order
 *
 * @package mp_restaurant_menu\classes\models
 */
final class Order extends Model {
	protected static $instance;
	public $ID = 0;
	protected $_ID = 0;
	protected $new = false;
	protected $number = '';
	protected $mode = 'live';
	protected $key = '';
	protected $total = 0.00;
	protected $subtotal = 0;
	protected $tax = 0;
	protected $fees = array();
	protected $fees_total = 0;
	protected $discounts = 'none';
	protected $date = '';
	protected $completed_date = '';
	protected $status = 'mprm-pending';
	protected $post_status = 'mprm-pending';
	protected $old_status = '';
	protected $status_nicename = '';
	protected $customer_id = null;
	protected $user_id = 0;
	protected $first_name = '';
	protected $last_name = '';
	protected $email = '';
	protected $address = array();
	protected $transaction_id = '';
	protected $menu_items = array();
	protected $ip = '';
	protected $customer_note = '';
	protected $shipping_address = '';
	protected $phone_number = '';
	protected $gateway = '';
	protected $currency = '';
	protected $cart_details = array();
	protected $has_unlimited_menu_items = false;
	protected $parent_payment = 0;
	private $user_info = array();
	private $payment_meta = array();
	private $pending;
	/**
	 * Order constructor.
	 *
	 * @param bool $payment_id
	 *
	 * @return mixed
	 */
	public function __construct($payment_id = false) {
		parent::__construct();
		if (empty($payment_id)) {
			return false;
		}
		$this->setup_payment($payment_id);
	}
	/**
	 * Setup payment by ID
	 *
	 * @param $payment_id
	 *
	 * @return bool
	 */
	public function setup_payment($payment_id) {
		$this->pending = array();
		if (empty($payment_id)) {
			return false;
		}
		$payment = get_post($payment_id);
		if (!$payment || is_wp_error($payment)) {
			return false;
		}
		if ('mprm_order' !== $payment->post_type) {
			return false;
		}
		// Allow extensions to perform actions before the payment is loaded
		do_action('mprm_pre_setup_payment', $this, $payment_id);
		// Primary Identifier
		$this->ID = absint($payment_id);
		// Protected ID that can never be changed
		$this->_ID = absint($payment_id);
		// We have a payment, get the generic payment_meta item to reduce calls to it
		$this->payment_meta = $this->get_meta();
		// Status and Dates
		$this->date = $payment->post_date;
		$this->completed_date = $this->setup_completed_date();
		$this->status = $payment->post_status;
		$this->post_status = $this->status;
		$this->mode = $this->setup_mode();
		$this->parent_payment = $payment->post_parent;
		$all_payment_statuses = $this->get('payments')->get_payment_statuses();
		$this->status_nicename = array_key_exists($this->status, $all_payment_statuses) ? $all_payment_statuses[$this->status] : ucfirst($this->status);
		// Items
		$this->fees = $this->setup_fees();
		$this->cart_details = $this->setup_cart_details();
		$this->menu_items = $this->setup_menu_items();
		// Currency Based
		$this->total = $this->setup_total();
		$this->tax = $this->setup_tax();
		$this->fees_total = $this->setup_fees_total();
		$this->subtotal = $this->setup_subtotal();
		$this->currency = $this->setup_currency();
		// Gateway based
		$this->gateway = $this->setup_gateway();
		$this->transaction_id = $this->setup_transaction_id();
		// User based
		$this->ip = $this->setup_ip();
		$this->customer_id = $this->setup_customer_id();
		$this->user_id = $this->setup_user_id();
		$this->email = $this->setup_email();
		$this->user_info = $this->setup_user_info();
		$this->address = $this->setup_address();
		$this->discounts = $this->user_info['discount'];
		$this->first_name = $this->user_info['first_name'];
		$this->last_name = $this->user_info['last_name'];
		//additional information
		$this->phone_number = $this->setup_phone_number();
		$this->shipping_address = $this->setup_shipping_address();
		$this->customer_note = $this->setup_customer_note();
		// Other Identifiers
		$this->key = $this->setup_payment_key();
		$this->number = $this->setup_payment_number();
		// Additional Attributes
		$this->has_unlimited_menu_items = $this->setup_has_unlimited();
		// Allow extensions to add items to this object via hook
		do_action('mprm_setup_payment', $this, $payment_id);
		return true;
	}
	/**
	 * Get order meta
	 *
	 * @param string $meta_key
	 * @param bool $single
	 *
	 * @return mixed
	 */
	public function get_meta($meta_key = '_mprm_order_meta', $single = true) {
		$meta = get_post_meta($this->ID, $meta_key, $single);
		if ( $meta_key === '_mprm_order_meta' && $meta && is_array($meta) ) {
			if (empty($meta['key'])) {
				$meta['key'] = $this->setup_payment_key();
			}
			if (empty($meta['email'])) {
				$meta['email'] = $this->setup_email();
			}
			if (empty($meta['date'])) {
				$meta['date'] = get_post_field('post_date', $this->ID);
			}
		}
		$meta = apply_filters('mprm_get_payment_meta_' . $meta_key, $meta, $this->ID);
		return apply_filters('mprm_get_payment_meta', $meta, $this->ID, $meta_key);
	}
	/**
	 * Setup payment key
	 *
	 * @return mixed
	 */
	private function setup_payment_key() {
		$key = $this->get_meta('_mprm_order_purchase_key', true);
		return $key;
	}
	/**
	 * @return mixed
	 */
	public function setup_email() {
		$email = $this->get_meta('_mprm_order_user_email', true);
		if (empty($email)) {
			$email = $this->get('customer')->get_column('email', $this->customer_id);
		}
		return $email;
	}
	/**
	 * @return bool|mixed
	 */
	private function setup_completed_date() {
		$payment = get_post($this->ID);
		if ('mprm-pending' == $payment->post_status || 'preapproved' == $payment->post_status) {
			return false; // This payment was never completed
		}
		$date = ($date = $this->get_meta('_mprm_completed_date', true)) ? $date : $payment->modified_date;
		return $date;
	}
	/**
	 * @return mixed
	 */
	private function setup_mode() {
		return $this->get_meta('_mprm_order_mode');
	}
	/**
	 * @return array
	 */
	private function setup_fees() {
		$payment_fees = isset($this->payment_meta['fees']) ? $this->payment_meta['fees'] : array();
		return $payment_fees;
	}
	/**
	 * @return array|mixed
	 */
	private function setup_cart_details() {
		$cart_details = isset($this->payment_meta['cart_details']) ? maybe_unserialize($this->payment_meta['cart_details']) : array();
		return $cart_details;
	}
	/**
	 * @return array|mixed
	 */
	private function setup_menu_items() {
		$menu_items = isset($this->payment_meta['menu_items']) ? maybe_unserialize($this->payment_meta['menu_items']) : array();
		return $menu_items;
	}
	/**
	 * Setup total
	 * @return mixed
	 */
	private function setup_total() {
		$amount = $this->get_meta('_mprm_order_total', true);
		if (empty($amount) && '0.00' != $amount) {
			$meta = $this->get_meta('_mprm_order_meta', true);
			$meta = maybe_unserialize($meta);
			if (isset($meta['amount'])) {
				$amount = $meta['amount'];
			}
		}
		return $amount;
	}
	/**
	 * @return int|mixed
	 */
	private function setup_tax() {
		$tax = $this->get_meta('_mprm_order_tax', true);
		// We don't have tax as it's own meta and no meta was passed
		if ('' === $tax) {
			$tax = isset($payment_meta['tax']) ? $payment_meta['tax'] : 0;
		}
		return $tax;
	}
	/**
	 * @return float
	 */
	private function setup_fees_total() {
		$fees_total = (float)0.00;
		$payment_fees = isset($this->payment_meta['fees']) ? $this->payment_meta['fees'] : array();
		if (!empty($payment_fees)) {
			foreach ($payment_fees as $fee) {
				$fees_total += (float)$fee['amount'];
			}
		}
		return $fees_total;
	}
	/**
	 * @return float|int
	 */
	private function setup_subtotal() {
		$subtotal = 0;
		$cart_details = $this->cart_details;
		if (is_array($cart_details)) {
			foreach ($cart_details as $item) {
				if (isset($item['subtotal'])) {
					$subtotal += $item['subtotal'];
				}
			}
		} else {
			$subtotal = $this->total;
			$tax = $this->get('taxes')->use_taxes() ? $this->tax : 0;
			$subtotal -= $tax;
		}
		return $subtotal;
	}
	/**
	 * @return mixed
	 */
	private function setup_currency() {
		$currency = isset($this->payment_meta['currency']) ? $this->payment_meta['currency'] : apply_filters('mprm_payment_currency_default', $this->get('settings')->get_currency(), $this);
		return $currency;
	}
	/**
	 * @return mixed
	 */
	private function setup_gateway() {
		$gateway = $this->get_meta('_mprm_order_gateway', true);
		return $gateway;
	}
	/**
	 * @return mixed
	 */
	private function setup_transaction_id() {
		$transaction_id = $this->get_meta('_mprm_order_transaction_id', true);
		if (empty($transaction_id) || (int)$transaction_id === (int)$this->ID) {
			$gateway = $this->gateway;
			$transaction_id = apply_filters('mprm_get_payment_transaction_id-' . $gateway, $this->ID);
		}
		return $transaction_id;
	}
	/**
	 * @return mixed
	 */
	private function setup_ip() {
		$ip = $this->get_meta('_mprm_order_user_ip', true);
		return $ip;
	}
	/**
	 * @return mixed
	 */
	private function setup_customer_id() {
		$customer_id = $this->get_meta('_mprm_order_customer_id', true);
		return $customer_id;
	}
	/**
	 * @return mixed
	 */
	private function setup_user_id() {
		$user_id = $this->get_meta('_mprm_order_user_id', true);
		return $user_id;
	}
	/**
	 * Setup user
	 * @return array|mixed
	 */
	private function setup_user_info() {
		$defaults = array(
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'discount' => $this->discounts,
		);
		$user_info = isset($this->payment_meta['user_info']) ? maybe_unserialize($this->payment_meta['user_info']) : array();
		$user_info = wp_parse_args($user_info, $defaults);
		if (empty($user_info)) {
			// Get the customer, but only if it's been created
			$customer = new Customer(array('field' => 'id', 'value' => $this->customer_id));
			if ($customer->id > 0) {
				$name = explode(' ', $customer->name, 2);
				$user_info = array(
					'first_name' => $name[0],
					'last_name' => $name[1],
					'email' => $customer->email,
					'discount' => 'none',
				);
			}
		} else {
			// Get the customer, but only if it's been created
			$customer = new Customer(array('field' => 'id', 'value' => $this->customer_id));
			if ($customer->id > 0) {
				foreach ($user_info as $key => $value) {
					if (!empty($value)) {
						continue;
					}
					switch ($key) {
						case 'first_name':
							$name = explode(' ', $customer->name, 2);
							$user_info[$key] = $name[0];
							break;
						case 'last_name':
							$name = explode(' ', $customer->name, 2);
							$last_name = !empty($name[1]) ? $name[1] : '';
							$user_info[$key] = $last_name;
							break;
						case 'email':
							$user_info[$key] = $customer->email;
							break;
					}
				}
			}
		}
		return $user_info;
	}
	/**
	 * @return array
	 */
	private function setup_address() {
		$address = !empty($this->payment_meta['user_info']['address']) ? $this->payment_meta['user_info']['address'] : array('line1' => '', 'line2' => '', 'city' => '', 'country' => '', 'state' => '', 'zip' => '');
		return $address;
	}
	/**
	 * @return mixed
	 */
	private function setup_phone_number() {
		$phone_number = $this->get_meta('_mprm_order_phone_number', true);
		return $phone_number;
	}
	/**
	 * @return mixed
	 */
	private function setup_shipping_address() {
		$shipping_address = $this->get_meta('_mprm_order_shipping_address', true);
		return $shipping_address;
	}
	/**
	 * @return mixed
	 */
	private function setup_customer_note() {
		$customer_note = $this->get_meta('_mprm_order_customer_note', true);
		return $customer_note;
	}
	/**
	 * @return int|mixed
	 */
	private function setup_payment_number() {
		$number = $this->ID;
		if ($this->get('settings')->get_option('enable_sequential')) {
			$number = $this->get_meta('_mprm_order_number', true);
			if (!$number) {
				$number = $this->ID;
			}
		}
		return $number;
	}
	/**
	 * @return bool
	 */
	private function setup_has_unlimited() {
		$unlimited = (bool)$this->get_meta('_mprm_order_unlimited_menu_items', true);
		return $unlimited;
	}
	/**
	 * @return Order
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Magic method get
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function __get($key) {
		if (method_exists($this, 'get_' . $key)) {
			$value = call_user_func(array($this, 'get_' . $key));
		} else {
			$value = $this->$key;
		}
		return $value;
	}
	/**
	 * Magic method set
	 *
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$ignore = array('menu_items', 'cart_details', 'fees', '_ID');
		if ($key === 'status') {
			$this->old_status = $this->status;
		}
		if (!in_array($key, $ignore)) {
			$this->pending[$key] = $value;
		}
		if ('_ID' !== $key) {
			$this->$key = $value;
		}
	}
	/**
	 * Init metaboxes
	 */
	public function init_metaboxes() {
		//Side meta box
		add_meta_box(
			'update-order',
			__('Update Order', 'mp-restaurant-menu'),
			array($this, 'render_meta_box'),
			$this->get_post_type('order'),
			'side',
			'high',
			array('post_type' => $this->get_post_type('order'))
		);
		add_meta_box(
			'order-meta',
			__('Order Meta', 'mp-restaurant-menu'),
			array($this, 'render_meta_box'),
			$this->get_post_type('order'),
			'side',
			'high',
			array('post_type' => $this->get_post_type('order'))
		);
		add_meta_box(
			'order-log',
			__('Order logs', 'mp-restaurant-menu'),
			array($this, 'render_meta_box'),
			$this->get_post_type('order'),
			'side',
			'low',
			array('post_type' => $this->get_post_type('order'))
		);
		add_meta_box(
			'order-purchased',
			__('Purchased products', 'mp-restaurant-menu'),
			array($this, 'render_meta_box'),
			$this->get_post_type('order'),
			'advanced',
			'high',
			array('post_type' => $this->get_post_type('order'))
		);
		add_meta_box(
			'customer-notes',
			__('Order Notes', 'mp-restaurant-menu'),
			array($this, 'render_meta_box'),
			$this->get_post_type('order'),
			'advanced',
			'high',
			array('post_type' => $this->get_post_type('order'))
		);
		add_meta_box(
			'order-customer',
			__('Customer Details', 'mp-restaurant-menu'),
			array($this, 'render_meta_box'),
			$this->get_post_type('order'),
			'advanced',
			'low',
			array('post_type' => $this->get_post_type('order'))
		);
		add_meta_box(
			'order-delivery',
			__('Delivery details', 'mp-restaurant-menu'),
			array($this, 'render_meta_box'),
			$this->get_post_type('order'),
			'advanced',
			'low',
			array('post_type' => $this->get_post_type('order'))
		);
		add_meta_box(
			'order-billing-address',
			__('Billing Address', 'mp-restaurant-menu'),
			array($this, 'render_meta_box'),
			$this->get_post_type('order'),
			'advanced',
			'low',
			array('post_type' => $this->get_post_type('order'))
		);
		add_meta_box(
			'order-notes',
			__('Payment Notes', 'mp-restaurant-menu'),
			array($this, 'render_meta_box'),
			$this->get_post_type('order'),
			'advanced',
			'low',
			array('post_type' => $this->get_post_type('order'))
		);
	}
	/**
	 * Render meta box
	 *
	 * @param \WP_Post $post
	 * @param array $params
	 */
	public function render_meta_box(\WP_Post $post, array $params) {
		// add nonce field
		wp_nonce_field('mp-restaurant-menu' . '_nonce', 'mp-restaurant-menu' . '_nonce_box');
		// render Meta-box html
		$data['name'] = $params['id'];
		$data['title'] = $params['title'];
		$data['value'] = get_post_meta($post->ID, $params['id'], true);
		View::get_instance()->render_html("../admin/metaboxes/order/{$params['id']}", $data);
	}
	/**
	 * Columns Order
	 *
	 * @param array $existing_columns
	 *
	 * @return array
	 */
	public function order_columns(array $existing_columns) {
		$columns = array();
		$columns['cb'] = $existing_columns['cb'];
		$columns['order_title'] = __('Order', 'mp-restaurant-menu');
		$columns['order_status'] = __('Status', 'mp-restaurant-menu');
		$columns['order_ship_to'] = __('Delivery', 'mp-restaurant-menu');
		$columns['order_customer_note'] = __('Order notes', 'mp-restaurant-menu');
		$columns['order_items'] = __('Purchased', 'mp-restaurant-menu');
		$columns['order_total'] = __('Total', 'mp-restaurant-menu');
		$columns['order_date'] = __('Date', 'mp-restaurant-menu');
		$columns['order_actions'] = __('Actions', 'mp-restaurant-menu');
		$columns['order_invoice'] = __('Invoice', 'mp-restaurant-menu');
		return $columns;
	}
	/**
	 * Render columns order
	 *
	 * @param $column
	 *
	 * @return mixed
	 */
	public function render_order_columns($column) {
		global $post;
		$this->setup_payment($post->ID);
		switch ($column) {
			case 'order_status':
				$get_status = $this->get('payments')->get_payment_status($post);
				if($get_status != "Urgency"){
					?><mark class="order-status status-<?php echo $post->post_status; ?>"><span><?php
						echo $this->get('payments')->get_payment_status($post);
					?></span></mark><?php
				}else{
					?><mark class="order-status status-mprm-urgency"><span>Urgency</span></mark><?php
				}
				break;
			case  'order_title':
				$order_user = $this->get_user($post);
				if (!empty($order_user)) {
					$user_info = get_userdata($order_user);
				}
				if (!empty($user_info)) {
					if (empty($this->user_info)) {
						$username = '<a href="user-edit.php?user_id=' . absint($user_info->ID) . '">';
						if ($user_info->first_name || $user_info->last_name) {
							$username .= esc_html(sprintf(_x('%1$s %2$s', 'full name', 'mp-restaurant-menu'), ucfirst($user_info->first_name), ucfirst($user_info->last_name)));
						} else {
							$username .= esc_html(ucfirst($user_info->display_name));
						}
						$username .= '</a>';
					} else {
						$customer = $this->get('customer')->get_customer(array('field' => 'email', 'value' => $this->user_info['email']));
						if (!empty($customer)) {
							$username = '<a href="' . admin_url('edit.php?post_type=mp_menu_item&page=mprm-customers&s=' . $customer->id) . '">' . $this->user_info['first_name'] . ' ' . $this->user_info['last_name'] . ' </a><br/><a href="tel:' . $this->phone_number . '">' . $this->phone_number . '</a>';
						} else {
							$username = $this->user_info['first_name'] . ' ' . $this->user_info['last_name'] . '<br><a href="tel:' . $this->phone_number . '">' . $this->phone_number . '</a>';
						}
					}
				} else {
					if ($post->billing_first_name || $post->billing_last_name) {
						$username = trim(sprintf(_x('%1$s %2$s', 'full name', 'mp-restaurant-menu'), $post->billing_first_name, $post->billing_last_name));
					} else {
						$username = __('Guest', 'mp-restaurant-menu');
					}
				}
				printf(_x('%s by %s', 'Order number by X', 'mp-restaurant-menu'), '<a href="' . admin_url('post.php?post=' . absint($post->ID) . '&action=edit') . '" class="row-title"><strong>#' . esc_attr($this->get_order_number($post)) . '</strong></a>', $username);
				if ($post->billing_email) {
					echo '<small class="meta email"><a href="' . esc_url('mailto:' . $post->billing_email) . '">' . esc_html($post->billing_email) . '</a></small>';
				}
				break;
			case 'order_ship_to':
				echo apply_filters('mprm_orders_list_delivery', $this->shipping_address);
				break;
			case 'order_customer_note':
				echo empty( $this->customer_note ) ? '—' : '<span title="' . $this->customer_note . '">' . mprm_cut_str( 90, $this->customer_note ) . '</span>';
				break;
			case 'order_items' :
				echo apply_filters('mprm_admin_order_item_count', sprintf(_n('%d item', '%d items', count($this->menu_items), 'mp-restaurant-menu'), count($this->menu_items)), $this);
				break;
			case 'order_date' :
				$date = strtotime($this->date);
				$value = esc_html( human_time_diff( strtotime($this->date), current_time('timestamp') ) ) . ' ago';
				$value .= '<br/><span style="color:#999">' . date_i18n(get_option('date_format') . '  ' . get_option('time_format'), $date) . '</span>';
				echo $value;
				break;
			case 'order_total' :
				$total = mprm_currency_filter( mprm_format_amount( $this->total ) );
				$total .= '<br/><span style="color:#999">' . $this->get( 'gateways' )->get_gateway_admin_label( $this->gateway ) . '</span>';
				echo $total;
				break;
			case 'order_actions' :
				$this->render_order_actions($post);
				break;
			case 'order_invoice' :
				echo '<a id="hidden_order_id" href="'.site_url().'/wp-content/plugins/mp-restaurant-menu/download_invoice.php?data='.$post->ID.'">Invoice</a>';
				break;
			default:
				break;
		}
		return $column;
	}
	/**
	 * @param \WP_Post $post
	 *
	 * @return int
	 */
	public function get_user(\WP_Post $post) {
		return get_current_user_id();
	}
	/**
	 * @param \WP_Post $post
	 *
	 * @return int
	 */
	public function get_order_number(\WP_Post $post) {
		return $post->ID;
	}
	/**
	 * Get order items
	 *
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	public function get_order_items(\WP_Post $post) {
		$items = array();
		return $items;
	}
	/**
	 * @param \WP_Post $post
	 *
	 * @return int
	 */
	public function get_order_total(\WP_Post $post) {
		return 0;
	}
	/**
	 * @param \WP_Post $post
	 *
	 * @return int
	 */
	public function get_item_count(\WP_Post $post) {
		return 0;
	}
	/**
	 * @param $columns
	 *
	 * @return array
	 */
	public function order_sortable_columns($columns) {
		$custom = array(
			'order_title' => 'ID',
			'order_total' => 'order_total',
			'order_date' => 'date'
		);
		unset($columns['comments']);
		return wp_parse_args($custom, $columns);
	}
	/**
	 * @param $name
	 *
	 * @return bool|null
	 */
	public function __isset($name) {
		if (property_exists($this, $name)) {
			return false === empty($this->$name);
		} else {
			return null;
		}
	}
	/**
	 * @return array
	 */
	public function get_search_params() {
		$search_params = array(
			'_mprm_order_user_email',
			'_mprm_order_customer_id',
			'_mprm_order_total',
			'_mprm_completed_date'
		);
		return $search_params;
	}
	/**
	 * @param int $menu_item_id
	 * @param array $args
	 * @param array $options
	 *
	 * @return bool
	 */
	public function add_menu_item($menu_item_id = 0, $args = array(), $options = array()) {
		$menu_item_post = get_post($menu_item_id);
		if (!is_a($menu_item_post, 'WP_Post') || ($menu_item_post->post_type !== 'mp_menu_item')) {
			return false;
		}
		$menu_item = new Menu_item($menu_item_id);
		// Bail if this post isn't a menu_item
		if (!$menu_item) {
			return false;
		}
		// Set some defaults
		$defaults = array(
			'quantity' => 1,
			'price_id' => false,
			'item_price' => false,
			'discount' => 0,
			'tax' => 0.00,
			'fees' => array(),
		);
		$args = wp_parse_args(apply_filters('mprm_payment_add_menu_item_args', $args, $menu_item->get_ID()), $defaults);
		// Allow overriding the price
		if (false !== $args['item_price']) {
			$item_price = $args['item_price'];
		} else {
			// Deal with variable pricing
			if ($this->get('menu_item')->has_variable_prices($menu_item->get_ID())) {
				$prices = get_post_meta($menu_item->get_ID(), 'mprm_variable_prices', true);
				if ($args['price_id'] && array_key_exists($args['price_id'], (array)$prices)) {
					$item_price = $prices[$args['price_id']]['amount'];
				} else {
					$item_price = $this->get('menu_item')->get_price_option($menu_item->get_ID(), 'min');
					$args['price_id'] = $this->get('menu_item')->get_price_option($menu_item->get_ID(), 'max');
				}
			} else {
				$item_price = $this->get('menu_item')->get_price($menu_item->get_ID());
			}
		}
		// Sanitizing the price here so we don't have a dozen calls later
		$item_price = $this->get('formatting')->sanitize_amount($item_price);
		$quantity = $this->get('cart')->item_quantities_enabled() ? absint($args['quantity']) : 1;
		$amount = round($item_price * $quantity, $this->get('formatting')->currency_decimal_filter());
		// Setup the menu_items meta item
		$new_menu_item = array(
			'id' => $menu_item->get_ID(),
			'quantity' => $quantity,
		);
		$default_options = array(
			'quantity' => $quantity,
		);
		if (false !== $args['price_id']) {
			$default_options['price_id'] = (int)$args['price_id'];
		}
		$options = wp_parse_args($options, $default_options);
		$new_menu_item['options'] = $options;
		$this->menu_items[] = $new_menu_item;
		$discount = $args['discount'];
		$subtotal = $amount;
		$tax = $args['tax'];
		if ($this->get('taxes')->prices_include_tax()) {
			$subtotal -= round($tax, $this->get('formatting')->currency_decimal_filter());
		}
		$total = $subtotal - $discount + $tax;
		// Do not allow totals to go negative
		if ($total < 0) {
			$total = 0;
		}
		// Silly item_number array
		$item_number = array(
			'id' => $menu_item->get_ID(),
			'quantity' => $quantity,
			'options' => $options,
		);
		$this->cart_details[] = array(
			'name' => $menu_item->post_title,
			'id' => $menu_item->get_ID(),
			'item_number' => $item_number,
			'item_price' => round($item_price, $this->get('formatting')->currency_decimal_filter()),
			'quantity' => $quantity,
			'discount' => $discount,
			'subtotal' => round($subtotal, $this->get('formatting')->currency_decimal_filter()),
			'tax' => round($tax, $this->get('formatting')->currency_decimal_filter()),
			'fees' => $args['fees'],
			'price' => round($total, $this->get('formatting')->currency_decimal_filter()),
		);
		$added_menu_item = end($this->cart_details);
		$added_menu_item['action'] = 'add';
		$this->pending['menu_items'][] = $added_menu_item;
		reset($this->cart_details);
		$this->increase_subtotal($subtotal - $discount);
		$this->increase_tax($tax);
		return true;
	}
	/**
	 * @param float $amount
	 */
	private function increase_subtotal($amount = 0.00) {
		$amount = (float)$amount;
		$this->subtotal += $amount;
		$this->recalculate_total();
	}
	/**
	 * Recalculate total
	 */
	private function recalculate_total() {
		$this->total = $this->subtotal + $this->tax + $this->fees_total;
	}
	/**
	 * @param float $amount
	 */
	public function increase_tax($amount = 0.00) {
		$amount = (float)$amount;
		$this->tax += $amount;
		$this->recalculate_total();
	}
	/**
	 * @param $menu_item_id
	 * @param array $args
	 *
	 * @return bool
	 */
	public function remove_menu_item($menu_item_id, $args = array()) {
		// Set some defaults
		$defaults = array(
			'quantity' => 1,
			'item_price' => false,
			'price_id' => false,
			'cart_index' => false,
		);
		$args = wp_parse_args($args, $defaults);
		// Bail if this post isn't a menu_item
		if (get_post_type($menu_item_id) != $this->post_types['menu_item']) {
			return false;
		}
		$menu_item = new Menu_item($menu_item_id);
		if (!$menu_item) {
			return false;
		}
		$this->check_remove_args($menu_item_id, $args);
		if (false === $args['cart_index']) {
			$found_cart_key = $this->search_cart_key($menu_item_id, $args);
		} else {
			$found_cart_key = $this->check_cart_index(absint($args['cart_index']), $menu_item_id);
		}
		// exit if cart key false
		if ($found_cart_key === false) {
			return false;
		}
		$this->apply_remove_args($menu_item_id, $args, $found_cart_key);
		return true;
	}
	/**
	 * Check remove args
	 *
	 * @param $menu_item_id
	 * @param $args
	 */
	public function check_remove_args($menu_item_id, $args) {
		foreach ($this->menu_items as $key => $item) {
			if ($menu_item_id != $item['id']) {
				continue;
			}
			if (false !== $args['price_id']) {
				if (isset($item['options']['price_id']) && $args['price_id'] != $item['options']['price_id']) {
					continue;
				}
			} elseif (false !== $args['cart_index']) {
				$cart_index = absint($args['cart_index']);
				$cart_item = !empty($this->cart_details[$cart_index]) ? $this->cart_details[$cart_index] : false;
				if (!empty($cart_item)) {
					// If the cart index item isn't the same item ID, don't remove it
					if ($cart_item['id'] != $item['id']) {
						continue;
					}
					// If this item has a price ID, make sure it matches the cart indexed item's price ID before removing
					if (isset($item['options']['price_id']) && $item['options']['price_id'] != $cart_item['item_number']['options']['price_id']) {
						continue;
					}
				}
			}
			$item_quantity = $this->menu_items[$key]['quantity'];
			if ($item_quantity > $args['quantity']) {
				$this->menu_items[$key]['quantity'] -= $args['quantity'];
				break;
			} else {
				unset($this->menu_items[$key]);
				break;
			}
		}
	}
	/**
	 * Search cart key
	 *
	 * @param $menu_item_id
	 * @param $args
	 *
	 * @return int|string
	 */
	public function search_cart_key($menu_item_id, $args) {
		$found_cart_key = false;
		foreach ($this->cart_details as $cart_key => $item) {
			if ($menu_item_id != $item['id']) {
				continue;
			}
			if (false !== $args['price_id']) {
				if (isset($item['item_number']['options']['price_id']) && $args['price_id'] != $item['item_number']['options']['price_id']) {
					continue;
				}
			}
			if (false !== $args['item_price']) {
				if (isset($item['item_price']) && $args['item_price'] != $item['item_price']) {
					continue;
				}
			}
			$found_cart_key = $cart_key;
			break;
		}
		return $found_cart_key;
	}
	/**
	 * Check cart index exists
	 *
	 * @param $cart_index
	 * @param $item_id
	 *
	 * @return bool
	 */
	public function check_cart_index($cart_index, $item_id) {
		if (!array_key_exists($cart_index, $this->cart_details)) {
			return false; // Invalid cart index passed.
		}
		if ((int)$this->cart_details[$cart_index]['id'] !== (int)$item_id) {
			return false; // We still need the proper Menu item ID to be sure.
		}
		return $cart_index;
	}
	/**
	 * @param $item_id
	 * @param $args
	 * @param $found_cart_key
	 */
	public function apply_remove_args($item_id, $args, $found_cart_key) {
		$orig_quantity = $this->cart_details[$found_cart_key]['quantity'];
		if ($orig_quantity > $args['quantity']) {
			$this->cart_details[$found_cart_key]['quantity'] -= $args['quantity'];
			$item_price = $this->cart_details[$found_cart_key]['item_price'];
			$tax = $this->cart_details[$found_cart_key]['tax'];
			$discount = !empty($this->cart_details[$found_cart_key]['discount']) ? $this->cart_details[$found_cart_key]['discount'] : 0;
			// The total reduction equals the number removed * the item_price
			$total_reduced = round($item_price * $args['quantity'], $this->get('formatting')->currency_decimal_filter());
			$tax_reduced = round(($tax / $orig_quantity) * $args['quantity'], $this->get('formatting')->currency_decimal_filter());
			$new_quantity = $this->cart_details[$found_cart_key]['quantity'];
			$new_tax = $this->cart_details[$found_cart_key]['tax'] - $tax_reduced;
			$new_subtotal = $new_quantity * $item_price;
			$new_discount = 0;
			$new_total = 0;
			$this->cart_details[$found_cart_key]['subtotal'] = $new_subtotal;
			$this->cart_details[$found_cart_key]['discount'] = $new_discount;
			$this->cart_details[$found_cart_key]['tax'] = $new_tax;
			$this->cart_details[$found_cart_key]['price'] = $new_subtotal - $new_discount + $new_tax;
		} else {
			$total_reduced = $this->cart_details[$found_cart_key]['item_price'];
			$tax_reduced = $this->cart_details[$found_cart_key]['tax'];
			unset($this->cart_details[$found_cart_key]);
		}
		$pending_args = $args;
		$pending_args['id'] = $item_id;
		$pending_args['amount'] = $total_reduced;
		$pending_args['price_id'] = false !== $args['price_id'] ? $args['price_id'] : false;
		$pending_args['quantity'] = $args['quantity'];
		$pending_args['action'] = 'remove';
		$this->pending['menu_items'][] = $pending_args;
		$this->decrease_subtotal($total_reduced);
		$this->decrease_tax($tax_reduced);
	}
	/**
	 * @param float $amount
	 */
	private function decrease_subtotal($amount = 0.00) {
		$amount = (float)$amount;
		$this->subtotal -= $amount;
		if ($this->subtotal < 0) {
			$this->subtotal = 0;
		}
		$this->recalculate_total();
	}
	/**
	 * @param float $amount
	 */
	public function decrease_tax($amount = 0.00) {
		$amount = (float)$amount;
		$this->tax -= $amount;
		if ($this->tax < 0) {
			$this->tax = 0;
		}
		$this->recalculate_total();
	}
	/**
	 * Add a fee to a given payment
	 *
	 * @since  2.5
	 *
	 * @param  array $args Array of arguments for the fee to add
	 * @param bool $global
	 *
	 * @return bool If the fee was added
	 */
	public function add_fee($args, $global = true) {
		$default_args = array(
			'label' => '',
			'amount' => 0,
			'type' => 'fee',
			'id' => '',
			'no_tax' => false,
			'menu_item_id' => 0,
		);
		$fee = wp_parse_args($args, $default_args);
		$this->fees[] = $fee;
		$added_fee = $fee;
		$added_fee['action'] = 'add';
		$this->pending['fees'][] = $added_fee;
		reset($this->fees);
		$this->increase_fees($fee['amount']);
		return true;
	}
	/**
	 * @param float $amount
	 */
	private function increase_fees($amount = 0.00) {
		$amount = (float)$amount;
		$this->fees_total += $amount;
		$this->recalculate_total();
	}
	/**
	 * Remove a fee from the payment
	 *
	 * @since  2.5
	 *
	 * @param  int $key The array key index to remove
	 *
	 * @return bool     If the fee was removed successfully
	 */
	public function remove_fee($key) {
		$removed = false;
		if (is_numeric($key)) {
			$removed = $this->remove_fee_by('index', $key);
		}
		return $removed;
	}
	/**
	 * @param $key
	 * @param $value
	 * @param bool $global
	 *
	 * @return bool
	 */
	public function remove_fee_by($key, $value, $global = false) {
		$allowed_fee_keys = apply_filters('mprm_payment_fee_keys', array(
			'index', 'label', 'amount', 'type',
		));
		if (!in_array($key, $allowed_fee_keys)) {
			return false;
		}
		$removed = false;
		if ('index' === $key && array_key_exists($value, $this->fees)) {
			$removed_fee = $this->fees[$value];
			$removed_fee['action'] = 'remove';
			$this->pending['fees'][] = $removed_fee;
			$this->decrease_fees($removed_fee['amount']);
			unset($this->fees[$value]);
			$removed = true;
		} else if ('index' !== $key) {
			foreach ($this->fees as $index => $fee) {
				if (isset($fee[$key]) && $fee[$key] == $value) {
					$removed_fee = $fee;
					$removed_fee['action'] = 'remove';
					$this->pending['fees'][] = $removed_fee;
					$this->decrease_fees($removed_fee['amount']);
					unset($this->fees[$index]);
					$removed = true;
					if (false === $global) {
						break;
					}
				}
			}
		}
		if (true === $removed) {
			$this->fees = array_values($this->fees);
		}
		return $removed;
	}
	/**
	 * @param float $amount
	 */
	private function decrease_fees($amount = 0.00) {
		$amount = (float)$amount;
		$this->fees_total -= $amount;
		if ($this->fees_total < 0) {
			$this->fees_total = 0;
		}
		$this->recalculate_total();
	}
	/**
	 * @param string $type
	 *
	 * @return mixed
	 */
	public function get_fees($type = 'all') {
		$fees = array();
		if (!empty($this->fees) && is_array($this->fees)) {
			foreach ($this->fees as $fee_id => $fee) {
				if ('all' != $type && !empty($fee['type']) && $type != $fee['type']) {
					continue;
				}
				$fee['id'] = $fee_id;
				$fees[] = $fee;
			}
		}
		return apply_filters('mprm_get_payment_fees', $fees, $this->ID, $this);
	}
	/**
	 * @param bool $note
	 *
	 * @return bool
	 */
	public function add_note($note = false) {
		// Bail if no note specified
		if (!$note) {
			return false;
		}
		$this->get('payments')->insert_payment_note($this->ID, $note);
	}
	/**
	 * Refund process
	 */
	public function refund() {
		$this->old_status = $this->status;
		$this->status = 'mprm-refunded';
		$this->pending['status'] = $this->status;
		$this->save();
	}
	/**
	 * @return bool
	 */
	public function save() {
		
		$saved = false;
		if (empty($this->ID)) {
			$payment_id = $this->insert_payment();
			if (false === $payment_id) {
				$saved = false;
			} else {
				$this->ID = $payment_id;
			}
		}
		if ($this->ID !== $this->_ID) {
			$this->ID = $this->_ID;
		}
		// If we have something pending, let's save it
		if (!empty($this->pending)) {
			$total_increase = 0;
			$total_decrease = 0;
			
			$webpushr_id_value = $_POST['webpushr_id_value'];
			//echo $webpushr_id_value;exit;
			$this->update_meta('webpushr_id', $webpushr_id_value);
			
			foreach ($this->pending as $key => $value) {
				switch ($key) {
					case 'menu_items':
						// Update totals for pending menu_items
						foreach ($this->pending[$key] as $item) {
							switch ($item['action']) {
								case 'add':
									$price = $item['price'];
									$taxes = $item['tax'];
									if ('publish' === $this->status || 'mprm-complete' === $this->status || 'mprm-revoked' === $this->status) {
										// Add sales logs
										$log_date = date_i18n('Y-m-d G:i:s', current_time('timestamp'));
										$price_id = isset($item['item_number']['options']['price_id']) ? $item['item_number']['options']['price_id'] : 0;
										$y = 0;
//										while ($y < $item['quantity']) {
//											mprm_record_sale_in_log($item['id'], $this->ID, $price_id, $log_date);
//											$y++;
//										}
										$menu_item = new Menu_item($item['id']);
										$menu_item->increase_sales($item['quantity']);
										$menu_item->increase_earnings($price);
										$total_increase += $price;
									}
									break;
								case 'remove':
									$log_args = array(
										'post_type' => 'mprm_log',
										'post_parent' => $item['id'],
										'numberposts' => $item['quantity'],
										'meta_query' => array(
											array(
												'key' => '_mprm_log_payment_id',
												'value' => $this->ID,
												'compare' => '=',
											),
											array(
												'key' => '_mprm_log_price_id',
												'value' => $item['price_id'],
												'compare' => '='
											)
										)
									);
									$found_logs = get_posts($log_args);
									foreach ($found_logs as $log) {
										wp_delete_post($log->ID, true);
									}
									if ('publish' === $this->status || 'mprm-complete' === $this->status || 'mprm-revoked' === $this->status) {
										$menu_item = new Menu_item($item['id']);
										$menu_item->decrease_sales($item['quantity']);
										$menu_item->decrease_earnings($item['amount']);
										$total_decrease += $item['amount'];
									}
									break;
							}
						}
						break;
					case 'fees':
						if ('publish' !== $this->status && 'mprm-complete' !== $this->status && 'mprm-revoked' !== $this->status) {
							break;
						}
						if (empty($this->pending[$key])) {
							break;
						}
						foreach ($this->pending[$key] as $fee) {
							switch ($fee['action']) {
								case 'add':
									$total_increase += $fee['amount'];
									break;
								case 'remove':
									$total_decrease += $fee['amount'];
									break;
							}
						}
						break;
					case 'status':
						$this->update_status($this->status);
						break;
					case 'gateway':
						$this->update_meta('_mprm_order_gateway', $this->gateway);
						break;
					case 'mode':
						$this->update_meta('_mprm_order_mode', $this->mode);
						break;
					case 'transaction_id':
						$this->update_meta('_mprm_order_transaction_id', $this->transaction_id);
						break;
					case 'ip':
						$this->update_meta('_mprm_order_user_ip', $this->ip);
						break;
					case 'customer_id':
						$this->update_meta('_mprm_order_customer_id', $this->customer_id);
						break;
					case 'user_id':
						$this->update_meta('_mprm_order_user_id', $this->user_id);
						break;
					case 'first_name':
						$this->user_info['first_name'] = $this->first_name;
						break;
					case 'last_name':
						$this->user_info['last_name'] = $this->last_name;
						break;
					case 'discounts':
						if (!is_array($this->discounts)) {
							$this->discounts = explode(',', $this->discounts);
						}
						$this->user_info['discount'] = implode(',', $this->discounts);
						break;
					case 'address':
						$this->user_info['address'] = $this->address;
						break;
					case 'email':
						$this->update_meta('_mprm_order_user_email', $this->email);
						break;
					case 'key':
						$this->update_meta('_mprm_order_purchase_key', $this->key);
						break;
					case 'number':
						$this->update_meta('_mprm_order_number', $this->number);
						break;
					case 'customer_note':
						$this->update_meta('_mprm_order_customer_note', $this->customer_note);
						break;
					case 'shipping_address':
						$this->update_meta('_mprm_order_shipping_address', $this->shipping_address);
						break;
					case 'phone_number':
						$this->update_meta('_mprm_order_phone_number', $this->phone_number);
						break;
					case 'date':
						$args = array(
							'ID' => $this->ID,
							'post_date' => $this->date,
							'edit_date' => true,
						);
						wp_update_post($args);
						break;
					case 'completed_date':
						$this->update_meta('_mprm_completed_date', $this->completed_date);
						break;
					case 'has_unlimited_menu_items':
						$this->update_meta('_mprm_order_unlimited_menu_items', $this->has_unlimited_menu_items);
						break;
					case 'parent_payment':
						$args = array(
							'ID' => $this->ID,
							'post_parent' => $this->parent_payment,
						);
						wp_update_post($args);
						break;
					default:
						do_action('mprm_order_save', $this, $key);
						break;
				}
			}
			if ('mprm-pending' !== $this->status) {
				$customer = new Customer(array('field' => 'id', 'value' => $this->customer_id));
				$total_change = $total_increase - $total_decrease;
				if ($total_change < 0) {
					$total_change = -($total_change);
					// Decrease the customer's purchase stats
					$customer->decrease_value($total_change);
					$this->get('payments')->decrease_total_earnings($total_change);
				} else if ($total_change > 0) {
					// Increase the customer's purchase stats
					$customer->increase_value($total_change);
					$this->get('payments')->increase_total_earnings($total_change);
				}
			}
			$this->update_meta('_mprm_order_total', $this->total);
			$this->update_meta('_mprm_order_tax', $this->tax);
			$this->menu_items = array_values($this->menu_items);
			$new_meta = array(
				'menu_items' => $this->menu_items,
				'cart_details' => $this->cart_details,
				'fees' => $this->fees,
				'currency' => $this->currency,
				'user_info' => $this->user_info,
			);
			$meta = $this->get_meta();
			$merged_meta = array_merge($meta, $new_meta);
			// Only save the payment meta if it's changed
			if (md5(serialize($meta)) !== md5(serialize($merged_meta))) {
				$updated = $this->update_meta('_mprm_order_meta', $merged_meta);
				if (false !== $updated) {
					$saved = true;
				}
			}
			$this->pending = array();
			$saved = true;
		}
		if (true === $saved) {
			$this->setup_payment($this->ID);
		}
		return $saved;
	}
	/**
	 * Insert o
	 *
	 * @param array $payment_data
	 *
	 * @return int|\WP_Error
	 */
	public function insert_payment($payment_data = array()) {
		
		$get_delivery_address = $_POST['delivery']['delivery_street'];
		// Construct the payment title
		$phone_number = $_POST['phone_number'];
		if($_POST['delivery-mode'] == 'delivery'){
			if(!empty($phone_number) && !empty($get_delivery_address)){
				$get_settings_option = get_option('mprm_settings');
				$get_postmates_key = $get_settings_option['postmates_sandbox_key_text'];
				$get_postmates_customer_id = $get_settings_option['postmates_customer_id_text'];
				
				if(!empty($get_postmates_key) && !empty($get_postmates_customer_id)){
				
					$admin_user_id = '1';
					$get_admin_phone_number = get_user_meta( $admin_user_id, 'admin_phone_number');
					$get_admin_business_address = get_user_meta( $admin_user_id, 'admin_business_address');
					$get_admin_nickname = get_user_meta( $admin_user_id, 'nickname');

					$url1 = "https://api.postmates.com/v1/customers/".$get_postmates_customer_id."/deliveries";
					$uname = $get_postmates_key;
					$pwd = "";

					$create_delivery_data = "dropoff_address=".$get_delivery_address."&dropoff_name=".$this->first_name."&dropoff_phone_number=".$phone_number."&manifest=cardboard box&manifest_items=[]&pickup_address=".$get_admin_business_address[0]."&pickup_name=".$get_admin_nickname[0]."&pickup_phone_number=".$get_admin_phone_number[0];

					$ch_url = curl_init($url1);
					curl_setopt($ch_url, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Accept: application/json'));
					curl_setopt($ch_url, CURLOPT_USERPWD, $uname . ":" . $pwd);
					curl_setopt($ch_url, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch_url, CURLOPT_POST, 1);
					curl_setopt($ch_url, CURLOPT_POSTFIELDS, $create_delivery_data);
					curl_setopt($ch_url, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch_url, CURLOPT_SSL_VERIFYPEER, false);
					$create_del_return = curl_exec($ch_url);
					curl_close($ch_url);
					
					$created_delivery_data = json_decode($create_del_return);
					
					if($created_delivery_data->kind == 'error'){
						$phone_not_valid = $created_delivery_data->params->dropoff_phone_number;
						$phone_not_valid = rtrim($phone_not_valid, '.');
						$address_not_valid = $created_delivery_data->code;
						if(empty($address_not_valid) && !empty($phone_not_valid)){
							wp_redirect( site_url().'/checkout/?error='.$phone_not_valid );
						}
						if(!empty($address_not_valid) && empty($phone_not_valid)){
							wp_redirect( site_url().'/checkout/?error2='.$address_not_valid );
						}
						if(!empty($phone_not_valid) && !empty($address_not_valid)){
							wp_redirect( site_url().'/checkout/?error='.$phone_not_valid.'&error2='.$address_not_valid );
						}
					}
					$delivery_id = $created_delivery_data->id;
				}
			}
		}
		
		$payment_title = '';
		if (!empty($this->first_name) && !empty($this->last_name)) {
			$payment_title = $this->first_name . ' ' . $this->last_name;
		} else if (!empty($this->first_name) && empty($this->last_name)) {
			$payment_title = $this->first_name;
		} else if (!empty($this->email) && is_email($this->email)) {
			$payment_title = $this->email;
		}
		if (empty($this->key)) {
			$auth_key = defined('AUTH_KEY') ? AUTH_KEY : '';
			$this->key = strtolower(md5($this->email . date('Y-m-d H:i:s') . $auth_key . uniqid('mprm', true)));  // Unique key
			$this->pending['key'] = $this->key;
		}
		if (empty($this->ip)) {
			$this->ip = $this->get_ip();
			$this->pending['ip'] = $this->ip;
		}
		$payment_data = array(
			'price' => $this->total,
			'date' => $this->date,
			'user_email' => $this->email,
			'purchase_key' => $this->key,
			'currency' => $this->currency,
			'menu_items' => $this->menu_items,
			'user_info' => array(
				'id' => $this->user_id,
				'email' => $this->email,
				'first_name' => $this->first_name,
				'last_name' => $this->last_name,
				'discount' => $this->discounts,
				'address' => $this->address,
			),
			'cart_details' => $this->cart_details,
			'status' => $this->status,
			'fees' => $this->fees,
		);
		$args = apply_filters('mprm_insert_payment_args', array(
			'post_title' => $payment_title,
			'post_status' => $this->status,
			'post_type' => 'mprm_order',
			'post_date' => !empty($this->date) ? $this->date : null,
			'post_date_gmt' => !empty($this->date) ? get_gmt_from_date($this->date) : null,
			'post_parent' => $this->parent_payment,
		), $payment_data);

		// Create a blank payment
		$payment_id = wp_insert_post($args);
		
		if (!empty($payment_id)) {
			
			$this->ID = $payment_id;
			$this->_ID = $payment_id;
			$customer = new \stdClass;
			if (did_action('mprm_pre_process_purchase') && is_user_logged_in()) {
				$customer = new Customer(array('field' => 'user_id', 'value' => get_current_user_id()));
			}
			if (empty($customer->id)) {
				$customer = new Customer(array('field' => 'email', 'value' => $this->email));
			}
			if (empty($customer->id)) {
				$customer_data = array(
					'name' => !is_email($payment_title) ? $this->first_name . ' ' . $this->last_name : '',
					'email' => $this->email,
					'user_id' => $this->user_id,
					'phone' => $this->phone_number
				);
				$customer->create($customer_data);
			}
			$this->customer_id = $customer->id;
			$this->pending['customer_id'] = $this->customer_id;
			$customer->attach_payment($this->ID, false);
			$this->payment_meta = apply_filters('mprm_payment_meta', $this->payment_meta, $payment_data);
			if (!empty($this->payment_meta['fees'])) {
				$this->fees = array_merge($this->fees, $this->payment_meta['fees']);
				foreach ($this->fees as $fee) {
					$this->increase_fees($fee['amount']);
				}
			}
			$this->update_meta('_mprm_order_meta', $this->payment_meta);
			if($_POST['delivery-mode'] == 'delivery'){
				$this->update_meta('postmates_delivery_id', $delivery_id);
			}
			$this->new = true;
		}
		return $this->ID;
	}
	/**
	 * @return mixed
	 */
	private function get_ip() {
		return apply_filters('mprm_payment_user_ip', $this->ip, $this->ID, $this);
	}
	/**
	 * @param string $meta_key
	 * @param string $meta_value
	 * @param string $prev_value
	 *
	 * @return bool|int
	 */
	public function update_meta($meta_key = '', $meta_value = '', $prev_value = '') {
		if (empty($meta_key)) {
			return false;
		}
		if ($meta_key == 'key' || $meta_key == 'date') {
			$current_meta = $this->get_meta();
			$current_meta[$meta_key] = $meta_value;
			$meta_key = '_mprm_order_meta';
			$meta_value = $current_meta;
		} else if ($meta_key == 'email' || $meta_key == '_mprm_order_user_email') {
			$meta_value = apply_filters('mprm_mprm_update_payment_meta_' . $meta_key, $meta_value, $this->ID);
			update_post_meta($this->ID, '_mprm_order_user_email', $meta_value);
			$current_meta = $this->get_meta();
			$current_meta['user_info']['email'] = $meta_value;
			$meta_key = '_mprm_order_meta';
			$meta_value = $current_meta;
		}
		$meta_value = apply_filters('mprm_update_payment_meta_' . $meta_key, $meta_value, $this->ID);
		return update_post_meta($this->ID, $meta_key, $meta_value, $prev_value);
	}
	/**
	 * Update post status
	 *
	 * @param bool $status
	 *
	 * @return bool|int|\WP_Error
	 */
	public function update_status($status = false) {
		if ($status == 'mprm-completed' || $status == 'mprm-complete') {
			$status = 'publish';
		}
		$old_status = !empty($this->old_status) ? $this->old_status : false;
		if ($old_status === $status) {
			return false; // Don't permit status changes that aren't changes
		}
		$do_change = apply_filters('mprm_should_update_payment_status', true, $this->ID, $status, $old_status);
		$updated = false;
		if ($do_change) {
			do_action('mprm_before_payment_status_change', $this->ID, $status, $old_status);
			$update_fields = array('ID' => $this->ID, 'post_status' => $status, 'edit_date' => current_time('mysql'));
			$updated = wp_update_post(apply_filters('mprm_update_payment_status_fields', $update_fields));
			$all_payment_statuses = $this->get('payments')->get_payment_statuses();
			$this->status_nicename = array_key_exists($status, $all_payment_statuses) ? $all_payment_statuses[$status] : ucfirst($status);
			// Process any specific status functions
			switch ($status) {
				case 'mprm-refunded':
					$this->process_refund();
					break;
				case 'mprm-failed':
					$this->process_failure();
					break;
				case 'mprm-pending':
					$this->process_pending();
					break;
			}
			do_action('mprm_update_payment_status', $this->ID, $status, $old_status);
		}
		return $updated;
	}
	/**
	 * Process refund
	 */
	private function process_refund() {
		$process_refund = true;
		// If the payment was not in publish or mprm-revoked status, don't decrement stats as they were never incremented
		if (('publish' != $this->old_status && 'mprm-revoked' != $this->old_status) || 'mprm-refunded' != $this->status) {
			$process_refund = false;
		}
		// Allow extensions to filter for their own payment types, Example: Recurring Payments
		$process_refund = apply_filters('mprm_should_process_refund', $process_refund, $this);
		if (false === $process_refund) {
			return;
		}
		do_action('mprm_pre_refund_payment', $this);
		$decrease_store_earnings = apply_filters('mprm_decrease_store_earnings_on_refund', true, $this);
		$decrease_customer_value = apply_filters('mprm_decrease_customer_value_on_refund', true, $this);
		$decrease_purchase_count = apply_filters('mprm_decrease_customer_purchase_count_on_refund', true, $this);
		$this->maybe_alter_stats($decrease_store_earnings, $decrease_customer_value, $decrease_purchase_count);
		// Clear the This Month earnings (this_monththis_month is NOT a typo)
		delete_transient(md5('mprm_earnings_this_monththis_month'));
		do_action('mprm_post_refund_payment', $this);
	}
	/**
	 * @param $alter_store_earnings
	 * @param $alter_customer_value
	 * @param $alter_customer_purchase_count
	 */
	private function maybe_alter_stats($alter_store_earnings, $alter_customer_value, $alter_customer_purchase_count) {
		$this->get('payments')->undo_purchase(false, $this->ID);
		// Decrease store earnings
		if (true === $alter_store_earnings) {
			$this->get('payments')->decrease_total_earnings($this->total);
		}
		// Decrement the stats for the customer
		if (!empty($this->customer_id)) {
			$customer = new Customer(array('field' => 'id', 'value' => $this->customer_id));
			if (true === $alter_customer_value) {
				$customer->decrease_value($this->total);
			}
			if (true === $alter_customer_purchase_count) {
				$customer->decrease_purchase_count();
			}
		}
	}
	/**
	 * Process failure
	 */
	private function process_failure() {
		$discounts = $this->discounts;
		if ('none' === $discounts || empty($discounts)) {
			return;
		}
		if (!is_array($discounts)) {
			$discounts = array_map('trim', explode(',', $discounts));
		}
		foreach ($discounts as $discount) {
			$this->get('discount')->decrease_discount_usage($discount);
		}
	}
	/**
	 *  Process_pending
	 */
	private function process_pending() {
		$process_pending = true;
		// If the payment was not in publish or revoked status, don't decrement stats as they were never incremented
		if (('publish' != $this->old_status && 'mprm-revoked' != $this->old_status) || 'mprm-pending' != $this->status) {
			$process_pending = false;
		}
		// Allow extensions to filter for their own payment types
		$process_pending = apply_filters('mprm_should_process_pending', $process_pending, $this);
		if (false === $process_pending) {
			return;
		}
		$decrease_store_earnings = apply_filters('mprm_decrease_store_earnings_on_pending', true, $this);
		$decrease_customer_value = apply_filters('mprm_decrease_customer_value_on_pending', true, $this);
		$decrease_purchase_count = apply_filters('mprm_decrease_customer_purchase_count_on_pending', true, $this);
		$this->maybe_alter_stats($decrease_store_earnings, $decrease_customer_value, $decrease_purchase_count);
		$this->completed_date = false;
		$this->update_meta('_mprm_completed_date', '');
		// Clear the This Month earnings (this_monththis_month is NOT a typo)
		delete_transient(md5('mprm_earnings_this_monththis_month'));
	}
	/**
	 * @return array
	 */
	public function array_convert() {
		return get_object_vars($this);
	}
	/**
	 * @return array
	 */
	private function setup_discounts() {
		$discounts = !empty($this->payment_meta['user_info']['discount']) ? $this->payment_meta['user_info']['discount'] : array();
		return $discounts;
	}
	/**
	 * @return mixed
	 */
	private function get_cart_details() {
		return apply_filters('mprm_payment_cart_details', $this->cart_details, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_phone_number() {
		return apply_filters('mprm_payment_phone_number', $this->phone_number, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_shipping_address() {
		return apply_filters('mprm_payment_shipping_address', $this->shipping_address, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_customer_note() {
		return apply_filters('mprm_payment_customer_note', $this->customer_note, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_completed_date() {
		return apply_filters('mprm_payment_completed_date', $this->completed_date, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_tax() {
		return apply_filters('mprm_get_payment_tax', $this->tax, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_subtotal() {
		return apply_filters('mprm_get_payment_subtotal', $this->subtotal, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_total() {
		return apply_filters('mprm_get_payment_total', $this->total, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_discounts() {
		return apply_filters('mprm_payment_discounts', $this->discounts, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_currency() {
		return apply_filters('mprm_payment_currency_code', $this->currency, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_gateway() {
		return apply_filters('mprm_payment_gateway', $this->gateway, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_transaction_id() {
		return apply_filters('mprm_get_payment_transaction_id', $this->transaction_id, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_customer_id() {
		return apply_filters('mprm_payment_customer_id', $this->customer_id, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_user_id() {
		return apply_filters('mprm_payment_user_id', $this->user_id, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_email() {
		return apply_filters('mprm_payment_user_email', $this->email, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_user_info() {
		return apply_filters('mprm_payment_meta_user_info', $this->user_info, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_address() {
		return apply_filters('mprm_payment_address', $this->address, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_key() {
		return apply_filters('mprm_payment_key', $this->key, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_number() {
		return apply_filters('mprm_payment_number', $this->number, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_menu_items() {
		return apply_filters('mprm_payment_meta_menu_items', $this->menu_items, $this->ID, $this);
	}
	/**
	 * @return mixed
	 */
	private function get_unlimited() {
		return apply_filters('mprm_payment_unlimited_menu_items', $this->unlimited, $this->ID, $this);
	}
	private function render_order_actions($post) {
		$actions = array();
		// $get_post_meta_delivery = get_post_meta($post->ID, 'mpde_delivery', true);
		// $get_delivery_method = $get_post_meta_delivery['delivery_mode'];
		// if($get_delivery_method == "collection"){
			
		// }
		// if($get_delivery_method == "delivery"){
			if ( $this->status == 'publish' ) {
				$actions['mprm-shipping'] = array(
					'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=mprm_mark_order_status&status=mprm-shipping&order_id=' . $post->ID ), 'mprm-mark-order-status' ),
					'name'   => __( 'On the way', 'mp-restaurant-menu' ),
					'action' => 'mprm-shipping',
					'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="25" width="25" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
					<g>
						<g>
							<path d="M386.689,304.403c-35.587,0-64.538,28.951-64.538,64.538s28.951,64.538,64.538,64.538    c35.593,0,64.538-28.951,64.538-64.538S422.276,304.403,386.689,304.403z M386.689,401.21c-17.796,0-32.269-14.473-32.269-32.269    c0-17.796,14.473-32.269,32.269-32.269c17.796,0,32.269,14.473,32.269,32.269C418.958,386.738,404.485,401.21,386.689,401.21z"/>
						</g>
					</g>
					<g>
						<g>
							<path d="M166.185,304.403c-35.587,0-64.538,28.951-64.538,64.538s28.951,64.538,64.538,64.538s64.538-28.951,64.538-64.538    S201.772,304.403,166.185,304.403z M166.185,401.21c-17.796,0-32.269-14.473-32.269-32.269c0-17.796,14.473-32.269,32.269-32.269    c17.791,0,32.269,14.473,32.269,32.269C198.454,386.738,183.981,401.21,166.185,401.21z"/>
						</g>
					</g>
					<g>
						<g>
							<path d="M430.15,119.675c-2.743-5.448-8.32-8.885-14.419-8.885h-84.975v32.269h75.025l43.934,87.384l28.838-14.5L430.15,119.675z"/>
						</g>
					</g>
					<g>
						<g>
							<rect x="216.202" y="353.345" width="122.084" height="32.269"/>
						</g>
					</g>
					<g>
						<g>
							<path d="M117.781,353.345H61.849c-8.912,0-16.134,7.223-16.134,16.134c0,8.912,7.223,16.134,16.134,16.134h55.933    c8.912,0,16.134-7.223,16.134-16.134C133.916,360.567,126.693,353.345,117.781,353.345z"/>
						</g>
					</g>
					<g>
						<g>
							<path d="M508.612,254.709l-31.736-40.874c-3.049-3.937-7.755-6.239-12.741-6.239H346.891V94.655    c0-8.912-7.223-16.134-16.134-16.134H61.849c-8.912,0-16.134,7.223-16.134,16.134s7.223,16.134,16.134,16.134h252.773v112.941    c0,8.912,7.223,16.134,16.134,16.134h125.478l23.497,30.268v83.211h-44.639c-8.912,0-16.134,7.223-16.134,16.134    c0,8.912,7.223,16.134,16.134,16.134h60.773c8.912,0,16.134-7.223,16.135-16.134V264.605    C512,261.023,510.806,257.538,508.612,254.709z"/>
						</g>
					</g>
					<g>
						<g>
							<path d="M116.706,271.597H42.487c-8.912,0-16.134,7.223-16.134,16.134c0,8.912,7.223,16.134,16.134,16.134h74.218    c8.912,0,16.134-7.223,16.134-16.134C132.84,278.82,125.617,271.597,116.706,271.597z"/>
						</g>
					</g>
					<g>
						<g>
							<path d="M153.815,208.134H16.134C7.223,208.134,0,215.357,0,224.269s7.223,16.134,16.134,16.134h137.681    c8.912,0,16.134-7.223,16.134-16.134S162.727,208.134,153.815,208.134z"/>
						</g>
					</g>
					<g>
						<g>
							<path d="M180.168,144.672H42.487c-8.912,0-16.134,7.223-16.134,16.134c0,8.912,7.223,16.134,16.134,16.134h137.681    c8.912,0,16.134-7.223,16.134-16.134C196.303,151.895,189.08,144.672,180.168,144.672z"/>
						</g>
					</g>
					<g>
					</g>
					<g>
					</g>
					<g>
					</g>
					<g>
					</g>
					<g>
					</g>
					<g>
					</g>
					<g>
					</g>
					<g>
					</g>
					<g>
					</g>
					<g>
					</g>
					<g>
					</g>
					<g>
					</g>
					<g>
					</g>
					<g>
					</g>
					<g>
					</g>
					</svg>'
				);
			}
			if ( $this->status == 'publish' || $this->status == 'mprm-shipping' ) {
				$actions['mprm-shipped'] = array(
					'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=mprm_mark_order_status&status=mprm-shipped&order_id=' . $post->ID ), 'mprm-mark-order-status' ),
					'name'   => __( 'Complete', 'mp-restaurant-menu' ),
					'action' => 'mprm-shipped',
					'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" id="Capa_1" data-name="Capa 1" height="21" viewBox="0 0 512 512"><title>Artboard 1</title><path d="M68,495.32V512H0V478.39H33.42V376H.09V342H67.82v16.73c7.4,0,14.47-.49,21.44.12,9.75.84,16.86-1.83,24.07-9.33,16.31-16.93,36.9-25.19,60.83-25,40.91.28,81.82,0,122.74.09,25.1,0,28.5,1.24,48.25,16.43l34.23-23.13C396.9,306,414.28,293.92,432,282.37c17.38-11.33,35.8-12.75,54-2.39,17.85,10.18,26.92,26.24,26,46.86-.9,19.31-11.81,33-27.4,43.36q-65.79,43.57-131.68,87c-12.31,8.14-24.59,16.32-36.92,24.43a81.74,81.74,0,0,1-45.79,13.67H68Zm.27-34.16h6q97.65,0,195.31,0a49.61,49.61,0,0,0,28.14-8.4q84.06-55.62,168.1-111.24a40.5,40.5,0,0,0,7-5.68c6.53-6.8,6.91-15.14,1.28-22.16a16.4,16.4,0,0,0-22.5-3.33Q406.15,340.82,361,371.61a6.85,6.85,0,0,0-2.54,4.62c-1.91,30.67-23.15,50.74-54,50.77q-56,0-112.07,0c-1.52,0-3-.17-4.4-.26V392.84h6.84q55,0,109.93,0a27.67,27.67,0,0,0,5.83-.4,17.15,17.15,0,0,0,13.5-18.48c-1-8.79-8.21-15.2-17.64-15.21q-66.7-.09-133.41,0c-28.56,0-53.73,25.3-53.79,53.93,0,4.72,0,9.44,0,14.14H84.69c.49-11.38,1-22.46,1.46-33.55H68.24Z"/><path d="M255.58,307.43c-84.75-.17-153.66-69.41-153.42-154.15S171.7-.33,256.45,0C341,.31,409.7,69.3,409.6,153.86,409.51,238.64,340.35,307.6,255.58,307.43ZM136.32,153.91c.09,65.77,53.83,119.38,119.64,119.36s119.46-53.7,119.48-119.51c0-66-53.82-119.76-119.9-119.61C189.75,34.3,136.24,88.06,136.32,153.91Z"/><path d="M314.87,87.23,338.48,111,230.14,219.32l-56.67-57,22.8-22.82c10.53,10.57,21.74,21.8,33.11,33.2Z"/></svg>'
				);
			}
		// }
		echo '<p>';
		echo $this->render_action_buttons( $actions );
		echo '</p>';
	}
	function render_action_buttons( $actions ) {
		$actions_html = '';
		foreach ( $actions as $action ) {
			if ( isset( $action['action'], $action['url'], $action['name'] ) ) {
				$actions_html .= sprintf( '<a class="button mprm-action-button mprm-action-button-%1$s" href="%2$s" aria-label="%3$s" title="%3$s">%4$s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( isset( $action['title'] ) ? $action['title'] : $action['name'] ), $action['icon'] );
			}
		}
		return $actions_html;
	}
}