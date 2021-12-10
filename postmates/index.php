<?php

// // $curl = curl_init();
// // $url = "https://api.postmates.com";
// // curl_setopt($curl, CURLOPT_URL, $url);
// // $username = "4c10b1cd-db32-4e28-9589-033f452462a3";
// // $password = ""; // leave this blank, as per the doc

// // curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
 
// // $res = curl_exec($curl);
// // curl_close($curl);
// // echo "<pre>";
// // print_r($res);
// // echo "</pre>";


// // $post_data = '{"dropoff_address":"20 McAllister St, San Francisco, CA 94102",
// //                 "dropoff_name": "Alice Cust",
// //                 "dropoff_phone_number":"4155555555",
// //                 "manifest":{"reference": "SP 937-215",
// //                         "description": "10kg cardboard box"},
// //                 "manifest_items": [{"name": "Cardboard box",
// //                                   "quantity": 1,
// //                                   "size": "large"}],
// //                 "pickup_address": "101 Market St, San Francisco, CA 94105",
// //                 "pickup_name": "John Doe",
// //                 "pickup_phone_number": "5444444444"
// //             }';



// echo "<br><br><br>Create Delivery<br><br><br>";


// $url1 = "https://api.postmates.com/v1/customers/cus_MrehWzNA4_1pN-/deliveries";
// $uname = "4c10b1cd-db32-4e28-9589-033f452462a3";
// $pwd = "";

// $create_delivery_data = "dropoff_address=20 McAllister St, San Francisco, CA 94102&dropoff_name=Alice Cust&dropoff_phone_number=4155555555&manifest=10kg cardboard box&manifest_items=[]&pickup_address=101 Market St, San Francisco, CA 94105&pickup_name=John Doe&pickup_phone_number=5444444444&tip_by_customer=10";

// $ch_url = curl_init($url1);
// curl_setopt($ch_url, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Accept: application/json'));
// //curl_setopt($ch_url, CURLOPT_HEADER, 1);
// curl_setopt($ch_url, CURLOPT_USERPWD, $uname . ":" . $pwd);
// curl_setopt($ch_url, CURLOPT_TIMEOUT, 30);
// curl_setopt($ch_url, CURLOPT_POST, 1);
// curl_setopt($ch_url, CURLOPT_POSTFIELDS, $create_delivery_data);
// curl_setopt($ch_url, CURLOPT_RETURNTRANSFER, TRUE);
// curl_setopt($ch_url, CURLOPT_SSL_VERIFYPEER, false);
// $create_del_return = curl_exec($ch_url);
// curl_close($ch_url);

// $created_delivery_data = json_decode($create_del_return);

// //$json= json_decode($return, true);
// echo '<pre>'; print_r($created_delivery_data); echo '</pre>';

// $delivery_id = $created_delivery_data->id;




// echo "<br><br><br>Get Delivery<br><br><br>";


// $url2 = "https://api.postmates.com/v1/customers/cus_MrehWzNA4_1pN-/deliveries/".$delivery_id;
// $uname = "4c10b1cd-db32-4e28-9589-033f452462a3";
// $pwd = "";

// //$delivery_quotes_data = "dropoff_address=20 McAllister St, San Francisco, CA 94102&pickup_address=101 Market St, San Francisco, CA 94105";

// $ch_url = curl_init($url2);
// curl_setopt($ch_url, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Accept: application/json'));
// // curl_setopt($ch_url, CURLOPT_HEADER, 1);
// curl_setopt($ch_url, CURLOPT_USERPWD, $uname . ":" . $pwd);
// curl_setopt($ch_url, CURLOPT_TIMEOUT, 30);
// // curl_setopt($ch_url, CURLOPT_POST, 1);
// // curl_setopt($ch_url, CURLOPT_POSTFIELDS, $delivery_quotes_data);
// curl_setopt($ch_url, CURLOPT_RETURNTRANSFER, TRUE);
// curl_setopt($ch_url, CURLOPT_SSL_VERIFYPEER, false);
// $get_delivery_return = curl_exec($ch_url);
// curl_close($ch_url);
// $get_delivery_data = json_decode($get_delivery_return);
// //$json= json_decode($return, true);
// echo '<pre>'; print_r($get_delivery_data); echo '</pre>';



// echo "<br><br><br>Delivery Quotes<br><br><br>";


// $url3 = "https://api.postmates.com/v1/customers/cus_MrehWzNA4_1pN-/delivery_quotes";
// $uname = "4c10b1cd-db32-4e28-9589-033f452462a3";
// $pwd = "";

// $delivery_quotes_data = "dropoff_address=20 McAllister St, San Francisco, CA 94102&pickup_address=101 Market St, San Francisco, CA 94105";

// $ch_url = curl_init($url3);
// curl_setopt($ch_url, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Accept: application/json'));
// // curl_setopt($ch_url, CURLOPT_HEADER, 1);
// curl_setopt($ch_url, CURLOPT_USERPWD, $uname . ":" . $pwd);
// curl_setopt($ch_url, CURLOPT_TIMEOUT, 30);
// curl_setopt($ch_url, CURLOPT_POST, 1);
// curl_setopt($ch_url, CURLOPT_POSTFIELDS, $delivery_quotes_data);
// curl_setopt($ch_url, CURLOPT_RETURNTRANSFER, TRUE);
// curl_setopt($ch_url, CURLOPT_SSL_VERIFYPEER, false);
// $delivery_quote_return = curl_exec($ch_url);
// curl_close($ch_url);
// $delivery_quote_data = json_decode($delivery_quote_return);
// //$json= json_decode($return, true);
// echo '<pre>'; print_r($delivery_quote_data); echo '</pre>';



// echo "<br><br><br>Cancel Delivery<br><br><br>";


// $url4 = "https://api.postmates.com/v1/customers/cus_MrehWzNA4_1pN-/deliveries/".$delivery_id."/cancel";
// $uname = "4c10b1cd-db32-4e28-9589-033f452462a3";
// $pwd = "";

// $ch_url = curl_init($url4);
// curl_setopt($ch_url, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Accept: application/json'));
// curl_setopt($ch_url, CURLOPT_USERPWD, $uname . ":" . $pwd);
// curl_setopt($ch_url, CURLOPT_TIMEOUT, 30);
// curl_setopt($ch_url, CURLOPT_RETURNTRANSFER, TRUE);
// curl_setopt($ch_url, CURLOPT_SSL_VERIFYPEER, false);
// $cancel_delivery_return = curl_exec($ch_url);
// curl_close($ch_url);
// $cancel_delivery_data = json_decode($cancel_delivery_return);
// echo '<pre>'; print_r($cancel_delivery_data); echo '</pre>';



// //echo "<br><br><br>List All Deliveries<br><br><br>";


// $url5 = "https://api.postmates.com/v1/customers/cus_MrehWzNA4_1pN-/deliveries";
// $uname = "4c10b1cd-db32-4e28-9589-033f452462a3";
// $pwd = "";

// $ch_url = curl_init($url5);
// curl_setopt($ch_url, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Accept: application/json'));
// curl_setopt($ch_url, CURLOPT_USERPWD, $uname . ":" . $pwd);
// curl_setopt($ch_url, CURLOPT_RETURNTRANSFER, TRUE);
// curl_setopt($ch_url, CURLOPT_SSL_VERIFYPEER, false);
// $list_deliveries_return = curl_exec($ch_url);
// curl_close($ch_url);
// $list_deliveries_data = json_decode($list_deliveries_return);
// //$json= json_decode($return, true);
// //echo '<pre>'; print_r($list_deliveries_data); echo '</pre>';

?>