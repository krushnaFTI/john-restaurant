<h2><?php _e('Export', 'mp-restaurant-menu') ?></h2>
<p><?php _e('When you click the button below WordPress will create an XML file for you to save to your computer. Once you\'ve saved the download file, you can use the Import function in another WordPress installation to import the content from this site.', 'mp-restaurant-menu') ?></p>
<form novalidate="novalidate" method="post" id="mprm_export">
	<input type="hidden" name="controller" value="import">
	<input type="hidden" name="mprm_action" value="export">
	<p class="submit"><input type="submit" value="<?php _e('Export', 'mp-restaurant-menu') ?>" class="button button-primary" id="submit" name="submit"></p>
</form>

<h3>Export order data in between dates.</h3>
<form id="order_export_form" method="post">
	<input type="text" name="order_export_start_date" id="order_export_start_date" required placeholder="yyyy-mm-dd">
	<input type="text" name="order_export_end_date" id="order_export_end_date" required placeholder="yyyy-mm-dd">
	<input type="submit" name="order_export_submit" class="button button-primary" value="Export"> 
</form>