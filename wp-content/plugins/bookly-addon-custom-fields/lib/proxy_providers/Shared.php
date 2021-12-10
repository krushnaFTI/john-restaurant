<?php
namespace BooklyCustomFields\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;

/**
 * Class Shared
 * @package BooklyCustomFields\Lib\ProxyProviders;
 */
class Shared extends BooklyLib\Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareTableColumns( $columns, $table )
    {
        if ( $table == BooklyLib\Utils\Tables::APPOINTMENTS ) {
            foreach ( Local::getAll( array( 'captcha', 'text-content', 'file' ) ) as $custom_field ) {
                $columns[ 'custom_fields_' . $custom_field->id ] = $custom_field->label;
            }
        }

        return $columns;
    }
}