<?php
/**
 * Template Name: Stripe Checkout
 */
session_start();
get_header(); 
global $wpdb;

$stripe_data_array = unserialize($_SESSION['mprm']['mprm_cart']);
$product_title = get_the_title($stripe_data_array[0]['id']);

$_SESSION['total_amount_cart'] = $_POST['stripe-total-amount'];
$total_amount = $_SESSION['total_amount_cart'];

$_SESSION['shipping_user_email'] = $_POST['stripe-checkout-email'];
$user_email = $_SESSION['shipping_user_email'];

$_SESSION['webpushr_id_value'] = $_POST['webpushr_id_value'];
$webpushr_id_value = $_SESSION['webpushr_id_value'];

$_SESSION['stripe-address_type'] = $_POST['stripe-address_type'];
$address_type = $_SESSION['stripe-address_type'];

$_SESSION['stripe-delivery_street'] = $_POST['stripe-delivery_street'];
$delivery_street = $_SESSION['stripe-delivery_street'];

$_SESSION['stripe-delivery_apartment'] = $_POST['stripe-delivery_apartment'];
$delivery_appartment = $_SESSION['stripe-delivery_apartment'];

$_SESSION['stripe-delivery_gate_code'] = $_POST['stripe-delivery_gate_code'];
$gate_code = $_SESSION['stripe-delivery_gate_code'];

$_SESSION['stripe-delivery_notes'] = $_POST['stripe-delivery_notes'];
$delivery_note = $_SESSION['stripe-delivery_notes'];

$_SESSION['time-mode'] = $_POST['stripe-time-mode'];
$time_mode = $_SESSION['time-mode'];

$_SESSION['order-hours'] = $_POST['stripe-order-hours'];
$order_hours = $_SESSION['order-hours'];

$_SESSION['order-minutes'] = $_POST['stripe-order-minutes'];
$order_minutes = $_SESSION['order-minutes'];

$_SESSION['user-fname'] = $_POST['stripe-user-fname'];
$user_fname = $_SESSION['user-fname'];

$_SESSION['user-lname'] = $_POST['stripe-user-lname'];
$user_lname = $_SESSION['user-lname'];

$_SESSION['user-phone'] = $_POST['stripe-user-phone'];
$user_phone = $_SESSION['user-phone'];

$_SESSION['payment-method'] = $_POST['stripe-payment-method'];
$payment_method = $_SESSION['payment-method'];

$_SESSION['delivery-amount'] = $_POST['stripe-delivery-amount'];
$delivery_amount = $_SESSION['delivery-amount'];

$_SESSION['tax'] = $_POST['stripe-tax'];
$tax = $_SESSION['tax'];

$_SESSION['subtotal'] = $_POST['stripe-subtotal'];
$subtotal = $_SESSION['subtotal'];

$_SESSION['total-amount'] = $_POST['stripe-total-amount'];
$total_amount = $_SESSION['total-amount'];

$_SESSION['checkout-email'] = $_POST['stripe-checkout-email'];
$checkout_email = $_SESSION['checkout-email'];

$_SESSION['item-name'] = $_POST['stripe-item-name'];
$item_name = $_SESSION['item-name'];

$_SESSION['item-description'] = $_POST['stripe-item-description'];
$item_description = $_SESSION['item-description']; ?>

<div class="main-content">
	<div class="container">
        <h3>Stripe Payment Confirmation</h3>
        <?php echo "Your Email: ".$user_email."<br><br>";
        echo "Total Amount: $".$total_amount."<br><br>";


        $site_title = get_bloginfo( 'name' );

        echo do_shortcode('[wp_stripe_checkout item_name="'.$product_title.'" description="'.$site_title.'" amount="'.$total_amount.'" label="Pay Now"]'); ?> 

    </div>
</div>

<?php get_footer(); ?>