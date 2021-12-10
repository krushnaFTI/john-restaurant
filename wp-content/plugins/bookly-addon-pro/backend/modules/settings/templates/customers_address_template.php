<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Settings\Inputs;

Inputs::renderTextArea( 'bookly_l10n_cst_address_template', __( 'Customer address', 'bookly' ), __( 'Configure how the customer\'s address will be displayed in notifications.', 'bookly' ), 3 );

$codes = array(
    'country'            => get_option( 'bookly_l10n_label_country' ),
    'state'              => get_option( 'bookly_l10n_label_state' ),
    'postcode'           => get_option( 'bookly_l10n_label_postcode' ),
    'city'               => get_option( 'bookly_l10n_label_city' ),
    'street'             => get_option( 'bookly_l10n_label_street' ),
    'street_number'      => get_option( 'bookly_l10n_label_street_number' ),
    'additional_address' => get_option( 'bookly_l10n_label_additional_address' ),
);

echo Bookly\Lib\Utils\Codes::tableHtml( $codes );
?>
<br>