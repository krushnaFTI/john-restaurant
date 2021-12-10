<?php global $mprm_view_args; ?>

<?php if (mprm_get_template_mode() == "theme") { ?>

	<div class="mprm-content-container mprm-title">
		<?php if (!empty($mprm_view_args['link_item'])) { ?>
			<?php /* <a href="<?php echo get_permalink($mprm_menu_item->ID) ?>"><?php echo $mprm_menu_item->post_title ?></a> */ ?>
			<h3><?php echo $mprm_menu_item->post_title ?></h3>
		<?php } else { ?>
			<h3><?php echo $mprm_menu_item->post_title ?></h3>
		<?php } ?>
		
		<?php
            if (empty($price)) {
            	$price = mprm_get_price();
            }
            if (!empty($price)) {
	            $price = mprm_currency_filter(mprm_format_amount($price));
	            if (mprm_get_template_mode() == "theme") { ?>
		            <span class="mprm-price"><?php echo $price ?></span>
                <?php } else { ?>
		            <span class="mprm-price"><?php echo $price ?></span>
	            <?php }
	       } ?>
	</div>
<?php } else { ?>
	<h3 class="mprm-title">
		<?php if (!empty($mprm_view_args['link_item'])) { ?>
			<?php /* <a href="<?php echo get_permalink($mprm_menu_item->ID) ?>"><?php echo $mprm_menu_item->post_title ?></a> */ ?>
			<b><?php echo $mprm_menu_item->post_title ?></b>
		<?php } else { ?>
			<?php echo $mprm_menu_item->post_title ?>
		<?php } ?>

	</h3>
<?php } ?>

