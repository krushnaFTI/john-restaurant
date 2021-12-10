<?php 

require_once("dompdf/vendor/autoload.php"); 

require_once('../../../wp-load.php');

global $wpdb;
global $post;

$html .= '<html>';
$html .= '<head>';
$html .= '<style>
            table.order, table.total_table{ width: 50%; margin: 0 auto;}
            table.order, table.order tr td{ border: 1px solid #eee;}
            table.order tr th{background-color:#eee; padding: 10px 0;}
            table.order tr th:first-child{width: 50%;}    
            table.order tr th:nth-child(2){width: 25%;}    
            table.order tr th:last-child{width: 25%;}    
            table.order td:nth-child(2), table.order td:last-child { text-align: center; }

            table.total_table{padding-top: 10px; }
            table.total_table tr td:first-child{width: 75%; text-align: right;}
            table.total_table tr td:last-child{width: 25%; text-align: center;}
        </style>';
$html .= '</head>';
$html .= '<body>';




$get_order_id = $_GET['data'];

$get_post_meta = get_post_meta( $get_order_id, '_mprm_order_meta', true );
$get_gateway = get_post_meta( $get_order_id, '_mprm_order_gateway', true );
$get_phone_number = get_post_meta( $get_order_id, '_mprm_order_phone_number', true );
$get_completed_date = get_post_meta( $get_order_id, '_mprm_completed_date', true );
$get_delivery = get_post_meta( $get_order_id, 'mpde_delivery', true );
$get_total = get_post_meta( $get_order_id, '_mprm_order_total', true );
$get_total_tax = get_post_meta( $get_order_id, '_mprm_order_tax', true );
$get_shipping_address = get_post_meta( $get_order_id, '_mprm_order_shipping_address', true );


$get_order_data = maybe_unserialize($get_post_meta);
$get_delivery_meta = maybe_unserialize($get_delivery);

/* Get Order Meta */
$first_name = $get_order_data['user_info']['first_name'];
$last_name = $get_order_data['user_info']['last_name'];
$discount = $get_order_data['user_info']['discount'];
$email_user = $get_order_data['user_info']['email'];
$address = $get_order_data['user_info']['address'];
$purchase_date = $get_order_data['date'];

$menu_items = $get_order_data['menu_items'];
$cart_details = $get_order_data['cart_details'];
$currency = $get_order_data['currency'];

$array_length = count($cart_details);



/* Get Delivery Meta */
$address_type = $get_delivery_meta['address_type'];
$delivery_street = $get_delivery_meta['delivery_street'];
$delivery_apartment = $get_delivery_meta['delivery_apartment'];
$delivery_gate_code = $get_delivery_meta['delivery_gate_code'];
$delivery_notes = $get_delivery_meta['delivery_notes'];
$time_mode = $get_delivery_meta['time-mode'];
$order_hours = $get_delivery_meta['order-hours'];
$order_minutes = $get_delivery_meta['order-minutes'];
$delivery_cost = $get_delivery_meta['delivery_cost'];
$delivery_mode = $get_delivery_meta['delivery_mode'];

use Dompdf\Dompdf;
$dompdf = new Dompdf(); 

$html .= "<strong>Order No:</strong> ".$get_order_id."<br><br>";
$html .= "<strong>Purchase Date:</strong> ".$purchase_date."<br><br>";
if($get_shipping_address){
    $html .= 'Shipping Address: '.$get_shipping_address.'<br><br>';
}

$html .= "<strong>Address:</strong><br>";
$html .= $first_name." ".$last_name."<br>".$address_type.", ".$delivery_street.", ".$delivery_apartment.", ".$delivery_gate_code."<br><br>";
$html .= "<strong>Phone Number:</strong> ".$get_phone_number."<br><br>";
$html .= "<strong>Email:</strong> ".$email_user."<br><br>";
$html .= "<strong>Payment Method:</strong> ".$get_gateway."<br><br>";
$html .= "<strong>Delivery Mode:</strong> ".$delivery_mode."<br><br>";
$html .= "<strong>When is it for?:</strong> ".$time_mode."<br><br>";

$html .= '<table class="order">';
$html .= '<tr>';
$html .= '<th>Item Name</th>';
$html .= '<th>Quantity</th>';
$html .= '<th>Price</th>';
$html .= '</tr>';

$i=0;
foreach($cart_details as $get_order_details){
    if($i <= $array_length){
        $menu_item_id = $get_order_data['cart_details'][$i]['id'];

        $cart_item_name = get_the_title($menu_item_id);
        
        $quantity = $get_order_data['cart_details'][$i]['quantity'];
        $item_price = $get_order_data['cart_details'][$i]['item_price'];
        $item_discount = $get_order_data['cart_details'][$i]['discount'];
        $item_subtotal = $get_order_data['cart_details'][$i]['subtotal'];
        $item_tax = $get_order_data['cart_details'][$i]['tax'];
        //$item_price = $get_order_data['cart_details'][$i]['subtotal'];
        $item_fees = $get_order_data['cart_details'][$i]['fees'];
        
        $html .= '<tr>';
            $html .= "<td>".$cart_item_name."</td>";
            $html .= "<td>".$quantity."</td>";
            $html .= "<td>".$item_price."</td>";
        $html .= "</tr>";
    }
    $i++;  
}
$html .= '</table>';


$html .= '<table class="total_table">';
$html .= '<tr><td>Delivery: </td><td>'.$delivery_cost.'</td></tr>';
$html .= '<tr><td>Discount: </td><td>';
if($discount == 'none'){
    $html .= "0";
}else{
    $html .= $discount;
}
$html .= "</td></tr>";
$html .= '<tr><td>Tax: </td><td>'.$get_total_tax.'</td></tr>';
$html .= '<tr><td>Total: </td><td>'.$get_total.'</td></tr>';
$html .= '</table>';


$html .= '</body>';
$html .= '</html>';

$dompdf->loadHtml($html);

$dompdf->setPaper('Letter', 'landscape');
$dompdf->render();
$dompdf->stream('invoice-'.$get_order_id);