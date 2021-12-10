
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

        global $wpdb;
        $booklyStaffTable = $wpdb->prefix.'bookly_staff';
        $userID = get_current_user_id();
        $staffQuery = $wpdb->get_row("SELECT * FROM $booklyStaffTable WHERE wp_user_id = $userID", ARRAY_A);
        if(!empty($staffQuery)){
            $items .= '<li class="right"><a href="'. site_url() .'/staff-schedule/">'. __("Staff Schedule") .'</a></li>';    
        }

        $items .= '<li class="right"><a href="'. wp_logout_url(home_url()) .'">'. __("Log Out") .'</a></li>';
      } else {
        $items .= '<li class="right"><a href="'. site_url() .'/login/">'. __("Log In") .'</a></li>';
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
			<?php mprm_checkout_summary_table(); ?>
	        <div class="cart-popup-buttons">
	            <a href="#" class="button btn_checkout">Checkout</a>
	        </div>
        </div>
    <?php echo ob_get_clean(); die();
}

add_action('wp_ajax_get_edit_item_button', 'get_edit_item_button'); // Call when edit topping button click
add_action('wp_ajax_nopriv_get_edit_item_button', 'get_edit_item_button'); // Call when edit topping button click
function get_edit_item_button() {
	$ID = $_POST['id'];
	$out = '';
	$out .='<div class="topping-popup-content" id="topping_popup_content" style="display:block;">
		<div class="topping-modal" style="display:block;"><div class="header"><a href="#" class="cancel-edit-topping-popup">X</a></div>
		<div class="content">';
		$class = '';
		if (\mp_restaurant_menu\classes\models\Settings::get_instance()->is_ajax_disabled()) {
			$class = 'mprm-no-js';
		}	
		$toppings = mprm_get_selected_toppings($ID);
		$args['color'] = mprm_get_option('checkout_color', 'mprm-btn blue');
		$args['padding'] = mprm_get_option('checkout_padding');
		$args['style'] = mprm_get_option('button_style', 'button');
		$args['toppings'] = $toppings;
		$toppings_button = mprm_get_option('toppings_title', __('Add toppings', 'mprm-toppings'));
		$pro_price = mpto_get_price($ID);
		//$pro_quantity = mprm_get_cart_item_quantity($ID);
		
		if (!empty($toppings)) {
			$out .=$pro_quantity.'<div class="mpto-topping-buy-button mprm-display-inline">
					   <div class="mprm-text mprm-add-topping mprm-content-container mprm_purchase_submit_wrapper">
						  <a rel="nofollow" href="" class="mprm-submit button inherit mprm-inherit mprm-topping-popup-open mprm-open mprm-display-inline">
						  <span class="mprm-text">View Item</span>
						  </a>
					   </div>
					   <form id="mprm_toppings_form-'.$ID.'" class="mprm_form  mprm_purchase_form " method="POST">
						  <div id="toppings-wrapper-'.$ID.'" class="mprm-section" data-menu_id="'.$ID.'">
							 <div class="mprm-cart-toppings-wrapper">
								<div class="mprm-title">You can order optional toppings</div>
								<div class="mprm-close mprm-topping-popup-close"></div>
								<input type="hidden" name="menu_item_id" value="'.$ID.'">
								<div class="mprm-list-wrapper">
								   <ul class="mprm-list">';
								    foreach($toppings as $toppings_items){
									  $topping_id = $toppings_items->ID;
									  $topping_meta = mpto_get_topping_meta($topping_id);
									  if(!empty($topping_meta)){
										$topping_type = $topping_meta['topping_type'];
										if($topping_type == 'checkbox'){
											$price = mpto_get_price($topping_id);
											if ($price <= 0) {
												$price = '';
											} else {
												$price = mprm_currency_filter(mprm_format_amount($price));
											} 
												
											$out .='<li class="mprm-topping_'.$topping_id.'">
												<div class="mprm-item" data-id="'.$topping_id.'">
													<span class="mprm-topping-row mprm-topping-title">';
														$out .= wp_get_attachment_image(get_post_thumbnail_id($topping_id), 'thumbnail', false, array('class' => "mprm-gallery-image"));
														$out .= get_the_title($topping_id); 
													$out .='</span>
													<span class="mprm-topping-row mprm-topping-option">
														<input type="checkbox" class="mprm-checkbox" value="'.mpto_get_price($topping_id).'">
													</span>
													<a class="pop-topping-id" style="display:none;" data-menuitem_id="'.$ID.'" data-topping_id="'.$topping_id.'">'.$topping_id.'</a>
													<span class="mprm-topping-row mprm-topping-price">'.$price.'</span>
												</div>
											</li>';	
										}else if($topping_type == 'radio'){
											if (!empty($topping_meta['variable_prices'])) {
												foreach ($topping_meta['variable_prices'] as $key => $price) {
													if ($price['amount'] <= 0) {
														$price['amount'] = '';
													} else {
														$price['amount'] = mprm_currency_filter(mprm_format_amount($price['amount']));
													}
													$out .='<li class="mprm-topping_'.$topping->ID . '-' . $key .'mprm-option-select">
														<div class="mprm-item">
															<label class="mprm-topping-row mprm-topping-title" for="mprm-price-option-'.$key.'">'.$price['name'].'</label>
															<span class="mprm-topping-row mprm-topping-option">
																<input class="mprm-radio-input" type="radio" '.checked($default_price_id, $key).' value="'.$price['name'].'" id="mprm-price-option-'.$key.'" name="price-index-'.$topping->ID.'" data-price-name="'.$price['name'].'" data-price-index="'.$price['index'].'" data-id="'.$topping->ID.'">
															</span>
															<a class="pop-topping-id" data-menuitem_id="'.$ID.'" style="display:none;" data-topping_id="'.$topping_id.'">'.$topping_id.'</a>
															<span class="mprm-topping-row mprm-topping-price">'.$price['amount'].'</span>
														</div>
													</li>';
												}
											} 
										}else if($topping_type == 'stepper'){
											$topping_meta = mpto_get_topping_meta($topping_id, true);
											$min = empty($topping_meta['min']) ? '0' : $topping_meta['min'];
											$max = empty($topping_meta['max']) ? '99' : $topping_meta['max'];
											$out .='<li class="mprm-topping_'.$topping_id.'">
														<div class="mprm-item">
															<span class="mprm-topping-row mprm-topping-title">';
																$out .= wp_get_attachment_image(get_post_thumbnail_id($topping_id), 'thumbnail', false, array('class' => "mprm-gallery-image"));
																$out .= get_the_title($topping_id); 
															$out .='</span>
															<span class="mprm-topping-row mprm-topping-option">
																<input type="number" min="'.$min.'" max="'.$max.'" class="mprm-spinner" value="'.$min.'" data-id="'.$topping_id.'">
															</span>
															<a class="pop-topping-id" data-menuitem_id="'.$ID.'" style="display:none;" data-topping_id="'.$topping_id.'">'.$topping_id.'</a>
															<span class="mprm-topping-row mprm-topping-price">'.mprm_currency_filter(mprm_format_amount(mpto_get_price($topping_id))).'</span>
														</div>
													</li>';
										}
								    }}  
								   $out .='</ul>
								</div>
								<div class="mprm-topping-footer mprm-display-inline mprm_purchase_submit_wrapper" style="position: relative;">
								   <div class="mprm-container-preloader">
									  <div class="mprm-floating-circles mprm-floating-circle-wrapper small-preloader mprm-hidden">
										 <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-1"></div>
										 <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-2"></div>
										 <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-3"></div>
										 <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-4"></div>
										 <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-5"></div>
										 <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-6"></div>
										 <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-7"></div>
										 <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-8"></div>
									  </div>
								   </div>
								   <a href="javascript:void(0)" class="mprm-submit mprm-display-inline mprm-topping-add-to-cart button inherit mprm-inherit" data-pro-id="'.$ID.'" data-price-val="'.$pro_price.'">Add to Cart</a> 
								</div>
							 </div>
						  </div>
					   </form>
					</div>';
		}	
		$out .='</div>
		</div>
	</div>';
	echo $out; die();
}

