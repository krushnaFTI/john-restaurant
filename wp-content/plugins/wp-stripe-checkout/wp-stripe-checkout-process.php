<?php

session_start();
unset($_SESSION['elFinderCaches']);
function wp_stripe_checkout_process_webhook(){

    if(!isset($_REQUEST['wp_stripe_co_webhook']))

    {

        return;

    }

    //status_header(200);

    http_response_code(200);

    // retrieve the request's body and parse it as JSON

    $body = @file_get_contents('php://input');

    // grab the event information

    $event_json = json_decode($body);



    $allowed_events = array("checkout.session.completed"); //add event types that we want to handle

    if (!in_array($event_json->type, $allowed_events))   

    {

        return;

    }

    wp_stripe_checkout_debug_log("Received event notification from Stripe. Event type: ".$event_json->type, true);

    wp_stripe_checkout_debug_log_array($event_json, true);

    $client_reference_id = sanitize_text_field($event_json->data->object->client_reference_id);

    if(!isset($client_reference_id) || empty($client_reference_id)){

        wp_stripe_checkout_debug_log("Client Reference ID could not be found. This notification cannot be processed.", false);

        return;

    }

    if(strpos($client_reference_id, 'wpsc') === false){

        wp_stripe_checkout_debug_log("This payment was not initiated by the Stripe checkout plugin.", false);

        return;

    }

    $payment_data = array();

    $subscription_id = sanitize_text_field($event_json->data->object->subscription);

    if(isset($subscription_id) && !empty($subscription_id)){

        $payment_data['txn_id'] = $subscription_id;

        wp_stripe_checkout_debug_log("This notification is for a subscription payment.", true);

        $payment_data['stripe_customer_id'] = sanitize_text_field($event_json->data->object->customer);

        if(!isset($payment_data['stripe_customer_id']) || empty($payment_data['stripe_customer_id'])){

            wp_stripe_checkout_debug_log("Customer ID could not be found. This notification cannot be processed.", false);

            return;

        }

        $customers = WP_SC_Stripe_API::retrieve('customers/'.$payment_data['stripe_customer_id']);

        $payment_data['customer_email'] = sanitize_text_field($customers->email);

        if(!isset($payment_data['customer_email']) || empty($payment_data['customer_email'])){

            wp_stripe_checkout_debug_log("Customer email could not be found. This notification cannot be processed.", false);

            return;

        }

        $subscriptions = WP_SC_Stripe_API::retrieve('subscriptions/'.$subscription_id);

        $product_id = sanitize_text_field($subscriptions->plan->product);

        if(!isset($product_id) || empty($product_id)){

            wp_stripe_checkout_debug_log("Product ID could not be found. This notification cannot be processed.", false);

            return;

        }

        $products = WP_SC_Stripe_API::retrieve('products/'.$product_id);

        $payment_data['product_name'] = sanitize_text_field($products->name);

        $amount = sanitize_text_field($event_json->data->object->amount_total);

        $payment_data['price'] = $amount/100;

        $currency = sanitize_text_field($event_json->data->object->currency);

        $payment_data['currency_code'] = strtoupper($currency);

        

        $payment_method_id = sanitize_text_field($subscriptions->default_payment_method);

        if(!isset($payment_method_id) || empty($payment_method_id)){

            wp_stripe_checkout_debug_log("Payment method could not be found. This notification cannot be processed.", false);

            return;

        }

        $payment_methods = WP_SC_Stripe_API::retrieve('payment_methods/'.$payment_method_id);               

        $billing_name = $payment_methods->billing_details->name;

        $payment_data['billing_name'] = isset($billing_name) && !empty($billing_name) ? sanitize_text_field($billing_name) : '';

        $payment_data['billing_first_name'] = '';

        $payment_data['billing_last_name'] = '';

        if(!empty($payment_data['billing_name'])){

            $billing_name_parts = explode(" ", $payment_data['billing_name']);

            $payment_data['billing_first_name'] = isset($billing_name_parts[0]) && !empty($billing_name_parts[0]) ? $billing_name_parts[0] : '';

            $payment_data['billing_last_name'] = isset($billing_name_parts[1]) && !empty($billing_name_parts[1]) ? array_pop($billing_name_parts) : '';

        }

        $address_line1 = $payment_methods->billing_details->address->line1;

        $payment_data['billing_address_line1'] = isset($address_line1) && !empty($address_line1) ? sanitize_text_field($address_line1) : '';

        $address_zip = $payment_methods->billing_details->address->postal_code;

        $payment_data['billing_address_zip'] = isset($address_zip) && !empty($address_zip) ? sanitize_text_field($address_zip) : '';

        $address_state = $payment_methods->billing_details->address->state;

        $payment_data['billing_address_state'] = isset($address_state) && !empty($address_state) ? sanitize_text_field($address_state) : '';

        $address_city = $payment_methods->billing_details->address->city;

        $payment_data['billing_address_city'] = isset($address_city) && !empty($address_city) ? sanitize_text_field($address_city) : '';

        $address_country = $payment_methods->billing_details->address->country;

        $payment_data['billing_address_country'] = isset($address_country) && !empty($address_country) ? sanitize_text_field($address_country) : '';

    }

    else{

        $payment_intent_id = $event_json->data->object->payment_intent;

        if(!isset($payment_intent_id) || empty($payment_intent_id)){

            wp_stripe_checkout_debug_log("Payment Intent ID could not be found. This notification cannot be processed.", false);

            return;

        }



        $payment_intent = WP_SC_Stripe_API::retrieve('payment_intents/'.$payment_intent_id);



        $payment_data['product_name'] = sanitize_text_field($payment_intent->charges->data[0]->description);

        $amount = sanitize_text_field($payment_intent->charges->data[0]->amount);

        $payment_data['price'] = $amount/100;

        $currency = sanitize_text_field($payment_intent->charges->data[0]->currency);

        $payment_data['currency_code'] = strtoupper($currency);



        $billing_name = $payment_intent->charges->data[0]->billing_details->name;

        $payment_data['billing_name'] = isset($billing_name) && !empty($billing_name) ? sanitize_text_field($billing_name) : '';

        $payment_data['billing_first_name'] = '';

        $payment_data['billing_last_name'] = '';

        if(!empty($payment_data['billing_name'])){

            $billing_name_parts = explode(" ", $payment_data['billing_name']);

            $payment_data['billing_first_name'] = isset($billing_name_parts[0]) && !empty($billing_name_parts[0]) ? $billing_name_parts[0] : '';

            $payment_data['billing_last_name'] = isset($billing_name_parts[1]) && !empty($billing_name_parts[1]) ? array_pop($billing_name_parts) : '';

        }

        $address_line1 = $payment_intent->charges->data[0]->billing_details->address->line1;

        $payment_data['billing_address_line1'] = isset($address_line1) && !empty($address_line1) ? sanitize_text_field($address_line1) : '';

        $address_zip = $payment_intent->charges->data[0]->billing_details->address->postal_code;

        $payment_data['billing_address_zip'] = isset($address_zip) && !empty($address_zip) ? sanitize_text_field($address_zip) : '';

        $address_state = $payment_intent->charges->data[0]->billing_details->address->state;

        $payment_data['billing_address_state'] = isset($address_state) && !empty($address_state) ? sanitize_text_field($address_state) : '';

        $address_city = $payment_intent->charges->data[0]->billing_details->address->city;

        $payment_data['billing_address_city'] = isset($address_city) && !empty($address_city) ? sanitize_text_field($address_city) : '';

        $address_country = $payment_intent->charges->data[0]->billing_details->address->country;

        $payment_data['billing_address_country'] = isset($address_country) && !empty($address_country) ? sanitize_text_field($address_country) : '';

        $customer_email = $payment_intent->charges->data[0]->billing_details->email;

        $payment_data['customer_email'] = sanitize_email($customer_email);

        $payment_data['stripe_customer_id'] = sanitize_text_field($event_json->data->object->customer);

        //process data

        $txn_id = sanitize_text_field($payment_intent->charges->data[0]->id);

        if(!isset($txn_id) || empty($txn_id)){

            $txn_id = $payment_intent_id;

        }

        $payment_data['txn_id'] = $txn_id;

    }

    $args = array(

        'post_type' => 'mprm_order',

        'meta_query' => array(

            array(

                'key' => '_txn_id',

                'value' => $payment_data['txn_id'],

                'compare' => '=',

            ),

        ),

    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {  //a record already exists

        wp_stripe_checkout_debug_log("An order with this transaction ID already exists. This payment will not be processed.", false);

        return;

    }

    $content = '';

    $content .= '<strong>Transaction ID:</strong> '.$payment_data['txn_id'].'<br />';

    $content .= '<strong>Product name:</strong> '.$payment_data['product_name'].'<br />';

    $content .= '<strong>Amount:</strong> '.$payment_data['price'].'<br />';

    $content .= '<strong>Currency:</strong> '.$payment_data['currency_code'].'<br />';

    if(!empty($payment_data['billing_name'])){

        $content .= '<strong>Billing Name:</strong> '.$payment_data['billing_name'].'<br />';

    }

    if(!empty($payment_data['customer_email'])){

        $content .= '<strong>Email:</strong> '.$payment_data['customer_email'].'<br />'; 

    }

    if(!empty($payment_data['stripe_customer_id'])){

        $content .= '<strong>Stripe Customer ID:</strong> '.$payment_data['stripe_customer_id'].'<br />'; 

    }

    if(!empty($payment_data['billing_address_line1'])){

        $content .= '<strong>Billing Address:</strong> '.$payment_data['billing_address_line1'];

        if(!empty($payment_data['billing_address_city'])){

            $content .= ', '.$payment_data['billing_address_city'];

        }

        if(!empty($payment_data['billing_address_state'])){

            $content .= ', '.$payment_data['billing_address_state'];

        }

        if(!empty($payment_data['billing_address_zip'])){

            $content .= ', '.$payment_data['billing_address_zip'];

        }

        if(!empty($payment_data['billing_address_country'])){

            $content .= ', '.$payment_data['billing_address_country'];

        }

        $content .= '<br />';

    }

    $payment_data['order_id'] = '';

    $wp_stripe_checkout_order = array(

        'post_title' => 'order',

        'post_type' => 'mprm_order',

        'post_content' => '',

        'post_status' => 'publish',

    );

    wp_stripe_checkout_debug_log("Updating order information", true);

    $post_id = wp_insert_post($wp_stripe_checkout_order);  //insert a new order

    $post_updated = false;

    if ($post_id > 0) {

        $post_content = $content;

        $updated_post = array(

            'ID' => $post_id,

            'post_title' => $post_id,

            'post_type' => 'mprm_order',

            'post_content' => $post_content

        );

        $updated_post_id = wp_update_post($updated_post);  //update the order

        if ($updated_post_id > 0) {  //successfully updated

            $post_updated = true;

        }

    }

    //save order information

    if ($post_updated) {

        $payment_data['order_id'] = $post_id;

        update_post_meta($post_id, '_txn_id', $payment_data['txn_id']);

        update_post_meta($post_id, '_name', $payment_data['billing_name']);

        update_post_meta($post_id, '_amount', $payment_data['price']);

        update_post_meta($post_id, '_email', $payment_data['customer_email']);

        wp_stripe_checkout_debug_log("Order information updated", true);

        $email_options = wp_stripe_checkout_get_email_option();

        add_filter('wp_mail_from', 'wp_stripe_checkout_set_email_from');

        add_filter('wp_mail_from_name', 'wp_stripe_checkout_set_email_from_name');

        if(isset($email_options['purchase_email_enabled']) && !empty($email_options['purchase_email_enabled']) && !empty($payment_data['customer_email'])){

            $subject = $email_options['purchase_email_subject'];

            $type = $email_options['purchase_email_type'];

            $body = $email_options['purchase_email_body'];

            $body = wp_stripe_checkout_do_email_tags($payment_data, $body);

            if($type == "html"){

                add_filter('wp_mail_content_type', 'wp_stripe_checkout_set_html_email_content_type');

                $body = apply_filters('wp_stripe_checkout_email_body_wpautop', true) ? wpautop($body) : $body;

            }

            wp_stripe_checkout_debug_log("Sending a purchase receipt email to ".$payment_data['customer_email'], true);

            $mail_sent = wp_mail($payment_data['customer_email'], $subject, $body);

            if($type == "html"){

                remove_filter('wp_mail_content_type', 'wp_stripe_checkout_set_html_email_content_type');

            }

            if($mail_sent == true){

                wp_stripe_checkout_debug_log("Email was sent successfully by WordPress", true);

            }

            else{

                wp_stripe_checkout_debug_log("Email could not be sent by WordPress", false);

            }

        }

        if(isset($email_options['sale_notification_email_enabled']) && !empty($email_options['sale_notification_email_enabled']) && !empty($email_options['sale_notification_email_recipient'])){

            $subject = $email_options['sale_notification_email_subject'];

            $type = $email_options['sale_notification_email_type'];

            $body = $email_options['sale_notification_email_body'];

            $body = wp_stripe_checkout_do_email_tags($payment_data, $body);

            if($type == "html"){

                add_filter('wp_mail_content_type', 'wp_stripe_checkout_set_html_email_content_type');

                $body = apply_filters('wp_stripe_checkout_email_body_wpautop', true) ? wpautop($body) : $body;

            }

            wp_stripe_checkout_debug_log("Sending a sale notification email to ".$email_options['sale_notification_email_recipient'], true);

            $mail_sent = wp_mail($email_options['sale_notification_email_recipient'], $subject, $body);

            if($type == "html"){

                remove_filter('wp_mail_content_type', 'wp_stripe_checkout_set_html_email_content_type');

            }

            if($mail_sent == true){

                wp_stripe_checkout_debug_log("Email was sent successfully by WordPress", true);

            }

            else{

                wp_stripe_checkout_debug_log("Email could not be sent by WordPress", false);

            }

        }

        remove_filter('wp_mail_from', 'wp_stripe_checkout_set_email_from');

        remove_filter('wp_mail_from_name', 'wp_stripe_checkout_set_email_from_name');      

        do_action('wpstripecheckout_order_processed', $post_id);

    } else {

        wp_stripe_checkout_debug_log("Order information could not be updated", false);

        return;

    }

    wp_stripe_checkout_debug_log("Oder processing completed", true, true);

    do_action('wpstripecheckout_payment_completed', $payment_data);

}



function wp_stripe_checkout_process_order() {

    if (!isset($_POST['wp_stripe_checkout_legacy']) && !isset($_POST['wp_stripe_checkout_legacy'])) {

        return;

    }

    if (!isset($_POST['stripeToken']) && !isset($_POST['stripeTokenType'])) {

        return;

    }

    $nonce = $_REQUEST['_wpnonce'];

    if ( !wp_verify_nonce($nonce, 'wp_stripe_checkout_legacy')){

        $error_msg = __('Error! Nonce Security Check Failed!', 'wp-stripe-checkout');

        wp_die($error_msg);

    }

    $_POST = stripslashes_deep($_POST);

    $stripeToken = sanitize_text_field($_POST['stripeToken']);

    if (empty($stripeToken)) {

        $error_msg = __('Please make sure your card details have been entered correctly and that your browser supports JavaScript.', 'wp-stripe-checkout');

        $error_msg .= ' ' . __('Please also make sure that you are including jQuery and there are no JavaScript errors on the page.', 'wp-stripe-checkout');

        wp_die($error_msg);

    }

    if (!isset($_POST['item_name']) || empty($_POST['item_name'])) {

        $error_msg = __('Product name could not be found.', 'wp-stripe-checkout');

        wp_die($error_msg);

    }

    $payment_data = array();

    $payment_data['product_name'] = sanitize_text_field($_POST['item_name']);

    /*

    $transient_name = 'wpstripecheckout-amount-' . sanitize_title_with_dashes($payment_data['product_name']);

    $payment_data['price'] = get_transient($transient_name);

    if(!isset($payment_data['price']) || !is_numeric($payment_data['price'])){

        $error_msg = __('Product price could not be found.', 'wp-stripe-checkout');

        wp_die($error_msg);

    }

    $transient_name = 'wpstripecheckout-currency-' . sanitize_title_with_dashes($payment_data['product_name']);

    $payment_data['currency_code'] = get_transient($transient_name);

    if(!isset($payment_data['currency_code']) || empty($payment_data['currency_code'])){

        $error_msg = __('Currency could not be found.', 'wp-stripe-checkout');

        wp_die($error_msg);

    }

    */

    if (!isset($_POST['item_price']) || !is_numeric($_POST['item_price'])) {

        $error_msg = __('Product price could not be found.', 'wp-stripe-checkout');

        wp_die($error_msg);

    }

    $payment_data['price'] = sanitize_text_field($_POST['item_price']);

    if (!isset($_POST['item_amount']) || !is_numeric($_POST['item_amount'])) {

        $error_msg = __('Product amount could not be found.', 'wp-stripe-checkout');

        wp_die($error_msg);

    }

    $payment_data['amount'] = sanitize_text_field($_POST['item_amount']);

    if (!isset($_POST['item_currency']) || empty($_POST['item_currency'])) {

        $error_msg = __('Currency could not be found.', 'wp-stripe-checkout');

        wp_die($error_msg);

    }

    $payment_data['currency_code'] = sanitize_text_field($_POST['item_currency']);

    $payment_data['product_description'] = '';

    if(isset($_POST['item_description']) && !empty($_POST['item_description'])){

        $payment_data['product_description'] = sanitize_text_field($_POST['item_description']);

    }

    $success_url = '';

    if (isset($_POST['success_url']) && !empty($_POST['success_url'])) {

        $success_url = esc_url_raw($_POST['success_url']);

    }

    $payment_data['billing_name'] = isset($_POST['stripeBillingName']) && !empty($_POST['stripeBillingName']) ? sanitize_text_field($_POST['stripeBillingName']) : '';

    $customer_description = '';

    $payment_data['billing_first_name'] = '';

    $payment_data['billing_last_name'] = '';

    if(!empty($payment_data['billing_name'])){

        $customer_description = __('Name', 'wp-stripe-checkout').': '.$payment_data['billing_name'];

        $billing_name_parts = explode(" ", $payment_data['billing_name']);

        $payment_data['billing_first_name'] = isset($billing_name_parts[0]) && !empty($billing_name_parts[0]) ? $billing_name_parts[0] : '';

        $payment_data['billing_last_name'] = isset($billing_name_parts[1]) && !empty($billing_name_parts[1]) ? array_pop($billing_name_parts) : '';

    }

    $payment_data['billing_address_line1'] = isset($_POST['stripeBillingAddressLine1']) && !empty($_POST['stripeBillingAddressLine1']) ? sanitize_text_field($_POST['stripeBillingAddressLine1']) : '';

    $payment_data['billing_address_zip'] = isset($_POST['stripeBillingAddressZip']) && !empty($_POST['stripeBillingAddressZip']) ? sanitize_text_field($_POST['stripeBillingAddressZip']) : '';

    $payment_data['billing_address_state'] = isset($_POST['stripeBillingAddressState']) && !empty($_POST['stripeBillingAddressState']) ? sanitize_text_field($_POST['stripeBillingAddressState']) : '';

    $payment_data['billing_address_city'] = isset($_POST['stripeBillingAddressCity']) && !empty($_POST['stripeBillingAddressCity']) ? sanitize_text_field($_POST['stripeBillingAddressCity']) : '';

    $payment_data['billing_address_country'] = isset($_POST['stripeBillingAddressCountry']) && !empty($_POST['stripeBillingAddressCountry']) ? sanitize_text_field($_POST['stripeBillingAddressCountry']) : '';

    $payment_data['shipping_name'] = isset($_POST['stripeShippingName']) && !empty($_POST['stripeShippingName']) ? sanitize_text_field($_POST['stripeShippingName']) : '';

    $payment_data['shipping_first_name'] = '';

    $payment_data['shipping_last_name'] = '';

    if(!empty($payment_data['shipping_name'])){

        $shipping_name_parts = explode(" ", $payment_data['shipping_name']);

        $payment_data['shipping_first_name'] = isset($shipping_name_parts[0]) && !empty($shipping_name_parts[0]) ? $shipping_name_parts[0] : '';

        $payment_data['shipping_last_name'] = isset($shipping_name_parts[1]) && !empty($shipping_name_parts[1]) ? array_pop($shipping_name_parts) : '';

    }

    $payment_data['shipping_address_line1'] = isset($_POST['stripeShippingAddressLine1']) && !empty($_POST['stripeShippingAddressLine1']) ? sanitize_text_field($_POST['stripeShippingAddressLine1']) : '';

    $payment_data['shipping_address_zip'] = isset($_POST['stripeShippingAddressZip']) && !empty($_POST['stripeShippingAddressZip']) ? sanitize_text_field($_POST['stripeShippingAddressZip']) : '';

    $payment_data['shipping_address_state'] = isset($_POST['stripeShippingAddressState']) && !empty($_POST['stripeShippingAddressState']) ? sanitize_text_field($_POST['stripeShippingAddressState']) : '';

    $payment_data['shipping_address_city'] = isset($_POST['stripeShippingAddressCity']) && !empty($_POST['stripeShippingAddressCity']) ? sanitize_text_field($_POST['stripeShippingAddressCity']) : '';

    $payment_data['shipping_address_country'] = isset($_POST['stripeShippingAddressCountry']) && !empty($_POST['stripeShippingAddressCountry']) ? sanitize_text_field($_POST['stripeShippingAddressCountry']) : '';

    wp_stripe_checkout_debug_log("Post Data", true);

    wp_stripe_checkout_debug_log_array($_POST, true);

    // Other charge data

    $post_data['currency'] = strtolower($payment_data['currency_code']);

    $post_data['amount'] = $payment_data['amount']; //$payment_data['price'] * 100;

    $post_data['description'] = $payment_data['product_description'];

    $post_data['capture'] = 'true';

    $payment_data['customer_email'] = '';

    if(isset($_POST['stripeEmail'])) {

        $payment_data['customer_email'] = sanitize_email($_POST['stripeEmail']);

        $post_data['receipt_email'] = $payment_data['customer_email'];

        //create a Stripe customer

        $customer_args = array(

                'email'       => $payment_data['customer_email'],

                'description' => $customer_description,

                'source' => $stripeToken,

        );

        wp_stripe_checkout_debug_log("Creating a Stripe customer", true);

        $response = WP_SC_Stripe_API::request($customer_args, 'customers');

        wp_stripe_checkout_debug_log("Response Data", true);

        wp_stripe_checkout_debug_log(print_r($response, true), true);

        $post_data['customer'] = $response->id;

    }

    //only specify a source if no customber is created

    if(!isset($post_data['customer'])) {

        $post_data['source'] = $stripeToken;

    }

    $post_data['expand[]'] = 'balance_transaction';



    // Make the request

    wp_stripe_checkout_debug_log("Creating a charge request", true);

    $response = WP_SC_Stripe_API::request($post_data);

    wp_stripe_checkout_debug_log("Response Data", true);

    wp_stripe_checkout_debug_log(print_r($response, true), true);

    //process data

    $payment_data['txn_id'] = $response->id;

    $args = array(

        'post_type' => 'mprm_order',

        'meta_query' => array(

            array(

                'key' => '_txn_id',

                'value' => $payment_data['txn_id'],

                'compare' => '=',

            ),

        ),

    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {  //a record already exists

        wp_stripe_checkout_debug_log("An order with this transaction ID already exists. This payment will not be processed.", false);

        return;

    }

    $content = '';

    $content .= '<strong>Transaction ID:</strong> '.$payment_data['txn_id'].'<br />';

    $content .= '<strong>Product name:</strong> '.$payment_data['product_name'].'<br />';

    $content .= '<strong>Amount:</strong> '.$payment_data['price'].'<br />';

    $content .= '<strong>Currency:</strong> '.$payment_data['currency_code'].'<br />';

    if(!empty($payment_data['billing_name'])){

        $content .= '<strong>Billing Name:</strong> '.$payment_data['billing_name'].'<br />';

    }

    if(!empty($payment_data['customer_email'])){

        $content .= '<strong>Email:</strong> '.$payment_data['customer_email'].'<br />'; 

    }

    if(!empty($payment_data['billing_address_line1'])){

        $content .= '<strong>Billing Address:</strong> '.$payment_data['billing_address_line1'];

        if(!empty($payment_data['billing_address_city'])){

            $content .= ', '.$payment_data['billing_address_city'];

        }

        if(!empty($payment_data['billing_address_state'])){

            $content .= ', '.$payment_data['billing_address_state'];

        }

        if(!empty($payment_data['billing_address_zip'])){

            $content .= ', '.$payment_data['billing_address_zip'];

        }

        if(!empty($payment_data['billing_address_country'])){

            $content .= ', '.$payment_data['billing_address_country'];

        }

        $content .= '<br />';

    }

    if(!empty($payment_data['shipping_address_line1'])){

        $content .= '<strong>Shipping Address:</strong> '.$payment_data['shipping_address_line1'];

        if(!empty($payment_data['shipping_address_city'])){

            $content .= ', '.$payment_data['shipping_address_city'];

        }

        if(!empty($payment_data['shipping_address_state'])){

            $content .= ', '.$payment_data['shipping_address_state'];

        }

        if(!empty($payment_data['shipping_address_zip'])){

            $content .= ', '.$payment_data['shipping_address_zip'];

        }

        if(!empty($payment_data['shipping_address_country'])){

            $content .= ', '.$payment_data['shipping_address_country'];

        }

        $content .= '<br />';

    }

    $payment_data['order_id'] = '';

    $wp_stripe_checkout_order = array(

        'post_title' => 'order',

        'post_type' => 'mprm_order',

        'post_content' => '',

        'post_status' => 'publish',

    );

    wp_stripe_checkout_debug_log("Updating order information", true);

    $post_id = wp_insert_post($wp_stripe_checkout_order);  //insert a new order

    $post_updated = false;

    if ($post_id > 0) {

        $post_content = $content;

        $updated_post = array(

            'ID' => $post_id,

            'post_title' => $post_id,

            'post_type' => 'mprm_order',

            'post_content' => $post_content

        );

        $updated_post_id = wp_update_post($updated_post);  //update the order

        if ($updated_post_id > 0) {  //successfully updated

            $post_updated = true;

        }

    }





    /* custom code */
   
    $get_currency = get_option('wp_stripe_checkout_options');
    if($get_currency['stripe_testmode'] == '1'){
        $_SESSION['stripe_mode'] = "test";
    }else{
        $_SESSION['stripe_mode'] = "live";
    }

    $stripe_data_array = unserialize($_SESSION['mprm']['mprm_cart']);

    $get_currency = get_option('wp_stripe_checkout_options');
    $get_currency_details = $get_currency['stripe_currency_code'];

    // $stripe_mode = $_SESSION['stripe_mode'];
    // $mprm_purchase_data = $_SESSION['mprm']['mprm_purchase'];
    // $mprm_purchase_data = unserialize($mprm_purchase_data);

    /* Session data */

    // $menu_item_array = $mprm_purchase_data['menu_items'];
    // $fees_array = $mprm_purchase_data['fees'];
    // $subtotal_array = $mprm_purchase_data['subtotal'];
    // $discount_array = $mprm_purchase_data['discount'];
    // $tax_array = $mprm_purchase_data['tax'];
    // $price_array = $mprm_purchase_data['price'];
    // $purchase_key_array = $mprm_purchase_data['purchase_key'];
    // $user_email_array = $mprm_purchase_data['user_email'];
    // $date_array = $mprm_purchase_data['date'];
    
    // $user_info_array = $mprm_purchase_data['user_info'];
    // $user_id_array = $mprm_purchase_data['user_info']['id'];
    // $user_address_array = $mprm_purchase_data['user_info']['address'];

    // $post_data_array = $mprm_purchase_data['post_data'];
    // $post_gateway_array = $mprm_purchase_data['post_data']['mprm-gateway'];
    // $delivery_mode_array = $mprm_purchase_data['post_data']['delivery-mode'];
    // $mprm_email_array = $mprm_purchase_data['post_data']['mprm_email'];
    // $address_type_array = $mprm_purchase_data['post_data']['delivery']['address_type'];
    // $delivery_street_array = $mprm_purchase_data['post_data']['delivery']['delivery_street'];
    // $delivery_apartment_array = $mprm_purchase_data['post_data']['delivery']['delivery_apartment'];
    // $delivery_gate_code_array = $mprm_purchase_data['post_data']['delivery']['delivery_gate_code'];
    // $delivery_notes_array = $mprm_purchase_data['post_data']['delivery']['delivery_notes'];
    // $time_mode_array = $mprm_purchase_data['post_data']['delivery']['time-mode'];
    // $order_hours_array = $mprm_purchase_data['post_data']['delivery']['order-hours'];
    // $order_minutes_array = $mprm_purchase_data['post_data']['delivery']['order-minutes'];    

    // $cart_details_array = $mprm_purchase_data['cart_details'];
    // $gateway_array = $mprm_purchase_data['gateway'];
    // $card_info_array = $mprm_purchase_data['card_info'];
    // $customer_note_array = $mprm_purchase_data['customer_note'];
    // $shipping_address_array = $mprm_purchase_data['shipping_address'];
    // $phone_number_array = $mprm_purchase_data['phone_number'];
    // $shipping_cost_array = $mprm_purchase_data['shipping_cost'];
    // $no_shipping_array = $mprm_purchase_data['no_shipping'];
    // $shipping_array = $mprm_purchase_data['shipping'];

    // $session_order_meta = array(
    //     'user_info' => $user_info_array,
    //     'key' => $purchase_key_array,
    //     'email' => $mprm_email_array,
    //     'date' => $date_array,
    //     'menu_items' => $menu_item_array,
    //     'cart_details' => $cart_details_array,
    //     'fees' => $fees_array,
    //     'currency' => 'INR'
    // );

    // $final_mprm_delivery = array(
    //     'address_type' => $address_type_array,
    //     'delivery_street' => $delivery_street_array,
    //     'delivery_apartment' => $delivery_apartment_array,
    //     'delivery_gate_code' => $delivery_gate_code_array,
    //     'delivery_notes' => $delivery_notes_array,
    //     'time-mode' => $time_mode_array,
    //     'order-hours' => $order_hours_array,
    //     'order-minutes' => $order_minutes_array,
    //     'delivery_mode' => $delivery_mode_array
    // );

    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
        $get_user_ip = $_SERVER['HTTP_CLIENT_IP'];  
    }  
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
        $get_user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
    } else{  
        $get_user_ip = $_SERVER['REMOTE_ADDR'];  
    }  

    $stripe_mode = $_SESSION['stripe_mode'];
    $total_amount_cart = $_SESSION['total_amount_cart'];
    $shipping_user_email = $_SESSION['shipping_user_email'];
    if($shipping_user_email == ""){
        $shipping_user_email = $_POST['stripeEmail'];
    }
    $address_type = $_SESSION['stripe-address_type'];
    $delivery_street = $_SESSION['stripe-delivery_street'];
    $delivery_apartment = $_SESSION['stripe-delivery_apartment'];
    $delivery_gate_code = $_SESSION['stripe-delivery_gate_code'];
    $delivery_notes = $_SESSION['stripe-delivery_notes'];
    $time_mode = $_SESSION['time-mode'];
    $order_hours = $_SESSION['order-hours'];
    $order_minutes = $_SESSION['order-minutes'];
    $user_fname = $_SESSION['user-fname'];
    $user_lname = $_SESSION['user-lname'];
    $user_phone = $_SESSION['user-phone'];
    $payment_method = $_SESSION['payment-method'];
    $delivery_amount = $_SESSION['delivery-amount'];
    $tax = $_SESSION['tax'];
    $tax = ltrim($tax, '$');
    $subtotal = $_SESSION['subtotal'];
    $total_amount = $_SESSION['total-amount'];
    $checkout_email = $_SESSION['checkout-email'];
    $item_name = $_SESSION['item-name'];
    if($item_name == ""){
        $item_name = $_POST['item_name'];
    }
    $description = $_SESSION['item-description'];

    $delivery_mode = $_SESSION['mprm']['mpde_delivery_type'];
    
    $final_mprm_delivery = array(
        'address_type' => $address_type,
        'delivery_street' => $delivery_street,
        'delivery_apartment' => $delivery_apartment,
        'delivery_gate_code' => $delivery_gate_code,
        'delivery_notes' => $delivery_notes,
        'time-mode' => $time_mode,
        'order-hours' => $order_hours,
        'order-minutes' => $order_minutes,
        'delivery_mode' => $delivery_mode
    );
    
    // foreach($stripe_data_array as $stripe_value){
    //     $cart_details_array[] = $stripe_data_array;
        
    //     $get_product_id = $stripe_value['id'];
    //     $product_item_price = get_post_meta($get_product_id, 'price');
    //     $product_title = get_the_title($get_product_id);

    //     $cart_details_array['name'] = $product_title;
    //     $cart_details_array['item_number'] = array($stripe_data_array);
    //     $cart_details_array['item_price'] = $product_item_price;

    // }
    
    $session_order_meta = array(
        'user_info' => array(
            'first_name' => $user_fname,
            'last_name' => $user_lname,
            'discount' => 'none',
            'id' => '',
            'email' => $shipping_user_email,
            'address' => ''
        ),
        'key' => "",
        'email' => $shipping_user_email,
        'date' => "",
        'menu_items' => $stripe_data_array,
        'cart_details' => $stripe_data_array,
        'fees' => array(),
        'currency' => $get_currency_details
    );

    $webpushr_id_value = $_SESSION['webpushr_id_value'];

    $get_current_user_id = get_current_user_id();
    

    // $execut= $wpdb->query( $wpdb->prepare( "UPDATE `wp_users` SET user_nicename = %d WHERE ID = %s", "Harde_Bande", 546 ) );
    // $wpdb->query( $wpdb->prepare( "UPDATE `wp_users` SET webpushr_id = 17371332 WHERE ID = 7") );
    
    // UPDATE `wp_users` SET `webpushr_id` = '12' WHERE `wp_users`.`ID` = 7;
    // wp_update_user(array('ID' => $get_current_user_id, 'webpushr_id' => $webpushr_id_value));

    // if($get_current_user_id){
    // }

    //save order information

    if ($post_updated) {
        $payment_data['order_id'] = $post_id;
        
        // update_post_meta($post_id, '_txn_id', $payment_data['txn_id']);
        // update_post_meta($post_id, '_name', $payment_data['billing_name']);
        // update_post_meta($post_id, '_amount', $payment_data['price']);
        // update_post_meta($post_id, '_email', $payment_data['customer_email']);

        // update_post_meta($post_id, '_mprm_order_meta', $session_order_meta);
        // update_post_meta($post_id, '_mprm_order_gateway', $post_gateway_array);
        // update_post_meta($post_id, '_mprm_order_user_id', $user_id_array);
        // update_post_meta($post_id, '_mprm_order_user_email', $mprm_email_array);
        // update_post_meta($post_id, '_mprm_order_user_ip', $get_user_ip);
        // update_post_meta($post_id, '_mprm_order_purchase_key', $purchase_key_array);
        // update_post_meta($post_id, '_mprm_order_mode', $stripe_mode);
        // update_post_meta($post_id, '_mprm_order_customer_note', $customer_note_array);
        // update_post_meta($post_id, '_mprm_order_shipping_address', $user_address_array);
        // update_post_meta($post_id, '_mprm_order_phone_number', $phone_number_array);
        // update_post_meta($post_id, '_mprm_order_customer_id', $user_id_array);
        // update_post_meta($post_id, '_mprm_order_total', $price_array);
        // update_post_meta($post_id, '_mprm_order_tax', $tax_array);
        // update_post_meta($post_id, 'mpde_delivery', $final_mprm_delivery);
        // update_post_meta($post_id, '_mprm_completed_date', $date_array);

        update_post_meta($post_id, '_mprm_order_meta', $session_order_meta);
        update_post_meta($post_id, '_mprm_order_gateway', $payment_method);
        update_post_meta($post_id, '_mprm_order_user_id', '1');
        update_post_meta($post_id, '_mprm_order_user_email', $shipping_user_email);
        update_post_meta($post_id, '_mprm_order_user_ip', $get_user_ip);
        update_post_meta($post_id, '_mprm_order_purchase_key', '');
        update_post_meta($post_id, '_mprm_order_mode', $stripe_mode);
        update_post_meta($post_id, '_mprm_order_customer_note', '');
        update_post_meta($post_id, '_mprm_order_shipping_address', '');
        update_post_meta($post_id, '_mprm_order_phone_number', $user_phone);
        update_post_meta($post_id, '_mprm_order_customer_id', '1');
        update_post_meta($post_id, '_mprm_order_total', $total_amount);
        update_post_meta($post_id, '_mprm_order_tax', $tax);
        update_post_meta($post_id, 'mpde_delivery', $final_mprm_delivery);
        update_post_meta($post_id, '_mprm_completed_date', "");
        update_post_meta($post_id, 'webpushr_id', $webpushr_id_value);
        
        wp_stripe_checkout_debug_log("Order information updated", true);

        $email_options = wp_stripe_checkout_get_email_option();

        add_filter('wp_mail_from', 'wp_stripe_checkout_set_email_from');

        add_filter('wp_mail_from_name', 'wp_stripe_checkout_set_email_from_name');

        if(isset($email_options['purchase_email_enabled']) && !empty($email_options['purchase_email_enabled']) && !empty($payment_data['customer_email'])){


            $subject = $email_options['purchase_email_subject'];

            $type = $email_options['purchase_email_type'];

            $body = $email_options['purchase_email_body'];

            $body = wp_stripe_checkout_do_email_tags($payment_data, $body);

            if($type == "html"){

                add_filter('wp_mail_content_type', 'wp_stripe_checkout_set_html_email_content_type');

                $body = apply_filters('wp_stripe_checkout_email_body_wpautop', true) ? wpautop($body) : $body;

            }

            wp_stripe_checkout_debug_log("Sending a purchase receipt email to ".$payment_data['customer_email'], true);

            $mail_sent = wp_mail($payment_data['customer_email'], $subject, $body);

            if($type == "html"){

                remove_filter('wp_mail_content_type', 'wp_stripe_checkout_set_html_email_content_type');

            }

            if($mail_sent == true){

                wp_stripe_checkout_debug_log("Email was sent successfully by WordPress", true);

            }

            else{

                wp_stripe_checkout_debug_log("Email could not be sent by WordPress", false);

            }

        }

        if(isset($email_options['sale_notification_email_enabled']) && !empty($email_options['sale_notification_email_enabled']) && !empty($email_options['sale_notification_email_recipient'])){

            $subject = $email_options['sale_notification_email_subject'];

            $type = $email_options['sale_notification_email_type'];

            $body = $email_options['sale_notification_email_body'];

            $body = wp_stripe_checkout_do_email_tags($payment_data, $body);

            if($type == "html"){

                add_filter('wp_mail_content_type', 'wp_stripe_checkout_set_html_email_content_type');

                $body = apply_filters('wp_stripe_checkout_email_body_wpautop', true) ? wpautop($body) : $body;

            }

            wp_stripe_checkout_debug_log("Sending a sale notification email to ".$email_options['sale_notification_email_recipient'], true);

            $mail_sent = wp_mail($email_options['sale_notification_email_recipient'], $subject, $body);

            if($type == "html"){

                remove_filter('wp_mail_content_type', 'wp_stripe_checkout_set_html_email_content_type');

            }

            if($mail_sent == true){

                wp_stripe_checkout_debug_log("Email was sent successfully by WordPress", true);

            }

            else{

                wp_stripe_checkout_debug_log("Email could not be sent by WordPress", false);

            }

        }

        remove_filter('wp_mail_from', 'wp_stripe_checkout_set_email_from');

        remove_filter('wp_mail_from_name', 'wp_stripe_checkout_set_email_from_name');      

        do_action('wpstripecheckout_order_processed', $post_id);

    } else {

        wp_stripe_checkout_debug_log("Order information could not be updated", false);

        return;

    }

    wp_stripe_checkout_debug_log("Oder processing completed", true, true);

    do_action('wpstripecheckout_payment_completed', $payment_data);

    $stripe_options = wp_stripe_checkout_get_option();

    if(!empty($success_url)){

        wp_safe_redirect($success_url);

        exit;

    }

    else if(isset($stripe_options['return_url']) && !empty($stripe_options['return_url'])){

        wp_safe_redirect($stripe_options['return_url']);

        exit;

    }

}





