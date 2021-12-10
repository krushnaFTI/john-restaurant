<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * @var Bookly\Lib\Entities\Staff $staff
 */
?>
<div class="form-group">
    <label>Zoom</label>
    <div class="custom-control custom-radio">
        <input type="radio" id="bookly-zoom_personal-0" name="zoom_personal" value="0"<?php checked( $staff->getZoomPersonal() != 1 ) ?> class="custom-control-input"/>
        <label for="bookly-zoom_personal-0" class="custom-control-label"><?php esc_html_e( 'Default', 'bookly' ) ?></label>
    </div>
    <div class="custom-control custom-radio">
        <input type="radio" id="bookly-zoom_personal-1" name="zoom_personal" value="1"<?php checked( $staff->getZoomPersonal() ) ?> class="custom-control-input"/>
        <label for="bookly-zoom_personal-1" class="custom-control-label"><?php esc_html_e( 'Personal', 'bookly' ) ?></label>
    </div>
    <small class="form-text text-muted"><?php esc_html_e( 'This setting allows to set up personal Zoom account for staff member', 'bookly' ) ?></small>
</div>
<div class="form-group border-left ml-4 pl-3 bookly-js-zoom-keys"<?php if ( ! $staff->getZoomPersonal() ): ?> style="display: none"<?php endif ?>>
    <div class="form-group">
        <label for="bookly-zoom_jwt_api_key"><?php esc_html_e( 'API Key', 'bookly' ) ?></label>
        <input type="text" class="form-control" id="bookly-zoom_jwt_api_key" name="zoom_jwt_api_key" value="<?php echo esc_attr( $staff->getZoomJwtApiKey() ) ?>"/>
    </div>
    <div class="form-group">
        <label for="bookly-zoom_jwt_api_secret"><?php esc_html_e( 'API Secret', 'bookly' ) ?></label>
        <input type="text" class="form-control" id="bookly-zoom_jwt_api_secret" name="zoom_jwt_api_secret" value="<?php echo esc_attr( $staff->getZoomJwtApiSecret() ) ?>"/>
    </div>
</div>