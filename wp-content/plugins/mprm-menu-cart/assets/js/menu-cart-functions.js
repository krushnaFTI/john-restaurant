/* global mp_menu_cart_ajax:false*/
jQuery(function($) {
	'use strict';
	var buttons = [
		".mprm-add-to-cart",
		".mprm-topping-add-to-cart"
	];

	jQuery(document.body).on('click', buttons.join(','), function() {
		MPMenuCartTimeout();
	});

	function MPMenuCartTimeout() {
		setTimeout(function() {
			MPMenuCartLoadJS();
		}, 1000);
	}

	function MPMenuCartLoadJS() {
		$('.mp-menu-cart-li').load(mp_menu_cart_ajax.ajax_url + '?action=mp_menu_cart_ajax&_wpnonce=' + mp_menu_cart_ajax.nonce);
	}

	$(document).ready(function() {
		setTimeout(function() {
			MPMenuCartTimeout();
		}, 1000);
	});
});

