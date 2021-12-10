<?php
namespace BooklyPro\Backend\Components\Dialogs\Service\Edit\ProxyProviders;

use Bookly\Lib\Entities;
use Bookly\Backend\Components\Dialogs\Service\Edit\Proxy;

/**
 * Class Local
 * @package BooklyPro\Backend\Modules\Services\ProxyProviders
 */
class Local extends Proxy\Pro
{
    /**
     * @inheritdoc
     */
    public static function renderVisibility( array $service )
    {
        parent::renderTemplate( 'visibility', compact( 'service' ) );
    }

    /**
     * @inheritdoc
     */
    public static function renderPadding( array $service )
    {
        $time_interval = get_option( 'bookly_gen_time_slot_length' );

        parent::renderTemplate( 'padding', compact( 'service', 'time_interval' ) );
    }

    /**
     * @inheritdoc
     */
    public static function renderStaffPreference( array $service )
    {
        $preferences = array(
            Entities\Service::PREFERRED_ORDER                     => __( 'Specified order', 'bookly' ),
            Entities\Service::PREFERRED_LEAST_OCCUPIED            => __( 'Least occupied that day', 'bookly' ),
            Entities\Service::PREFERRED_MOST_OCCUPIED             => __( 'Most occupied that day', 'bookly' ),
            Entities\Service::PREFERRED_LEAST_OCCUPIED_FOR_PERIOD => __( 'Least occupied for period', 'bookly' ),
            Entities\Service::PREFERRED_MOST_OCCUPIED_FOR_PERIOD  => __( 'Most occupied for period', 'bookly' ),
            Entities\Service::PREFERRED_LEAST_EXPENSIVE           => __( 'Least expensive', 'bookly' ),
            Entities\Service::PREFERRED_MOST_EXPENSIVE            => __( 'Most expensive', 'bookly' ),
        );

        $staff_preference = Entities\Service::query( 's' )
            ->leftJoin( 'StaffPreferenceOrder', 'sp', 'sp.service_id = s.id', '\BooklyPro\Lib\Entities' )
            ->leftJoin( 'Staff', 'st', 'st.id = sp.staff_id' )
            ->where( 's.id', $service['id'] )
            ->whereNot( 'st.visibility', 'archive' )
            ->fetchCol( 'GROUP_CONCAT(DISTINCT sp.staff_id ORDER BY sp.position ASC)' );

        $settings = array_replace_recursive(
            array(
                'period' => array(
                    'before' => 0,
                    'after'  => 0,
                ),
                'random' => false
            ),
            (array) json_decode( $service['staff_preference_settings'], true )
        );

        parent::renderTemplate( 'staff_preference', compact( 'service', 'preferences', 'staff_preference', 'settings' ) );
    }

    /**
     * @inheritDoc
     */
    public static function getAdvancedHtml( $service, $service_types, $service_collection, $staff_dropdown_data, $categories_collection )
    {
        return self::renderTemplate( 'advanced_settings', compact( 'service', 'service_types', 'service_collection', 'staff_dropdown_data', 'categories_collection' ), false );
    }

    /**
     * @inheritDoc
     */
    public static function renderAdvancedTab()
    {
        self::renderTemplate( 'advanced_tab' );
    }
}