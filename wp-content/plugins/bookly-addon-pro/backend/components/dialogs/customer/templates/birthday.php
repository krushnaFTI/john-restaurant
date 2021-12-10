<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="form-group">
    <label for="birthday"><?php esc_html_e( 'Date of birth', 'bookly' ) ?></label>
    <input date-range-picker class="form-control" type="text" ng-model=form.birthday id="birthday" options="{parentEl:'#bookly-customer-dialog',singleDatePicker:true,showDropdowns:true,autoUpdateInput:false,locale:datePickerOptions}" autocomplete="off"/>
</div>