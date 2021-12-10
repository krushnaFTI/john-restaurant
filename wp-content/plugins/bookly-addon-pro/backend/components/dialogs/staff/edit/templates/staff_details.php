<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib as BooklyLib;
/**
 * @var array $categories
 * @var BooklyLib\Entities\Staff $staff
 */
?>
<div class="form-group">
    <label for="bookly-category"><?php esc_html_e( 'Category', 'bookly' ) ?></label>
    <select name="category_id" class="form-control custom-select" id="bookly-category">
        <option value="0"><?php esc_html_e( 'Uncategorized', 'bookly' ) ?></option>
        <?php foreach ( $categories as $category ) : ?>
            <option value="<?php echo $category['id'] ?>" <?php selected( $category['id'], $staff->getCategoryId() ) ?>><?php echo $category['name'] ?></option>
        <?php endforeach ?>
    </select>
</div>
<div class="form-group">
    <label for="bookly-timezone"><?php esc_html_e( 'Timezone', 'bookly' ) ?></label>
    <select name="time_zone" class="form-control custom-select" id="bookly-timezone">
        <option value=""><?php esc_html_e( 'Default', 'bookly' ) ?></option>
        <?php echo wp_timezone_choice( $staff->getTimeZone( false ) ?: 'default' ) ?>
    </select>
    <small class="form-text text-muted"><?php esc_html_e( 'The staff member\'s schedule will be considered to be in the selected time zone. This time zone will also be used for the dates and times in notifications sent to the staff member', 'bookly' ) ?></small>
</div>