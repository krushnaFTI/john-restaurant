<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Dialogs\Appointment\AttachPayment\Proxy as AttachPaymentProxy;
$taxes_included = get_option( 'bookly_taxes_in_price' ) == 'included';
?>
<div id="bookly-payment-attach-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Attach payment', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="custom-control custom-radio">
                    <input class="custom-control-input" id="bookly-ap-create" type="radio" name="attach_method" value="create" ng-model="form.attach.payment_method" />
                    <label class="custom-control-label" for="bookly-ap-create"><?php esc_html_e( 'Create payment', 'bookly' ) ?></label>
                </div>

                <div class="form-group">
                    <label for="bookly-attach-payment-price"><?php esc_html_e( 'Total price', 'bookly' ) ?></label>
                    <input id="bookly-attach-payment-price" class="form-control bookly-js-attach-payment-price" type="text" ng-model="form.attach.payment_price" <?php if ( $taxes_included ) : ?> ng-class="{'is-invalid': (form.attach.payment_price * 1) < (form.attach.payment_tax * 1)}"<?php endif ?>/>
                </div>
                <?php AttachPaymentProxy\Taxes::renderAttachPayment() ?>

                <div class="custom-control custom-radio">
                    <input class="custom-control-input" id="bookly-ap-search" type="radio" name="attach_method" value="search" ng-model="form.attach.payment_method" />
                    <label class="custom-control-label" for="bookly-ap-search"><?php esc_html_e( 'Search payment', 'bookly' ) ?></label>
                </div>
                <div class="form-group">
                    <label for="bookly-attach-payment-id"><?php esc_html_e( 'Payment ID', 'bookly' ) ?></label>
                    <input id="bookly-attach-payment-id" class="form-control bookly-js-attach-payment-id" type="text" ng-model="form.attach.payment_id"/>
                </div>
            </div>
            <div class="modal-footer">
                <div ng-hide=loading>
                    <?php $disabled = $taxes_included ? '(form.attach.payment_price * 1) < (form.attach.payment_tax * 1) || ' : '' ?>
                    <?php Buttons::renderSubmit( 'bookly-attach-payment-apply', null, __( 'Apply', 'bookly' ), array( 'data-dismiss' => 'bookly-modal', 'ng-disabled' => $disabled . '(form.attach.payment_method==\'create\' && !form.attach.payment_price) || (form.attach.payment_method==\'search\' && !form.attach.payment_id)',  'ng-click' => 'attachPayment( form.attach.payment_method, form.attach.payment_price, form.attach.payment_tax, form.attach.payment_id, form.attach.customer_id, form.attach.customer_index )' ) ) ?>
                    <?php Buttons::renderCancel() ?>
                </div>
            </div>
        </div>
    </div>
</div>