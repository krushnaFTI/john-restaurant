<?php
namespace BooklyCustomFields\Backend\Modules\CustomFields;

use Bookly\Lib as BooklyLib;

/**
 * Class Page
 * @package BooklyCustomFields\Backend\Modules\CustomFields
 */
class Page extends BooklyLib\Base\Component
{
    /**
     *  Render page.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'bookly' => array(
                'backend/resources/bootstrap/css/bootstrap-theme.min.css',
                'frontend/resources/css/ladda.min.css',
            ),
        ) );

        self::enqueueScripts( array(
            'bookly' => array(
                'backend/resources/bootstrap/js/bootstrap.min.js' => array( 'jquery' ),
                'backend/resources/js/help.js'  => array( 'jquery' ),
                'backend/resources/js/alert.js'  => array( 'jquery' ),
                'backend/resources/js/dropdown.js'  => array( 'jquery' ),
                'frontend/resources/js/spin.min.js' => array( 'jquery' ),
                'frontend/resources/js/ladda.min.js' => array( 'jquery' ),
            ),
            'module' => array( 'js/custom_fields.js' => array( 'jquery-ui-sortable', 'bookly-dropdown.js' ) ),
        ) );

        wp_localize_script( 'bookly-custom_fields.js', 'BooklyCustomFieldsL10n', array(
            'csrf_token' => BooklyLib\Utils\Common::getCsrfToken(),
            'custom_fields' => get_option( 'bookly_custom_fields_data' ),
            'saved'    => __( 'Settings saved.', 'bookly' ),
        ) );

        $service_dropdown_data = BooklyLib\Utils\Common::getServiceDataForDropDown( 's.type = "simple"' );

        self::renderTemplate( 'index', array(
            'services_html' => self::renderTemplate( '_services', compact( 'service_dropdown_data' ), false )
        ) );
    }

}