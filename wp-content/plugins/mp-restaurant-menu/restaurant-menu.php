<?php

/**

 * Plugin Name: Restaurant Menu

 * Plugin URI: https://motopress.com/products/restaurant-menu/

 * Description: This plugin gives you the power to effectively create, maintain and display online menus for almost any kind of restaurant, cafes and other typical food establishments.

 * Version: 2.4.0

 * Author: MotoPress

 * Author URI: https://motopress.com

 * License: GPLv2 or later

 * Text Domain: mp-restaurant-menu

 * Domain Path: /languages

 */



if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



use mp_restaurant_menu\classes\Core;

use mp_restaurant_menu\classes\Media;

use mp_restaurant_menu\classes\upgrade\Install;



define('MP_RM_PLUGIN_PATH', plugin_dir_path(__FILE__));

define('MP_RM_MEDIA_URL', plugins_url(plugin_basename(__DIR__) . '/media/'));

define('MP_RM_JS_URL', MP_RM_MEDIA_URL . 'js/');

define('MP_RM_CSS_URL', MP_RM_MEDIA_URL . 'css/');

define('MP_RM_PLUGIN_NAME', str_replace('-', '_', dirname(plugin_basename(__FILE__))));

define('MP_RM_LANG_PATH', MP_RM_PLUGIN_PATH . 'languages/');

define('MP_RM_TEMPLATES_PATH', MP_RM_PLUGIN_PATH . 'templates/');

define('MP_RM_CLASSES_PATH', MP_RM_PLUGIN_PATH . 'classes/');

define('MP_RM_PREPROCESSORS_PATH', MP_RM_CLASSES_PATH . 'preprocessors/');

define('MP_RM_CONTROLLERS_PATH', MP_RM_CLASSES_PATH . 'controllers/');

define('MP_RM_WIDGETS_PATH', MP_RM_CLASSES_PATH . 'widgets/');

define('MP_RM_MODELS_PATH', MP_RM_CLASSES_PATH . 'models/');

define('MP_RM_MODULES_PATH', MP_RM_CLASSES_PATH . 'modules/');

define('MP_RM_LIBS_PATH', MP_RM_CLASSES_PATH . 'libs/');

define('MP_RM_CONFIGS_PATH', MP_RM_PLUGIN_PATH . 'configs/');

define('MP_RM_TEMPLATES_ACTIONS', MP_RM_PLUGIN_PATH . 'templates-actions/');

define('MP_RM_TEMPLATES_FUNCTIONS', MP_RM_PLUGIN_PATH . 'templates-functions/');



if ( ! defined( 'MP_RM_DEBUG' ) ) {

	define('MP_RM_DEBUG', FALSE);

}



// Custom Code for customization
/*
add_action('show_user_profile', 'custom_user_profile_fields');
add_action('edit_user_profile', 'custom_user_profile_fields');

function custom_user_profile_fields( $user ) {
	$admin_user_id = get_current_user_id();
	$get_admin_phone_number = get_user_meta( $admin_user_id, 'admin_phone_number');
	$get_admin_business_address = get_user_meta( $admin_user_id, 'admin_business_address');
	
	$get_admin_webpushr_key = get_user_meta( $admin_user_id, 'admin_webpushr_key');
	$get_admin_webpushr_auth_token = get_user_meta( $admin_user_id, 'admin_webpushr_auth_token'); ?>
	<h3>Business Info</h3>
    <table class="form-table">
		<tr>
			<th><label for="admin_phone_number">Mobile Number</label></th>
			<td><input type="text" class="input-text form-control" name="admin_phone_number" id="admin_phone_number" value="<?php echo $get_admin_phone_number[0]; ?>"/></td>
		</tr>
		<tr>
			<th><label for="admin_business_address">Business Address</label></th>
			<td><input type="text" class="input-text form-control" name="admin_business_address" id="admin_business_address" value="<?php echo $get_admin_business_address[0]; ?>"/></td>
		</tr>
    </table>

	<h3>Webpushr Info</h3>
    <table class="form-table">
		<tr>
			<th><label for="admin_webpushr_key">Webpushr Key</label></th>
			<td><input type="text" class="input-text form-control" name="admin_webpushr_key" id="admin_webpushr_key" value="<?php echo $get_admin_webpushr_key[0]; ?>"/></td>
		</tr>
		<tr>
			<th><label for="admin_webpushr_auth_token">Webpushr AuthToken</label></th>
			<td><input type="text" class="input-text form-control" name="admin_webpushr_auth_token" id="admin_webpushr_auth_token" value="<?php echo $get_admin_webpushr_auth_token[0]; ?>"/></td>
		</tr>
    </table>
<?php } */





