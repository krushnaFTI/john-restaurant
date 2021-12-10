<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="col-md-3">
    <div class="checkbox">
        <label>
            <input type="checkbox" id="bookly-show-custom-fields" <?php checked( get_option( 'bookly_custom_fields_enabled' ) ) ?>>
            <?php esc_html_e( 'Show custom fields', 'bookly' ) ?>
        </label>
    </div>
</div>