add_action('wp_ajax_get_menu_checkout_content', 'get_menu_checkout_content');
add_action('wp_ajax_nopriv_get_menu_checkout_content', 'get_menu_checkout_content');
function get_menu_checkout_content() {	?> 
	<div class="menu-cart-popup-content">
		<a href="javascript:void(0);" class="cart-popup-close">close</a>
		<?php echo do_shortcode('[mprm_checkout]'); ?>
		<?php //mprm_checkout_submit();
				//mprm_purchase_form_cart_items_after(); ?>
		<?php //mprm_checkout_login_fields(); ?>
		<?php //mprm_login_form(); ?>
		<?php //mprm_purchase_form(); ?>			
		<?php //mprm_purchase_form_before_submit(); ?>
	</div>
    <?php echo ob_get_clean(); die();
}

add_action('wp_ajax_get_menu_success_content', 'get_menu_success_content');
add_action('wp_ajax_nopriv_get_menu_success_content', 'get_menu_success_content');

function get_menu_success_content(){ ?> 
	<div class="menu-cart-popup-content">
		<a href="javascript:void(0);" class="cart-popup-close">close</a>
		<?php //do_action('mprm_purchase_form_after_submit'); ?>
		<?php echo do_shortcode('[mprm_success]'); ?>	
	</div>
    <?php echo ob_get_clean(); die();
}

