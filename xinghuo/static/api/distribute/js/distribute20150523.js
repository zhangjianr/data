// JavaScript Document

var distribute_obj = {
	
	init:function(){
        //展开分组列表
        $("button.group-count").click(function() {
			
            var status = $(this).attr("status");
          
		    if (status == 'close') {
              
                $(this).parent().next().css({
                    display: 'block'
                });
                $(this).attr("status", "open");
            } else {
               
                $(this).parent().next().css({
                    display: 'none'
                });
                $(this).attr("status", "close");
            }


        });
		
		//展开二维码
        $("#open_qrcode_title").click(function() {
			
            var status = $(this).attr("status");
            
			if (status == 'close') {
                $(this).find("span.fa").removeClass("fa-chevron-up").addClass("fa-chevron-down");
                $(this).next().css({
                    display: 'block'
                });
                $(this).attr("status", "open");
            } else {
                $(this).find("span.fa").removeClass("fa-chevron-down").addClass("fa-chevron-up");
                $(this).next().css({
                    display: 'none'
                });
                $(this).attr("status", "close");
            }


        });

	
	},
	
	withdraw_page:function(){
		//提现申请
	
        $("#btn-withdraw").click(function() {

           
			if($("input[name='money']").val().length == 0){
			   
			   $("#withdraw-money").after("<label id=\"withdraw_tip\" class=\"fc_red\">不可为空</label>");
                return false;
			}
			
            var balance = parseFloat($("#balance").val());
            var money = parseFloat($("input[name='money']").val());
			withdraw_limit = parseFloat(withdraw_limit);
			
			
			if(money <= 0){
				$("#withdraw-money").after("<label id=\"withdraw_tip\" class=\"fc_red\">必须大于0</label>");
                return false;
			}
			
			if(money < withdraw_limit){
				$("#withdraw-money").after("<label id=\"withdraw_tip\" class=\"fc_red\">最小为"+withdraw_limit+"元</label>");
                return false;
			}
			
            if (balance < money) {
                
                $("#withdraw-money").after("<label id=\"withdraw_tip\" class=\"fc_red\">余额不足</label>");
                return false;
            }

			
            var url = $("#withdraw-form").attr("action");
            var param = $("#withdraw-form").serialize();

            $.post(url, param, function(data) {
                if (data.status == 1) {
                    global_obj.win_alert('提现申请提交成功', function() {
                        window.location.reload();
                        window.location.href = base_url+'api/'+UsersID+'/shop/distribute/';
                    });
                }
            }, 'json');

        });
		
		/*添加银行卡表单验证*/
		$("#bank_card_form").validate({
				rules: {
					Card_Name: {required:true},
					Card_Bank: {required:true},
					Card_No: {required:true},
				},
            
				onfocusout: function(element) {
					this.element(element);
				}
			});
		
		/*清除提示*/
	    $("#withdraw-money").keydown(function() {
            $("#withdraw_tip").remove();
            $("#withdraw-money").css({
                border: '0px solid red'
            });
        });
		
		/*显示添加银行卡*/
		$("#add-card").click(function(){
			
			
			$("#add_card_panel").css("display","block");
			
		});
		
		$("#btn-addcard").click(function(){
			var url = $("#bank_card_form").attr("action");
            var param = $("#bank_card_form").serialize();
          	if($("#bank_card_form").valid()){

            	$.post(url, param, function(data) {
                	if (data.status == 1) {
                	global_obj.win_alert('添加银行卡成功', function() {
                       window.location.href = base_url + 'api/' + UsersID + '/shop/distribute/';
				
						});
                 	}
					
            	 }, 'json');

           }else{
           	return false;
           }
		
		});

			
	},
	
	apply_distribute:function(){
	
	   $("#join-distribute-form").validate({
            rules: {
                Real_Name: {required:true},
				User_Mobile:{required:true},
            },
            
            onfocusout: function(element) {
                this.element(element);
            }
        });
		
        /*成为分销商申请*/
        $("#submit-btn").click(function() {

            var url = $("#join-distribute-form").attr("action");
            var param = $("#join-distribute-form").serialize();
          	if($("#join-distribute-form").valid()){

            	$.post(url, param, function(data) {
                	if (data.status == 1) {
                	global_obj.win_alert('您已经成为分销商', function() {
                       window.location.href = base_url + 'api/' + UsersID + '/shop/distribute/';
                    });
					
                 	}
            	 }, 'json');

           }else{
           	return false;
           }
           
        });
	},
	bank_card_manage:function(){
		$("a.remove-card").click(function(){
			var url = base_url+'api/'+UsersID+'/shop/distribute/ajax/';
			var param = {action:'delete_bank_card',card_id:$(this).attr("data-card-id")};
			
			$.post(url, param, function(data) {
                	if (data.status == 1) {
                	global_obj.win_alert('您已成功删除此银行卡', function() {
                       window.location.href = base_url + 'api/' + UsersID + '/shop/distribute/bankcards/';
                    });
                 	}
            }, 'json');
			
		});
	},
	withdraw_record_init:function(){
		//展开提现记录
        $("a.record-title").click(function() {
            var status = $(this).attr("status");
			
            if (status == 'close') {
                $(this).find("span.fa").removeClass("fa-chevron-up").addClass("fa-chevron-down");
                
				$(this).next().css({
                    display: 'block'
                });
				
                $(this).attr("status", "open");
            } else {
                $(this).find("span.fa").removeClass("fa-chevron-down").addClass("fa-chevron-up");
                $(this).next().css({
                    display: 'none'
                });
                $(this).attr("status", "close");
            }


        });
	}


}