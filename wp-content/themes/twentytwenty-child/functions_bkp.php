<?php
/**
 * Twenty Twenty functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */
wp_enqueue_script( 'jquery' );

// remove admin bar topbar
add_filter( 'show_admin_bar', '__return_false' );


function member_add_meta_box() {

    $screens = array( 'mp_menu_item' );
    foreach ( $screens as $screen ) {    
        add_meta_box('banner_section_subtitle', __( 'Banner Section Subtitle', 'member_textdomain' ), 'member_meta_box_callback', $screen );
    }
}
add_action( 'add_meta_boxes', 'member_add_meta_box' );
    
function member_meta_box_callback( $post ) {
    
    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'member_save_meta_box_data', 'member_meta_box_nonce' );
    
    /*
    * Use get_post_meta() to retrieve an existing value
    * from the database and use the value for the form.
    */
    $value = get_post_meta( $post->ID, '_product_banner_subtitle_text', true );
    
    echo '<label for="banner_subtitle_content">';
    _e( 'Subtitle', 'member_textdomain' );
    echo '</label> ';
    echo '<input type="text" id="banner_subtitle_content" name="banner_subtitle_content" value="' . esc_attr( $value ) . '" size="25" />';
}
    
function member_save_meta_box_data( $post_id ) {

    if ( ! isset( $_POST['member_meta_box_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['member_meta_box_nonce'], 'member_save_meta_box_data' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

        } else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    if ( ! isset( $_POST['banner_subtitle_content'] ) ) {
        return;
    }

    $my_data = sanitize_text_field( $_POST['banner_subtitle_content'] );
    
    update_post_meta( $post_id, '_product_banner_subtitle_text', $my_data );
}
add_action( 'save_post', 'member_save_meta_box_data' );


function remove_core_updates(){
    global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}
add_filter('pre_site_transient_update_core','remove_core_updates');
add_filter('pre_site_transient_update_plugins','remove_core_updates');
add_filter('pre_site_transient_update_themes','remove_core_updates');


add_filter( 'wp_nav_menu_items', 'wti_loginout_menu_link', 10, 2 );

function wti_loginout_menu_link( $items, $args ) {
   if ($args->theme_location == 'primary') {
      if (is_user_logged_in()) {
          $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="'. site_url() .'/checkout">'. __("Checkout") .'</a><span class="icon"></span><ul class="sub-menu">
          <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="'. site_url() .'/checkout/transaction-failed/">Transaction Failed</a></li>
          <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="'. site_url() .'/checkout/purchase-history/">Purchase history</a></li>
          <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="'. site_url() .'/checkout/success/">Success</a></li>
      </ul></li>';
         $items .= '<li class="right"><a href="'. wp_logout_url(home_url()) .'">'. __("Log Out") .'</a></li>';
      } 
   }
   return $items;
}

function twentytwentychild_sidebar_registration() {
	// Arguments used in all register_sidebar() calls.
	$childshared_args = array(
		'before_title'  => '<h2 class="widget-title subheading heading-size-3">',
		'after_title'   => '</h2>',
		'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
		'after_widget'  => '</div></div>',
	);
	
	// Footer #1.
	register_sidebar(
		array_merge(
			$childshared_args,
			array(
				'name'        => __( 'Footer #3', 'twentytwenty' ),
				'id'          => 'sidebar-3',
				'description' => __( 'Widgets in this area will be displayed in the Third column in the footer.', 'twentytwenty' ),
			)
		)
	);
	
	register_sidebar(
		array_merge(
			$childshared_args,
			array(
				'name'        => __( 'Item Listing Page Sidebar', 'twentytwenty' ),
				'id'          => 'item_listing_page_sidebar',
				'description' => __( 'Widgets in this area will be displayed in the item listing page.', 'twentytwenty' ),
			)
		)
	);
}
add_action( 'widgets_init', 'twentytwentychild_sidebar_registration' );

add_action('wp_ajax_get_menu_cart_content', 'get_menu_cart_content');
add_action('wp_ajax_nopriv_get_menu_cart_content', 'get_menu_cart_content');
function get_menu_cart_content() {
    ?>
        <div class="menu-cart-popup-content">
            <a href="javascript:void(0);" class="cart-popup-close">close</a>
        	<?php mprm_get_checkout_cart_template(); ?>
			<?php //mprm_render_delivery_form(); ?>
			<?php mprm_checkout_summary_table(); ?>
	        <div class="cart-popup-buttons">
	            <a href="<?php echo site_url(); ?>/checkout" class="button">Checkout</a>
	        </div>
        </div>
    <?php echo ob_get_clean(); die();
}




/*dashboard order charts*/
function restaurant_menu_orders_charts() {
    echo "Charts Code Here..";
}
function orders_add_dashboard_widgets() {
    wp_add_dashboard_widget('owner_dashboard_widget', 'Restaurant Orders', 'restaurant_menu_orders_charts');
}
add_action('wp_dashboard_setup', 'orders_add_dashboard_widgets' );