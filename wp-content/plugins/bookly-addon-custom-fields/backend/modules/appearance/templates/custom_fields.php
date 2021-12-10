<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib as BooklyLib;
?>
<div class="bookly-box bookly-js-custom-fields"<?php if ( ! get_option( 'bookly_custom_fields_enabled' ) ) : ?> style="display: none;"<?php endif ?>>
    <div class="bookly-form-group">
        <label for="bookly-custom-fields"><?php _e( 'Custom Fields', 'bookly' ) ?></label>
        <div class="bookly-form-field">
            <input class="bookly-form-element" id="bookly-custom-fields" type="text"/>
        </div>
    </div>
</div>