/* global MP_RM_Registry:false,console:false,$:false,jQuery:false,chosen:false*/
var MprmDelivery = (function($) {
	"use strict";

	var instance;

	function removeRequiredAttr() {
		var checkoutForm = $('#mprm_purchase_form');
		var elements = checkoutForm.find('.mprm-required');

		if (elements.length) {
			$.each(elements, function() {
				var element = $(this);
				element.data('required', element.attr('required'));
				element.removeAttr('required');
			});
		}
	}

	function addRequiredAttr() {
		var checkoutForm = $('#mprm_purchase_form');
		var elements = checkoutForm.find('.mprm-required');

		if (elements.length) {
			$.each(elements, function() {
				var element = $(this);
				element.attr('required', element.data('required'));
			});
		}
	}

	// Private methods and properties
	/**
	 * Show/hide current delivery wrapper
	 */
	function initDeliveryRadioBox() {

		if ( $('input[name="delivery-mode"]').length ) {

			// remove required attributes if 
			var selectedDeliveryMode = $( 'input[name="delivery-mode"]:checked' );
			if (selectedDeliveryMode && selectedDeliveryMode.length && selectedDeliveryMode.val() && selectedDeliveryMode.val() === 'collection') {
				removeRequiredAttr();
			}

			$(document).on('change', 'input[name="delivery-mode"]', function() {
				var input = $(this);
				var deliveryType = input.val();
				var ajaxData = {};

				ajaxData.delivery_type = deliveryType;
				ajaxData.nonce = $('input[name="delivery_checkout_form_nonce"]').val();

				var $params = {
					mprde_controller: 'delivery',
					action: 'get_delivery_cost',
					data: ajaxData
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


						var deliveryAmount = $('.mprm_cart_delivery_amount');

						if (deliveryType === 'collection') {
							deliveryAmount.html(deliveryAmount.data('free'));
							deliveryAmount.data('cost', data.delivery_cost);
						} else {
							deliveryAmount.html(data.delivery_cost);
						}
					},
					function(data) {
						console.warn('Some error!!!');
						console.warn(data);
					}
				);
				if (deliveryType === 'collection') {
					removeRequiredAttr();
				} else {
					addRequiredAttr();
				}
			});

		}
	}

	/**
	 * Get params By name
	 * @param url
	 * @param name
	 * @returns {*}
	 */
	function getParameterByName(url, name) {
		var vars = [], hash;
		if (url) {
			var hashes = url.slice(url.indexOf('?') + 1).split('&');
			for (var i = 0; i < hashes.length; i++) {
				hash = hashes[i].split('=');
				vars.push(hash[0]);
				vars[hash[0]] = hash[1];
			}
			if ((typeof name) !== "undefined") {
				return vars[name];
			}
			return vars;
		} else {
			return false;
		}
	}

	// Constructor
	function MprmDelivery() {

		if (!instance) {
			instance = this;
		}
		else {
			return instance;
		}

		// Public properties
	}

	// Public methods
	MprmDelivery.prototype.init = function() {
		initDeliveryRadioBox();
	};
	/**
	 * Listen ajax by request params
	 */
	MprmDelivery.prototype.addCartListener = function() {
		$(document).ajaxComplete(function(event, xhr, settings) {
			var ajaxParams = getParameterByName(settings.data);

			if ((typeof ajaxParams) !== "undefined") {
				var controller = ajaxParams.controller,
					action = ajaxParams.mprm_action;
				if ((action === "update_cart_item_quantity") && (controller === "cart")) {
					try {
						var responseData = $.parseJSON(xhr.responseText);
						var deliveryAmount = $('.mprm_cart_delivery_amount');

						if ($('input[name="delivery-mode"]:checked').val() === 'collection') {
							deliveryAmount.html(deliveryAmount.data('free'));
							deliveryAmount.data('cost', responseData.data.delivery_cost);
						} else {
							deliveryAmount.html(responseData.data.delivery_cost);
						}
					} catch (e) {
						console.log(e);
					}
				}
			}
		});
	};

	return MprmDelivery;
})
(jQuery);

(function($) {
	"use strict";
	$(document).ready(function() {
		var mprmDelivery = new MprmDelivery();
		mprmDelivery.init();
		mprmDelivery.addCartListener();
	});
})(jQuery);