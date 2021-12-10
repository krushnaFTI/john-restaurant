<?php
namespace mprm_toppings\classes;

/**
 * Class MPRM_post_types
 */
class MPRM_post_types {

	private static $_instance;

	/**
	 * @return MPRM_post_types
	 */
	public static function get_instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Add posts type/ add menu
	 */
	public function init() {
		$this->register_post_type();
	}

	/**
	 * Register post type
	 */
	public function register_post_type() {

		if (post_type_exists('mprm_toppings')) {
			return;
		}

		register_post_type('mprm_toppings', array(
			'labels' => array(
				'name' => __('Toppings', 'mprm-toppings'),
				'singular_name' => _x('Topping', 'shop_order post type singular name', 'mprm-toppings'),
				'add_new' => __('Add Topping', 'mprm-toppings'),
				'add_new_item' => __('Add New Topping', 'mprm-toppings'),
				'edit' => __('Edit', 'mprm-toppings'),
				'edit_item' => __('Edit Topping', 'mprm-toppings'),
				'new_item' => __('New Topping', 'mprm-toppings'),
				'view' => __('View Topping', 'mprm-toppings'),
				'view_item' => __('View Topping', 'mprm-toppings'),
				'search_items' => __('Search Toppings', 'mprm-toppings'),
				'not_found' => __('No Toppings found', 'mprm-toppings'),
				'not_found_in_trash' => __('No Toppings found in trash', 'mprm-toppings'),
				'parent' => __('Parent Toppings', 'mprm-toppings'),
				'menu_name' => _x('Toppings', 'Admin menu name', 'mprm-toppings')
			),
			'description' => __('This is where store toppings are stored.', 'mprm-toppings'),
			'public' => false,
			'show_ui' => true,
			'capability_type' => 'mp_menu_item',
			'map_meta_cap' => true,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'show_in_menu' => false,
			'hierarchical' => false,
			'show_in_nav_menus' => false,
			'rewrite' => false,
			'query_var' => false,
			'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
			'has_archive' => false,
		));
	}

	/**
	 * Add Menu
	 */
	public function add_custom_menu_order() {
		global $submenu;
		$add_toppings_menu = array(
			__('Toppings', 'mprm-toppings'),
			'edit_posts',
			'edit.php?post_type=mprm_toppings',
			__('Toppings', 'mprm-toppings')
		);
		if (isset($submenu['edit.php?post_type=mp_menu_item'])) {
			array_splice($submenu['edit.php?post_type=mp_menu_item'], 6, 0, array($add_toppings_menu));
		}
	}
}