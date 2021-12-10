<?php
namespace BooklyPro\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;

/**
 * Class Local
 * @package BooklyPro\Backend\Modules\Appointments\ProxyProviders
 */
class Local extends Proxy\Pro
{
    /**
     * @inheritDoc
     */
    public static function renderBookingStatesSelector()
    {
        self::renderTemplate( 'booking_states_selector' );
    }

    /**
     * @inheritDoc
     */
    public static function renderBookingStatesText()
    {
        self::renderTemplate( 'booking_states_text' );
    }

    /**
     * @inheritDoc
     */
    public static function renderShowAddress()
    {
        self::renderTemplate( 'show_address' );
    }

    /**
     * @inheritDoc
     */
    public static function renderShowBirthday()
    {
        self::renderTemplate( 'show_birthday' );
    }

    /**
     * @inheritDoc
     */
    public static function renderTimeZoneSwitcher()
    {
        $current_offset = get_option( 'gmt_offset' );
        $tz_string      = get_option( 'timezone_string' );
        if ( $tz_string == '' ) { // Create a UTC+- zone if no timezone string exists
            if ( $current_offset == 0 ) {
                $tz_string = 'UTC+0';
            } else if ( $current_offset < 0 ) {
                $tz_string = 'UTC' . $current_offset;
            } else {
                $tz_string = 'UTC+' . $current_offset;
            }
        }

        self::renderTemplate( 'time_zone_switcher', compact( 'tz_string' ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderTimeZoneSwitcherCheckbox()
    {
        self::renderTemplate( 'time_zone_switcher_checkbox' );
    }

    /**
     * @inheritDoc
     */
    public static function renderFacebookButton()
    {
        self::renderTemplate( 'fb_button' );
    }

    /**
     * @inheritDoc
     */
    public static function renderShowFacebookButton()
    {
        self::renderTemplate( 'show_fb_button_checkbox' );
    }
}