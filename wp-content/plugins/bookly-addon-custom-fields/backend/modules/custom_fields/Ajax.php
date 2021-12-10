<?php
namespace BooklyCustomFields\Backend\Modules\CustomFields;

use Bookly\Lib as BooklyLib;

/**
 * Class Ajax
 * @package BooklyCustomFields\Backend\Modules\CustomFields
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * Save custom fields.
     */
    public static function saveCustomFields()
    {
        $fields = self::parameter( 'fields', '[]' );
        $per_service     = (int) self::parameter( 'per_service' );
        $merge_repeating = (int) self::parameter( 'merge_repeating' );
        $custom_fields   = json_decode( $fields, true );

        foreach ( $custom_fields as $custom_field ) {
            switch ( $custom_field['type'] ) {
                case 'textarea':
                case 'text-content':
                case 'text-field':
                case 'captcha':
                case 'file':
                    do_action(
                        'wpml_register_single_string',
                        'bookly',
                        sprintf(
                            'custom_field_%d_%s',
                            $custom_field['id'],
                            sanitize_title( $custom_field['label'] )
                        ),
                        $custom_field['label']
                    );
                    break;
                case 'checkboxes':
                case 'radio-buttons':
                case 'drop-down':
                    do_action(
                        'wpml_register_single_string',
                        'bookly',
                        sprintf(
                            'custom_field_%d_%s',
                            $custom_field['id'],
                            sanitize_title( $custom_field['label'] )
                        ),
                        $custom_field['label']
                    );
                    foreach ( $custom_field['items'] as $label ) {
                        do_action(
                            'wpml_register_single_string',
                            'bookly',
                            sprintf(
                                'custom_field_%d_%s=%s',
                                $custom_field['id'],
                                sanitize_title( $custom_field['label'] ),
                                sanitize_title( $label )
                            ),
                            $label
                        );
                    }
                    break;
            }
        }

        BooklyLib\Proxy\Files::saveCustomFields( $custom_fields );

        update_option( 'bookly_custom_fields_data', $fields );
        update_option( 'bookly_custom_fields_per_service', $per_service );
        update_option( 'bookly_custom_fields_merge_repeating', $merge_repeating );
        wp_send_json_success();
    }
}