<?php
namespace BooklyCustomFields\Lib\Notifications\Assets\Item\ProxyProviders;

use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Assets\Item\Proxy;
use BooklyCustomFields\Lib\ProxyProviders\Local;

/**
 * Class Shared
 * @package BooklyCustomFields\Lib\Notifications\Assets\Item\ProxyProviders
 */
abstract class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareCodes( Codes $codes )
    {
        $codes->custom_fields    = Local::getFormatted( $codes->getItem()->getCA(), 'text' );
        $codes->custom_fields_2c = Local::getFormatted( $codes->getItem()->getCA(), 'html' );
    }

    /**
     * @inheritdoc
     */
    public static function prepareReplaceCodes( array $replace_codes, Codes $codes, $format )
    {
        $replace_codes['{custom_fields}']    = $codes->custom_fields;
        $replace_codes['{custom_fields_2c}'] = $format == 'html' ? $codes->custom_fields_2c : $codes->custom_fields;

        return $replace_codes;
    }
}