<?php
namespace mprm_menu_cart\classes\models;

use mprm_menu_cart\classes\Model;

/**
 * Class Menu_cart
 */
class Menu_cart extends Model {
	
	protected static $instance;
	
	/**
	 * Get instance
	 *
	 * @return Menu_cart
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Init menu cart action
	 */
	public function init_action() {
		add_action('init', array($this, 'filter_nav_menus'));
		add_filter('mprm_settings_tabs', array($this, 'mp_menu_settings_tabs'), 10, 1);
		add_filter('mprm_settings_sections', array($this, 'add_settings_sections'), 10, 1);
		add_filter('mprm_settings_sections_menu_cart', array($this, 'add_settings_sections_menu_cart'), 10, 1);
		add_filter('mprm_registered_settings', array($this, 'change_registered_settings'), 10, 1);
		add_filter('mprm_settings_menu_cart', array($this, 'add_settings_data'), 10, 1);
	}
	
	/**
	 * Register settings menu cart
	 *
	 * @param $settings
	 *
	 * @return array
	 */
	public function change_registered_settings($settings) {
		if (is_array($settings)) {
			$settings[ 'menu_cart' ] = apply_filters('mprm_settings_menu_cart', array());
		}
		
		return $settings;
	}
	
	/**
	 * Settings list
	 *
	 * @param $settings_data
	 *
	 * @return mixed
	 */
	public function add_settings_data($settings_data) {
		if (empty($settings_data)) {
			$settings_data[ 'main' ] = $this->get_model('settings')->get_settings_list();
		}
		
		return $settings_data;
	}
	
	/**
	 * Add settings sections license
	 *
	 * @param $sections
	 *
	 * @return mixed
	 */
	public function add_settings_sections_menu_cart($sections) {
		if (empty($sections)) {
			$sections[ 'main' ] = __('Main', 'mprm-menu-cart');
		}
		
		return $sections;
	}
	
	/**
	 * Add menu tabs
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	public function mp_menu_settings_tabs($tabs) {
		if (is_array($tabs)) {
			$tabs[ 'menu_cart' ] = __('Menu Cart', 'mprm-menu-cart');
		}
		
		return $tabs;
	}
	
	/**
	 * Add menu section
	 *
	 * @param $sections
	 *
	 * @return mixed
	 */
	public function add_settings_sections($sections) {
		if (is_array($sections)) {
			$sections[ 'menu_cart' ] = apply_filters('mprm_settings_sections_menu_cart', array());
		}
		
		return $sections;
	}
	
	/**
	 * Add filters to selected menus to add cart item <li>
	 */
	public function filter_nav_menus() {
		$mp_menu_nav_menu__ids = $this->get_model('settings')->get_option('mpme_select_menu_id', false);
		
		if (!empty($mp_menu_nav_menu__ids)) {
			
			if (is_array($mp_menu_nav_menu__ids)) {
				foreach ($mp_menu_nav_menu__ids as $menu_id) {
					add_filter('wp_nav_menu_' . $menu_id . '_items', array($this, 'add_item_cart_to_menu'), 10, 2);
				}
			} else {
				add_filter('wp_nav_menu_' . $mp_menu_nav_menu__ids . '_items', array($this, 'add_item_cart_to_menu'), 10, 2);
			}
		}
	}
	
	/**
	 * Load custom ajax
	 */
	public function load_custom_ajax() {
		
	}
	
