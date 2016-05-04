// 商品购买流程js操作
var flow_obj = {

    checkout_init: function() {
        //提交订单页面js操作 

        $('.qty_selector a[name=minus]').click(function() {

            var qty_input_obj = $(this).next();
            var qty = $(qty_input_obj).attr('value') - 1;
            var qty_id = $(qty_input_obj).attr('id');
            var cart_id = flow_obj.getCartID(qty_id);

            if (qty < 1) {
                qty = 1;
                return false;
            }

            flow_obj.update_checkout_qty(qty, cart_id);

        });

        $('.qty_selector a[name=add]').click(function() {
            var qty_input_obj = $(this).prev();
            var qty = parseInt($(qty_input_obj).attr('value')) + 1;
            var qty_id = $(qty_input_obj).attr('id');
            var cart_id = flow_obj.getCartID(qty_id);
            var Products_ID = cart_id.split('_')[0];
            var products_Count = parseInt($("#Products_Count_" + Products_ID).attr('value'));

            if (qty > products_Count) {
                qty = products_Count;
                return false;
            }

            flow_obj.update_checkout_qty(qty, cart_id);


        });

        $('.qty_selector input').change(function() {
            var qty_input_obj = $(this);
            var qty = parseInt($(qty_input_obj).attr('value'));
            var qty_id = $(qty_input_obj).attr('id');
            var cart_id = flow_obj.getCartID(qty_id);
            var Products_ID = cart_id.split('_')[0];
            var products_Count = parseInt($("#Products_Count_" + Products_ID).attr('value'));

            if (qty < 1) {
                qty = 1;
                $(qty_input_obj).attr('value', 1);
                return false;
            }

            if (qty > products_Count) {
                qty = products_Count;
                $(qty_input_obj).attr('value', products_Count);
                return false;
            }

            flow_obj.update_checkout_qty(qty, cart_id);
        });

        $("#submit-btn").removeAttr('disabled');

        $('#checkout_form').submit(function() {
            return false;
        });

        $('#checkout_form #submit-btn').click(function() {
            var AddressID = parseInt($('#checkout_form input[name=AddressID]').val());

            if (AddressID == 0 || isNaN(AddressID)) {
                if (global_obj.check_form($('*[notnull]'))) {
                    return false
                };
            }

            $(this).attr('disabled', true);

            var param = $('#checkout_form').serialize();
            var url = $('#checkout_form').attr('action') + 'ajax/';


            $.post(url, param, function(data) {
            
				if (data.status == 1) {
					
				   window.location = data.url;
                }

            }, 'json');
        });

		$("input.coupon").live('click',function(e){
				var pre_coupon_value = parseInt($("#coupon_value").attr('value'));
				var total_price = parseInt($("#total_price").attr('value'));
				if(pre_coupon_value > 0){
					total_price = total_price + pre_coupon_value;
				}
				
				var coupon_price = parseInt($(this).attr('price'));
				total_price -= coupon_price;
			
				$("#total_price_txt").html('&yen' + total_price);
				$("#total_price").attr('value', total_price);	
				$("#coupon_value").attr('value',coupon_price);
		});
		
		$("#shipping_method").click(function(){
		
			var top = $(window).height()/2;
			$("#shipping-modal").css('top',top-80);
			$("#shipping-modal").modal('show');
			
		});
		
		$("#confirm_shipping_btn").live('click',function(){
			$("#shipping-modal").modal('hide');
			flow_obj.change_shipping_method();
		});
		
		$("#cancel_shipping_btn").live('click',function(){
		    $("#shipping-modal").modal('hide');
		});
		
		
		
		
        /**
         * json对象转字符串形式
         */
        function json2str(o) {
            var arr = [];
            var fmt = function(s) {
                if (typeof s == 'object' && s != null) return json2str(s);
                return /^(string|number)$/.test(typeof s) ? "'" + s + "'" : s;
            }
            for (var i in o) arr.push("'" + i + "':" + fmt(o[i]));
            return '{' + arr.join(',') + '}';
        }
    },
	coupon_price:function(){
		
			
	},
    update_checkout_qty: function(qty,cart_id) {

        var City_Code = $("#City_Code").attr('value');
        var Products_ID = cart_id.split('_')[0];
        var Shipping_ID = parseInt($("input[name='Shiping_ID']:checked").attr('value'));
        var Business = $("#Business_" + Products_ID).attr('value');
        var IsShippingFree = parseInt($("#IsShippingFree_" + Products_ID).attr('value'));
        var virtual = parseInt($("#virtual").attr('value'));
        var needcart = parseInt($("#needcart").attr('value'));

        var param = {
            Shipping_ID:Shipping_ID,
            Business: Business,
            City_Code: City_Code,
            _Qty: qty,
            _CartID: cart_id,
            IsShippingFree: IsShippingFree,
            action: 'checkout_update',
            virtual: virtual,
            needcart: needcart
        };

        var url = base_url + 'api/' + Users_ID + '/shop/cart/ajax/';
        var Cart_ID = cart_id;
        $.post(url, param, function(data) {

            if (data.status == 1) {
				
                if (parseInt(data.total_shipping_fee) == 0) {
                    $('#total_shipping_fee_txt').html('免运费');
                } else {
                    $('#total_shipping_fee_txt').html(data.total_shipping_fee + '元');
                }

                $('#subtotal_price_' + Cart_ID).html('&yen' + data.Sub_Total);
                $('#subtotal_qty_' + Cart_ID).html(data.Sub_Qty);
                $('#qty_' + Cart_ID).attr('value', data.Sub_Qty);

                //更新订单合计信息
                var total_price = parseInt(data.total) + parseInt(data.total_shipping_fee);
                $("#total_price_txt").html('&yen' + total_price);
                $("#total_price").attr('value', total_price);
                $("#total_shipping_fee").attr('value', data.total_shipping_fee);
				$("#coupon_value").attr('value',0);
                if (data.man_flag == 1) {
                    $("#reduce_txt").html('&yen' + data.reduce);
                }

                var integral = parseInt(data.integral);

                if (integral > 0) {
                    $("#total_integral").html(integral);
                }

                if (data.coupon_html.length > 0) {
                    $("#coupon-list").html(data.coupon_html);
                } else {
                    $("#coupon-list").html('');
                }

            }
        }, 'json');
    },
	change_shipping_method:function(){
		var Shipping_ID = parseInt($("input[name='Shiping_ID']:checked").attr('value'));
		var Shipping_Name  = $("input[name='Shiping_ID']:checked").attr('shipping_name');
		
		var City_Code = $("#City_Code").attr('value');
		var virtual = parseInt($("#virtual").attr('value'));
        var needcart = parseInt($("#needcart").attr('value'));
		var action = 'change_shipping_method';
		var url = base_url + 'api/' + Users_ID + '/shop/cart/ajax/';
		var param = {
			Shipping_ID:Shipping_ID,
			City_Code:City_Code,
			virtual:virtual,
			needcart:needcart,
			action:action
			};
			
		$.post(url, param, function(data) {
			
			if(data.status == 1){
				var total_price = parseInt(data.total) + parseInt(data.total_shipping_fee);
				$("#shipping_name").html(Shipping_Name); 
				$("#Order_Shipping_Express").attr('value',Shipping_Name);
				$("#total_price_txt").html('&yen' + total_price);
                $("#total_price").attr('value', total_price);
                $("#total_shipping_fee").attr('value', data.total_shipping_fee);
				
				if (parseInt(data.total_shipping_fee) == 0) {
                    $('#total_shipping_fee_txt').html('免运费');
                } else {
                    $('#total_shipping_fee_txt').html(data.total_shipping_fee + '元');
                }
			
			}
			
		},'json');
		
	},
    getCartID: function(qty_id) {
        var length = qty_id.length;
        var cart_id = qty_id.substring(length - 5, length);
        return cart_id;
    }

}
