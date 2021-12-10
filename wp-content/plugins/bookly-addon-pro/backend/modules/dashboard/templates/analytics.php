<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components;
use Bookly\Lib\Utils\Common;
?>

<h4 class="mb-3"><?php esc_html_e( 'Analytics', 'bookly' ) ?></h4>

<div class="d-flex mb-3">
    <ul id="bookly-js-filter-staff"
        data-txt-select-all="<?php esc_attr_e( 'All staff', 'bookly' ) ?>"
        data-txt-all-selected="<?php esc_attr_e( 'All staff', 'bookly' ) ?>"
        data-txt-nothing-selected="<?php esc_attr_e( 'No staff selected', 'bookly' ) ?>"
    >
        <?php foreach ( $dropdown_data['staff'] as $category_id => $category ): ?>
            <li<?php if ( ! $category_id ) : ?> data-flatten-if-single<?php endif ?>><?php echo esc_html( $category['name'] ) ?>
                <ul>
                    <?php foreach ( $category['items'] as $staff ) : ?>
                        <li data-value="<?php echo $staff['id'] ?>" data-selected="1">
                            <?php echo esc_html( $staff['full_name'] ) ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            </li>
        <?php endforeach ?>
    </ul>
    <div class="ml-2 mr-auto">
        <ul id="bookly-js-filter-services"
            data-icon-class="far fa-dot-circle"
            data-txt-select-all="<?php esc_attr_e( 'All services', 'bookly' ) ?>"
            data-txt-all-selected="<?php esc_attr_e( 'All services', 'bookly' ) ?>"
            data-txt-nothing-selected="<?php esc_attr_e( 'No service selected', 'bookly' ) ?>"
        >
            <li data-value="0" data-selected="1">
                <?php esc_html_e( 'Custom', 'bookly' ) ?>
            </li>
            <?php foreach ( $dropdown_data['service'] as $category_id => $category ): ?>
                <li<?php if ( ! $category_id ) : ?> data-flatten-if-single<?php endif ?>><?php echo esc_html( $category['name'] ) ?>
                    <ul>
                        <?php foreach ( $category['items'] as $service ) : ?>
                            <li data-value="<?php echo $service['id'] ?>" data-selected="1">
                                <?php echo esc_html( $service['title'] ) ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
    <?php Components\Controls\Buttons::render( null, 'btn-default mr-2', __( 'Export to CSV', 'bookly' ), array( 'data-toggle' => 'bookly-modal', 'data-target' => '#bookly-export-dialog' ), '<i class="far fa-fw fa-share-square mr-1"></i>{caption}…' ) ?>
    <?php Components\Controls\Buttons::render( null, 'btn-default', __( 'Print', 'bookly' ), array( 'data-toggle' => 'bookly-modal', 'data-target' => '#bookly-print-dialog' ), '<i class="far fa-fw fa-file mr-1"></i>{caption}…' ) ?>
</div>

<table id="bookly-analytics-table" class="table table-striped table-bordered" style="width: 100%">
    <thead>
        <tr>
            <th rowspan="2"><?php echo esc_html( Common::getTranslatedOption( 'bookly_l10n_label_employee' ) ) ?></th>
            <th rowspan="2"><?php echo esc_html( Common::getTranslatedOption( 'bookly_l10n_label_service' ) ) ?></th>
            <th colspan="5" class="border-bottom-0"><?php esc_html_e( 'Appointments', 'bookly' ) ?></th>
            <th colspan="2" class="border-bottom-0"><?php esc_html_e( 'Customers', 'bookly' ) ?></th>
            <th rowspan="2"><?php esc_html_e( 'Revenue', 'bookly' ) ?></th>
        </tr>
        <tr>
            <th><?php esc_html_e( 'Total', 'bookly' ) ?></th>
            <th><?php esc_html_e( 'Approved', 'bookly' ) ?></th>
            <th><?php esc_html_e( 'Pending', 'bookly' ) ?></th>
            <th><?php esc_html_e( 'Rejected', 'bookly' ) ?></th>
            <th><?php esc_html_e( 'Cancelled', 'bookly' ) ?></th>
            <th><?php esc_html_e( 'Total', 'bookly' ) ?></th>
            <th style="border-right: 1px solid #dee2e6"><?php esc_html_e( 'New', 'bookly' ) ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th colspan="2"><?php esc_html_e( 'Total', 'bookly' ) ?>:</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>

<small class="text-muted form-text">
    <?php esc_html_e( 'Note: If payment is made for several services, then for each service you will see the entire amount paid as revenue.', 'bookly' ) ?>
</small>

<?php include '_export_dialog.php' ?>
<?php include '_print_dialog.php' ?>