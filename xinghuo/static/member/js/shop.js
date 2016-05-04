
  function addTr(tab, row, trHtml){
     //获取table最后一行 $("#tab tr:last")
     //获取table第一行 $("#tab tr").eq(0)
     //获取table倒数第二行 $("#tab tr").eq(-2)
     var $tr=$("#"+tab+" tr").eq(row);
     if($tr.size()==0){
        alert("指定的table id或行数不存在！");
        return;
     }
     $tr.after(trHtml);
  }
   
  function delTr(ckb){
     //获取选中的复选框，然后循环遍历删除
     var ckbs=$("input[name="+ckb+"]:checked");
     if(ckbs.size()==0){
        alert("要删除指定行，需选中要删除的行！");
        return;
     }
           ckbs.each(function(){
              $(this).parent().parent().remove();
           });
  }

var shop_obj={

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
	skin_init:function(){
		$('#skin li .item').click(function(){
			if(!confirm('您确定要选择此风格吗？')){return false};
			$.post('?', "do_action=shop.skin_mod&SId="+$(this).attr('SId'), function(data){
				if(data.status==1){
					window.location.reload();
				}
			}, 'json');
		});
	},
	
	category_init:function(){
		global_obj.file_upload($('#ImgUpload'), $('#category_form input[name=ImgPath]'), $('#ImgDetail'));
		
	},
	
	home_init:function(){
		//加载上传按钮
		global_obj.file_upload($('#HomeFileUpload'), $('#home_form input[name=ImgPath]'), $('#home .shop_skin_index_list').eq($('#home_form input[name=no]')).find('.img'));
		for(var i=0;i<5;i++){
			global_obj.file_upload($('#HomeFileUpload_'+i), $('#home_form input[name=ImgPathList\\[\\]]').eq(i), $('#home_form .b_r').eq(i));
		}
		$('.m_lefter a').attr('href', '#').css({'cursor':'default', 'text-decoration':'none'}).click(function(){
			$(this).blur();
			return false;
		});
		$('.m_lefter form').submit(function(){
			return false;
		});
		//加载版面内容
		for(i=0; i<shop_skin_data.length; i++){
			var obj=$("#shop_skin_index div").filter('[rel=edit-'+shop_skin_data[i]['Postion']+']');
			obj.attr('no', i);
			if(shop_skin_data[i]['ContentsType']==1){
				var dataObj=eval("("+shop_skin_data[i]['ImgPath']+")");
				if(dataObj[0].indexOf('http://')!=-1){
					var s='';
				}else if(dataObj[0].indexOf('/u_file/')!=-1){
					var s=domain.img;
					dataObj[0]=dataObj[0].replace('/u_file', '');
				}else if(dataObj[0].indexOf('/api/')!=-1){
					var s=domain.static;
				}else{
					var s='';
				}
				obj.find('.img').html('<img src="'+s+dataObj[0]+'" />');
			}else{
				if(shop_skin_data[i]['ImgPath'].indexOf('http://')!=-1){
					var s='';
				}else if(shop_skin_data[i]['ImgPath'].indexOf('/u_file/')!=-1){
					var s=domain.img;
					shop_skin_data[i]['ImgPath']=shop_skin_data[i]['ImgPath'].replace('/u_file', '');
				}else if(shop_skin_data[i]['ImgPath'].indexOf('/api/')!=-1){
					var s=domain.static;
				}else{
					var s='';
				}
				if(shop_skin_data[i]['NeedLink']==1){
					obj.find('.text').html('<a href="">'+shop_skin_data[i]['Title']+'</a>')
				}else{
					obj.find('.text').html(shop_skin_data[i]['Title'])
				}
				obj.find('.img').html('<img src="'+s+shop_skin_data[i]['ImgPath']+'" />');
			}
		}
		
		$('.shop_skin_index_list div').after('<div class="mod">&nbsp;</div>');	//追加编辑按钮
		$('#shop_skin_index .shop_skin_index_list').hover(function(){$(this).find('.mod').show();}, function(){$(this).find('.mod').hide();});
		
		//点击图标切换编辑内容
		$('#shop_skin_index .shop_skin_index_list .mod').click(function(){
			var parent=$(this).parent();
			var no=parent.attr('no');
		
			$('#SetHomeCurrentBox').remove();
			parent.append("<div id='SetHomeCurrentBox'></div>");
			$('#SetHomeCurrentBox').css({'height':parent.height()-10, 'width':parent.width()-10})
			$("#setbanner, #setimages").hide();
			$('.url_select').css('display', shop_skin_data[no]['NeedLink']==1?'block':'none');
			
			if(shop_skin_data[no]['ContentsType']==1){
				$("#setbanner").show();
				var dataImgPath=eval("("+shop_skin_data[no]['ImgPath']+")");
				var dataUrl=eval("("+shop_skin_data[no]['Url']+")");
				var dataTitle=eval("("+shop_skin_data[no]['Title']+")");
				$('#home_form #setbanner .tips label').html(shop_skin_data[no]['Width']+'*'+shop_skin_data[no]['Height']);
				for(var i=0; i<dataImgPath.length; i++){
					$('#home_form input[name=ImgPathList\\[\\]]').eq(i).val(dataImgPath[i]);
					$('#home_form input[name=UrlList\\[\\]]').eq(i).val(dataUrl[i]);
					$('#home_form input[name=TitleList\\[\\]]').eq(i).val(dataTitle[i]);
					
					if(dataImgPath[i].indexOf('http://')!=-1){
						var s='';
					}else if(dataImgPath[i].indexOf('/u_file/')!=-1){
						var s=domain.img;
						dataImgPath[i]=dataImgPath[i].replace('/u_file', '');
					}else if(dataImgPath[i].indexOf('/api/')!=-1){
						var s=domain.static;
					}else{
						var s='';
					}
					dataImgPath[i] && $("#home_form .b_r").eq(i).html('<a href="'+s+dataImgPath[i]+'" target="_blank"><img src="'+s+dataImgPath[i]+'" /></a>');
					if(dataUrl[i]){
						$("#home_form select[name=UrlList\\[\\]]").eq(i).find("option[value='"+dataUrl[i]+"']").attr("selected", true);
					}else{
						$("#home_form select[name=UrlList\\[\\]]").eq(i).find("option").eq(0).attr("selected", true);
					}
				}
			}else{
				if(parent.find('.text').length){
					$("#setimages div[value=title]").show();
				}else{
					$("#setimages div[value=title]").hide();
				}
				if(parent.find('.img').length){
					$("#setimages div[value=images]").show();
				}else{
					$("#setimages div[value=images]").hide();
				}
				$("#setimages").show();
				$('#home_form input').filter('[name=Title]').val(shop_skin_data[no]['Title'])
				.end().filter('[name=ImgPath]').val(shop_skin_data[no]['ImgPath'])
				.end().filter('[name=Title]').focus();
				$('#home_form #setimages .tips label').html(shop_skin_data[no]['Width']+'*'+shop_skin_data[no]['Height']);
				if(shop_skin_data[no]['Url']){
					$("#home_form select[name=Url] option[value='"+shop_skin_data[no]['Url']+"']").attr("selected", true);
				}else{
					$("#home_form select[name=Url] option").eq(0).attr("selected", true);
				}
			}	
					
			$('#home_form input').filter('[name=PId]').val(shop_skin_data[no]['PId'])
			.end().filter('[name=SId]').val(shop_skin_data[no]['SId'])
			.end().filter('[name=ContentsType]').val(shop_skin_data[no]['ContentsType'])
			.end().filter('[name=no]').val(no);
		});
		
		//加载默认内容
		$('#shop_skin_index .shop_skin_index_list .mod').eq(0).click();
		
		//ajax提交更新，返回
		$('#home_form').submit(function(){return false;});
		$('#home_form input:submit').click(function(){
			$(this).attr('disabled', true);
			$.post('?', $('#home_form').serialize()+'&do_action=shop.set_home_mod&ajax=1', function(data){
				$('#home_form input:submit').attr('disabled', false);
				if(data.status==1){
					$('#home_mod_tips .tips').html('首页设置成功！');
					$('#home_mod_tips').leanModal();
					
					var _no=$('#home_form input[name=no]').val();
					var _v=$("div[no="+_no+"]");
					shop_skin_data[_no]['ImgPath']=data.ImgPath;
					shop_skin_data[_no]['Title']=data.Title;
					shop_skin_data[_no]['Url']=data.Url;
					
					if(shop_skin_data[_no]['ContentsType']==1){
						var dataImgPath=eval("("+shop_skin_data[_no]['ImgPath']+")");
						if(dataImgPath[0].indexOf('http://')!=-1){
							var s='';
						}else if(dataImgPath[0].indexOf('/u_file/')!=-1){
							var s=domain.img;
							dataImgPath[0]=dataImgPath[0].replace('/u_file', '');
						}else if(dataImgPath[0].indexOf('/api/')!=-1){
							var s=domain.static;
						}else{
							var s='';
						}
						_v.find('.img').html('<img src="'+s+dataImgPath[0]+'" />');
					}else{
						if(shop_skin_data[_no]['ImgPath'].indexOf('http://')!=-1){
							var s='';
						}else if(shop_skin_data[_no]['ImgPath'].indexOf('/u_file/')!=-1){
							var s=domain.img;
							shop_skin_data[_no]['ImgPath']=shop_skin_data[_no]['ImgPath'].replace('/u_file', '');
						}else if(shop_skin_data[_no]['ImgPath'].indexOf('/api/')!=-1){
							var s=domain.static;
						}else{
							var s='';
						}
						_v.find('.text').html('<a href="">'+shop_skin_data[_no]['Title']+'</a>');
						_v.find('.img').html('<img src="'+s+shop_skin_data[_no]['ImgPath']+'" />');
					}
				}else{
					$('#home_mod_tips .tips').html('首页设置失败，请重试！');
					$('#home_mod_tips').leanModal();
				};
			}, 'json');
		});
		
		$('#home_form .item .rows .b_l a[href=#shop_home_img_del]').click(function(){
			var _no=$(this).attr('value');
			$('#home_form .b_r').eq(_no).html('');
			$('#home_form input[name=ImgPathList\\[\\]]').eq(_no).val('');
			this.blur();
			return false;
		});
	
	},
	withdraw_method_init:function(){
		
		var method_name_rows = '<div style="display:block" class="rows method_name_rows"><label>名称</label><span class="input"><input value="" class="form_input" name="Method_Name" notnull="" type="text"></span><div class="clear"></div></div>'; 
	      
		$('#create_method_form input:submit').click(function(){
			if(global_obj.check_form($('#create_method_form *[notnull]'))){return false};
		});
		
		$(".method_edit_btn").click(function(){
			
			var Method_ID  = $(this).attr("method-id");
			var param = {'Method_ID':Method_ID,'action':'get_withdraw_edit_form'};
			
			$.get(base_url+'member/shop/ajax.php',param,function(data){
			   if(data.status == 1){
					$("#method_edit_content").html(data.content);
					$('#mod_edit_method').leanModal();
			   }
			},'json');
		});
		
		$("#create_method_btn").click(function(){
			$('#mod_create_method').leanModal();
		});
		
		$("input[name='Method_Type']").live('click',function(){
			
			var Method_Type = $(this).attr('value');
			
			if(Method_Type == 'bank_card'){
				if($(this).parent().parent().siblings(".method_name_rows").length == 0){
					$(this).parent().parent().after(method_name_rows);
				}
				$(this).parent().parent().siblings(".method_type_rows").hide();
			}else if(Method_Type == 'alipay'){
				$(this).parent().parent().siblings(".method_name_rows").remove();
				$(this).parent().parent().siblings(".method_type_rows").hide();
			}else if(Method_Type == 'red'){
				$(this).parent().parent().siblings(".method_name_rows").remove();
				$(this).parent().parent().siblings(".method_type_rows").show();
			}
			
		});
	},
	
	property_edit_init:function(){
	
		$('#property_edit_form input:submit').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
		});
		
	},
	
	property_add_init:function(){
	   
	   $("#products .font_btn").click(function(){
			
			var TypeID = $("#Type_ID").val();
			var ProductsID = $("#ProductsID").val();
			
			if(TypeID.length > 0){
				$.ajax({
					type	: "POST",
					url		: "ajax.php",
					data	: "action=get_properity&UsersID="+$("#UsersID").val()+"&TypeID="+$("#Type_ID").val()+"&ProductsID="+$("#ProductsID").val(),
					dataType: "json",
					async : false,
					success	: function(data){
						if(data.msg){
							$("#propertys").css("display","block");
							$("#propertys").html(data.msg);
						}else{
							alert("暂无属性！");
						}
					}
				});	
			} else {
				
			   alert("请选择产品类型");
			   $("#Category").focus();
				
			}
		});
		
		$("#products .font_btn_clear").click(function(){
		    $("#propertys").css("display","none");
			$("#propertys").html("");
		});
		
		
		$('#property_add_form input:submit').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
		});
	
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

		shop_obj.products_form_init();
	},
	
	products_edit_init:function(){
		
		$("#product_edit_form").submit(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
		});
		
		shop_obj.products_form_init();
	},
	
	products_list_init:function(){
		$('a[href=#search]').click(function(){
			$('form.search').slideDown();
			return false;
		});
	},
	
	products_category_init:function(){
		global_obj.file_upload($('#HomeFileUpload'), $('#shop_category_form input[name=ImgPath]'), $('#look'));
		$('#products .category .m_lefter dl').dragsort({
			dragSelector:'dd',
			dragEnd:function(){
				var data=$(this).parent().children('dd').map(function(){
					return $(this).attr('CateId');
				}).get();
				$.get('?m=shop&a=products', {do_action:'shop.products_category_order', sort_order:data.join('|')});
			},
			dragSelectorExclude:'ul, a',
			placeHolderTemplate:'<dd class="placeHolder"></dd>',
			scrollSpeed:5
		});
		
		$('#products .category .m_lefter ul').dragsort({
			dragSelector:'li',
			dragEnd:function(){
				var data=$(this).parent().children('li').map(function(){
					return $(this).attr('CateId');
				}).get();
				$.get('?m=shop&a=products', {do_action:'shop.products_category_order', sort_order:data.join('|')});
			},
			dragSelectorExclude:'a',
			placeHolderTemplate:'<li class="placeHolder"></li>',
			scrollSpeed:5
		});
		
		$('#products .category .m_lefter ul li').hover(function(){
			$(this).children('.opt').show();
		}, function(){
			$(this).children('.opt').hide();
		});
		
		$('#pro-list-type .item').removeClass('item_on').each(function(){
			$(this).click(function(){
				$('#pro-list-type .item').removeClass('item_on');
				$(this).addClass('item_on');
				$('#shop_category_form input[name=ListTypeId]').val($(this).attr('ListTypeId'));
			});
		}).filter('[ListTypeId='+$('#shop_category_form input[name=ListTypeId]').val()+']').addClass('item_on');
		
		$('#shop_category_form').submit(function(){return false;});
		$('#shop_category_form input:submit').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			$(this).attr('disabled', true);
			$.post('?', $('#shop_category_form').serialize(), function(data){
				if(data.status==1){
					window.location='?m=shop&a=products&d=category';
				}else{
					alert(data.msg);
					$('#shop_category_form input:submit').attr('disabled', false);
				}
			}, 'json');
		});
	},
	products_attr_add_init:function(){
		shop_obj.products_attr_cu();
		$('#shop_attr_add_form input:submit').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
		});
	},
	products_attr_edit_init:function(){
		shop_obj.products_attr_cu();
		$('#shop_attr_edit_form input:submit').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
		});
	
	},
	products_attr_cu:function(){
		//产品属性create 和  update 共用属性
		
		//如录入方式为手工录入和多行文本框，则禁用可选值textarea
		$(".Attr_Input_Type").click(function(){
			var value  = parseInt($(this).val());
			$("#Attr_Values").removeAttr('style');
			if(value == 0||value == 2){
				
				$("#Attr_Values").attr({"disabled":true});
			}else{
				$("#Attr_Values").removeAttr('disabled');
			   
		
			}
			
			//若为手工录入和多行文本框，可不填
			if(value == 0||value == 2){
				 $("#Attr_Values").removeAttr('notnull');
			}else{
				$("#Attr_Values").attr({"notnull":true});
			}
			
		
		});
		
		//获取此分类下属性组
		$("#Type_ID").change(function(){
			var Type_ID_Opt_Item = $(this).parent();
			var Type_ID  =  $(this).val();
			if(Type_ID.length >0 ){
				$('#attr_group_opt').remove();	
				$.get(base_url+'member/shop/ajax.php',{Type_ID:Type_ID,action:'get_attr_group'},function(data){
					
					if(data.status == 1){
						var select = document.createElement("select");
						$(data.content).each(function(i){
							select.options[i] = new Option(this, i);
						});
						
						var select_dom = $(select);
						select_dom.attr("name","Attr_Group");
						var opt_item = $(document.createElement("div"));
						opt_item.attr({class:"opt_item",id:"attr_group_opt"});
					    
						var span_input = $('<span class="input"></span>');
						span_input.append(select_dom);
					    
						opt_item.append($("<label>属性组:</label>"));
						opt_item.append(span_input);
						
						Type_ID_Opt_Item.after(opt_item);
						
						var clear_div = $('<div class="clear"></div>');
						Type_ID_Opt_Item.after(clear_div);
						//testDiv.appendChild(select); 					
					}
					
				},'json');
			}
			
		});
		
	},
	products_property_init:function(){
		var ul=$('#products_property_form ul');
		var add_btn=ul.find('img[src*=add]');
		var add_fun=function(){
			add_btn.click(function(){
				ul.append(ul.children('li:last').clone(true));
				ul.children('li').eq(-2).children('img[src*=add]').remove();
				ul.find('li:last input').val('');
			});
		};
		add_fun();
		ul.find('img[src*=del]').click(function(){
			if(ul.children('li').size()>1){
				$(this).parent().remove();
				
				if(ul.find('img[src*=add]').size()==0){
					ul.children('li:last').append(add_btn);
					add_fun();
				}
			}
		});
		
		$('#products .property .m_lefter ul li').hover(function(){
			$(this).children('.opt').show();
		}, function(){
			$(this).children('.opt').hide();
		});
		
		$('#products_property_form').submit(function(){return false;});
		$('#products_property_form input:submit').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			$(this).attr('disabled', true);
			$.post('?', $('#products_property_form').serialize(), function(data){
				if(data.status==1){
					window.location='?m=shop&a=products&d=property';
				}else{
					alert(data.msg);
					$('#products_property_form input:submit').attr('disabled', false);
				}
			}, 'json');
		});
	},
	
	orders_init:function(){
		$('#search_form input:button').click(function(){
			window.location='./?'+$('#search_form').serialize()+'&do_action=shop.orders_export';
		});
		
		$("#search_form .output_btn").click(function(){
			window.location='./output.php?'+$('#search_form').serialize()+'&type=order_detail_list';
		});
		$("#search_form .output_btns").click(function(){
			alert(1);
			window.location='../shop/output.php?'+$('#search_form').serialize()+'&type=spark_order_list';
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
				$.get('?', 'action=set_read&OrderID='+o.attr('OrderId'), function(data){
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
					
					$.get('?', 'do_action=shop.orders_is_not_read', function(data){
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
										html+='<td nowrap="nowrap" class="last"><a href="?m=shop&a=orders&d=view&OrderId='+data.msg[i]['OrderId']+'"><img src="'+domain.static+'/member/images/ico/view.gif" align="absmiddle" alt="修改" /></a><a href="?m=shop&a=orders&do_action=shop.orders_del&OrderId='+data.msg[i]['OrderId']+'" title="删除" onClick="if(!confirm(\'删除后不可恢复，继续吗？\')){return false};"><img src="'+domain.static+'/member/images/ico/del.gif" align="absmiddle" /></a></td>';
									html+='</tr>';
								}
							}
							if(have_new_order){
								$('#search_form div label').html('<span>数据拉取成功</span>');
								$('#order_list tbody').prepend(html);
								change_is_read();
								$('body').prepend('<bgsound src="'+domain.static+'/member/images/shop/tips.mp3" autostart="true" loop="1">');
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
			$('#get_data').attr('src','./?m=shop&a=print&OrderId='+OrderId+'&n='+Math.random());
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
	 distribute_config_init:function(){
		 
		$('#level_form').submit(function(){
			alert("abcd");
			if(global_obj.check_form($('*[notnull]'))){return false};
			$('#level_form input:submit').attr('disabled', true);
			return true;
		});
		
		//添加分销规则
		$("#add_distribute").click(function(){
		  var li_item = '<li class=\"item\"><input name=\"distribute_from[]\" value=\"\" class=\"form_input\" size=\"3\" maxlength=\"10\" type=\"text\"> 到 <input name=\"distribute_to[]\" value=\"\" class=\"form_input\" size=\"3\" maxlength=\"10\" type=\"text\"> (含) &nbsp;&nbsp; <input name=\"distribute_rate[]\" value=\"\" class=\"form_input\" size=\"3\" maxlength=\"10\" type=\"text\"> <span>%</span> <a><img src=\"/static/member/images/ico/del.gif\" hspace=\"5\"></a></li>';
			$("ul#distribute_panel").append(li_item);

		});

		$("#distribute_panel li.item a").live('click',function(){
				$(this).parent().remove();
		});
	},
	
	confirm_form_init:function(){
		
		$("#add_man").click(function(){
			var li_item = '<li class="item">满：￥ <input name="man_reach[]" value="" class="form_input" size="3" maxlength="10" type="text"> 送：￥ <input name="man_award[]" value="" class="form_input" size="3" maxlength="10" type="text"> <a><img src="/static/member/images/ico/del.gif" hspace="5"></a></li>';
			$("ul#man_panel").append(li_item);
		});
		
		$("#man_panel li.item a").live('click',function(){
				$(this).parent().remove();
		});
		
		$("#add_integral_law").click(function(){
			var li_item = '<li class="item">满：￥ <input name="Integral_Man[]" value="" class="form_input" size="3" maxlength="10" type="text"> 可用<input name="Integral_Use[]" value="" class="form_input" size="3" maxlength="10" type="text">个<a><img src="/static/member/images/ico/del.gif" hspace="5"></a></li>';
			$("ul#integral_panel").append(li_item);
		});
		
		$("#integral_panel li.item a").live('click',function(){
				$(this).parent().remove();
		});

		//添加分销规则
		$("#add_distribute").click(function(){
		  var li_item = '<li class=\"item\"><input name=\"distribute_from[]\" value=\"\" class=\"form_input\" size=\"3\" maxlength=\"10\" type=\"text\"> 到 <input name=\"distribute_to[]\" value=\"\" class=\"form_input\" size=\"3\" maxlength=\"10\" type=\"text\"> (含) &nbsp;&nbsp; <input name=\"distribute_rate[]\" value=\"\" class=\"form_input\" size=\"3\" maxlength=\"10\" type=\"text\"> <span>%</span> <a><img src=\"/static/member/images/ico/del.gif\" hspace=\"5\"></a></li>';
			$("ul#distribute_panel").append(li_item);

		});

		$("#distribute_panel li.item a").live('click',function(){
				$(this).parent().remove();
		});
		
		
	},
	dis_title_init:function(){
		
		$('#level_form').submit(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			$('#level_form input:submit').attr('disabled', true);
			return true;
		});
		
		$(".title_val").live('blur',function(){
			
		});
		
		$("#add_dis_title").click(function(){
			var count = $("#dis_pro_title_table tr").length;
			var dis_title_tr = shop_obj.dis_title_tr.replace(/tr_index/,count);
	
			if(count <= 4){
				addTr('dis_pro_title_table', -1,dis_title_tr);
			}
		});
	
		$(".input_del").live('click',function(){
			$(this).parent().parent().remove();
		});
		
		$("#clear_form").click(function(){
			    /*清除本form内容*/
		        
				var  param = {'action':'clear_dis_level','UsersID':Users_ID};
				
				$.post(base_url+'member/shop/ajax.php',param,function(data){
					if(data.status == 1){
						$(':input','#level_form')  
						.not(':button, :submit, :reset, :hidden')  
						.val('')  
						.removeAttr('checked')  
						.removeAttr('selected'); 
						
						alert(data.msg);
					}
					   
				},'json');
				
				
		});
	},	
	
	dis_channel_init:function(){
		
		$('#level_form').submit(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			$('#level_form input:submit').attr('disabled', true);
			return true;
		});
		
		$(".title_val").live('blur',function(){
			
		});
		
		$("#add_dis_title").click(function(){
			var count = $("#dis_pro_title_table tr").length;
			var dis_title_tr = shop_obj.dis_title_tr.replace(/tr_index/,count);
	
			if(count <= 4){
				addTr('dis_pro_title_table', -1,dis_title_tr);
			}
		});
	
		$(".input_del").live('click',function(){
			$(this).parent().parent().remove();
		});
		$('.level_del').click(function(){
		 
			var level_id=$(this).attr('level_id');
			$('.level_'+level_id).remove();
			$('input[name="submit_button"]').attr('disabled','disabled');
			$('#level_form').submit();
			
			
		});
		
		$("#clear_form").click(function(){
			    /*清除本form内容*/
		        
				var  param = {'action':'clear_dis_level','UsersID':Users_ID};
				
				$.post(base_url+'member/shop/ajax.php',param,function(data){
					if(data.status == 1){
						$(':input','#level_form')  
						.not(':button, :submit, :reset, :hidden')  
						.val('')  
						.removeAttr('checked')  
						.removeAttr('selected'); 
						
						alert(data.msg);
					}
					   
				},'json');
				
				
		});
	},
	distribute_init:function(){
		
		
        //弹出代理信息对话框
		$(".agent_info").click(function(){
			
			var account_id = $(this).attr('agent-id');
			$("#agent-info-modal").modal('show');
			var param = {account_id:account_id,action:'get_dis_agent_form'};
			$.get(base_url+'member/shop/ajax.php',param,function(data){
				if(data.status == 1){
					$("#agent-info-modal").find('div.modal-body').html(data.content);	
					 //展开城市列表
					 $(".county_select").select2({placeholder: "请输入县区名字！"});
					 $(".county_select").val(checkedCounty).trigger("change");
					$("img.trigger").click(function() {
					$('div.ecity ').removeClass('showCityPop');
					$(this).parent().parent().addClass('showCityPop');
					});

					//关闭城市列表
					$("input.close_button").click(function() {
						$(this).parent().parent().parent().removeClass('showCityPop');
					});
		
				}
			},'json');
		     //$("#province_dialog").removeClass("hidden");
		});
		
		 //弹出独立合伙人 一级锁定比例对话框
		$(".channel_info").click(function(){
			var account_id = $(this).attr('agent-id');
			var param = {account_id:account_id,action:'get_channel_type'};
			$("#account_id").val(account_id);
			$.get(
				base_url+'member/shop/ajax.php',
				param,
				function(data){
					if(data.status == 1){
						$("#channel-type").val(data.content.Channel_Type);
						$("#channel-info-modal").modal('show');
					}else{
						$("#channel-type").val('');
						$("#channel-info-modal").modal('show');
					}
				},
			'json'
			);
			
		})
		$("#confirm_channel_type_btn").click(function(){
			var account_id = $("#account_id").val();
			var type_id = $("#channel-type").val();
			
			var param = {account_id:account_id,type_id:type_id,action:'save_channel_type'};
			$.post(base_url+'member/shop/ajax.php',param,function(data){
				if(data.status == 1){
					$("#channel-info-modal").modal('hide');	
					alert("设置成功");
					location.reload();
				}
			},'json');
		})
		
		//选中大区反应
		$(".J_Group").live('click',function(){
			var province_ids = $(this).attr('value');
			var checked = $(this).prop('checked');
			
			if(checked){
				province_ids.split(',').each(function(province_id){
			    	if(!$("#J_Province_"+province_id).prop('disabled')){
						$("#J_Province_"+province_id).prop('checked',true);
					}
				});
			}else{
					
				province_ids.split(',').each(function(province_id){
					if(!$("#J_Province_"+province_id).prop('disabled')){
						$("#J_Province_"+province_id).prop('checked',false);
					}
				});
			}
			
		});
		
		
       
		
		$("#confirm_dis_area_agent_btn").click(function(){
			var JProvinces = $("#dis_agent_form input[name='J_Province']").fieldValue(); 
		    var KCitys = $("#dis_agent_form input[name='K_City']").fieldValue();
		    var County = $(".county_select").fieldValue();
			var account_id = $("#account_id").attr('value');
	
			var param = {JProvinces:JProvinces,
						  KCitys:KCitys,	
						  County:County,	
			             account_id:account_id,
						 action:'save_dis_agent_area'};
			
			$.post(base_url+'member/shop/ajax.php',param,function(data){
				if(data.status == 1){
					$("#agent-info-modal").modal('hide');	
				}
			},'json');
			
		});
		
		//弹出驳回用户申请框
		$("#reject_btn").click(function(){
			$('#reject_withdraw form input[name=Bank_Card]').val('');
			$('#reject_withdraw form input[name=Record_ID]').val($(this).parent().parent().attr('Record_ID'));
			$('#reject_withdraw form').show();
			$('#reject_withdraw .tips').hide();
			$('#reject_withdraw').leanModal();
		});

		//提交驳回用户申请
		$('#reject_withdraw form').submit(function(){return false;});
		$('#reject_withdraw form input:submit').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};

			$(this).attr('disabled', true);
			$.post('/member/shop/ajax.php?', $('#reject_withdraw form').serialize(), function(data){
				$('#reject_withdraw form input:submit').attr('disabled', false);
			
				if(data.status == 1){
					$('#reject_withdraw .tips').html('驳回用户提现申请成功').show();
				}else{
					$('#reject_withdraw .tips').html('驳回用户提现申请失败，出现未知错误').show();
				};
				
				$('#reject_withdraw form').hide();
				$('#reject_withdraw').leanModal();
			}, 'json');
		});	


		$('a.mod-card').click(function(){
			$('#Bank_Card').inputFormat('account');	
			$('#Bank_Card').inputFormat('account');	
			$('#mod_account_card .h span').html(' ('+$(this).parent().parent().children('td[field=1]').html()+')');
			$('#mod_account_card form input[name=Bank_Card]').val('');
			$('#mod_account_card form input[name=UserID]').val($(this).parent().parent().attr('UserID'));
			$('#mod_account_card form').show();
			$('#mod_account_card .tips').hide();
			$('#mod_account_card').leanModal();
		});

		$('#mod_account_card form').submit(function(){return false;});
		
		$('#mod_account_card form input:submit').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			$(this).attr('disabled', true);
			$.post('/member/shop/ajax.php?', $('#mod_account_card form').serialize(), function(data){
				$('#mod_account_card form input:submit').attr('disabled', false);
				
				if(data.status == 1){
					$('#mod_account_card .tips').html('修改银行账号成功！').show();
				}else{
					$('#mod_account_card .tips').html('修改银行账号失败，出现未知错误！').show();
				};
				
				$('#mod_account_card form').hide();
				$('#mod_account_card').leanModal();
			}, 'json');
		});	

		var date_str=new Date();
		$('#search_form input[name=AccTime_S], #search_form input[name=AccTime_E]').omCalendar({
			date:new Date(date_str.getFullYear(), date_str.getMonth(), date_str.getDate(), 00, 00, 00),
			maxDate:new Date(date_str.getFullYear(), date_str.getMonth(), date_str.getDate()),
			showTime:true
		});	


	},
	
	dis_config_init:function(){
		$('#type').change(function(){
			for(var i=1; i<=4; i++){
				$('#rows_'+i).hide();
			}
			$('#rows_'+this.value).show();
		});
		
		
		$("#search").click(function(){
			var param = {cate_id:$("#Category").val(),keyword:$("#keyword").val(),action:'get_product'};
			$.get('?',param,function(data){
				$("#select_product").html(data);
			});
		});
		
		//选择您所要选的产品
		$("#select_product").change(function(){
			$("#limit3").attr("value",$(this).val());
			$("#products_name").attr("value",$(this).find("option:selected").text().split('---')[0]);
		});
		
		$('#dtype').change(function(){
			for(var i=2; i<=3; i++){
				$('#drows_'+i).hide();
			}
			$('#drows_'+this.value).show();
		});
		
		$("#dsearch").click(function(){
			var param = {cate_id:$("#DCategory").val(),keyword:$("#dkeyword").val(),action:'get_product'};
			$.get('?',param,function(data){
				$("#dselect_product").html(data);
			});
		});
		
		$("#dselect_product").change(function(){
			$("#dlimit2").attr("value",$(this).val());
			$("#dproducts_name").attr("value",$(this).find("option:selected").text().split('---')[0]);
		});
		
		$('.menu_add').click(function(){
			var add_cont=$('#for_menu').html();
			$('#menubox').append(add_cont).end();
			$('#menubox').find('.items_del').click(function(){
				$(this).parent().parent().remove();  
			});
		});		
		
		$('.dis_add').click(function(){
			var add_cont=$('#dis_rate').html();
			$('#dis_box').append(add_cont).end();
			$('#dis_box').find('.items_del').click(function(){
				$(this).parent().parent().remove();  
			});
		});
		
		
		$("input[name=Dis_Agent_Type]").click(function(){
			var region_aget = "省%<input name=\"Agent_Rate[Province]\" value=\"0\" class=\"form_input\" size=\"3\" maxlength=\"10\" type=\"text\"> 市%<input name=\"Agent_Rate[City]\" value=\"0\" class=\"form_input\" size=\"3\" maxlength=\"10\" type=\"text\"> 县%<input name=\"Agent_Rate[County]\" value=\"0\" class=\"form_input\" size=\"3\" maxlength=\"10\" type=\"text\">";
			var common_aget = "%<input name=\"Agent_Rate\" value=\"0\" class=\"form_input\" size=\"3\" maxlength=\"10\" notnull=\"\" type=\"text\">";
			var type = $(this).attr('value');
			if(type == 0){
				$("#Agent_Rate_Input").html('');
			}else if(type == 1){
				$("#Agent_Rate_Input").html(common_aget);
			}else if(type == 2){
				$("#Agent_Rate_Input").html(region_aget);
			}else if(type == 3){
				$("#Agent_Rate_Input").html('');
			}else if(type == 4){
				$("#Agent_Rate_Input").html(region_aget);
			}
		});
		
		
		 $('#distribute_config_form input:submit').click(function(){
			
			if(global_obj.check_form($('#distribute_config_form *[notnull]'))){
				return false;
			};
			
		});
		
		$('.level_del').click(function(){
		
			var level_id=$(this).attr('level_id');
			$('.level_'+level_id).remove();
			$('input[name="submit_button"]').attr('disabled','disabled');
			$('#products_form').submit();
			
			
		});
		 
	},
	dis_title_tr:"<tr fieldtype=\"text\"><td>tr_index</td><td><input class=\"form_input\" value=\"\" name=\"Dis_Pro_Title[Name][]\" notnull=\"\" type=\"text\"></td><td><input class=\"form_input title_val\" value=\"0\" name=\"Dis_Pro_Title[Saleroom][]\"  type=\"text\"></td><td><input class=\"form_input\" value=\"0\" name=\"Dis_Pro_Title[Bonus][]\" type=\"text\"></td><td><a href=\"javascript:void(0);\" class=\"input_add\"><img src=\"/static/member/images/ico/del.gif\"></a></td></tr>",
	agent_province_check:function(obj,type){
		 
		 if (type == 'Province') {
			 
            var checked = $(obj).prop('checked');
            var province_id = $(obj).attr('value');

        }
	}
}