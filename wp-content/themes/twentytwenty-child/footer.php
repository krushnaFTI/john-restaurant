<?php
/**
 * The template for displaying the footer
 *
 * Contains the opening of the #site-footer div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */
$has_sidebar_1 = is_active_sidebar( 'sidebar-1' );
$has_sidebar_2 = is_active_sidebar( 'sidebar-2' );
$has_sidebar_3 = is_active_sidebar( 'sidebar-3' );

?>
<div class="sidebar-checkout-div"></div>

<footer id="site-footer" role="contentinfo" class="header-footer-group">
	<div class="section-inner"> 
		<?php if ( $has_social_menu || $has_sidebar_1 || $has_sidebar_2 || $has_sidebar_3) { ?>
			<aside class="footer-widgets-outer-wrapper" role="complementary">
				<div class="footer-widgets-wrapper">
					<?php if ( $has_sidebar_1 ) { ?>
						<div class="footer-widgets column-one grid-item">
							<?php dynamic_sidebar( 'sidebar-1' ); ?>
						</div>
					<?php } ?>
					<?php if ( $has_sidebar_2 ) { ?>
						<div class="footer-widgets column-two grid-item">
							<?php dynamic_sidebar( 'sidebar-2' ); ?>
						</div>
					<?php } ?>
					<?php if ( $has_sidebar_3 ) { ?>
						<div class="footer-widgets column-three grid-item">
							<?php dynamic_sidebar( 'sidebar-3' ); ?>
						</div>
					<?php } ?>
				</div><!-- .footer-widgets-wrapper -->
			</aside><!-- .footer-widgets-outer-wrapper -->
		<?php } ?>
		<div class="footer-credits">
			<p class="footer-copyright">Copyright &copy;
				<?php
				echo date_i18n(
					/* translators: Copyright date format, see https://www.php.net/date */
					_x( 'Y', 'copyright date format', 'twentytwenty' )
				);
				?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>. All right reserved.
			</p><!-- .footer-copyright -->
		</div>
	</div>
</footer>

<div class="topping-popup-content-data" style="display:none;"  id="container"></div> <!-- Used for Topping Content Ajax data -->
<div style="display: none;" id="webpusher-toggle-div">
	<span id="webpushr-subscription-toggle-button" data-size="1.1" data-text-when-denied="You've blocked push notifications." data-tooltip-position="right" data-color="#2c7be5"></span>
</div>

			<!-- #site-footer -->

<?php wp_footer(); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/easyResponsiveTabs.js"></script>

