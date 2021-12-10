/* global jQuery:false, MP_RM_Registry:false, _:false,console:false,wp:false,jBox:false,alert:false,mprm_admin_vars:false,pagenow:false*/
MP_RM_Registry.register("MPRM-toppings", (function($) {
	"use strict";
	var state;

	var delayTimer;

	function createInstance() {
		return {
			initPreloader: function(view, container) {
				if (view === 'show') {
					container.find('.mprm-topping-footer .mprm-topping-add-to-cart').addClass('mprm-preloader-color');
					container.find('.mprm-topping-footer .mprm-container-preloader .mprm-floating-circle-wrapper').removeClass('mprm-hidden');
				} else {
					container.find('.mprm-topping-footer .mprm-container-preloader .mprm-floating-circle-wrapper').addClass('mprm-hidden');
					container.find('.mprm-topping-footer .mprm-topping-add-to-cart').removeClass('mprm-preloader-color');
				}
			},
			init: function() {
				state.removeVariablePrice();
				state.addVariablePrice();
				state.enableVariablePricing();
				state.move();
				state.updatePrices();
			},
			/**
			 * Enable variable price
			 */
			enableVariablePricing: function() {

				$(document).on('change', '#mprm_variable_pricing', function() {
					if ($(this).attr('checked')) {
						$('#mprm_variable_price_fields').show();
						$('#mprm_regular_price_field').hide();
					} else {
						$('#mprm_variable_price_fields').hide();
						$('#mprm_regular_price_field').show();
					}
				});
			},
			/**
			 * Update Pricing
			 */
			updatePrices: function() {
				$('#mprm_pricing_fields').on('keyup', '.mprm_toppings_variable_prices_name', function() {

					var key = $(this).parents('tr').data('key'),
						name = $(this).val(),
						field_option = $('.mprm_repeatable_condition_field option[value=' + key + ']');

					if (field_option.length > 0) {
						field_option.text(name);
					} else {
						$('.mprm_repeatable_condition_field').append(
							$('<option></option>')
								.attr('value', key)
								.text(name)
						);
					}
				});
			},
			/**
			 * Drag
			 */
			move: function() {
				$(".mprm_toppings_repeatable_table tbody").sortable({
					handle: '.mprm_toppings_drag-handle', items: '.mprm_toppings_repeatable_row', opacity: 0.6, cursor: 'move', axis: 'y', update: function() {
						var count = 0;
						$(this).find('tr').each(function() {
							$(this).find('input.mprm_toppings_repeatable_index').each(function() {
								$(this).val(count);
							});
							count++;
						});
					}
				});
			},
			/**
			 * Remove variable price
			 */
			removeVariablePrice: function() {
				$(document.body).on('click', '.mprm_toppings_remove_repeatable', function(event) {
					event.preventDefault();

					var row = $(this).parent().parent('tr'),
						count = row.parent().find('tr').length - 1,
						type = $(this).data('type'),
						repeatable = 'tr.mprm_toppings_repeatable_' + type + 's',
						focusElement,
						focusable,
						firstFocusable;

					// Set focus on next element if removing the first row. Otherwise set focus on previous element.
					if ($(this).is('.ui-sortable tr:first-child .mprm_toppings_remove_repeatable:first-child')) {
						focusElement = row.next('tr');
					} else {
						focusElement = row.prev('tr');
					}

					focusable = focusElement.find('select, input, textarea, button').filter(':visible');
					firstFocusable = focusable.eq(0);

					if (type === 'price') {
						var price_row_id = row.data('key');
						/** remove from price condition */
						$('.mprm_toppings_repeatable_condition_field option[value="' + price_row_id + '"]').remove();
					}

					if (count > 1) {
						$('input, select', row).val('');
						row.fadeOut('fast').remove();
						firstFocusable.focus();
					} else {
						switch (type) {
							case 'price' :
								alert(mprm_admin_vars.one_price_min);
								break;
							case 'file' :
								$('input, select', row).val('');
								break;
							default:
								alert(mprm_admin_vars.one_field_min);
								break;
						}
					}

					/* re-index after deleting */
					$(repeatable).each(function(rowIndex) {
						$(this).find('input, select').each(function() {
							var name = $(this).attr('name');
							name = name.replace(/\[(\d+)\]/, '[' + rowIndex + ']');
							$(this).attr('name', name).attr('id', name);
						});
					});
				});
			},
			/**
			 * Clone variable price
			 * @param row
			 * @returns {*}
			 */
			clone_repeatable: function(row) {

				// Retrieve the highest current key
				var key, highest = 1;
				row.parent().find('tr.mprm_toppings_repeatable_row').each(function() {
					var current = $(this).data('key');
					if (parseInt(current) > highest) {
						highest = current;
					}
				});
				key = highest += 1;

				var clone = row.clone();

				/** manually update any select box values */
				clone.find('select').each(function() {
					$(this).val(row.find('select[name="' + $(this).attr('name') + '"]').val());
				});

				clone.removeClass('mprm_toppings_add_blank');

				clone.attr('data-key', key);
				clone.find('td input, td select, textarea').val('');
				clone.find('input, select, textarea').each(function() {
					var name = $(this).attr('name');
					var id = $(this).attr('id');

					if (name) {

						name = name.replace(/\[(\d+)]/, '[' + parseInt(key) + ']');
						$(this).attr('name', name);

					}

					if (typeof id !== 'undefined') {

						id = id.replace(/(\d+)/, key);
						$(this).attr('id', id);

					}

				});

				clone.find('span.mprm_toppings_price_id').each(function() {
					$(this).text(parseInt(key));
				});

				clone.find('span.mprm_toppings_file_id').each(function() {
					$(this).text(parseInt(key));
				});

				clone.find('.mprm_toppings_repeatable_default_input').each(function() {
					$(this).val(parseInt(key)).removeAttr('checked');
				});

				// Remove Chosen elements
				clone.find('.search-choice').remove();
				clone.find('.chosen-container').remove();

				return clone;
			},
			/**
			 * Add variable price
			 */
			addVariablePrice: function() {

				$(document.body).on('click', '.mprm_toppings_add_repeatable', function(event) {
					event.preventDefault();

					var button = $(this),
						row = button.parent().parent().prev('tr'),
						clone = state.clone_repeatable(row);

					clone.insertAfter(row).find('input, textarea, select').filter(':visible').eq(0).focus();

					// Setup chosen fields again if they exist
					clone.find('.mprm-select-chosen').chosen({
						inherit_select_classes: true,
						placeholder_text_single: mprm_admin_vars.one_option,
						placeholder_text_multiple: mprm_admin_vars.one_or_more_option
					});
					clone.find('.mprm-select-chosen').css('width', '100%');
					clone.find('.mprm-select-chosen .chosen-search input').attr('placeholder', mprm_admin_vars.search_placeholder);
				});
			},
			/**
			 * Listen ajax by request params
			 */
			addCartListener: function() {
				$(document).ajaxComplete(function(event, xhr, settings) {
					var ajaxParams = MP_RM_Registry._get("MP_RM_Functions").getParameterByName(settings.data);
					if ((typeof ajaxParams) !== "undefined") {
						var menuItemId = ajaxParams.menu_item_id,
							controller = ajaxParams.controller,
							action = ajaxParams.mprm_action;
						if ((action === "add_to_cart") && (controller === "cart")) {
							setTimeout(function() {
								$('[data-menu_id="' + menuItemId + '"]').removeClass('mprm-hidden');
							}, 3000);
						}
					}
				});
			},
			/**
			 * Init Open/close button
			 */
			toggleTopping: function() {
				$('.mpto-topping-buy-button .mprm-open,.mprm-widget-items .mprm-open').on('click', function(event) {
					event.preventDefault();
					var parent = $(this).parents('.mpto-topping-buy-button');
					parent.find('.mprm-cart-toppings-wrapper').removeClass('mprm-hidden');
					$('.mprm-notice').addClass('mprm-hidden');
				});

				$('.mpto-topping-buy-button .mprm-close,.mprm-widget-items .mprm-close').on('click', function(event) {
					event.preventDefault();
					$(this).parents('.mpto-topping-buy-button').find('.mprm-cart-toppings-wrapper').addClass('mprm-hidden');
				});
			},
			/**
			 * Add topping to cart
			 */
			addToppingToCart: function() {

				$(document).on('click', '.mprm-topping-add-to-cart', function(event) {
					event.preventDefault();
					var section = $(this).parents('.mprm-section');
					var menuID = section.attr('data-menu_id');
					var ajaxData = {menuID: menuID};
					var value = '';
					var priceIndex = '';
					var inputs = section.find('.mprm-list').find('input');

					var noticeContainer = section.find('.mprm-notice');

					$('.mprm-notice').addClass('mprm-hidden');


					state.initPreloader('show', section);

					if ((typeof inputs) === "undefined") {
						return;
					}

					if (!MP_RM_Registry._get('MP_RM_Functions').validateForm('mprm_toppings_form-' + menuID)) {
						return;
					}

					section.find('.mprm-spinner-loader').removeClass('mprm-hidden').addClass('mprm-is-active');

					$.each(inputs, function(index, input) {

						var inputObject = $(input);
						var type = inputObject.attr('type');
						var id = inputObject.attr('data-id');

						switch (type) {
							case 'radio':
								var checked = inputObject.attr('checked');
								if (checked) {
									value = inputObject.val();
									priceIndex = inputObject.attr('data-price-index');
									ajaxData[id] = {value: value, id: id, type: type, index: priceIndex, order: index};
								}
								break;
							case 'number':
								value = parseInt(inputObject.val());
								if ((typeof value !== "undefined") && value) {
									ajaxData[id] = {quantity: value, id: id, type: type, order: index};
								}
								break;
							case 'checkbox':
								checked = inputObject.attr('checked');
								if (checked) {
									value = parseInt(inputObject.val());
									if ((typeof value !== "undefined")) {
										ajaxData[id] = {quantity: 1, id: id, type: type, order: index};
									}
								}
								break;
							default:
								break;
						}
					});

					section.find('.mprm-list').css('opacity', 0.5);

					var $params = {
						action: 'add_to_cart',
						mpto_controller: 'toppings',
						data: ajaxData
					};

					MP_RM_Registry._get('MP_RM_Functions').wpAjax($params,
						function(data) {

							state.initPreloader('hide', section);

							noticeContainer.addClass('mprm-notice-success').removeClass('mprm-hidden');

							if ((typeof data.cart) !== "undefined") {
								$('.widget_mprm_cart_widget .mprm-cart-content').html(data.cart);
							}

							section.find('.mprm-list').css('opacity', 1);
						},
						function(data) {

							state.initPreloader('hide', section);

							section.find('.mprm-list').css('opacity', 1);
							console.warn('Some error!!!');
							console.warn(data);
						}
					);
				});
			},
			getDataToppings: function(type) {
				var data = [];
				var selector = $('option', '#mprm-toppings-data');
				if (type === "tags") {
					$.each(selector, function(index) {
						data[index] = $(this).val();
					});
				}
				if (type === "data") {
					$.each(selector, function(index) {
						data[index] = {id: $(this).val(), text: $(this).text()};
					});
				}

				return data;
			},
			initSelect2: function() {
				var toppingsInput = $('#toppings-input-hidden');

				toppingsInput.select2({
					tags: state.getDataToppings('tags'),
					data: state.getDataToppings('data'),
					placeholder: "Select toppings",
					allowClear: true
				});

				toppingsInput.select2("container").find("ul.select2-choices").sortable({
					containment: 'parent',
					start: function() {
						toppingsInput.select2("onSortStart");
					},
					update: function() {
						toppingsInput.select2("onSortEnd");
					}
				});

			},
			/**
			 *
			 */
			update_topping_quantities: function() {
				$(document.body).on('change', '.mprm-item-topping-quantity', function() {
					var stepper = this;

					clearTimeout(delayTimer);

					delayTimer = setTimeout(function() {
						var parent = $(stepper).parents('.mprm-cart-topping');
						var menuID = parent.attr('data-menu-id');
						var toppingID = parent.attr('data-topping-id');
						var data = {menuID: menuID};

						data[toppingID] = $(stepper).val();

						var $params = {
							action: 'update_cart_topping',
							mpto_controller: 'toppings',
							data: data
						};

						MP_RM_Registry._get('MP_RM_Functions').wpAjax($params,
							/**
							 * @param {Object} data
							 */
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
							},

							function(data) {

								console.warn('Some error!!!');
								console.warn(data);
							}
						);
					}, 1000);
				});
			}
		};
	}

	return {
		getInstance: function() {
			if (!state) {
				state = createInstance();
			}
			return state;
		}
	};
})(jQuery));


(function($) {
	"use strict";
	$(document).ready(function() {

		if ((typeof pagenow) !== "undefined" && pagenow === "mprm_toppings") {
			MP_RM_Registry._get("MPRM-toppings").init();
		}

		if ((typeof pagenow) !== "undefined" && pagenow === "mp_menu_item") {

			MP_RM_Registry._get("MPRM-toppings").initSelect2();
		}

		if ($('.mprm_go_to_checkout').length) {

			MP_RM_Registry._get("MPRM-toppings").addCartListener();
			MP_RM_Registry._get("MPRM-toppings").toggleTopping();
			MP_RM_Registry._get("MPRM-toppings").addToppingToCart();
		}

		if ($('#mprm_checkout_wrap').length) {
			MP_RM_Registry._get("MPRM-toppings").update_topping_quantities();
		}
	});
}(jQuery));