<?php
namespace Bookly\Lib\Notifications\Booking;

use Bookly\Lib\DataHolders\Booking\Series;
use Bookly\Lib\Entities\Notification;
use Bookly\Lib\DataHolders\Booking\Item;
use Bookly\Lib\DataHolders\Booking\Order;
use Bookly\Lib\Notifications\Base;
use Bookly\Lib\Notifications\Assets\Item\Attachments;
use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\WPML;



/**
 * Class BaseSender
 * @package Bookly\Lib\Notifications\Base
 */
abstract class BaseSender extends Base\Sender
{
    /**
     * Notify client.
     *
     * @param Notification[] $notifications
     * @param Item           $item
     * @param Order          $order
     * @param Codes          $codes
     * @param bool|array     $queue
     */
    protected static function notifyClient( array $notifications, Item $item, Order $order, Codes $codes, &$queue = false )
    {
        if ( $item->getCA()->getLocale() ) {
            WPML::switchLang( $item->getCA()->getLocale() );
        } else {
            WPML::switchToDefaultLang();
        }
        
        $codes->prepareForItem( $item, 'client' );
        $attachments = new Attachments( $codes );
        $i = 1;
        foreach ( $notifications as $notification ) {
            if ( $notification->matchesItemForClient( $item ) ) {
                static::sendToClient( $order->getCustomer(), $notification, $codes, $attachments, $queue );
                
                $cust_reco = $order->getCustomer();
                $cust_id = $cust_reco->getId();
                
                global $wpdb;                
                if($i==1){
                    $get_appointment = $wpdb->get_results("SELECT * FROM `wp_bookly_customer_appointments` WHERE customer_id = $cust_id ORDER BY created_at desc LIMIT 1");
                    $get_custom_field_value = $get_appointment[0]->custom_fields;
                    $get_custom_field_value = json_decode ($get_custom_field_value);
                    $get_webpushr_value = $get_custom_field_value[0]->value;
                    
                    
                    if($get_webpushr_value != 'false'){
                        
                        
                        $site_title = get_bloginfo( 'name' );
                        // $get_admin_webpushr_key = get_user_meta( 1, 'admin_webpushr_key');
                        // $get_admin_webpushr_auth_token = get_user_meta( 1, 'admin_webpushr_auth_token');
                        
                        $get_admin_webpushr_key = get_option('admin_webpushr_key_enter');
	                    $get_admin_webpushr_auth_token = get_option('admin_webpushr_authtoken_enter');
                        
                        $http_header = array( 
                    		"Content-Type: Application/Json", 
                    		"webpushrKey: ".$get_admin_webpushr_key, 
                    		"webpushrAuthToken: ".$get_admin_webpushr_auth_token
                    	);
                    	
                    	$end_point = 'https://api.webpushr.com/v1/notification/send/sid';
                    	
                        $req_data = array(
                            'title' 			=> $site_title, //required
                            'message' 		=> "Thank you for the booking.", //required
                            'target_url'	=> 'https://www.webpushr.com', //required
                            'sid'		=> $get_webpushr_value
                        );
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
                        curl_setopt($ch, CURLOPT_URL, $end_point );
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data) );
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_exec($ch);
                    }
                }
            } $i++;
        }
        

        if ( $queue === false ) {
            $attachments->clear();
        }

        WPML::restoreLang();
    }

    /**
     * Notify staff and/or administrators.
     *
     * @param Notification[] $notifications
     * @param Item           $item
     * @param Order          $order
     * @param Codes          $codes
     * @param array|bool     $queue
     */
    protected static function notifyStaffAndAdmins( array $notifications, Item $item, Order $order, Codes $codes, &$queue = false )
    {
        WPML::switchToDefaultLang();

        // Reply to customer.
        $reply_to = null;
        if ( get_option( 'bookly_email_reply_to_customers' ) ) {
            $customer = $order->getCustomer();
            $reply_to = array( 'email' => $customer->getEmail(), 'name' => $customer->getFullName() );
        }

        /** @var Series $item */
        $sub_items = $item->isSeries() ? $item->getFirstItem()->getItems() : $item->getItems();

        foreach ( $sub_items as $sub_item ) {
            $codes->prepareForItem( $sub_item, 'staff' );
            $attachments = new Attachments( $codes );
            foreach ( $notifications as $notification ) {
                switch ( $notification->getType() ) {
                    case Notification::TYPE_NEW_BOOKING_RECURRING:
                    case Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED_RECURRING:
                        $send = $notification
                            ->getSettingsObject()
                            ->allowedServiceWithStatus( $sub_item->getService(), $sub_item->getCA()->getStatus() );
                        break;
                    default:
                        $send = $notification->matchesItemForStaff( $sub_item, $sub_item->getService() );
                        break;
                }
                if ( $send ) {
                    static::sendToStaff( $sub_item->getStaff(), $notification, $codes, $attachments, $reply_to, $queue );
                    static::sendToAdmins( $notification, $codes, $attachments, $reply_to, $queue );
                    
                }
            }
            if ( $queue === false ) {
                $attachments->clear();
            }
        }

        WPML::restoreLang();
    }
}