// Add custom post type
function add_theme_menu_item(){
	add_menu_page("Restaurant Settings Page", "Restaurant Settings Page", "manage_options", "theme-panel", "theme_settings_page", null, 99);
}
add_action("admin_menu", "add_theme_menu_item");

function theme_settings_page(){ ?>
    <div class="wrap">
	    <h1>Restaurant Settings Page</h1>
	    <form method="post" action="options.php">
	        <?php settings_fields("section");
            do_settings_sections("theme-options");      
            submit_button();  ?>          
	    </form>
	</div>
<?php }

function display_business_address_element() { ?>
	<input type="text" name="admin_business_address" id="admin_business_address" style="width:500px;" value="<?php echo get_option('admin_business_address'); ?>" />
<?php }

function display_business_mobile_element(){ ?>
	<input type="text" name="admin_business_mobile" id="admin_business_mobile"  style="width:500px;" value="<?php echo get_option('admin_business_mobile'); ?>" />
<?php }

function display_webpushr_key_element(){ ?>
	<input type="text" name="admin_webpushr_key_enter" id="admin_webpushr_key_enter" style="width:500px;" value="<?php echo get_option('admin_webpushr_key_enter'); ?>" /> 
<?php }

function display_webpushr_authtoken_element(){ ?>
	<input type="text" name="admin_webpushr_authtoken_enter" id="admin_webpushr_authtoken_enter" style="width:500px;" value="<?php echo get_option('admin_webpushr_authtoken_enter'); ?>" /> 
<?php }

function display_theme_panel_fields(){
	add_settings_section("section", "Business Information", null, "theme-options");
	
	add_settings_field("admin_business_address", "Business Address", "display_business_address_element", "theme-options", "section");
    add_settings_field("admin_business_mobile", "Business Mobile Number", "display_business_mobile_element", "theme-options", "section");
    add_settings_field("admin_webpushr_key_enter", "Webpushr Key", "display_webpushr_key_element", "theme-options", "section");
    add_settings_field("admin_webpushr_authtoken_enter", "Webpushr Authtoken", "display_webpushr_authtoken_element", "theme-options", "section");
    
    register_setting("section", "admin_business_address");
    register_setting("section", "admin_business_mobile");
    register_setting("section", "admin_webpushr_key_enter");
    register_setting("section", "admin_webpushr_authtoken_enter");
}
add_action("admin_init", "display_theme_panel_fields");





// Updated User meta field
// add_action( 'personal_options_update', 'update_extra_profile_fields' );
// add_action( 'edit_user_profile_update', 'update_extra_profile_fields' );
// function update_extra_profile_fields( $user_id ) {
//     if ( current_user_can( 'edit_user', $user_id ) )
//         update_user_meta( $user_id, 'admin_phone_number', $_POST['admin_phone_number'] );
// 		update_user_meta( $user_id, 'admin_business_address', $_POST['admin_business_address'] );
// 		update_user_meta( $user_id, 'admin_webpushr_key', $_POST['admin_webpushr_key'] );
//         update_user_meta( $user_id, 'admin_webpushr_auth_token', $_POST['admin_webpushr_auth_token'] );
// }



//Register Meta box
add_action('add_meta_boxes',function (){
    add_meta_box('csm-id','Product In Stock','mprm_manage_stock_custom_field','mp_menu_item','side');
});

