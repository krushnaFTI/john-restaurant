<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BooklyCustomFields\Lib\Plugin;
use Bookly\Frontend\Modules\Booking\Proxy;
?>

<?php foreach ( $cf_data as $key => $cf_item ) : ?>
    <div class="bookly-custom-fields-container" data-key="<?php echo $key ?>">
    <?php if ( $show_service_title && ! empty ( $cf_item['custom_fields'] ) ) : ?>
        <div class="bookly-box"><b><?php echo $cf_item['service_title'] ?></b></div>
    <?php endif ?>
    <?php foreach ( $cf_item['custom_fields'] as $custom_field ) : ?>
        <div class="bookly-box bookly-custom-field-row" data-id="<?php echo $custom_field->id ?>" data-type="<?php echo $custom_field->type ?>">
            <div class="bookly-form-group">
                <?php if ( $custom_field->type != 'text-content' ) : ?>
                    <label><?php echo $custom_field->label ?></label>
                <?php endif ?>
                <div>
                    <?php if ( $custom_field->type == 'text-field' ) : ?>
                        <input type="text" class="bookly-custom-field" value="<?php echo esc_attr( @$cf_item['data'][ $custom_field->id ] ) ?>"/>
                    <?php elseif ( $custom_field->type == 'textarea' ) : ?>
                        <textarea rows="3" class="bookly-custom-field"><?php echo esc_html( @$cf_item['data'][ $custom_field->id ] ) ?></textarea>
                    <?php elseif ( $custom_field->type == 'text-content' ) : ?>
                        <?php echo nl2br( $custom_field->label ) ?>
                    <?php elseif ( $custom_field->type == 'checkboxes' ) : ?>
                        <?php foreach ( $custom_field->items as $item ) : ?>
                            <label>
                                <input type="checkbox" class="bookly-custom-field" value="<?php echo esc_attr( $item['value'] ) ?>" <?php checked( @in_array( $item['value'], @$cf_item['data'][ $custom_field->id ] ), true, true ) ?> />
                                <span><?php echo $item['label'] ?></span>
                            </label><br/>
                        <?php endforeach ?>
                    <?php elseif ( $custom_field->type == 'radio-buttons' ) : ?>
                        <?php foreach ( $custom_field->items as $item ) : ?>
                            <label>
                                <input type="radio" class="bookly-custom-field" name="bookly-custom-field-<?php echo $custom_field->id ?>-<?php echo $key ?>"
                                       value="<?php echo esc_attr( $item['value'] ) ?>" <?php checked( $item['value'], @$cf_item['data'][ $custom_field->id ], true ) ?> />
                                <span><?php echo $item['label'] ?></span>
                            </label><br/>
                        <?php endforeach ?>
                    <?php elseif ( $custom_field->type == 'drop-down' ) : ?>
                        <select class="bookly-custom-field">
                            <option value=""></option>
                            <?php foreach ( $custom_field->items as $item ) : ?>
                                <option value="<?php echo esc_attr( $item['value'] ) ?>" <?php selected( $item['value'], @$cf_item['data'][ $custom_field->id ], true ) ?>><?php echo esc_html( $item['label'] ) ?></option>
                            <?php endforeach ?>
                        </select>
                    <?php elseif ( $custom_field->type == 'captcha' ) : ?>
                        <img class="bookly-js-captcha-img" src="<?php echo esc_url( $captcha_url ) ?>" alt="<?php esc_attr_e( 'Captcha', 'bookly' ) ?>" height="75" width="160" style="width:160px;height:75px;"/>
                        <img class="bookly-js-captcha-refresh" width="16" height="16" title="<?php esc_attr_e( 'Another code', 'bookly' ) ?>" alt="<?php esc_attr_e( 'Another code', 'bookly' ) ?>"
                             src="<?php echo plugins_url( 'frontend/resources/images/refresh.png', Plugin::getMainFile() ) ?>" style="cursor: pointer"/>
                        <input type="text" class="bookly-custom-field bookly-captcha" value="<?php echo esc_attr( @$cf_item['data'][ $custom_field->id ] ) ?>"/>
                    <?php endif ?>
                    <?php Proxy\Files::renderCustomField( $custom_field, $cf_item ) ?>
                </div>
                <?php if ( $custom_field->type != 'text-content' ) : ?>
                    <div class="bookly-label-error bookly-custom-field-error"></div>
                <?php endif ?>
            </div>
        </div>
    <?php endforeach ?>
    </div>
<?php endforeach ?>