function wp_stripe_checkout_do_email_tags($payment_data, $content){

    $search = array(

        '{first_name}', 

        '{last_name}', 

        '{full_name}',

        '{txn_id}',

        '{product_name}',

        '{currency_code}',

        '{price}',

        '{customer_email}'

    );

    $replace = array(

        $payment_data['billing_first_name'], 

        $payment_data['billing_last_name'],

        $payment_data['billing_name'],

        $payment_data['txn_id'],

        $payment_data['product_name'],

        $payment_data['currency_code'],

        $payment_data['price'],

        $payment_data['customer_email']

    );

    $content = str_replace($search, $replace, $content);

    return $content;

}



function wp_stripe_checkout_set_email_from($from){

    $email_options = wp_stripe_checkout_get_email_option();

    if(isset($email_options['email_from_address']) && !empty($email_options['email_from_address'])){

        $from = $email_options['email_from_address'];

    }

    return $from;

}



function wp_stripe_checkout_set_email_from_name($from_name){

    $email_options = wp_stripe_checkout_get_email_option();

    if(isset($email_options['email_from_name']) && !empty($email_options['email_from_name'])){

        $from_name = $email_options['email_from_name'];

    }

    return $from_name;

}



function wp_stripe_checkout_set_html_email_content_type($content_type){

    $content_type = 'text/html';

    return $content_type;

}