//Meta callback function
function mprm_manage_stock_custom_field($post){
	global $post;
	$cs_meta_val=get_post_meta($post->ID);
	$get_stock_value = $cs_meta_val['stock_manage_field_custom'][0];
	if($get_stock_value == 'InStock'){ $checked = 'checked'; } else { $checked = ''; } ?>
    <input type="checkbox" name="stock_manage_field_custom" value="InStock" <?php echo $checked; ?>>
	<label for="stock_manage_field_custom">In Stock</label>
	<p>If product is not in stock then remove check mark.</p>
<?php }

//save meta value with save post hook
add_action('save_post',function($post_id){
	if(isset($_POST['stock_manage_field_custom'])){
        update_post_meta($post_id,'stock_manage_field_custom', $_POST['stock_manage_field_custom']);
    }else{
		update_post_meta($post_id,'stock_manage_field_custom', "");
	}
});
 
// show meta value after post content
add_filter('the_content',function($content){
    $meta_val=get_post_meta(get_the_ID(),'stock_manage_field_custom',true);
    return $content.$meta_val;
});

// add column
add_filter( 'manage_mp_menu_item_posts_columns', 'my_edit_movie_columns' ) ;
function my_edit_movie_columns( $columns ) {
	$columns = array(
		'cb' => '&lt;input type="checkbox" />',
		'title' => __( 'InStock' ),
		'instock' => __( 'In Stock' ),
		'date' => __( 'Date' )
	);
	return $columns;
}

// Add the data to the custom columns for the book post type:
add_action( 'manage_mp_menu_item_posts_custom_column' , 'custom_book_column', 10, 2 );
function custom_book_column( $columns, $post_id ) {
	$meta_val = get_post_meta(get_the_ID(),'stock_manage_field_custom',true);
	switch ( $columns ) {
		case 'instock' :
			if($meta_val == "InStock"){ 
				echo "<div class='menu_item_in_stock'>In Stock</div>"; 
			}else{
				echo "<div class='menu_item_not_in_stock'>Not Available</div>";
			}
		break;
	}
}

// Cronjob for updating post status from postmates api
add_action( 'isa_add_every_two_minutes', 'every_two_minutes_event_func' );
add_filter( 'cron_schedules', 'isa_add_every_two_minutes' );
function isa_add_every_two_minutes( $schedules ) {
    $schedules['every_two_minutes'] = array(
		'interval'  => 600,
		'display'   => __( 'Every 2 Minutes', 'textdomain' )
    );
    return $schedules;
}

// Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'isa_add_every_two_minutes' ) ) {
    wp_schedule_event( time(), 'every_two_minutes', 'isa_add_every_two_minutes' );
}