	/**
	 * Output ajax cart item
	 */
	public function output_menu_cart_ajax() {
		
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'mp-menu-cart' ) ) {
			wp_die( __( 'Security check', 'mprm-menu-cart' ), 403 ); 
		} else {
			$cart_menu_item = $this->get_cart_menu_item();
			echo $cart_menu_item;
		}

		wp_die();
	}
	
	/**
	 * Create HTML for Menu Cart item
	 */
	public function get_cart_menu_item() {
		$item_data = $this->get_menu_item();
		$always_display = $this->get_model('settings')->get_option('mpme_always_display', false);
		$icon_display = $this->get_model('settings')->get_option('mpme_icon_display', false);
		$display_type = $this->get_model('settings')->get_option('mpme_display_type', 'items');
		
		// Check empty cart settings
		if ($item_data[ 'cart_contents_count' ] == 0 && (!$always_display)) {
			$empty_menu_item = '<a class="mp-menu-cart-contents empty-mp-menu-cart" style="display:none">&nbsp;</a>';
			
			return $empty_menu_item;
		}
		$viewing_cart = __('View your shopping cart', 'mprm-menu-cart');
		$start_shopping = __('Start shopping', 'mprm-menu-cart');
		
		$items_label = apply_filters('mp-menu-cart-item-label', _n('%d item', '%d items', $item_data[ 'cart_contents_count' ], 'mprm-menu-cart'));
		
		$cart_contents = sprintf($items_label, $item_data[ 'cart_contents_count' ]);
		
		
		if ($item_data[ 'cart_contents_count' ] == 0) {
			$menu_item_href = apply_filters('mp-menu-cart_empty-url', $item_data[ 'shop_page_url' ]);
			$menu_item_title = apply_filters('mp-menu-cart_empty-title', $start_shopping);
			$menu_item_classes = 'mp-menu-cart-contents empty-mp-menu-cart-visible';
		} else {
			$menu_item_href = apply_filters('mp-menu-cart_full_url', $item_data[ 'cart_url' ]);
			$menu_item_title = apply_filters('mp-menu-cart_full_title', $viewing_cart);
			$menu_item_classes = 'mp-menu-cart-contents';
		}
		
		$menu_item = '<a class="' . $menu_item_classes . '" href="' . $menu_item_href . '" title="' . $menu_item_title . '">';
		
		$menu_item_a_content = '';
		
		if ($icon_display) {
			$icon_list = $this->get_model('settings')->get_option('mpme_icon_list', 'zero');
			$icon = isset($icon_list) ? $icon_list : '0';
			$menu_item_icon = '<i class="mprm-cart-font icon-mprm-cart' . $icon . '"></i>';
			$menu_item_a_content .= $menu_item_icon;
		} else {
			$menu_item_icon = '';
		}
		
		switch ($display_type) {
			case 'items': //items only
				$menu_item_a_content .= '<span class="mp-menu-cart-contents">' . $cart_contents . '</span>';
				break;
			case 'price': //price only
				$menu_item_a_content .= '<span class="mp-menu-cart-amount">' . $item_data[ 'cart_total' ] . '</span>';
				break;
			case 'price_and_items': //items & price
				$menu_item_a_content .= '<span class="mp-menu-cart-contents">' . $cart_contents . '</span><span class="mp-menu-cart-amount">' . $item_data[ 'cart_total' ] . '</span>';
				break;
		}
		
		$menu_item_a_content = apply_filters('mp_menu_item_a_content', $menu_item_a_content, $menu_item_icon, $cart_contents, $item_data);
		
		$menu_item .= $menu_item_a_content . '</a>';
		
		$menu_item = apply_filters('mp_menu_item_a', $menu_item, $item_data, $this->get_model('settings')->get_settings(), $menu_item_a_content, $viewing_cart, $start_shopping, $cart_contents);
		
		if (!empty($menu_item)) {
			return $menu_item;
		}
	}
	
	/**
	 * Menu item
	 *
	 * @return array
	 */
	public function get_menu_item() {
		$menu_item = array(
			'cart_url' => mprm_get_checkout_uri(),
			'shop_page_url' => get_home_url(),
			'cart_contents_count' => mprm_get_cart_quantity(),
			'cart_total' => mprm_currency_filter(mprm_format_amount(mprm_get_cart_subtotal())),
		);
		
		return $menu_item;
	}
	
	/**
	 * Add cart to menu
	 *
	 * @param $items
	 * @param $menu_object
	 *
	 * @return mixed
	 */
	public function add_item_cart_to_menu($items, $menu_object) {
		$custom_class = $this->get_model('settings')->get_option('mpme_custom_class', '');
		$classes = 'mp-menu-cart-li mp-cart-display-' . $this->get_model('settings')->get_option('mpme_alignment', 'default') . ' ' . $custom_class;
		
		if ($this->get_common_li_classes($items) != '') {
			$classes .= ' ' . $this->get_common_li_classes($items);
		}
		
		$classes = apply_filters('mp_menu_item_classes', $classes);
		$mp_menu_item = apply_filters('mp_menu_item_filter', $this->get_cart_menu_item());
		
		$menu_item_li = '<li class="' . $classes . '" id="mp-menu-cart-' . $this->get_menu_slug($menu_object) . '">' . $mp_menu_item . '</li>';
		
		if (apply_filters('mp_prepend_menu_item', false)) {
			$items = apply_filters('mp_menu_item_wrapper', $menu_item_li) . $items;
		} else {
			$items .= apply_filters('mp_menu_item_wrapper', $menu_item_li);
		}
		
		return $items;
	}
	
	/**
	 * Common li classes
	 *
	 * @param $items
	 *
	 * @return string
	 */
	public function get_common_li_classes($items) {
		if (empty($items)) {
			return '';
		}
		
		$libxml_previous_state = libxml_use_internal_errors(true); // enable user error handling
		
		$dom_items = new \DOMDocument;
		$dom_items->loadHTML($items);
		$lis = $dom_items->getElementsByTagName('li');
		
		if (empty($lis)) {
			libxml_clear_errors();
			libxml_use_internal_errors($libxml_previous_state);
			
			return '';
		}
		
		foreach ($lis as $li) {
			if ($li->parentNode->tagName != 'ul')
				$li_classes[] = explode(' ', $li->getAttribute('class'));
		}
		// clear errors and reset to previous error handling state
		libxml_clear_errors();
		libxml_use_internal_errors($libxml_previous_state);
		
		if (!empty($li_classes)) {
			$common_li_classes = array_shift($li_classes);
			foreach ($li_classes as $li_class) {
				$common_li_classes = array_intersect($li_class, $common_li_classes);
			}
			$common_li_classes_flat = implode(' ', $common_li_classes);
		} else {
			$common_li_classes_flat = '';
		}
		
		return $common_li_classes_flat;
	}
	
	/**
	 * @param $menu_object
	 *
	 * @return string
	 */
	protected function get_menu_slug($menu_object) {
		if (is_object($menu_object->menu)) {
			$menu_slug = $menu_object->menu->slug;
			
			return $menu_slug;
		} elseif (is_string($menu_object->menu)) {
			$menu_slug = empty($menu_object->menu) ? '' : $menu_object->menu;
			
			return $menu_slug;
		} else {
			$menu_slug = '';
		}
		
		return $menu_slug;
	}
	
	/**
	 * Get menu
	 *
	 * @return array
	 */
	public function get_menu_array() {
		$menus = get_terms('nav_menu', array('hide_empty' => false));
		$menu_list = array('0' => '');
		
		foreach ($menus as $menu) {
			$menu_list[ $menu->slug ] = $menu->name;
		}
		
		return $menu_list;
	}
}