<?php

namespace BooklyPro\Backend\Components\Dialogs\Staff\Edit\ProxyProviders;

use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;
use Bookly\Lib as BooklyLib;

/**
 * Class Local
 * @package BooklyPro\Backend\Components\Dialogs\Staff\Edit\ProxyProviders
 */
class Local extends Proxy\Pro
{
    /**
     * @inheritDoc
     */
    public static function enqueueAssets()
    {
        self::enqueueScripts( array(
            'module'   => array(
                /** bookly-staff-details.js enqueue in
                 * @see \Bookly\Backend\Components\Dialogs\Staff\Edit\Dialog::render
                 */
                'js/staff-edit-component.js' => array( 'bookly-staff-details.js', 'jquery' ),
                'js/staff-advanced.js' => array( 'bookly-staff-edit-component.js' ),
                'js/archive.js' => array( 'bookly-staff-advanced.js' ),
            ),
            'frontend' => array(
                'js/spin.min.js'  => array( 'jquery' ),
                'js/ladda.min.js' => array( 'jquery' ),
                'js/datatables.min.js' => array( 'jquery' ),
            ),
            'bookly'   => array(
                'backend/resources/js/alert.js' => array( 'bookly-archive.js' ),
            )
        ) );

        wp_localize_script( 'bookly-archive.js', 'BooklyL10nStaffEdit', array(
            'csrfToken'  => BooklyLib\Utils\Common::getCsrfToken(),
            'areYouSure' => __( 'Are you sure?', 'bookly' ),
            'saved'      => __( 'Settings saved.', 'bookly' ),
            'activeStaffId' => self::parameter( 'staff_id' ),
        ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderArchivingComponents()
    {
        self::renderTemplate( 'archive_dialog' );
    }

    /**
     * @inheritDoc
     */
    public static function getAdvancedHtml( $staff, $tpl_data, $for_backend = true )
    {
        return self::renderTemplate( 'advanced_settings', compact( 'staff', 'tpl_data', 'for_backend' ), false );
    }

    /**
     * @inheritDoc
     */
    public static function renderAdvancedTab()
    {
        self::renderTemplate( 'advanced_tab' );
    }
}