// Hook into that action that'll fire every two minutes
add_action( 'isa_add_every_two_minutes', 'every_two_minutes_event_func' );
function every_two_minutes_event_func() {

	$get_settings_option = get_option('mprm_settings');
	$get_postmates_key = $get_settings_option['postmates_sandbox_key_text'];
	$get_postmates_customer_id = $get_settings_option['postmates_customer_id_text'];
	
	if(!empty($get_postmates_key) && !empty($get_postmates_customer_id)){
	
		$uname = $get_postmates_key;
		$pwd = "";
		
		$url2 = "https://api.postmates.com/v1/customers/".$get_postmates_customer_id."/deliveries";

		$ch_url = curl_init($url2);
		curl_setopt($ch_url, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Accept: application/json'));
		curl_setopt($ch_url, CURLOPT_USERPWD, $uname . ":" . $pwd);
		curl_setopt($ch_url, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch_url, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch_url, CURLOPT_SSL_VERIFYPEER, false);
		$get_delivery_return = curl_exec($ch_url);
		curl_close($ch_url);
		$get_delivery_data = json_decode($get_delivery_return);

		// echo "<pre>"; print_r($get_delivery_data->data); die;
		$total_sizeof_records = sizeof($get_delivery_data->data); 
		global $wpdb;
		$pageposts = $wpdb->get_results("SELECT * FROM `wp_postmeta` WHERE `meta_key` LIKE 'postmates_delivery_id'");
		$total_result_query = sizeof($pageposts);

		for($j = 0; $j < $total_result_query; $j++){

			$postmates_order_id = $pageposts[$j]->post_id;
			$get_postmates_delivery_id = get_post_meta($postmates_order_id, 'postmates_delivery_id');
			
			$final_get_del_id = $get_postmates_delivery_id[0];

			for($i=0; $i<$total_sizeof_records; $i++){
				
				$get_delivery_id = $get_delivery_data->data[$i]->id; 
				$get_status = $get_delivery_data->data[$i]->status;
				if($get_delivery_id == $final_get_del_id){
					if($get_status == "dropoff"){ // on the way - mprm-shipping
						$data = array(
							'ID' => $postmates_order_id,
							'post_status' => 'mprm-shipping',
						);
						wp_update_post( $data );
					}
					if($get_status == "delivered"){ // delivered - mprm-shipped
						$data = array(
							'ID' => $postmates_order_id,
							'post_status' => 'mprm-shipped',
						);
						wp_update_post( $data );
					}
				}
			}
		}
	}		
}
// End cron job function


// get webpushr id 
add_action('wp_head','my_on_load_custom_function');
function my_on_load_custom_function(){ 
	 //if ( is_page('checkout') || is_page('success')){ ?>
		<script>
			jQuery(document).ready(function() {
			    
				webpushr('fetch_id',function (sid) { 
				    //alert(sid);
					console.log('webpushr_sub_id: ' + sid);
					setTimeout(() => {
						jQuery(".webpushr_id_value").val(sid);
						
						jQuery.ajax({
							type: "POST",
							dataType: 'json',
							url: "<?php echo admin_url('admin-ajax.php'); ?>",
							data : {action: "my_custom_ajax_function", subscription_id : sid},
							success: function(response){
								console.log(response.message);
								console.log(response.subscription_id);
								jQuery(".webpushr_id_value").val(response.subscription_id);
							},
							error: function (response) {
								console.log(response.message);
							}
						});
					}, 1000);
				});

				
				// Generate delivery fee from postmates
				jQuery("#mprm_delivery_mode_select-wrapper .mprm-type-delivery input.delivery_street_field").focusout(function(){
					
					$address_value = jQuery("#mprm_delivery_mode_select-wrapper .mprm-type-delivery input.delivery_street_field").val();
					$tax_value = jQuery("#mprm_payment_summary_table .mprm_cart_tax_amount").attr("data-tax");
					$subtotal_amount = jQuery("#mprm_payment_summary_table .mprm_cart_subtotal_amount").html();
					$subtotal_amount = $subtotal_amount.substring(1, $subtotal_amount.length);
					
					jQuery("#mprm_delivery_mode_select-wrapper .mprm-type-delivery .invalid_address").remove();
					if($address_value.length != ""){
						jQuery.ajax({
							type: "POST",
							dataType: 'json',
							url: "<?php echo admin_url('admin-ajax.php'); ?>",
							data : {action: "get_delivery_fee_from_postmates_function", address_value : $address_value},
							success: function(response){
								console.log(response.message);
								// console.log(response.session_array);
								// console.log(response.item_total);
								jQuery("#mprm_delivery_mode_select-wrapper .mprm-type-delivery .invalid_address").remove();
								if(response.delivery_fee_postmates){
									$total_checkout_value = $subtotal_amount + $tax_value + response.delivery_fee_postmates;
									sum = parseFloat($subtotal_amount);
									sum += parseFloat($tax_value);
									sum += parseFloat(response.delivery_fee_postmates);
									jQuery("#mprm_payment_summary_table .mprm-table .mprm_cart_delivery_row .mprm_cart_delivery_amount").text("$"+response.delivery_fee_postmates);
									jQuery("#mprm_payment_summary_table .mprm-table .mprm-checkout-total .mprm_cart_amount b").text("$"+sum);
									jQuery("#mprm_final_total_wrap .mprm_cart_amount").text('$'+sum);
								}else{
									jQuery("#mprm_delivery_mode_select-wrapper .mprm-type-delivery input.delivery_street_field").after('<label class="invalid_address">Please enter correct address</label>');
								}
							},
							error: function (response) {
								console.log(response.message);
								jQuery("#mprm_delivery_mode_select-wrapper .mprm-type-delivery .invalid_address").remove();
							}
						});
					}
				});
				
			});
		</script>
	<?php //} 
}


// Store webpushr id in db
function my_custom_ajax_function (){
	global $wpdb;
	$subscription_id = $_POST['subscription_id'];

	$get_current_user_id = get_current_user_id();
	$get_current_user_id_wp = wp_get_current_user();
	$response = array();
	$response['message'] = "";
	
	if($subscription_id != ''){
		//update_post_meta( $order_id, 'webpushr_subscription_id', $subscription_id );
		$response['message'] = "success";
		$response['subscription_id'] = $subscription_id;
	}else{
		$response['message'] = "fail";
		$response['subscription_id'] = $subscription_id;
	}
 	echo json_encode($response);
	exit;
}
add_action( 'wp_ajax_my_custom_ajax_function', 'my_custom_ajax_function' );
add_action( 'wp_ajax_nopriv_my_custom_ajax_function', 'my_custom_ajax_function' );


// Get delivery fee from postmates
function get_delivery_fee_from_postmates_function(){
	global $wpdb;
	
	$address_value = $_POST['address_value'];
	
	$response = array();
	$response['message'] = "";
	$response['session_array'] = $_SESSION;

	$response['cart_details'] = unserialize($_SESSION['mprm']['mprm_cart']);
	$response['product_id'] = $response['cart_details'][0]['id'];
	$response['product_quantity'] = $response['cart_details'][0]['quantity'];
	
	$get_product_price = get_post_meta($response['product_id'], 'price');
	$response['product_price'] = $get_product_price[0];

	$response['item_total'] = $response['product_price'] * $response['product_quantity'];

	if($address_value != ''){
		$response['message'] = "success";
		$get_settings_option = get_option('mprm_settings');
		$get_postmates_key = $get_settings_option['postmates_sandbox_key_text'];
		$get_postmates_customer_id = $get_settings_option['postmates_customer_id_text'];
		
		if(!empty($get_postmates_key) && !empty($get_postmates_customer_id)){
		
			$admin_user_id = '1';
			$get_admin_phone_number = get_user_meta( $admin_user_id, 'admin_phone_number');
			$get_admin_business_address = get_user_meta( $admin_user_id, 'admin_business_address');
			$get_admin_nickname = get_user_meta( $admin_user_id, 'nickname');

			$url1 = "https://api.postmates.com/v1/customers/".$get_postmates_customer_id."/delivery_quotes";
			$uname = $get_postmates_key;
			$pwd = "";

			$create_delivery_data1 = "dropoff_address=".$address_value."&pickup_address=".$get_admin_business_address[0];

			$ch_url = curl_init($url1);
			curl_setopt($ch_url, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Accept: application/json'));
			curl_setopt($ch_url, CURLOPT_USERPWD, $uname . ":" . $pwd);
			curl_setopt($ch_url, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch_url, CURLOPT_POST, 1);
			curl_setopt($ch_url, CURLOPT_POSTFIELDS, $create_delivery_data1);
			curl_setopt($ch_url, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch_url, CURLOPT_SSL_VERIFYPEER, false);
			$create_del_return1 = curl_exec($ch_url);
			curl_close($ch_url);
			
			$created_delivery_data1 = json_decode($create_del_return1);
			$postmates_delivery_price = $created_delivery_data1->fee;
			$postmates_delivery_duration = $created_delivery_data1->duration;
			$postmates_delivery_currency = $created_delivery_data1->currency;
			if($postmates_delivery_currency == "USD"){
				$response['delivery_fee_postmates'] = $postmates_delivery_price/100;
				$_SESSION['postmates_delivery_id'] = $response['delivery_fee_postmates'];
			}
		}
	}else{
		$response['message'] = "fail";
	}

	echo json_encode($response);
	exit;
}
add_action( 'wp_ajax_get_delivery_fee_from_postmates_function', 'get_delivery_fee_from_postmates_function' );
add_action( 'wp_ajax_nopriv_get_delivery_fee_from_postmates_function', 'get_delivery_fee_from_postmates_function' );



// Change order status send notification to customer
function wpdocs_run_on_publish_only( $new_status, $old_status, $post ) {
	
	$order_id = $post->ID;
	$post_type = $post->post_type;
	// $get_delivery_method = get_post_meta($order_id, 'mpde_delivery');
	// $get_delivery_method = $get_delivery_method[0]['delivery_mode'];

	$get_sub_id = get_post_meta($order_id, 'webpushr_id');
	$get_subscriber_id = $get_sub_id[0];
	$get_user_data = get_post_meta($order_id, '_mprm_order_meta');
	$get_user_data_email = $get_user_data[0]['user_info']['email'];
	$get_user_data_fname = $get_user_data[0]['user_info']['first_name'];

	$get_postmates_delivery_id = get_post_meta($order_id, 'postmates_delivery_id');
	$get_postmates_delivery_id = $get_postmates_delivery_id[0];

	//$get_admin_webpushr_key = get_user_meta( 1, 'admin_webpushr_key');
	//$get_admin_webpushr_auth_token = get_user_meta( 1, 'admin_webpushr_auth_token');
	
	$get_admin_webpushr_key = get_option('admin_webpushr_key_enter');
	$get_admin_webpushr_auth_token = get_option('admin_webpushr_authtoken_enter');
	//print_r($get_admin_webpushr_auth_token);exit;
	$end_point = 'https://api.webpushr.com/v1/notification/send/sid';
	$http_header = array( 
		"Content-Type: Application/Json", 
		"webpushrKey: ".$get_admin_webpushr_key, 
		"webpushrAuthToken: ".$get_admin_webpushr_auth_token
	);
    
	$tracking_url = 'https://postmates.com/track/'.$get_postmates_delivery_id;
	$site_title = get_bloginfo( 'name' );
    
    // if($get_delivery_method == "collection"){
		// changed order status to on the way and send notification to specific user.
		if ( 'mprm-shipping' === $new_status && 'publish' === $old_status ) {
			if( $get_subscriber_id !== ""){
    			$req_data = array(
    				'title' 			=> $site_title, //required
    				'message' 		=> "Your order is being process.", //required
    				'target_url'	=> 'https://www.webpushr.com', //required
    				'sid'		=> $get_subscriber_id,
    				//'sid' => '30162053',
    				'action_buttons'=> array(	
    					array('title'=> 'Track Order', 'url' => $tracking_url)
    				)
    			);
    			$ch = curl_init();
    			curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
    			curl_setopt($ch, CURLOPT_URL, $end_point );
    			curl_setopt($ch, CURLOPT_POST, 1);
    			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data) );
    			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    			$response = curl_exec($ch);
    		}
    		
    		// Send email when status changed
			$to_mail = $get_user_data_email;
			$subject_email = 'Order being process';
			
			if( $get_postmates_delivery_id != ''){
			    $body_email = "<html><head><title></title></head><body>Your order is being process. <a href='".$tracking_url."'>Click here</a> for track order.</body></html>";
			}else{
			    $body_email = "<html><head><title></title></head><body>Your order is being process.</body></html>";
			}
			
		    $headersOrderProcess = "MIME-Version: 1.0" . "\r\n";
		    $headersOrderProcess .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
        	$headersOrderProcess .= "From: John Restaurant <admin@epestic.com>" . "\r\n";
			
			wp_mail( $to_mail, $subject_email, $body_email, $headersOrderProcess );
        }
		// On the way to delivered change status send notification
		if ( 'mprm-shipped' === $new_status && 'mprm-shipping' === $old_status ) {
			if( $get_subscriber_id !== ""){
    			$req_data = array(
    				'title' 			=> $site_title, //required
    				'message' 		=> "Your order is ready for pickup.", //required
    				'target_url'	=> 'https://www.webpushr.com', //required
    				'sid'		=> $get_subscriber_id //required
    			);
    			$ch = curl_init();
    			curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
    			curl_setopt($ch, CURLOPT_URL, $end_point );
    			curl_setopt($ch, CURLOPT_POST, 1);
    			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data) );
    			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    			$response = curl_exec($ch);
			}

			// Send email when status changed
			$to_mail = $get_user_data_email;
			$subject_email = 'Order is ready';
			$body_email = "Your order is ready for pickup.";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
        	$headers .= "From: John Restaurant <admin@epestic.com>" . "\r\n";
	
            wp_mail( $to_mail, $subject_email, $body_email, $headers );
		}
		
		// Send Notificatation when change order status to Failed
		if ( 'mprm-failed' === $new_status ) {
			if($get_subscriber_id !== ""){
    			$req_data = array(
    				'title' 			=> $site_title, //required
    				'message' 		=> "Your order has been canceled as these items (list items) are no longer available. Please contact us (call button) so we can process your new order for you or order again at your convenience.", //required
    				'target_url'	=> 'https://www.webpushr.com', //required
    				'sid'		=> $get_subscriber_id //required
    			);
    			$ch = curl_init();
    			curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
    			curl_setopt($ch, CURLOPT_URL, $end_point );
    			curl_setopt($ch, CURLOPT_POST, 1);
    			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data) );
    			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    			$response = curl_exec($ch);
			}
			
			// Send email when status changed
			$to_mail = $get_user_data_email;
			$subject_email = 'Your order has been canceled';
			$body_email = "<html><head><title></title></head><body>Your order has been canceled as these items (list items) are no longer available. Please contact us <a href='https://shop.epestic.com/client/john-restaurant/'>(call button)</a> so we can process your new order for you or order again at your convenience.</body></html>";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
        	$headers .= "From: John Restaurant <admin@epestic.com>" . "\r\n";
	        
	        wp_mail( $to_mail, $subject_email, $body_email, $headers );
        }
	// }
}
add_action( 'transition_post_status', 'wpdocs_run_on_publish_only', 10, 3 );
// End notification