<script type="text/javascript">
	/* Start This is ajaxComplete Jquery Used for when Product added to cart and right sidebar open added product */
	jQuery( document ).ajaxComplete(function( event, xhr, settings ) {
		var ajaxurl_val = mp_menu_cart_ajax.ajax_url + '?action=mp_menu_cart_ajax&_wpnonce=' + mp_menu_cart_ajax.nonce;
		if ( settings.url === ajaxurl_val ) {
			
			if (jQuery('.active-checkout').length) {
				jQuery.ajax({
					type: "POST",
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					data: {
						action: 'get_menu_cart_content',
					},
					success: function (res) {
						//alert('HiHello1');
						//alert(res);console.log(res);
						jQuery('.topping-popup-content-data').hide();
						jQuery('.sidebar-checkout-div').find('.menu-cart-popup-content').remove();
						//jQuery('.sidebar-checkout-div').append(res);
						jQuery('.sidebar-checkout-div').append(res);
						jQuery('.sidebar-checkout-div').find('.menu-cart-popup-content h6').html('Cart Summary');
						 
						jQuery('.menu-cart-popup-content').find('.cart_item').each(function(){
							var cartItemQuantity = jQuery(this).find('.mprm-item-quantity').val();
						    jQuery(this).html('Qty :<input type="number" class="customqty" name="customqty" min="1" value='+cartItemQuantity+'>');
						});		


						getTotalItemCheckout();			
					}
				});			
			}		
		}
		
	}); 
	/* End This is ajaxComplete Jquery Used for when Product added to cart and right sidebar open added product */
	
	/* Start this jquery ajax used for when single product remove in cart */
	jQuery(document).on('click', '.mprm_cart_remove_item_btn', function(e){
		e.preventDefault();
		var cart_item = jQuery(this).attr("data-cart_item");
		var mprm_action = jQuery(this).attr("data-mprm_action");
		var controller = jQuery(this).attr("data-controller");
		jQuery.ajax({
			type: "POST",
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data: {
				action: 'get_menu_cart_content',
				cart_item: cart_item,
				mprm_action: mprm_action,
				controller: controller,
			},
			success: function (res) {
				
				//getCheckoutContent();
				jQuery.ajax({
					type: "POST",
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					data: {
						//action: 'mp_menu_cart_ajax',
						action: 'get_menu_cart_content',
						_wpnonce: mp_menu_cart_ajax.nonce
					}, 
					success: function (res) {
						
						jQuery('.topping-popup-content-data').hide();
						jQuery('.sidebar-checkout-div').find('.menu-cart-popup-content').remove();
						jQuery('.sidebar-checkout-div').append(res);
						jQuery('.sidebar-checkout-div').find('.menu-cart-popup-content h6').html('Cart Summary');
						
						jQuery('.menu-cart-popup-content').find('.cart_item').each(function(){
							var cartItemQuantity = jQuery(this).find('.mprm-item-quantity').val();
						    jQuery(this).html('Qty :<input type="number" class="customqty" name="customqty" min="1" value='+cartItemQuantity+'>');
						});

						//jQuery('#mp-menu-cart-primary').html(res);
						getTotalItemCheckout();
						
						var cart_key = 0,totalQuantity = 0;
						jQuery('#mprm_checkout_cart').find('.mprm_cart_item').each(function(){
							cart_key = jQuery(this).attr('data-cart-key');
							var itemQnty = jQuery(this).find('.customqty').val();
                            totalQuantity = (totalQuantity * 1) + (itemQnty * 1);
						});
						
						if(totalQuantity > 1) { 
							var menuQuantityText = totalQuantity + ' items'; 
						} else { 
							var menuQuantityText = totalQuantity + ' item'; 
						}
						
						if(totalQuantity > 0){
							jQuery('.mp-menu-cart-li').find('a .mp-menu-cart-contents').text(menuQuantityText);
							jQuery('.mp-menu-cart-li').find('a .mp-menu-cart-amount').text(jQuery('.mprm_cart_subtotal_amount').html());
						}else{
							jQuery('.mp-menu-cart-li').find('a .mp-menu-cart-contents').text(menuQuantityText);
							jQuery('.mp-menu-cart-li').find('a .mp-menu-cart-amount').text(jQuery('.mprm_cart_subtotal_amount').html());
						}		
					}
				});
			}
		});
	});
	/* End this jquery ajax used for when single product remove in cart */
	
	/* Start this jquery ajax used for when single product topping remove in cart */
	jQuery(document).on('click', '.mprm-cart-topping-remove-item-btn', function(e){
		e.preventDefault();
		
		var cart_item = jQuery(this).attr("data-cart_item"); // Get Cuurent Cart Item Id
		var topping_id = jQuery(this).attr("data-topping_id"); // Get Current Tppoing Id
		var menu_id = jQuery(this).attr("data-menu_id"); // Get Current topping menu Id
		var topping_price = jQuery(this).attr("data-mprm-topping-price"); // Get Current Topping Price
		var topping_val = parseFloat(topping_price.slice(1));
		var prev_topping_total = jQuery('#mprm_cart_item_'+cart_item+'_'+menu_id+' .mprm-custom-tot-price').html();
		//var topping_prev_val = parseFloat(prev_topping_total.slice(1));
		//var tot_topping = topping_prev_val - topping_val;
		var product_total_html = jQuery.trim(jQuery('#mprm_cart_item_'+cart_item+'_'+menu_id+' .total').html());
		var product_total = parseFloat(product_total_html.slice(1));
		var product_change =  Number(product_total)- Number(topping_val) ;
		
		jQuery.ajax({
			type: "POST",
			dataType: 'json',
			url: "<?php echo admin_url('admin-ajax.php'); ?>",
			data : {action: "remove_topping_cart_content", cart_item : cart_item, topping_id : topping_id},
			success: function (res) {
				//getCheckoutContent();
				jQuery.ajax({
					type: "POST",
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					data: {
						//action: 'mp_menu_cart_ajax',
						action: 'get_menu_cart_content',
						_wpnonce: mp_menu_cart_ajax.nonce
					},
					success: function (res) {
						
						jQuery('.topping-popup-content-data').hide();
						jQuery('.sidebar-checkout-div').find('.menu-cart-popup-content').remove();
						jQuery('.sidebar-checkout-div').append(res);
						jQuery('.menu-cart-popup-content').find('.cart_item').each(function(){
                       		var cartItemQuantity = jQuery(this).find('.mprm-item-quantity').val();
					    	jQuery(this).html('Qty :<input type="number" class="customqty" name="customqty" min="1" value='+cartItemQuantity+'>');
                    	});
						//jQuery('#mprm_cart_item_'+cart_item+'_'+menu_id+' .mprm-custom-tot-price').html('$'+tot_topping.toFixed(2));
						jQuery('.sidebar-checkout-div').find('.menu-cart-popup-content h6').html('Cart Summary');
						
						//jQuery('#mprm_cart_item_'+cart_item+'_'+menu_id+' .total').html('$'+product_change.toFixed(2));

						var cart_key = 0,totalQuantity = 0;
						jQuery('#mprm_checkout_cart').find('.mprm_cart_item').each(function(){
							cart_key = jQuery(this).attr('data-cart-key');
                            var itemQnty = jQuery(this).find('.customqty').val();
                            totalQuantity = (totalQuantity * 1) + (itemQnty * 1);
						});
						
						if(totalQuantity > 1) { 
							var menuQuantityText = totalQuantity + ' items'; 
						} else { 
							var menuQuantityText = totalQuantity + ' item'; 
						}
						
						if(totalQuantity > 0){
							jQuery('.mp-menu-cart-li').find('a .mp-menu-cart-contents').text(menuQuantityText);
							jQuery('.mp-menu-cart-li').find('a .mp-menu-cart-amount').text(jQuery('.mprm_cart_subtotal_amount').html());
						}else{
							jQuery('.mp-menu-cart-li').find('a .mp-menu-cart-contents').text(menuQuantityText);
							jQuery('.mp-menu-cart-li').find('a .mp-menu-cart-amount').text(jQuery('.mprm_cart_subtotal_amount').html());
						}	

						getTotalItemCheckout();
					}
				});
			}
		});
	});
	/* End this jquery ajax used for when single product topping remove in cart */
	
	function getCheckoutContent(){
		webpushr('fetch_id',function (sid) {
			
			jQuery.ajax({
				type: "POST",
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				data: {
					action: 'get_menu_checkout_content',
					metakey: sid
				},
				success: function (res1) {
				   jQuery('.sidebar-checkout-div').find('.menu-cart-popup-content').remove();
				   jQuery('.sidebar-checkout-div').append(res1);
				   jQuery('.menu-cart-popup-content').find('.cart_item').each(function(){
                       var cartItemQuantity = jQuery(this).find('.mprm-item-quantity').val();
					    jQuery(this).html('Qty :<input type="number" class="customqty" name="customqty" min="1" value='+cartItemQuantity+'>');
                    });

					setTimeout(function(){    
						var myDiv = jQuery('.sidebar-checkout-div .menu-cart-popup-content').find('#mprm_final_total_wrap');
						jQuery(jQuery('#webpusher-toggle-div').html()).insertAfter(myDiv);
						var spantext = "<p class='spantxt'>I would like to have notification: </p>";
						$("#webpushr-subscription-toggle-button").prepend(spantext);
   					},1000);
										
				    jQuery('.sidebar-checkout-div').find('.menu-cart-popup-content h6').html('Check out');
				    jQuery('.menu-cart-popup-content form.stripe-checkout-form').css('display','none');
				    jQuery('.menu-cart-popup-content #mprm_payment_mode_submit').css('display','none');
				    jQuery('.menu-cart-popup-content .webpushr_id_value').val(sid);

				    getTotalItemCheckout();
				
				  	var gateway = $('input[name=payment-mode]:checked', '#mprm_purchase_form').val();
				  	if (!!gateway) {
						var $params = [
							{
								name: 'controller',
								value: 'cart'
							},
							{
								name: 'mprm_action',
								value: 'load_gateway'
							},
							{
								name: 'payment-mode',
								value: gateway
							}
						];

						$('.mprm-cart-ajax').show();

						MP_RM_Registry._get('MP_RM_Functions').wpAjax($params,
							function(data) {
								$('.mprm-no-js:not(.mprm-add-to-cart)').hide();
								$('#mprm_purchase_form_wrap').html(data.html);
								
								$('#mprm_show_terms').find('.mprm_terms_links').on('click', function(e) {
									e.preventDefault();
									$(this).parents('#mprm_show_terms').find('.mprm_terms_links').toggle();
									$('#mprm_terms').toggle();
								});
							},
							function(data) {
								console.warn('Some error!!!');
								console.warn(data);
							}
						);
					}

					$('#mprm-purchase-button').hide();
				    $('form.stripe-checkout-form').hide();
				    setTimeout(function() {
				        var get_total_val = $(".primary-menu .menu-cart-popup-content #mprm_payment_summary_table .mprm-checkout-total .mprm_cart_amount").text();
				        get_total_val = get_total_val.substring(1, get_total_val.length);
				        $("#stripe-total-amount").val(get_total_val);
				        var get_gateway = $("#mprm-payment-mode-wrap .mprm-gateway:checked").val();
				        if (get_gateway == 'stripe') {
				            $('form.stripe-checkout-form').show();
				            $('#mprm-purchase-button').hide();
				        } else {
				            $('#mprm-purchase-button').show();
				            $('form.stripe-checkout-form').hide();
				        }
				        $delivery_mod = $("#mprm_delivery_mode_select-wrapper input[name='delivery-mode']:checked").val();
				        if ($delivery_mod == "collection") {
				            $("#mprm_payment_mode_select #mprm-gateway-option-manual").show();
				        } else {
				            $("#mprm_payment_mode_select #mprm-gateway-option-manual").hide();
				        }

				        $delivery_type = $("input.mprm-delivery-time:checked").val();
				        $("#stripe-time-mode").val($delivery_type);

				    }, 2000);	
				}
			});
		});
	}

	function getTotalItemCheckout(){
		if(jQuery('.sidebar-checkout-div').hasClass('active-checkout')){
			var totalPrice = 0;
			jQuery('.sidebar-checkout-div').find('.mprm_cart_item').each(function(){
				var cartMainDiv = jQuery(this);
				totalPrice = cartMainDiv.find('.cart_price').text();
				totalPrice = jQuery.trim(totalPrice.replace('$',''));
				
				var customqty = cartMainDiv.find('.customqty').val();

				totalPrice = ( totalPrice * 1 ) * ( customqty * 1 );
				
				if(cartMainDiv.find('.topping-item').length > 0){
					cartMainDiv.find('.topping-item').each(function(){
						var toppingPrice = jQuery(this).find('.custom-topping-price').text();
						toppingPrice = jQuery.trim(toppingPrice.replace('$',''));

						var totalToppingPrice = toppingPrice * customqty;
						//var totalToppingPrice = toppingPrice + customqty;
						totalPrice = ( totalPrice * 1 ) + ( totalToppingPrice * 1 );
					});
				}

				totalPrice = totalPrice * 1;
				console.log(totalPrice);
				cartMainDiv.find('.total').html('$'+totalPrice.toFixed(2));
				//cartMainDiv.find('.mprm-custom-tot-price').html('$'+totalPrice.toFixed(2));
	
			});
		}
	}

	jQuery(document).ready(function() { 

		/* Close Topping Popup */
		jQuery(document).on('click', '.mprm-topping-popup-close', function(event){	
			event.preventDefault();
			jQuery('#topping_popup_content').hide();
			jQuery('.topping-popup-content-data').hide();
		});
		
		/* View Item Topping Popup */
		jQuery(document).on('click', '.mprm-topping-popup-open', function(event){	
			event.preventDefault();
			jQuery('.mprm-cart-toppings-wrapper').addClass('mprm-hidden');
			jQuery(this).parents('.mpto-topping-buy-button').find('.mprm-cart-toppings-wrapper').removeClass('mprm-hidden');
		});
		
		jQuery(document).on('click', '.mprm-cart-toppings-wrapper .mprm-checkbox', function(event){	
			if( jQuery(this).is(':checked') ){
				jQuery(this).parents('li').addClass('checked');
				var dataId = jQuery(this).parents('.mprm-item').attr('data-id');
				jQuery(this).attr('data-id', dataId);
			}else{
				jQuery(this).parents('li').removeClass('checked');
				jQuery(this).removeAttr('data-id');
			}
		});

		var topping_sum = 0;
		jQuery(document).on('click', '.mprm-topping-add-to-cart', function(event){
			
			jQuery('.mprm-cart-toppings-wrapper ul').find('li').each(function(){
				if( !jQuery(this).hasClass('checked') ){
					jQuery(this).find('.mprm-checkbox').prop('checked', false);
					var custom_topping_id = jQuery('.mprm-checkbox').val(); //.mprm-checkbox:checked
				}
			});
			
			
			jQuery('.mprm-checkbox:checked').each(function() {
				topping_sum += Number(parseInt(this.value));
			});
			var topping_price_val = jQuery('.mprm-topping-add-to-cart').attr('data-price-val');
			var total = topping_sum + parseInt(topping_price_val);
			
			var custom_pro_id = jQuery('.mprm-topping-add-to-cart').attr('data-pro-id');
			var cartItemKey = jQuery(this).attr('data-cart-item');
			if(cartItemKey){
				var cartItemQty = jQuery(this).attr('data-cart-item-qty');
				jQuery('.sidebar-checkout-div').find('.mprm_cart_item').each(function(){
					var itmCartKey = jQuery(this).attr('data-cart-key');
					if(cartItemKey == itmCartKey){
						jQuery(this).find('.mprm_cart_remove_item_btn').click();
					}
				});
			}	
		});
		
		jQuery(document).on('click', '.menu-items-list #edit_item_topping', function(){
			
			var id = jQuery(this).attr("data-id");
			jQuery.ajax({
				type: "POST",
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				data: {
					action: 'get_edit_item_button',
					id: id,
				},
				success: function (res) {
					jQuery('.topping-popup-content-data').html( res );
					jQuery(".topping-popup-content-data").show();
				}
			});
        });

        jQuery(document).on('click', '.sidebar-checkout-div #edit_item_topping', function(){
			var id = jQuery(this).attr("data-id");
			var currTopping = jQuery(this);
			jQuery.ajax({
				type: "POST",
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				data: {
					action: 'get_edit_item_button',
					id: id,
				},
				success: function (res) {
					jQuery('.topping-popup-content-data').html( res );
					jQuery(".topping-popup-content-data").show();
					
					var cartItemKey = currTopping.parents('.mprm_cart_item').attr('data-cart-key');
					var cartItemQty = currTopping.parents('.mprm_cart_item').find('.customqty').val();
					
					currTopping.parents('.sidebar-checkout-div').find('.mprm-cart-topping-remove-item-btn').each(function(){
						var ToppingCartItemKey = jQuery(this).attr('data-cart_item');
						if(cartItemKey == ToppingCartItemKey){
							var ToppingId = jQuery(this).attr('data-topping_id');
							
							jQuery('.topping-popup-content-data .mprm-list').find('li').each(function(){
								var popupTopId = jQuery(this).find('.mprm-item').attr('data-id');
								if( ToppingId == popupTopId ){
									jQuery(this).find('.mprm-checkbox').prop('checked', true);
									jQuery(this).find('.mprm-checkbox').attr('data-id', popupTopId);
								}
							});
						}
					});

					jQuery('.topping-popup-content-data').find('.mprm-topping-add-to-cart').html('Update Item');
					jQuery('.topping-popup-content-data').find('.mprm-topping-add-to-cart').attr('data-cart-item', cartItemKey).attr('data-cart-item-qty', cartItemQty);
				}
			});
        });
		
		jQuery(document).on('click', '.cancel-edit-topping-popup', function(){	
			 jQuery(".topping-popup-content-data").hide();
		});

		jQuery(document).on('click', '.sidebar-checkout-div .menu-cart-popup-content #webpushrSubscriptionToggleButton', function(){
			jQuery('#webpusher-toggle-div #webpushrSubscriptionToggleButton').trigger('click');
		});
		
		jQuery(document).on('click', 'li#mp-menu-cart-primary a.mp-menu-cart-contents:not(.empty-mp-menu-cart-visible)', function(event){		   				
            event.preventDefault();				
			
			jQuery.ajax({
                type: "POST",
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                data: {
                    action: 'get_menu_cart_content',
                },
                success: function (res) {
					
                    jQuery('.sidebar-checkout-div').find('.menu-cart-popup-content').remove();
                    jQuery('.sidebar-checkout-div').append(res);
					jQuery('.sidebar-checkout-div').addClass('active-checkout');
					jQuery('body').addClass('checkout-open'); 
					jQuery('.sidebar-checkout-div').find('.menu-cart-popup-content h6').html('Cart Summary');
                    jQuery('.menu-cart-popup-content').find('.cart_item').each(function(){
                       var cartItemQuantity = jQuery(this).find('.mprm-item-quantity').val();
					   jQuery(this).html('Qty :<input type="number" class="customqty" name="customqty" min="1" value='+cartItemQuantity+'>');
                    });
					getTotalItemCheckout();
				}
            });
        });         
		
			
		jQuery(document).on('click', '.sidebar-checkout-div a.cart-popup-close', function(event){
			jQuery('.sidebar-checkout-div').removeClass('active-checkout');
			jQuery('body').removeClass('checkout-open');
            jQuery('.sidebar-checkout-div').find('.menu-cart-popup-content').remove();
			document.body.style.overflowY = "";
        });
		
 		jQuery(document).on('click', '.sidebar-checkout-div .btn_checkout', function(event1){
			var state;
            event1.preventDefault();
			getCheckoutContent();
		});
		
		
		jQuery(document).on('focusout', "#mprm_delivery_mode_select-wrapper .mprm-type-delivery input.delivery_street_field", function(){
			//jQuery("#mprm_delivery_mode_select-wrapper .mprm-type-delivery input.delivery_street_field").focusout(function(){
			$address_value = jQuery("#mprm_delivery_mode_select-wrapper .mprm-type-delivery input.delivery_street_field").val();
			$tax_value = jQuery("#mprm_payment_summary_table .mprm_cart_tax_amount").attr("data-tax");
			$subtotal_amount = jQuery("#mprm_payment_summary_table .mprm_cart_subtotal_amount").html();
			$subtotal_amount = $subtotal_amount.substring(1, $subtotal_amount.length);
			jQuery("#mprm_delivery_mode_select-wrapper .mprm-type-delivery .invalid_address").remove();
			
			if($address_value.length != ""){
				jQuery.ajax({
					type: "POST",
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					data : {action: "get_delivery_fee_from_postmates_function", address_value : $address_value},
					success: function(response){
						jQuery("#mprm_delivery_mode_select-wrapper .mprm-type-delivery .invalid_address").remove();
						if(response.delivery_fee_postmates){
							$total_checkout_value = $subtotal_amount + $tax_value + response.delivery_fee_postmates;
							sum = parseFloat($subtotal_amount);
							sum += parseFloat($tax_value);
							sum += parseFloat(response.delivery_fee_postmates);
							jQuery("#mprm_payment_summary_table .mprm-table .mprm_cart_delivery_row .mprm_cart_delivery_amount").text("$"+response.delivery_fee_postmates);
							jQuery("#mprm_payment_summary_table .mprm-table .mprm-checkout-total .mprm_cart_amount b").text("$"+sum);
							jQuery("#mprm_final_total_wrap .mprm_cart_amount").text('$'+sum);
						}else{
							jQuery("#mprm_delivery_mode_select-wrapper .mprm-type-delivery input.delivery_street_field").after('<label class="invalid_address">Please enter correct address</label>');
						}
					},
					error: function (response) {
						console.log(response.message);
						jQuery("#mprm_delivery_mode_select-wrapper .mprm-type-delivery .invalid_address").remove();
					}
				});
			}
		});
		
		jQuery(document).on('click', '#mprm_purchase_form #mprm_purchase_submit input[type=submit]', function(e) {
			var value1 = jQuery(".mprm-type-collection").val();
			if(value1 = 'collection')
			{
				jQuery("select[name='delivery[address_type]'").prop('required',false);
				jQuery("input[name='delivery[delivery_street]'").prop('required',false);
				jQuery("input[name='delivery[delivery_apartment]'").prop('required',false);
			}
			var form = $(this).parents('form');

			if (form.length) {
				if (!form.hasClass('mprm-no-js')) {
					var purchaseForm = document.getElementById('mprm_purchase_form');
					if (!MP_RM_Registry._get('MP_RM_Functions').validateForm('mprm_purchase_form')) {
						return;
					}
					e.preventDefault();

					jQuery(this).after('<span class="mprm-cart-ajax"><i class="mprm-icon-spinner mprm-icon-spin"></i></span>');
					var $params = $(purchaseForm).serializeArray();
					jQuery.each($params, function(index, element) {
						if (element) {
							if (element.name === "mprm_action" && element.value === "gateway_select") {
								$params.splice(index, 1);
							}
						}
					});

					jQuery('.mprm-cart-ajax').show();
					jQuery('.mprm-errors').remove();

					MP_RM_Registry._get('MP_RM_Functions').wpAjax($params,
						function(data) {
							if (data.errors) {
								jQuery('#mprm_final_total_wrap').before(data.errors);
							} else {
								var post_url = jQuery(purchaseForm).attr("action"); //get form action url
								var request_method = jQuery(purchaseForm).attr("method"); //get form GET/POST method
								var form_data = jQuery(purchaseForm).serialize(); //Encode form elements for submission
								
								$.ajax({
									url : post_url,
									type: request_method,
									data : form_data
								}).done(function(response){ //
									window.location.href = '<?php echo site_url();?>/checkout/success/';
									jQuery('.sidebar-checkout-div').find('.menu-cart-popup-content').remove();
									jQuery('.sidebar-checkout-div').append('<div class="menu-cart-popup-content"><a href="javascript:void(0);" class="cart-popup-close">close</a>'+response+'</div>');	
								});
							}
						},

						function(data) {
							if (data.error) {
								jQuery('#mprm_final_total_wrap').before(data.errors);
							}
							console.warn('Some error!!!');
							console.warn(data);
						}
					);
				}
			}
		});

		jQuery(document).on('click', '.mprm_checkout_register_login', function(event2){
			event2.preventDefault();
			var $this = $(this),
			$params = {
				action: 'get_login',
				controller: 'customer'
			};

			MP_RM_Registry._get('MP_RM_Functions').wpAjax($params,
				function(data) {
					$this.parent().html(data.html);
				},

				function(data) {
					console.warn('Some error!!!');
					console.warn(data);
				}
			);
		});
			
		jQuery(document).on('click', '#mprm_login_submit,[name="mprm_login_submit"]', function(e1) {
			e1.preventDefault();
			var $params = {
				action: 'login_ajax',
				controller: 'customer',
				nonce: $('[name="mprm_login_nonce"]').val(),
				redirect: $('[name="redirect"]').val(),
				pass: $('[name="mprm_user_pass"]').val(),
				login: $('[name="mprm_user_login"]').val()
			};

			MP_RM_Registry._get('MP_RM_Functions').wpAjax($params,
				function(data) {
					getCheckoutContent();
				},

				function(data) {
					if (data.data.html) {
						$('#mprm_checkout_form_wrap').find('.mprm-login-fields').after(data.data.html);
					} else {
						console.warn('Some error!!!');
						console.warn(data);
					}
				}
			);
		});

		var delayTimer;
		jQuery(document).on('change', '.mprm-item-quantity', function(){	
			var stepper = this;
			clearTimeout(delayTimer);
			delayTimer = setTimeout(function() {
				var $this = $(stepper),
					quantity = $this.val(),
					key = $this.data('key'),
					menu_item_id = $this.closest('.mprm_cart_item').data('menu-item-id'),
					options = $this.parent().find('input[name="mprm-cart-menu-item-' + key + '-options"]').val();

				var $params = {
					action: 'update_cart_item_quantity',
					controller: 'cart',
					quantity: quantity,
					menu_item_id: menu_item_id,
					options: options,
					position: key
				};
				
				MP_RM_Registry._get('MP_RM_Functions').wpAjax($params,
					function(data) {
						$('.mprm_cart_subtotal_amount').each(function() {
							var element = $(this);
							element.text(data.subtotal);
							element.attr('data-subtotal', data.subtotal);
							element.attr('data-total', data.subtotal);
						});

						$('.mprm_cart_tax_amount').each(function() {
							$(this).text(data.taxes);
						});

						$('.mprm_cart_amount').each(function() {
							var element = $(this);
							element.text(data.total);
							element.attr('data-subtotal', data.total);
							element.attr('data-total', data.total);
						});

						var totalQuantity = 0;
						$('#mprm_checkout_cart').find('.mprm-item-quantity').each(function(){
							var itemQuantity = $(this).val();
							totalQuantity = totalQuantity + ( itemQuantity * 1);
						});

						if(totalQuantity > 1) { var menuQuantityText = totalQuantity + ' items'; } else { var menuQuantityText = totalQuantity + ' item'; }

						if(totalQuantity > 0){
							$('.mp-menu-cart-li').find('a .mp-menu-cart-contents').text(menuQuantityText);
							$('.mp-menu-cart-li').find('a .mp-menu-cart-amount').text(data.subtotal);
						}	
					},
					function(data) {
						console.warn('Some error!!!');
						console.warn(data);
					}
				);

			}, 1000);
		});
		
		jQuery(document).on('change', '.customqty', function(){	
			var stepper = this;
			clearTimeout(delayTimer);
			delayTimer = setTimeout(function() {
				var $this = $(stepper),
					quantity = $this.val(),
					key = $this.data('key'),
					menu_item_id = $this.closest('.mprm_cart_item').data('menu-item-id'),
					datacart_key = $this.closest('.mprm_cart_item').data('cart-key'),
					item_price = $this.closest('.mprm_cart_item').data('sprice'),
					options = $this.parent().find('input[name="mprm-cart-menu-item-' + key + '-options"]').val();
				
				var $params = {
					action: 'update_cart_item_quantity',
					controller: 'cart',
					quantity: quantity,
					menu_item_id: menu_item_id,
					options: options,
					position: key
				};
				
			    var totalPrice; var totalToppingPrice=0;var total=0;var toppingPrice;var total_val=0;
				//jQuery('.mprm_cart_item').each(function() {
				jQuery('#mprm_cart_item_'+datacart_key+'_'+menu_item_id).each(function() {	
					loop_cart_key = jQuery(this).attr('data-cart-key');
					loop_menu_item_id = jQuery(this).attr('data-menu-item-id');
					
					if(jQuery('.topping-item').length > 0){
						jQuery('#mprm_cart_item_'+datacart_key+'_'+menu_item_id+' .topping-item').each(function(){
							toppingPrice = jQuery(this).find('.custom-topping-price').text();
							toppingPrice = jQuery.trim(toppingPrice.replace('$',''));
							totalToppingPrice = totalToppingPrice + parseFloat(toppingPrice);
						});
						total = (parseFloat(item_price) * parseFloat(quantity));  
						total_val = total + totalToppingPrice;
						//jQuery(this).find('.total-price').html('<div class="totalpricetitle">Total:</div><div class="total">$'+total_val.toFixed(2)+'</div>'); // .toFixed(2)
    				}else{
						total = (parseFloat(item_price) * parseFloat(quantity));  
						total_val = total + totalToppingPrice;
						//jQuery(this).find('.total-price').html('<div class="totalpricetitle">Total:</div><div class="total">$'+total_val.toFixed(2)+'</div>'); // .toFixed(2)
					}
				});	

				MP_RM_Registry._get('MP_RM_Functions').wpAjax($params,
					function(data) {
						$('.mprm_cart_subtotal_amount').each(function() {
							var element = $(this);
							element.text(data.subtotal);
							element.attr('data-subtotal', data.subtotal);
							element.attr('data-total', data.subtotal);
						});
						
						$('.mprm_cart_tax_amount').each(function() {
							$(this).text(data.taxes);
						});

						$('.mprm_cart_amount').each(function() {
							var element = $(this);
							element.text(data.total);
							element.attr('data-subtotal', data.total);
							element.attr('data-total', data.total);
						});
 
						/*var totalQuantity = 0;
						$('#mprm_checkout_cart').find('.mprm-item-quantity').each(function(){
							var itemQuantity = $(this).val();
							totalQuantity = totalQuantity + ( itemQuantity * 1);
						});*/

						var cart_key = 0,totalQuantity = 0;
						jQuery('#mprm_checkout_cart').find('.mprm_cart_item').each(function(){
							cart_key = jQuery(this).attr('data-cart-key');
                            var itemQnty = jQuery(this).find('.customqty').val();
                            totalQuantity = (totalQuantity * 1) + (itemQnty * 1);
						});

						if(totalQuantity > 1) { var menuQuantityText = totalQuantity + ' items'; } else { var menuQuantityText = totalQuantity + ' item'; }

						if(totalQuantity > 0){
							$('.mp-menu-cart-li').find('a .mp-menu-cart-contents').text(menuQuantityText);
							$('.mp-menu-cart-li').find('a .mp-menu-cart-amount').text(data.subtotal);
						}

						getTotalItemCheckout();
					},
					function(data) {
						console.warn('Some error!!!');
						console.warn(data);
					}
				); 
		
			}, 1000);
		});
    });

    jQuery(document).ready(function() {
        jQuery('.parentHorizontalTab').easyResponsiveTabs({
            type: 'default', //Types: default, vertical, accordion
            width: 'auto', //auto or any width like 600px
            fit: true, // 100% fit in a container
            tabidentify: 'hor_1', // The tab groups identifier
            activate: function(event) { // Callback function if tab is switched
                var $tab = jQuery(this);
                var $info = jQuery('#nested-tabInfo');
                var $name = jQuery('span', $info);
                $name.text($tab.text());
                $info.show();
            }
        });
		
		jQuery("#mprm-payment-mode-wrap input[name='payment-mode']:checked").parent().addClass("selected");
		
		jQuery(document).on('change', '#mprm-payment-mode-wrap input[name="payment-mode"]', function(){
		  	jQuery('#mprm-payment-mode-wrap input[name="payment-mode"]').parent().removeClass("selected");
		  	jQuery(this).parent().addClass("selected");
		});
		
		jQuery("#mprm_payment_summary_table").appendTo(".checkout_sidebar");
		
		var HeaderHeight = jQuery('#site-header').height();
		jQuery(window).scroll(function(event) {
			var scrolltop = jQuery(this).scrollTop();
			if (scrolltop >= HeaderHeight) {
				jQuery('#site-header').addClass('sticky');
			} else {
				jQuery('#site-header').removeClass('sticky');
			}
		});


		// Add cart button near to the hamburger menu
		if (jQuery(window).width() <= 999) {
			jQuery('.mobile-menu .mp-menu-cart-li').detach().appendTo("#selected_cart");
		}
	});
			
	equalheight = function(container){

		var currentTallest = 0,
			 currentRowStart = 0,
			 rowDivs = new Array(),
			 $el,
			 topPosition = 0;
			 
		jQuery(container).each(function() {
			   $el = jQuery(this);
			   jQuery($el).height('auto')
			   topPostion = $el.position().top;

			   if (currentRowStart != topPostion) {
				 for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
				   rowDivs[currentDiv].height(currentTallest);
				 }
				 rowDivs.length = 0; // empty the array
				 currentRowStart = topPostion;
				 currentTallest = $el.height();
				 rowDivs.push($el);
			   } else {
				 rowDivs.push($el);
				 currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
			  }
			   for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
				 rowDivs[currentDiv].height(currentTallest);
			   }
		});
	}

jQuery(window).load(function() {
  equalheight('.listing-items .listing-item');
});


jQuery(window).resize(function(){
  equalheight('.listing-items .listing-item');
});

</script>
</body>
</html>
