<?php
namespace BooklyCustomFields\Backend\Modules\Settings\ProxyProviders;

use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Backend\Components\Settings\Menu;

/**
 * Class Shared
 * @package BooklyCustomFields\Backend\Modules\Settings\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareCalendarAppointmentCodes( array $codes, $participants )
    {
        if ( $participants == 'one' ) {
            $codes[] = array( 'code' => 'custom_fields', 'description' => __( 'combined values of all custom fields', 'bookly' ) );
        }

        return $codes;
    }

    /**
     * @inheritdoc
     */
    public static function prepareWooCommerceCodes( array $codes )
    {
        $codes[] = array( 'code' => 'custom_fields', 'description' => __( 'combined values of all custom fields', 'bookly' ) );

        return $codes;
    }
}