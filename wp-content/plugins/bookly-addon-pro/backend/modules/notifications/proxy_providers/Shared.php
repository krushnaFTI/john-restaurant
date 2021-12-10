<?php
namespace BooklyPro\Backend\Modules\Notifications\ProxyProviders;

use Bookly\Backend\Modules\Notifications\Proxy;

/**
 * Class Shared
 * @package BooklyPro\Backend\Modules\Notifications\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareNotificationCodes( array $codes, $type )
    {
        $codes['appointment']['online_meeting_url'] = __( 'Online meeting URL', 'bookly' );
        $codes['appointment']['online_meeting_password'] = __( 'Online meeting password', 'bookly' );
        $codes['appointment']['online_meeting_start_url'] = __( 'Online meeting start URL', 'bookly' );
        $codes['appointment']['online_meeting_join_url'] = __( 'Online meeting join URL', 'bookly' );
        $codes['staff']['staff_timezone'] = __( 'Time zone of staff', 'bookly' );

        return $codes;
    }
}