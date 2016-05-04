<script>
    function load_page(page){
		$.ajax({
			type:'post',
			url:'?',
			data:{p:page},
			beforeSend:function(){
				$(".get_more").addClass('loading');
			},
			success:function(data){
				if(data['list'] != ''){
					var j = 0;
					$.each(data['list'],function(i){
						j++;
						v = data['list'][i];
						$htmltmp = '<div class="items">'+
								   '<div class="products_info">'+
									'<a href="'+v['link']+'"><img src="'+v['ImgPath']+'" /></a>'+
									'<h3 class="flex"><p class="flex_1">'+v['Products_Name']+' - 已售'+v['Products_Sales']+'笔</p><span>'+'&yen;'+v['Products_PriceX']+'</span></p>'+
								   '</div>'+
								  '</div>';
									
						$(".index_products").append($htmltmp);
					})
					if(data['totalpage'] == $(".get_more").attr('page')){
						$(".get_more").hide();
					}
				}else{
					$(".get_more").hide();
				}
			},
			complete:function(){
				$(".get_more").removeClass('loading');
			},
			dataType:'json',
		});
	}
	//加载第一页
	load_page($('.pullUp').attr('page'));
	$(".pullUp").click(function(){
		var page = parseInt($(this).attr('page'))+1;
		$(this).attr('page', page);
	    load_page(page);
	});
</script>
<!--懒加载--> 
