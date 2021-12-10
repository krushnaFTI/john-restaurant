<?php
namespace BooklyPro\Backend\Modules\Settings\ProxyProviders;

use Bookly\Backend\Components\Settings\Selects;
use Bookly\Backend\Components\Settings\Menu;
use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Lib\Utils;
use Bookly\Lib as BooklyLib;
use BooklyPro\Lib\Config;

/**
 * Class Local
 * @package BooklyPro\Backend\Modules\Settings\ProxyProviders
 */
class Local extends Proxy\Pro
{
    /**
     * @inheritDoc
     */
    public static function renderFinalStepUrl()
    {
        self::renderTemplate( 'final_step_url' );
    }

    /**
     * @inheritDoc
     */
    public static function renderCancellationConfirmationUrl()
    {
        self::renderTemplate( 'cancellation_confirmation_url' );
    }

    /**
     * @inheritDoc
     */
    public static function renderCustomersAddress()
    {
        self::renderTemplate( 'customers_address' );
    }

    /**
     * @inheritDoc
     */
    public static function renderCustomersLimitStatuses()
    {
        $statuses = array();
        foreach ( BooklyLib\Entities\CustomerAppointment::getStatuses() as $status ) {
            $statuses[] = array( $status, BooklyLib\Entities\CustomerAppointment::statusToString( $status ) );
        }
        Selects::renderMultiple( 'bookly_cst_limit_statuses', __( 'Do not count appointments in \'Limit appointments per customer\' with the following statuses', 'bookly' ), null, $statuses );
    }

    /**
     * @inheritDoc
     */
    public static function renderCustomersAddressTemplate()
    {
        self::renderTemplate( 'customers_address_template' );
    }

    /**
     * @inheritDoc
     */
    public static function renderCustomersBirthday()
    {
        self::renderTemplate( 'customers_birthday' );
    }

    /**
     * @inheritDoc
     */
    public static function renderGoogleCalendarMenuItem()
    {
        Menu::renderItem( __( 'Google Calendar', 'bookly' ), 'google_calendar' );
    }

    /**
     * @inheritDoc
     */
    public static function renderGoogleCalendarTab()
    {
        $fetch_limits = array(
            array( '0', __( 'Disabled', 'bookly' ) ),
            array( 25, 25 ),
            array( 50, 50 ),
            array( 100, 100 ),
            array( 250, 250 ),
            array( 500, 500 ),
            array( 1000, 1000 ),
            array( 2500, 2500 )
        );

        self::renderTemplate( 'google_calendar_tab', compact( 'fetch_limits' ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderPurchaseCodeMenuItem()
    {
        Menu::renderItem( __( 'Purchase Code', 'bookly' ), 'purchase_code' );
    }

    /**
     * @inheritDoc
     */
    public static function renderPurchaseCodeTab()
    {
        self::renderTemplate( 'purchase_code_tab', array( 'grace_remaining_days' => Config::graceRemainingDays() ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderMinimumTimeRequirement()
    {
        $values = array(
            'bookly_gen_min_time_prior_booking' => array( array( '0', __( 'Disabled', 'bookly' ) ) ),
            'bookly_gen_min_time_prior_cancel'  => array( array( '0', __( 'Disabled', 'bookly' ) ) ),
        );
        foreach ( array_merge( array( 0.5 ), range( 1, 12 ), range( 24, 144, 24 ), range( 168, 672, 168 ) ) as $hour ) {
            $values['bookly_gen_min_time_prior_booking'][] = array( $hour, Utils\DateTime::secondsToInterval( $hour * HOUR_IN_SECONDS ) );
        }
        foreach ( array_merge( array( 1 ), range( 2, 12, 2 ), range( 24, 168, 24 ) ) as $hour ) {
            $values['bookly_gen_min_time_prior_cancel'][] = array( $hour, Utils\DateTime::secondsToInterval( $hour * HOUR_IN_SECONDS ) );
        }

        self::renderTemplate( 'minimum_time_requirement', compact( 'values' ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderCreateWordPressUser()
    {
        Selects::renderSingle( 'bookly_cst_create_account', __( 'Create WordPress user account for customers', 'bookly' ), __( 'If this setting is enabled then Bookly will be creating WordPress user accounts for all new customers. If the user is logged in then the new customer will be associated with the existing user account.', 'bookly' ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderCancelAppointmentAction()
    {
        Selects::renderSingle( 'bookly_cst_cancel_action', __( 'Cancel appointment action', 'bookly' ), __( 'Select what happens when customer clicks cancel appointment link. With "Delete" the appointment will be deleted from the calendar. With "Cancel" only appointment status will be changed to "Cancelled".', 'bookly' ),
            array( array( 'delete', __( 'Delete', 'bookly' ) ), array( 'cancel', __( 'Cancel', 'bookly' ) ) ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderNewUserAccountRole()
    {
        $roles = array();
        $wp_roles = new \WP_Roles();
        foreach ( $wp_roles->get_names() as $role => $name ) {
            $roles[] = array( $role, $name );
        }
        Selects::renderSingle( 'bookly_cst_new_account_role', __( 'New user account role', 'bookly' ), __( 'Select what role will be assigned to newly created WordPress user accounts for customers.', 'bookly' ), $roles );
    }

    /**
     * @inheritDoc
     */
    public static function renderOnlineMeetingsMenuItem()
    {
        Menu::renderItem( __( 'Online Meetings', 'bookly' ), 'online_meetings' );
    }

     /**
     * @inheritDoc
     */
    public static function renderOnlineMeetingsTab()
    {
        self::renderTemplate( 'online_meetings_tab' );
    }
}