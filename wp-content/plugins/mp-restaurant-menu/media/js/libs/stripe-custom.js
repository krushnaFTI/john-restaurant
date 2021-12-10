$ = jQuery;
$(document).ready(function() {
    $('#mprm-purchase-button').hide();
    $('form.stripe-checkout-form').hide();
    setTimeout(function() {
        $get_total_val = $("#mprm_payment_summary_table .mprm-checkout-total .mprm_cart_amount b").text();
        $get_total_val = $get_total_val.substring(1, $get_total_val.length);
        $("#stripe-total-amount").val($get_total_val);
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
            // $("#mprm_payment_mode_select #mprm-gateway-option-paypal").show();
            // $("#mprm_payment_mode_select #mprm-gateway-option-stripe").show();
            // $("#mprm_payment_mode_select #mprm-gateway-option-test_manual").hide();
            $("#mprm_payment_mode_select #mprm-gateway-option-manual").show();
        } else {
            // $("#mprm_payment_mode_select #mprm-gateway-option-paypal").hide();
            // $("#mprm_payment_mode_select #mprm-gateway-option-stripe").hide();
            // $("#mprm_payment_mode_select #mprm-gateway-option-test_manual").show();
            $("#mprm_payment_mode_select #mprm-gateway-option-manual").hide();
        }

        $delivery_type = $("input.mprm-delivery-time:checked").val();
        $("#stripe-time-mode").val($delivery_type);

    }, 2000);

    $(document).on('click', 'input.mprm-delivery-time', function(){   
    //$("input.mprm-delivery-time").click(function() {
        $delivery_type = $(this).val();
        $("#stripe-time-mode").val($delivery_type);
    });

    $(document).on('click', '#mprm-payment-mode-wrap .mprm-gateway', function(){   
    //$("#mprm-payment-mode-wrap .mprm-gateway").click(function() {
        var get_gateway = $(this).val();
        setTimeout(function() {
            if (get_gateway == 'stripe') {
                $('form.stripe-checkout-form').show();
                $('#mprm-purchase-button').hide();
            } else {
                $('#mprm-purchase-button').show();
                $('form.stripe-checkout-form').hide();
            }
        }, 1000);
    });

    $(document).on('click', "#mprm_delivery_mode_select-wrapper input[name='delivery-mode']", function(){   
    //$("#mprm_delivery_mode_select-wrapper input[name='delivery-mode']").click(function() {
        $delivery_mod = $(this).val();
        setTimeout(function() {
            if ($delivery_mod == "collection") {
                // $("#mprm_payment_mode_select #mprm-gateway-option-paypal").show();
                // $("#mprm_payment_mode_select #mprm-gateway-option-stripe").show();
                // $("#mprm_payment_mode_select #mprm-gateway-option-test_manual").hide();
                $("#mprm_payment_mode_select #mprm-gateway-option-manual").show();
            } else {
                // $("#mprm_payment_mode_select #mprm-gateway-option-paypal").hide();
                // $("#mprm_payment_mode_select #mprm-gateway-option-stripe").hide();
                // $("#mprm_payment_mode_select #mprm-gateway-option-test_manual").show();
                $("#mprm_payment_mode_select #mprm-gateway-option-manual").hide();
            }
        }, 1000);
    });

    $(document).on('click', "#mprm_delivery_mode_select-wrapper input[type=radio]", function(){  
    //$("#mprm_delivery_mode_select-wrapper input[type=radio]").click(function() {
        setTimeout(function() {
            $get_total_val = $("#mprm_payment_summary_table .mprm_cart_amount").attr('data-total');
            $get_total_val = $get_total_val.substring(1, $get_total_val.length);
            $("#stripe-total-amount").val($get_total_val);
        }, 2000);
    });

    $(document).on('click', "#mprm-stripe-purchase-button", function(){  
    //$("#mprm-stripe-purchase-button").click(function() {
        $get_payment_method = $("#mprm-payment-mode-wrap input[name='payment-mode']:checked").val();
        $get_phone = $("#mprm_phone_number").val();
        $get_fname = $("#mprm-first").val();
        $get_lname = $("#mprm-last").val();
        $get_delivery_amount = $("#mprm_payment_summary_table .mprm_cart_delivery_amount").text();
        $get_tax = $("#mprm_payment_summary_table .mprm_cart_tax_amount").text();
        $get_subtotal = $("#mprm_payment_summary_table .mprm_cart_subtotal_amount").text();
        $customer_note = $("#customer_note").val();

        //$delivery_time = $("input[name='collection[time-mode]']").val();

        $address_type = $("select[name='delivery[address_type]']").val();
        $delivery_street = $("input[name='delivery[delivery_street]']").val();
        $delivery_apartment = $("input[name='delivery[delivery_apartment]']").val();
        $delivery_gate_code = $("input[name='delivery[delivery_gate_code]']").val();
        $delivery_notes = $("input[name='delivery[delivery_notes]']").val();
        $time_mode = $("input[name='delivery[time-mode]']").val();
        $order_hours = $("select.mprm-time-hours").val();
        $order_miutes = $("select.mprm-time-minutes").val();

        $get_email = $("#mprm_checkout_user_info #mprm-email").val();
        $get_itemname = $("#mprm_checkout_cart .mprm_checkout_cart_item_title").text();

        $("#stripe-checkout-email").val($get_email);
        $("#stripe-user-phone").val($get_phone);
        $("#stripe-user-fname").val($get_fname);
        $("#stripe-user-lname").val($get_lname);
        $("#stripe-payment-method").val($get_payment_method);
        $("#stripe-delivery-amount").val($get_delivery_amount);
        $("#stripe-tax").val($get_tax);
        $("#stripe-subtotal").val($get_subtotal);
        $("#stripe-customer-note").val($customer_note);

        //$('#stripe-delivery-time').val($delivery_time);

        $("#stripe-address_type").val($address_type);
        $("#stripe-delivery_street").val($delivery_street);
        $("#stripe-delivery_apartment").val($delivery_apartment);
        $("#stripe-delivery_gate_code").val($delivery_gate_code);
        $("#stripe-delivery_notes").val($delivery_notes);

        //$("#stripe-time-mode").val($time_mode);

        $("#stripe-order-hours").val($order_hours);
        $("#stripe-order-minutes").val($order_miutes);

        $("#stripe-checkout-email").val($get_email);
        $("#stripe-item-name").val($get_itemname);

        $(this).submit();
    });

});