// Generate csv in between two dates
function func_export_all_posts() {
    if(isset($_POST['order_export_submit'])) {
		global $wpdb;
		$order_start_date = $_POST['order_export_start_date'];
		$order_end_date = $_POST['order_export_end_date'];

		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="order-export-'.$order_start_date.'-'.$order_end_date.'.csv"');
		header('Pragma: no-cache');
		header('Expires: 0');

		$file = fopen('php://output', 'w');

		fputcsv($file, array('Order ID', 'Order Date', 'Total Amount', 'Person Name'));
  
       	$Table_Name = $wpdb->prefix.'posts'; 
		$sql_query = $wpdb->prepare("SELECT * FROM $Table_Name WHERE post_date >= '$order_start_date' AND post_date <= '$order_end_date' && post_type = 'mprm_order' && post_status NOT IN ('trash') ORDER BY ID") ;
		$rows = $wpdb->get_results($sql_query, ARRAY_A);
		if(!empty($rows)){ 
			foreach($rows as $Record) {
				$order_id = $Record['ID'];
				$order_total_amount = get_post_meta($Record['ID'], '_mprm_order_total');
				$total_amount = $order_total_amount[0];
				$get_order_meta = get_post_meta($Record['ID'], '_mprm_order_meta');
				$user_name = $get_order_meta[0]['user_info']['first_name'];
				
				$OutputRecord = array(
					$Record['ID'],
					$Record['post_date'],
					$total_amount,
					$user_name
				); 
				fputcsv($file, $OutputRecord);
			}
		}
  		exit();
    }
} 
add_action( 'init', 'func_export_all_posts' );





