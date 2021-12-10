<?php
namespace BooklyCustomFields\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Modules\Booking\Proxy;
use BooklyCustomFields\Lib;
use BooklyCustomFields\Lib\Captcha\Captcha;

/**
 * Class Shared
 * @package BooklyCustomFields\Frontend\Modules\Booking\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareInfoTextCodes( array $info_text_codes, array $data )
    {
        if ( get_option( 'bookly_custom_fields_enabled' ) ) {
            $info_text_codes['{custom_fields}'] = isset( $data['custom_fields'] ) ? implode( '<br>', $data['custom_fields'] ) : '';
        }

        return $info_text_codes;
    }

    /**
     * @inheritdoc
     */
    public static function prepareCartItemInfoText( $data, BooklyLib\CartItem $cart_item )
    {
        if ( get_option( 'bookly_custom_fields_enabled' ) ) {
            $data['custom_fields'] = Lib\ProxyProviders\Local::getForCartItem( $cart_item, true );
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public static function renderCustomFieldsOnDetailsStep( BooklyLib\UserBookingData $userData )
    {
        if ( get_option( 'bookly_custom_fields_enabled' ) ) {
            $cf_data = array();

            if ( BooklyLib\Config::customFieldsPerService() ) {
                // Prepare custom fields data per service.
                foreach ( $userData->cart->getItems() as $cart_key => $cart_item ) {
                    $data = array();
                    $service_id = $cart_item->getServiceId();
                    $key = get_option( 'bookly_custom_fields_merge_repeating' ) ? $service_id : $cart_key;

                    if ( !isset( $cf_data[ $key ] ) ) {
                        foreach ( $cart_item->getCustomFields() as $field ) {
                            $data[ $field[ 'id' ] ] = $field[ 'value' ];
                        }
                        if ( $cart_item->getService()->withSubServices() ) {
                            $custom_fields = array();
                            // Collect custom fields for compound service.
                            foreach ( $cart_item->getService()->getSubServices() as $sub_service ) {
                                foreach ( Lib\ProxyProviders\Local::getTranslated( $sub_service->getId() ) as $field ) {
                                    if ( !array_key_exists( $field->id, $custom_fields ) ) {
                                        $custom_fields[ $field->id ] = $field;
                                    }
                                }
                            }
                            $custom_fields = array_values( $custom_fields );
                        } else {
                            $custom_fields = Lib\ProxyProviders\Local::getTranslated( $service_id );
                        }

                        if ( ! BooklyLib\Config::filesActive() || ! get_option( 'bookly_files_enabled' ) ) {
                            $custom_fields = array_filter( $custom_fields, function ( $field ) {
                                return $field->type != 'file';
                            } );
                        }
                        $cf_data[ $key ] = array(
                            'service_title' => BooklyLib\Entities\Service::find( $cart_item->getServiceId() )->getTranslatedTitle(),
                            'custom_fields' => $custom_fields,
                            'data' => $data,
                        );
                    }
                }
            } else {
                $cart_items = $userData->cart->getItems();
                $cart_item = array_pop( $cart_items );
                $data = array();
                foreach ( $cart_item->getCustomFields() as $field ) {
                    $data[ $field[ 'id' ] ] = $field[ 'value' ];
                }
                $custom_fields = Lib\ProxyProviders\Local::getTranslated( null );
                if ( ! BooklyLib\Config::filesActive() || ! get_option( 'bookly_files_enabled' ) ) {
                    $custom_fields = array_filter( $custom_fields, function ( $field ) {
                        return $field->type != 'file';
                    } );
                }
                $cf_data[] = array(
                    'custom_fields' => $custom_fields,
                    'data' => $data,
                );
            }

            if ( strpos( get_option( 'bookly_custom_fields_data' ), '"captcha"' ) !== false ) {
                // Init Captcha.
                Captcha::init( $userData->getFormId() );
            }

            $show_service_title = BooklyLib\Config::customFieldsPerService() && count( $cf_data ) > 1;

            $captcha_url = admin_url( sprintf(
                'admin-ajax.php?action=bookly_custom_fields_captcha&csrf_token=%s&form_id=%s&%f',
                BooklyLib\Utils\Common::getCsrfToken(),
                $userData->getFormId(),
                microtime( true )
            ) );

            self::renderTemplate( '6_details', compact( 'cf_data', 'show_service_title', 'captcha_url' ) );
        }
    }
}