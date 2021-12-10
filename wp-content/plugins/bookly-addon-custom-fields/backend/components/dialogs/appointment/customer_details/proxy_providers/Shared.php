<?php
namespace BooklyCustomFields\Backend\Components\Dialogs\Appointment\CustomerDetails\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Backend\Components\Dialogs\Appointment\CustomerDetails\Proxy;
use BooklyCustomFields\Lib;

/**
 * Class Shared
 * @package BooklyCustomFields\Backend\Components\Dialogs\Appointment\CustomerDetails\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function renderDetails()
    {
        $custom_fields = Lib\ProxyProviders\Local::getWhichHaveData();

        if ( ! BooklyLib\Config::filesActive() ) {
            $custom_fields = array_filter( $custom_fields, function ( $field ) {
                return $field->type != 'file';
            } );
        }

        self::renderTemplate( 'details', compact( 'custom_fields' ) );
    }
}