add_action('wp_ajax_get_menu_cart_remove_content', 'get_menu_cart_remove_content');
add_action('wp_ajax_nopriv_get_menu_cart_remove_content', 'get_menu_cart_remove_content');
function get_menu_cart_remove_content() { ?> 
    <?php echo ob_get_clean(); die();
}

add_action('wp_ajax_remove_topping_cart_content', 'remove_topping_cart_content');
add_action('wp_ajax_nopriv_remove_topping_cart_content', 'remove_topping_cart_content');
function remove_topping_cart_content() {
	if(isset($_SESSION['mprm']['mprm_cart'])){
		$cartArray = unserialize($_SESSION['mprm']['mprm_cart']);
		$cartArray = array_values($cartArray);

		$cartItem = $_POST['cart_item'];
		$topping_id = $_POST['topping_id'];

		unset($cartArray[$cartItem]['toppings'][$topping_id]);
		$_SESSION['mprm']['mprm_cart'] = serialize($cartArray);
	}
	//echo ob_get_clean(); die();
}

/* function restaurant_menu_ajax_checkout() {
    //if (function_exists('is_product') && is_product()) {
        wp_enqueue_script('mp-restaurant-menu', plugin_dir_url(__FILE__) . 'templates/shop/checkout.php', , '', true);
    //}
}
add_action('wp_enqueue_scripts', 'restaurant_menu_ajax_checkout', 99); */

/* add_action( 'admin_enqueue_scripts', 'my_enqueue' );
function my_enqueue($hook) {
 //   if( 'index.php' != $hook ) return;  // Only applies to dashboard panel

    wp_enqueue_script( 'ajax-script', plugins_url( '/media/js/mp-restaurant-menu.js', __FILE__ ), array('jquery'));

    // in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
    wp_localize_script( 'ajax-script', 'ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
}
 */

function my_resource() {
    //wp_enqueue_script('my-jquery',get_template_directory_uri().'/jqfunctions.js');
    wp_localize_script( 'my-jquery', 'myback', 
    array('ajax_url' => admin_url( 'admin-ajax.php' )));
}
add_action('wp_enqueue_scripts', 'my_resource');

/*dashboard order charts*/
function restaurant_menu_orders_charts() {
    echo "Charts Code Here..";
}
function orders_add_dashboard_widgets() {
    wp_add_dashboard_widget('owner_dashboard_widget', 'Restaurant Orders', 'restaurant_menu_orders_charts');
}
add_action('wp_dashboard_setup', 'orders_add_dashboard_widgets' );

add_action('init', 'add_column_bookly_staff');
function add_column_bookly_staff(){
    global $wpdb;
    $booklyStaffTable = $wpdb->prefix.'bookly_staff';

    $row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE table_name = '$booklyStaffTable' AND column_name = 'wp_pusher_id'"  );  
      
    if(empty($row)){  
       $wpdb->query("ALTER TABLE $booklyStaffTable ADD wp_pusher_id VARCHAR(255) NOT NULL");  
    }   
}

// get webpushr id 
add_action('wp_footer','add_webpusher_id_staff_member');
function add_webpusher_id_staff_member(){ 
	 if ( isset($_GET['staffId']) && $_GET['staffId'] > 0 ){ 
    	 $_SESSION['staffID'] = $_GET['staffId'];
	 }
	 if( isset($_SESSION['staffID']) && $_SESSION['staffID'] > 0){
	 ?>
		<script>
			jQuery(document).ready(function() {
			    webpushr('fetch_id',function (sid) { 
				    setTimeout(() => {
						jQuery.ajax({
							type: "POST",
							dataType: 'json',
							url: "<?php echo admin_url('admin-ajax.php'); ?>",
							data : {action: "add_webpusher_id_staff_member_db", pusherId : sid, staffid : "<?php echo $_SESSION['staffID']; ?>"},
							success: function(response){
								
							},
							error: function (response) {
								
							}
						});
					}, 1000);
				});
			});
		</script>
	<?php	
        unset($_SESSION['staffID']);
	 }
}		

function add_webpusher_id_staff_member_db(){
	global $wpdb;
	$booklyStaffTable = $wpdb->prefix.'bookly_staff';
	if(isset($_POST['staffid']) && isset($_POST['pusherId'])){
    	if( $_POST['staffid'] > 0 && $_POST['pusherId'] > 0){
    	    $pusherId = $_POST['pusherId'];
    	    $staffid = $_POST['staffid'];
    	
    	    $wpdb->query("UPDATE $booklyStaffTable SET wp_pusher_id = '$pusherId' WHERE id = $staffid ");
    	}
	}
}
add_action( 'wp_ajax_add_webpusher_id_staff_member_db', 'add_webpusher_id_staff_member_db' );
add_action( 'wp_ajax_nopriv_add_webpusher_id_staff_member_db', 'add_webpusher_id_staff_member_db' ); 