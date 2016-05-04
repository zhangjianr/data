$(document).ready(function(){
	$("#search").click(function(){
		var param = {cate_id:$("#Category").val(),keyword:$("#keyword").val(),action:'get_product'};
		$.post(base_url+'/member/pifa/ajax.php',param,function(data){
			
			$("#select_product").html(data);
		});
		
	});
	//价格区间
	$("#add_range_law").click(function(){
		var htmlTpl = '<li>'+
					'数量:&nbsp;'+
					'<input type="text" name="numX[]" value="" class="form_input" size="5" maxlength="10" />'+
					'&nbsp;-&nbsp;'+
					'<input type="text" name="numY[]" value="" class="form_input" size="5" maxlength="10" />'+
					'&nbsp;价格:&nbsp;'+
					'<input type="text" name="price[]" value="" class="form_input" size="5" maxlength="10" />￥&nbsp<a><img hspace="5" src="/static/member/images/ico/del.gif"></a>'+
					'</li>';
		$('#range_box').append(htmlTpl);
	});
	$(document).on('click', '#range_box li a', function(){
		$(this).parent().remove();
	});
	//选择您所要选的产品
	$("#select_product").change(function(){
		$("#Products_ID").attr("value",$(this).val());
		$("#Products_Name").attr("value",$(this).find("option:selected").text().split('---')[0]);
		var price_part = $(this).find("option:selected").text().split('---')[1];
	    var length = price_part.length;
		var price = parseFloat(price_part.substring(1,length-1)).toFixed(2);
		$("#Products_Price").attr("value",price);
		$("#Products_Price_Txt").html(price);
	});
	
	//时间区间选择js初始化
	   var date_str = new Date();
		
		//$('#add_form input[name=AccTime_S], #add_form input[name=AccTime_E]').omCalendar({
		//	date: new Date(date_str.getFullYear(), date_str.getMonth(), date_str.getDate(), 00, 00, 00),
			
		//	showTime: true
		//});

		$("#begin_num,#end_num").change(function(){
			var num = parseInt($(this).attr('value'));
			
			if(!isNaN(num)){
				$(this).attr('value',parseInt($(this).attr('value')));
			}else{
				$(this).attr('value','');
			}
		});
	
		$('#add_form').submit(function() {

			if (global_obj.check_form($('*[notnull]'))) {
				return false
			};
			
			var Products_Price = Number($("#Products_Price").attr("value"));
			var Bottom_Price =  Number($("#Bottom_Price").attr("value"));
		
			var begin_num = Number($("#begin_num").attr('value'));
			var end_num = Number($("#end_num").attr('value'));

			if(Bottom_Price <0 || end_num < 0 || begin_num<0){
				alert('任何价格都不能是负数...');
				return false;
			}
		
			
			if(Bottom_Price >= Products_Price){
				alert('底价必须小于产品原价');
				return false;
			}
			
			if(end_num == begin_num){
				alert('起价与终价不能相等');
				return false;
			}
			
			if(end_num < begin_num){
				alert('终价必须大于起价');
				return false;
			}
			
			var max_num = Products_Price-Bottom_Price;
		
			if(end_num > max_num){
				alert('随机砍价区间必须在0到'+max_num+'之间');
				return false;
			}
			
			return true;

		});
});
var pifa_obj={
	pay_shipping_config_init:function(){
		$("input.Default_Shipping").click(function(){
			var template_exist = $(this).parent().prev().find('select').length;
		
			if(template_exist == 0){
				alert('请为此物流公司添加模板');
				return false;
			}
		});
	    $('#shipping_default_config input:submit').click(function(){
			if(global_obj.check_form($('#shipping_default_config *[notnull]'))){
				return false;
			};
		});
	},
	category_init:function(){
		global_obj.file_upload($('#ImgUpload'), $('#category_form input[name=ImgPath]'), $('#ImgDetail'));
		
	},
	products_form_init:function(){
		$("input[name='Products_IsShippingFree']").click(function(){
			var is_shipping_free = $(this).attr('value');
			
			if(is_shipping_free == 1){
				$('#free_shipping_company').css('display','block');
			}else{
				$('#free_shipping_company').css('display','none');
			}
		});
		
		$("#products #Type_ID").change(function(){
			
			var TypeID = $("#Type_ID").val();
			var ProductsID = 0;
			
			if(TypeID.length > 0){
				$.ajax({
					type	: "POST",
					url		: "ajax.php",
					data	: "action=get_attr&UsersID="+$("#UsersID").val()+"&TypeID="+$("#Type_ID").val()+"&ProductsID="+$("#ProductsID").val(),
					dataType: "json",
					async : false,
					success	: function(data){
						
						if(data.content){
							$("#attrs").css("display","block");
							$("#attrs").html(data.content);
						}else{
							alert("暂无属性！");
						}
					}
				});	
			} else {
				
			 $("#attrs").html('');
			   $("#Category").focus();
				
			}
		});
	},
	products_add_init:function(){
		
		$("#product_add_form").submit(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
		});

		pifa_obj.products_form_init();
	},
	
	products_edit_init:function(){
		
		$("#product_edit_form").submit(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
		});
	
		pifa_obj.products_form_init();
	},
	
	products_list_init:function(){
		$('a[href=#search]').click(function(){
			$('form.search').slideDown();
			return false;
		});
	},
	orders_init:function(){
		$('#search_form input:button').click(function(){
			window.location='./?'+$('#search_form').serialize()+'&do_action=pifa.orders_export';
		});
		
		$("#search_form .output_btn").click(function(){
			window.location='./output.php?'+$('#search_form').serialize()+'&type=order_detail_list';
		});
		
		var date_str=new Date();
		$('#search_form input[name=AccTime_S], #search_form input[name=AccTime_E]').omCalendar({
			date:new Date(date_str.getFullYear(), date_str.getMonth(), date_str.getDate(), 00, 00, 00),
			maxDate:new Date(date_str.getFullYear(), date_str.getMonth(), date_str.getDate()),
			showTime:true
		});
		
		$('#orders .cp_title #cp_view, #orders .cp_title #cp_mod').click(function(){
			$('#orders .cp_title div').removeClass('cur');
			$(this).addClass('cur');
			
			if($(this).attr('id')=='cp_view'){
				$('#orders_mod_form .cp_item_view').show();
				$('#orders_mod_form .cp_item_mod').hide();
			}else{
				$('#orders_mod_form .cp_item_view').hide();
				$('#orders_mod_form .cp_item_mod').show();
			}
		});
		$('#orders_mod_form').submit(function(){$('#orders_mod_form input:submit').attr('disabled', true);});
		$('#orders_mod_form .cp_item_mod .back').click(function(){$('#orders .cp_title #cp_view').click();});
		
		var change_is_read=function(){
			$('#order_list tr[IsRead=0]').off().click(function(){
				var o=$(this);
				$.get('?', 'do_action=pifa.orders_set_read&OrderId='+o.attr('OrderId'), function(data){
					if(data.ret==1){
						o.removeClass('is_not_read').off();
					}
				}, 'json');
			});
		};
		
		var refer_time=60;
		var refer_left_time=0;
		var refer_ing=false;
		var auto_refer=function(){
			if($('#auto_refer').is(':checked')){
				if(refer_left_time<refer_time){
					$('#search_form div label').html('<span><strong>'+(refer_time-refer_left_time)+'</strong></span>秒后自动刷新');
					refer_left_time++;
				}else if(refer_ing==false){
					refer_ing=true;
					$('#search_form div label').html('数据拉取中..');
					
					$.get('?', 'do_action=pifa.orders_is_not_read', function(data){
						refer_ing=false;
						refer_left_time=0;
						if(data.ret==1){
							var have_new_order=false;
							var html='';
							for(var i=0; i<data.msg.length; i++){
								if($('#order_list tr[OrderId='+data.msg[i]['OrderId']+']').size()==0){	//订单号不在列表中
									have_new_order=true;
									html+='<tr class="is_not_read" IsRead="0" OrderId="'+data.msg[i]['OrderId']+'">';
										html+='<td nowrap="nowrap">新订单</td>';
										html+='<td nowrap="nowrap">'+data.msg[i]['OId']+'</td>';
										html+='<td>'+data.msg[i]['Name']+'</td>';
										html+='<td nowrap="nowrap">￥'+data.msg[i]['Price']+'</td>';
										NeedShipping && (html+='<td nowrap="nowrap">'+data.msg[i]['Shipping']+'</td>');
										html+='<td nowrap="nowrap">'+orders_status[data.msg[i]['OrderStatus']]+'</td>';
										html+='<td nowrap="nowrap">￥'+data.msg[i]['OrderTime']+'</td>';
										html+='<td nowrap="nowrap" class="last"><a href="?m=pifa&a=orders&d=view&OrderId='+data.msg[i]['OrderId']+'"><img src="'+domain.static+'/member/images/ico/view.gif" align="absmiddle" alt="修改" /></a><a href="?m=pifa&a=orders&do_action=pifa.orders_del&OrderId='+data.msg[i]['OrderId']+'" title="删除" onClick="if(!confirm(\'删除后不可恢复，继续吗？\')){return false};"><img src="'+domain.static+'/member/images/ico/del.gif" align="absmiddle" /></a></td>';
									html+='</tr>';
								}
							}
							if(have_new_order){
								$('#search_form div label').html('<span>数据拉取成功</span>');
								$('#order_list tbody').prepend(html);
								change_is_read();
								$('body').prepend('<bgsound src="'+domain.static+'/member/images/pifa/tips.mp3" autostart="true" loop="1">');
							}else{
								$('#search_form div label').html('<span>没有新的订单</span>');
							}
						}else{
							$('#search_form div label').html('<span>数据拉取失败</span>');
						}
					}, 'json');
				}
			}else{
				$('#search_form div label').html('自动刷新订单');
				refer_left_time=0;
				refer_ing=false;
			}
			setTimeout(auto_refer, 1000);
		};
		auto_refer();
		change_is_read();
		
		$('#orders a[name=print]').click(function(){
			var pos_l=($(document.body).width()-670)/2;
			var pos_t=($(window).height()-500)/2;
			$('#print_cont').css({'top':pos_t+'px','left':pos_l+'px'}).fadeIn();
			var OrderId=$(this).parent().parent().attr('OrderId');
			$('#get_data').attr('src','./?m=pifa&a=print&OrderId='+OrderId+'&n='+Math.random());
		});
	},
	
	print_orders_init:function(){
		
		$('.r_nav, .ui-nav-tabs').hide();
		$('html,body').css('background','none');
		$('.iframe_content').removeClass('iframe_content');	
		$('.print_area input[name=print_close]').click(function(){
			$('#print_cont').fadeOut();
		});
		
		$('.print_area input[name=print_go]').click(function(){
			window.print();
		});
		$('.print_area input[name=print_close]').click(function(){
			$(window.parent.document).find('#print_cont').fadeOut();
		});
		
	},
}