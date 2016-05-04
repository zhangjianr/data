// 后台砍价js

$(document).ready(function(){
	$("#search").click(function(){
		var param = {cate_id:$("#Category").val(),keyword:$("#keyword").val(),action:'get_product'};
		$.post(base_url+'/member/kanjia/ajax.php',param,function(data){
			
			$("#select_product").html(data);
		});
		
	});
	
	
	//选择您所要选的产品
	$("#select_product").change(function(){
		$("#Products_ID").attr("value",$(this).val());
		$("#Products_Name").attr("value",$(this).find("option:selected").text().split('---')[0]);
	});
	
	//时间区间选择js初始化
	   var date_str = new Date();
		
		$('#add_form input[name=AccTime_S], #add_form input[name=AccTime_E]').omCalendar({
			date: new Date(date_str.getFullYear(), date_str.getMonth(), date_str.getDate(), 00, 00, 00),
			
			showTime: true
		});

	
		$('#add_form').submit(function() {

			if (global_obj.check_form($('*[notnull]'))) {

				return false

			};

			$('#products_form .submit').attr('disabled', true);

			return true;

		});

	
});