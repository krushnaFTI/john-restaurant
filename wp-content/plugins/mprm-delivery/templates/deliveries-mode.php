
<input class="mprm-type-delivery" type="radio" name="delivery-mode" <?php $delivery ? checked($delivery) : '' ?> <?php disabled(!$delivery) ?> id="mprm-delivery-choice" value="delivery"/>
<label for="mprm-delivery-choice"> <?php _e('Delivered', 'mprm-delivery'); ?></label>

<input class="mprm-type-collection" type="radio" name="delivery-mode" <?php !$delivery ? checked($collection) : '' ?> <?php disabled(!$collection) ?> id="mprm-collection-choice" value="collection"/>
<label for="mprm-collection-choice"> <?php _e('For pickup', 'mprm-delivery'); ?></label>