register_activation_hook(__FILE__, array(MP_Restaurant_Menu_Setup_Plugin::init(), 'on_activation'));
register_deactivation_hook(__FILE__, array('MP_Restaurant_Menu_Setup_Plugin', 'on_deactivation'));
add_action( 'plugins_loaded', array('MP_Restaurant_Menu_Setup_Plugin', 'init') );

/**
 * Class MP_Restaurant_Menu_Setup_Plugin
 */
class MP_Restaurant_Menu_Setup_Plugin {
	protected static $instance;
	/**
	 * MP_Restaurant_Menu_Setup_Plugin constructor.
	 */
	public function __construct() {
		MP_Restaurant_Menu_Setup_Plugin::include_all();
		Core::get_instance()->init_plugin(MP_RM_PLUGIN_NAME);
		if (!defined('MP_RM_TEMPLATE_PATH')) {

			define('MP_RM_TEMPLATE_PATH', $this->template_path());

		}

	}

	

	/**

	 * Include files

	 */

	static function include_all() {

		/**

		 * Include Gump Validator

		 */

		require_once MP_RM_LIBS_PATH . 'gump.class.php';

		/**

		 * Include WP Parser

		 */

		require_once MP_RM_LIBS_PATH . 'parsers.php';

		/**

		 * Include WP Parser

		 */

		require_once MP_RM_LIBS_PATH . 'gateways/ipnlistener.php';

		/**

		 * Include state

		 */

		require_once MP_RM_CLASSES_PATH . 'class-state-factory.php';

		/**

		 * Include Core class

		 */

		require_once MP_RM_CLASSES_PATH . 'class-core.php';

		/**

		 * Include Core class

		 */

		require_once MP_RM_CLASSES_PATH . 'class-capability.php';

		/**

		 * Include Model

		 */

		require_once MP_RM_CLASSES_PATH . 'class-model.php';

		/**

		 * Include Controller

		 */

		require_once MP_RM_CLASSES_PATH . 'class-controller.php';

		/**

		 * Include Preprocessor

		 */

		require_once MP_RM_CLASSES_PATH . 'class-preprocessor.php';

		/**

		 * Include Module

		 */

		require_once MP_RM_CLASSES_PATH . 'class-module.php';

		/**

		 * Include view

		 */

		require_once MP_RM_CLASSES_PATH . 'class-view.php';

		/**

		 * Include media

		 */

		require_once MP_RM_CLASSES_PATH . 'class-media.php';

		/**

		 * Include hooks

		 */

		require_once MP_RM_CLASSES_PATH . 'class-hooks.php';

		/**

		 * Include hooks

		 */

		require_once MP_RM_CLASSES_PATH . 'class-shortcodes.php';

		

		require_once MP_RM_CLASSES_PATH . 'upgrade/class-upgrade.php';

		require_once MP_RM_CLASSES_PATH . 'upgrade/class-install.php';

	}

	

	/**

	 * Get the template path.

	 * @return string

	 */

	public function template_path() {

		return apply_filters('mprm_template_path', 'mp-restaurant-menu/');

	}

	

	/**

	 * @return MP_Restaurant_Menu_Setup_Plugin

	 */

	public static function init() {

		if (null === self::$instance) {

			self::$instance = new self();

		}

		

		return self::$instance;

	}

	

	/**

	 * On activation plugin

	 */

	public static function on_activation() {



		//Register all custom post type, taxonomy and rewrite rule

		Media::get_instance()->register_all_post_type();

		Media::get_instance()->register_all_taxonomies();

		

		// User capability

		Install::get_instance()->setup_roles_capabilities();

		

		// Create table/tables

		Install::get_instance()->create_structure();

		

		flush_rewrite_rules();

	}

	

	/**

	 * On deactivation plugin

	 */

	public static function on_deactivation() {

		update_option('mprm_capabilities_version', '0.0.0');

		flush_rewrite_rules();

	}

	

}