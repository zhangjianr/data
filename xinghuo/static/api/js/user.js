var user_obj={
	user_login_init:function(){
		$('#user_form .submit input').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			
			$(this).attr('disabled', true);
			$.post('?', $('#user_form').serialize(), function(data){
				if(data.status==1){
					window.location=data.jump_url;
				}else{
					global_obj.win_alert('错误的用户名或密码，请重新登录！', function(){
						$('#user_form .submit input').attr('disabled', false)
						$('#user_form input[name=Password]').val('');
					});
				};
			}, 'json');
		});
	},
	
	user_create_init:function(){
		$('#user_form .submit input').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			
			if($('#user_form input[name=Password]').size()){	//微信认证号没有密码这一项
				if($('#user_form input[name=Password]').val()!=$('#user_form input[name=ConfirmPassword]').val()){
					global_obj.win_alert('两次输入的密码不一致，请重新输入登录密码！', function(){
						$('#user_form input[name=Password]').val('').focus();
						$('#user_form input[name=ConfirmPassword]').val('');
					});
					return false;
				}
			}
			
			var Mobile=$('#user_form input[name=Mobile]').val();
			if(Mobile=='' || Mobile.length!=11){
				global_obj.win_alert('请正确填写手机号码！', function(){
					$('input[name=Mobile]').focus();					
				});
				return false;
			}
			
			$(this).attr('disabled', true);
			$.post('?', $('#user_form').serialize(), function(data){
				if(data.status==1){
					global_obj.win_alert(data.msg, function(){
						window.location=data.url;		
					});					
				}else{
					global_obj.win_alert(data.msg, function(){
						history.back();
					});
				}
			}, 'json');
		});
		
		$('#user_form .sms_button').click(function(){
			var Mobile=$('input[name=Mobile]').val();
			if(Mobile=='' || Mobile.length!=11){
				global_obj.win_alert('请正确填写手机号码！', function(){
					$('input[name=Mobile]').focus();
				});
			}else{
				$(this).attr('disabled', true);
				var time=0;
				time_obj=function(){
					if(time>=30){
						$('#user_form .sms_button').val('获取验证码').attr('disabled', false);
						time=0;
						clearInterval(timer);
					}else{
						$('#user_form .sms_button').val('重新获取('+(30-time)+')');
						time++;
					}
				}
				var timer=setInterval('time_obj()', 1000);
				$.get('?d=get_sms&Mobile='+Mobile);
			}
		});
	},
	
	user_profile_init:function(){
		$('#user_form .submit input').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			$(this).attr('disabled', true);
			$.post('../../ajax/', $('#user_form').serialize(), function(data){
				if(data.status==1){
					global_obj.win_alert(data.msg, function(){
						window.location='../';
					});
				}else{
					global_obj.win_alert(data.msg, function(){
						$('#user_form .submit input').attr('disabled', false);
					});
				}
			}, 'json');
		});
	},
	
	user_payword_init:function(){
		$('#user_form .submit input').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			
			if($('#user_form input[name=PayPassword]').val()!=$('#user_form input[name=QPayPassword]').val()){
				global_obj.win_alert('两次输入的支付密码不一致，请重新输入支付密码！', function(){
					$('#user_form input[name=PayPassword]').val('').focus();
					$('#user_form input[name=QPayPassword]').val('');
				});
				return false;
			}
			
			$(this).attr('disabled', true);
			$.post('../ajax/', $('#user_form').serialize(), function(data){
				if(data.status==1){
					global_obj.win_alert(data.msg, function(){
						window.location='../';
					});
				}else{
					global_obj.win_alert(data.msg, function(){
						$('#user_form .submit input').attr('disabled', false);
					});
				}
			}, 'json');
		});
	},
	
	user_paymoney_init:function(){
		$('#user_form .submit input').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};			
			$(this).attr('disabled', true);
			$.post('../ajax/', $('#user_form').serialize(), function(data){
				if(data.status==1){
					global_obj.win_alert(data.msg, function(){
						window.location='../';
					});
				}else{
					global_obj.win_alert(data.msg, function(){
						$('#user_form .submit input').attr('disabled', false);
					});
				}
			}, 'json');
		});
	},
	
	user_charge_init:function(){
		$('#user_form .submit input').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};			
			$(this).attr('disabled', true);
			$.post('../ajax/', $('#user_form').serialize(), function(data){
				if(data.status==1){
					window.location=data.url;
				}else{
					global_obj.win_alert(data.msg, function(){
						$('#user_form .submit input').attr('disabled', false);
					});
				}
			}, 'json');
		});
	},
	
	user_complete_init:function(){
		$('#complete_form .submit input').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			
			var Mobile=$('#complete_form input[name=Mobile]').val();
			if(Mobile=='' || Mobile.length!=11){
				global_obj.win_alert('请正确填写手机号码！', function(){
					$('input[name=Mobile]').focus();					
				});
				return false;
			}
			
			$(this).attr('disabled', true);
			$.post('../ajax/', $('#complete_form').serialize(), function(data){
				if(data.status==1){
					global_obj.win_alert(data.msg, function(){
						window.location=$('#httphref').val();
					});
				}else{
					global_obj.win_alert(data.msg, function(){
						$('#complete_form .submit input').attr('disabled', false);
					});
				}
			}, 'json');
		});
	},
	
	card_init:function(){
		$('#card .sign').click(function(){
			$(this).html('签到中...');
			$.post('../ajax/','action=sign', function(data){
				if(data.status==1){
					$('#card .sign').html('今天已签到');
					$('#card .sign').off();
					$('#card .intergral').html('我的积分：'+data.integral);
				}else{
					$('#card .sign').html('签到失败');
				};
			}, 'json');
		});	
		
		$('#card .benefits_btn').click(function(){
			$('#card .benefits').slideToggle();
			$('#card .benefits_btn span:last').removeClass().addClass($('#card .benefits').is(':hidden')?'jt_up':'jt_down');
		});
	},
	
	my_init:function(){
		$('.modify_password').click(function(){
			var o=$(this);
			global_obj.div_mask();
			$('#modify_password_div').show();
			$('#modify_password_div .cancel').off().click(function(){
				global_obj.div_mask(1);
				$('#modify_password_div').hide();
			});
			
			$('#modify_password_form .submit').off().click(function(){
				if(global_obj.check_form($('#modify_password_form input[notnull]'))){return false};
				if($('#modify_password_form input[name=Password]').val()!=$('#modify_password_form input[name=ConfirmPassword]').val()){
					global_obj.win_alert('登录密码与确认密码不匹配，请重新输入！', function(){
						$('#modify_password_form input[name=Password]').focus();
					});
					return false;
				}
				
				$(this).attr('disabled', true);
				$.post('../ajax/', $('#modify_password_form').serialize()+'&action=modify_password', function(data){
					if(data.status==1){
						global_obj.win_alert(data.msg, function(){
							global_obj.div_mask(1);
							$('#modify_password_div').hide();
							$('#modify_password_form .submit').attr('disabled', false);
							$('#modify_password_form input[name=YPassword], #modify_password_form input[name=Password], #modify_password_form input[name=ConfirmPassword]').val('');
						});
					}else{
						global_obj.win_alert(data.msg, function(){
							$('#modify_password_form .submit').attr('disabled', false);
						});
					};
				}, 'json');
			});
		});
		
		$('.modify_mobile').click(function(){
			var o=$(this);
			global_obj.div_mask();
			$('#modify_mobile_div').show();
			$('#modify_mobile_div .cancel').off().click(function(){
				global_obj.div_mask(1);
				$('#modify_mobile_div').hide();
			});
			$('#modify_mobile_form .submit').off().click(function(){
				if(global_obj.check_form($('#modify_mobile_form input[notnull]'))){return false};
				$(this).attr('disabled', true);
				$.post('../ajax/', $('#modify_mobile_form').serialize()+'&action=modify_mobile', function(data){
					if(data.status==1){
						global_obj.win_alert(data.msg, function(){
							global_obj.div_mask(1);
							$('#modify_mobile_div').hide();
							$('#modify_mobile_form .submit').attr('disabled', false);
							$('#modify_mobile_form input[name=MobileCheck]').val($('#modify_mobile_form input[name=Mobile]').val());
						});
					}else{
						global_obj.win_alert(data.msg, function(){
							$('#modify_mobile_form .submit').attr('disabled', false);
						});
					};
				}, 'json');
			});
			
			$('#modify_mobile_form .sms_button').off().click(function(){
				var Mobile=$('input[name=Mobile]').val();
				
				
				if(Mobile=='' || Mobile.length!=11){
					global_obj.win_alert('请正确填写手机号码！', function(){
						$('input[name=Mobile]').focus();
					});
				}else{
					$(this).attr('disabled', true);
					
					time_obj=function(){
						if(time>=30){
							$('#modify_mobile_form .sms_button').val('获取验证码').attr('disabled', false);
							time=0;
							clearInterval(timer);
						}else{
							$('#modify_mobile_form .sms_button').val('重新获取('+(30-time)+')');
							time++;
						}
					}
					var timer=setInterval('time_obj()', 1000);
					$.get('?d=get_sms&Mobile='+Mobile);
				}
			});
		});
	},
	
	my_address_init:function(){
		$('#address_form #submit-btn').removeAttr('disabled');
		
		 $('#address_form').validate({
                onfocusout: function(element) {
                    this.element(element);
                },
                errorPlacement: function(error, element) {
                    if (element.is(":checkbox")) {
                        error.appendTo(element.parent());
                    } else {
                        error.appendTo(element.parent());
                    }

                } 
            });
			
			
		$('#address_form #submit-btn').click(function(){
	
		
	
			if($("#address_form").valid()){
				var Province = $("#loc_province").attr('value');
				var City = $("#loc_city").attr('value');
				var Area = $("#loc_town").attr('value');
			
				if(Province.length ==0 || City.length == 0 ||Area.length == 0){
					global_obj.win_alert("省市区缺一不可", function(){
						
					});
					return false;
				} 
				
				$.post($('#address_form').attr('action')+'ajax/', $('#address_form').serialize(), function(data){
				
			
				if(data.status==1){
					global_obj.win_alert(data.msg, function(){
						
						window.location= data.url;
					});
				}else{
					global_obj.win_alert(data.msg, function(){
						$('#user_form .submit input').attr('disabled', false);
					});
				}
				
			}, 'json');
		   
			
		}
			
			
			
		});
	},
	
	integral_init:function(){
		$('#integral_header .sign').click(function(){
			$(this).html('签到中');
			$.post('../ajax/','action=sign', function(data){
				if(data.status==1){
					$('#integral_header .sign').html('已签到').off().removeClass().addClass('sign_ok');
					$('#integral_header .l span').html(data.integral);
					$('#integral_header .r span').html(parseInt($('#integral_header .r span').html())+1);
				}else{
					$('#integral_header .sign').html('签到失败');
				};
			}, 'json');
		});	
		
		$('#integral_get_use div').click(function(){
			var o=$(this);
			global_obj.div_mask();
			$('.pop_form').show();
			$('.pop_form .cancel').off().click(function(){
				global_obj.div_mask(1);
				$('.pop_form').hide();
			});
			$('.pop_form h1').html(o.html());
			$('.pop_form input:text').attr('placeholder', o.html());
			$('.pop_form input[name=RecordType]').val(o.html());
			
			$('#integral_form .submit').off().click(function(){
				if(global_obj.check_form($('*[notnull]'))){return false};
				$(this).attr('disabled', true);
				$.post('../ajax/', $('#integral_form').serialize(), function(data){
					if(data.status==1){
						global_obj.win_alert(data.msg, function(){
							$('#integral_form .submit').attr('disabled', false);
							window.location.reload();
						});
					}else{
						global_obj.win_alert(data.msg, function(){
							$('#integral_form .submit').attr('disabled', false);
						});
					};
				}, 'json');
			});
		});
	},
	
	message_init:function(){
		$('#message .list').click(function(){
			var o=$(this);
			if(o.attr('Display')==0){
				o.attr('Display', 1);
				$.post('../ajax/','action=get_message_contents&MessageID='+o.attr('MessageID'), function(data){
					if(data.status==1){
						o.after('<div class="contents">'+data.msg+'</div>');
						o.removeClass().addClass('list is_read').find('div').addClass('up').html('');
						o.next().slideToggle();
						var not_read=$('#message .not_read').size();
						if(not_read<=0){
							$('#footer_user font').remove();
						}else{
							$('#footer_user font').html(not_read);
						}
					}else{
						global_obj.win_alert(data.msg, function(){
							o.attr('Display', 0);
						});
					};
				}, 'json');
			}else{
				$(this).attr('Display', 0);
				o.next().slideToggle(function(){
					o.next().remove();
					o.find('div').removeClass();
				});
			}
		});
	},
	
	coupon_init:function(){
		$('#coupon .use').click(function(){
			var o=$(this);
			global_obj.div_mask();
			$('.pop_form').show();
			$('.pop_form .cancel').off().click(function(){
				global_obj.div_mask(1);
				$('.pop_form').hide();
			});
			
			$('#coupon_use_form .submit').off().click(function(){
				if(global_obj.check_form($('*[notnull]'))){return false};
				$(this).attr('disabled', true);
				$.post('../ajax/', $('#coupon_use_form').serialize()+'&action=use_coupon&CouponID='+o.attr('CouponID'), function(data){
					if(data.status==1){
						global_obj.win_alert(data.msg, function(){
							window.location=$('#coupon .t_list a:first').attr('href');
						});
					}else{
						global_obj.win_alert(data.msg, function(){
							$('#coupon_use_form .submit').attr('disabled', false);
						});
					};
				}, 'json');
			});
		});
		
		$('#coupon .p img').click(function(){
			$(this).parent().parent().find('h3').slideToggle();
		});
		
		$('#coupon .get').click(function(){
			var o=$(this);
			o.html('领取中...');
			$.post('../../ajax/', 'action=get_coupon&CouponID='+o.attr('CouponID'), function(data){
				o.html('领取');
				if(data.status==1){
					global_obj.win_alert(data.msg, function(){
						window.location=$('#coupon .t_list a:first').attr('href');
					});
					
				}else{
					global_obj.win_alert(data.msg);
				};
			}, 'json');
		});
	},
	payment_init:function(){
		var PaymentMethod=$('#payment_form input[name=PaymentMethod]');
		if(PaymentMethod.size()){
			var change_payment_method=function(){
				if(PaymentMethod.filter(':checked').val()=='线下支付'){				
					$('#payment_form .payment_info').show();
					$('#payment_form .payment_password').hide();
				}else{
					if(PaymentMethod.filter(':checked').val()=='余额支付'){
						$('#payment_form .payment_password').show();
						$('#payment_form .payment_info').hide();
					}else{
						$('#payment_form .payment_info').hide();
						$('#payment_form .payment_password').hide();
					}
				}
			}
			PaymentMethod.click(change_payment_method);
			PaymentMethod.filter('[value='+$('#payment_form input[name=DefautlPaymentMethod]').val()+']').click();
			change_payment_method();
		}else{
			$('#payment_form').hide();
		}
		
		$('#payment_form').submit(function(){return false;});
		$('#payment_form .payment input').click(function(){
			$(this).attr('disabled', true);
			
			$.post($('#payment_form').attr('action')+'kanjia_ajax/', $('#payment_form').serialize(), function(data){
				$('#payment_form .payment input').attr('disabled', false);
								if(data.status==1){
					window.location=data.url
				}else{
					global_obj.win_alert(data.msg);
				}
				
				
			}, 'json');
		
		});
	},	
	commit_init:function(){
		$('#commit_form .back').click(function(){
			history.back();
		});
		
		$('#commit_form').submit(function(){return false;});
		$('#commit_form .submit').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			
			$(this).attr('disabled', true);
			$.post($('#commit_form').attr('action')+'kanjia_ajax/', $('#commit_form').serialize(), function(data){
				
				if(data.status==1){
					global_obj.win_alert('评论成功!', function(){
						window.location=$('#commit_form').attr('action')+'kanjia_order/status/3/';
					});					
				}else{
					global_obj.win_alert(data.msg);
				}
				
			}, 'json');
		});
	},
	
	gift_checkout_init:function(){
		$("#shipping_method").click(function(){
			var top = $(window).height()/2;
			$("#shipping-modal").css('top',top-80);
			$("#shipping-modal").modal('show');
			
		});
		
		$("#confirm_shipping_btn").live('click',function(){
			$("#shipping-modal").modal('hide');
			user_obj.change_shipping_method();
		});
		
		$("#cancel_shipping_btn").live('click',function(){
		    $("#shipping-modal").modal('hide');
		});
		
		$('#checkout_form #submit-btn').click(function() {
			var isshipping = parseInt($('#checkout_form input[name=isshipping]').val());
			if(isshipping==0){
				if(global_obj.check_form($('*[notnull]'))){return false};
			}else{
            	var AddressID = parseInt($('#checkout_form input[name=AddressID]').val());
				if (AddressID == 0 || isNaN(AddressID)) {
					alert("请选择收货地址");
					return false;
				}
			}

            $(this).attr('disabled', true);

            var param = $('#checkout_form').serialize();
            var url = $('#checkout_form').attr('action');

            $.post(url, param, function(data) {
				if (data.status == 1) {
				   window.location = data.url;
                }else{
					global_obj.win_alert(data.msg, function(){
						window.location.href=data.url;
					});
				}

            }, 'json');
        });
	},
	
	change_shipping_method:function(){
		var Shipping_ID = parseInt($("input[name='Shiping_ID']:checked").attr('value'));
		var Shipping_Name  = $("input[name='Shiping_ID']:checked").attr('shipping_name');
		if(FreeShipping==1){
			$("#shipping_name").html(Shipping_Name);
			$("#Order_Shipping_Express").attr('value',Shipping_Name);
			$("#total_price").attr('value', 0);
			$('#total_shipping_fee_txt').html('免运费');
		}else{		
			var City_Code = $("#City_Code").attr('value');
			var action = 'change_shipping_method';
			var url = base_url + 'api/' + Users_ID + '/user/ajax/';
			
			var param = {
				Shipping_ID:Shipping_ID,
				City_Code:City_Code,
				action:action
			};
				
			$.post(url, param, function(data) {
				if(data.status == 1){
					var total_price = parseFloat(data.total_shipping_fee);
					$("#shipping_name").html(Shipping_Name); 
					$("#Order_Shipping_Express").attr('value',Shipping_Name);
					$("#total_price_txt").html('&yen' + total_price);
					$("#total_price").attr('value', total_price);
					
					if (parseFloat(data.total_shipping_fee) == 0) {
						$('#total_shipping_fee_txt').html('免运费');
					} else {
						$('#total_shipping_fee_txt').html(data.total_shipping_fee + '元');
					}
				
				}
				
			},'json');
		}
	},
	
	gift_payment_init: function() {
		$("a.direct_pay").click(function(){
			var PaymentMethod = $(this).attr("data-value"); 
			var PaymentID = $(this).attr("id");
			$("#PaymentMethod_val").attr("value",PaymentMethod);
			user_obj.submit_payment();
		});
		
		/*余额支付确认支付,与线下支付确认*/
		$("#btn-confirm").click(function(){
			user_obj.submit_payment();
		});

        $('#payment_form').submit(function() {
            return false;
        });
		
       
    },
	
 	submit_payment:function(){
		 $('#payment_form .payment input').attr('disabled', true);
            $.post($('#payment_form').attr('action'), $('#payment_form').serialize(), function(data) {
                $('#payment_form .payment input').attr('disabled', false);
                if (data.status == 1) {
                    window.location = data.url
                } else {
                    global_obj.win_alert(data.msg);
                }
            }, 'json');
	},
	
	gift_init:function(){
		$('#gift .item a.concel').click(function(){
			var ordersid = $(this).attr("ret");
			$.post(ajax_url, {action:'concel',ordersid:ordersid}, function(data) {
                if (data.status == 1) {
					global_obj.win_alert('取消成功!', function(){
						window.location.href=data.url;
					});		
                } else {
                    global_obj.win_alert(data.msg);
                }
            }, 'json');
		});
		
		$('#gift .item a.recieve').click(function(){
			var ordersid = $(this).attr("ret");
			$.post(ajax_url, {action:'recieve',ordersid:ordersid}, function(data) {
                if (data.status == 1) {
					global_obj.win_alert('操作成功!', function(){
						window.location.href=data.url;
					});		
                } else {
                    global_obj.win_alert(data.msg);
                }
            }, 'json');
		});
	},
}