<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Dialogs\Appointment\CustomerDetails\Proxy;
?>
<h3 class="bookly-block-head bookly-color-gray">
    <?php _e( 'Custom Fields', 'bookly' ) ?>
</h3>
<div id="bookly-js-custom-fields">
    <?php foreach ( $custom_fields as $custom_field ) : ?>
        <div class="form-group" data-type="<?php echo esc_attr( $custom_field->type )?>" data-id="<?php echo esc_attr( $custom_field->id ) ?>" data-services="<?php echo esc_attr( json_encode( $custom_field->services ) ) ?>">
            <label for="custom_field_<?php echo esc_attr( $custom_field->id ) ?>"><?php echo $custom_field->label ?></label>
            <div>
                <?php if ( $custom_field->type == 'text-field' ) : ?>
                    <input id="custom_field_<?php echo esc_attr( $custom_field->id ) ?>" type="text" class="bookly-custom-field form-control" />

                <?php elseif ( $custom_field->type == 'textarea' ) : ?>
                    <textarea id="custom_field_<?php echo esc_attr( $custom_field->id ) ?>" rows="3" class="bookly-custom-field form-control"></textarea>

                <?php elseif ( $custom_field->type == 'checkboxes' ) : ?>
                    <?php foreach ( $custom_field->items as $item ) : ?>
                        <div class="checkbox">
                            <label>
                                <input class="bookly-custom-field" type="checkbox" value="<?php echo esc_attr( $item ) ?>" />
                                <?php echo $item ?>
                            </label>
                        </div>
                    <?php endforeach ?>

                <?php elseif ( $custom_field->type == 'radio-buttons' ) : ?>
                    <?php foreach ( $custom_field->items as $item ) : ?>
                        <div class="radio">
                            <label>
                                <input type="radio" name="<?php echo $custom_field->id ?>" class="bookly-custom-field" value="<?php echo esc_attr( $item ) ?>" />
                                <?php echo $item ?>
                            </label>
                        </div>
                    <?php endforeach ?>

                <?php elseif ( $custom_field->type == 'drop-down' ) : ?>
                    <select id="custom_field_<?php echo esc_attr( $custom_field->id ) ?>" class="bookly-custom-field form-control">
                        <option value=""></option>
                        <?php foreach ( $custom_field->items as $item ) : ?>
                            <option value="<?php echo esc_attr( $item ) ?>"><?php echo $item ?></option>
                        <?php endforeach ?>
                    </select>
                <?php else : ?>
                    <?php Proxy\Files::renderCustomField( $custom_field ) ?>
                <?php endif ?>
            </div>
        </div>
    <?php endforeach ?>
</div>