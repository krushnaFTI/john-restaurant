<?php
namespace BooklyCustomFields\Backend\Modules\Notifications\ProxyProviders;

use Bookly\Backend\Modules\Notifications\Proxy;

/**
 * Class Shared
 * @package BooklyCustomFields\Backend\Modules\Notifications\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareNotificationCodes( array $codes, $type )
    {
        $codes['customer_appointment']['custom_fields'] = __( 'combined values of all custom fields', 'bookly' );
        $codes['staff_agenda']['next_day_agenda_extended'] = __( 'extended staff agenda for next day', 'bookly' );
        if ( $type == 'email' ) {
            $codes['customer_appointment']['custom_fields_2c'] = __( 'combined values of all custom fields (formatted in 2 columns)', 'bookly' );
        }

        return $codes;
    }
}