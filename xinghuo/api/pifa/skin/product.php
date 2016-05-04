<?php require_once('header.php'); ?>
<link href="/static/css/bootstrap.min.css" rel="stylesheet">
<script type='text/javascript' src='/static/js/bootstrap.min.js'></script>
<link rel="stylesheet" href="/static/api/shop/skin/default/css/tao_checkout.css" />

<link href="/static/js/plugin/photoswipe/photoswipe.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/static/js/plugin/touchslider/touchslider.min.js"></script> 
<script type='text/javascript' src='/static/js/plugin/photoswipe/klass.min.js'></script> 
<script type='text/javascript' src='/static/js/plugin/photoswipe/photoswipe.jquery-3.0.5.min.js'></script> 
<script>
    $(document).ready(function(){
		pifa_obj.product_init();
	});
</script>
<style>
    body{background:#f7f7f7;}
	h2{font-size:16px;margin:0;}
</style>
    <div class="top">
	    <a class="go_back" href="javascript:;" onclick="history.go(-1);"></a>
		<span>商品详情</span>
	</div>
	<!-- 产品图片开始 -->
    <div id="detail_images">
		<div class="pro_img">
			<div class="touchslider">
				<div class="img">
					<div class="touchslider-viewport">
						<div class="list">
						    <?php if(isset($JSON["ImgPath"])){
									foreach($JSON["ImgPath"] as $key=>$value){
										echo '<div class="touchslider-item"><a href="'.$value.'" rel="'.$value.'"><img data-url="'.$value.'" src="/static/js/plugin/lazyload/grey.gif" /></a></div>';
									}
							}?>
						</div>
					</div>
				</div>
				<div class="touchslider-nav">
				    <?php
						if(isset($JSON["ImgPath"])){
							foreach($JSON["ImgPath"] as $key=>$value){
								echo $key==0 ? '<a class="touchslider-nav-item touchslider-nav-item-current"></a>' : '<a class="touchslider-nav-item"></a>';
							}
					}?>
				</div>
			</div>
		</div>
    </div>
    <!-- 产品图片结束 -->
    <div class="product_title"><b style="font-size:16px;"><?php echo $rsProducts['Products_Name'];?></b></div>
    <div class="product_other_info">
	    <span class="product_f1">已售<?php echo $rsProducts['Products_Sales'];?>件</span>
		<?php if($rsProducts["Products_IsShippingFree"] == 1){?>
		<span class="product_fm">卖家包邮</span>
		<?php }?>
		<span class="product_fr"><a href="javascript:;" id="seller-info"><b>...</b></a></span>
	</div>
	<div class="product_c">
	    <span><img src="/static/api/pifa/images/star_<?php echo $comment_aggregate['points'];?>.png"/>&nbsp;<?php echo number_format($comment_aggregate['points'],1,'.','');?></span><a href="<?=$base_url?>api/<?=$UsersID?>/pifa/commit/<?=$ProductsID?>/">查看全部评论>></a>
	</div>
	<div class="buynow">
	    <?php if(!empty($price_rule)){?>
	    <ul class="interval">
		<?php foreach($price_rule as $k => $v){
		    $str0 = $v[0];
			$str1 = $v[1].$rsProducts['Products_unit'];
			if($v[0] === 0) $str0 = '不限';
			if($v[1] === 0) $str1 = '不限';	
		?>
		    <li><span class="i_left"><?php echo $str0;?>-<?php echo $str1;?></span><span class="i_right"><b><?php echo $v[2];?></b>元/<?php echo $rsProducts['Products_unit'];?></span></li>
	    <?php }?>
		</ul>
		<?php }?>
		<a class="go_buynow" href="/api/<?php echo $UsersID;?>/pifa/order/<?php echo $rsProducts['Products_ID'];?>/">立即购买</a>
	</div>
	<div class="goods_info">
	    <h2>产品详情</h2>
		<div class="goods_info_body">
		<?php echo $rsProducts['Products_Description'];?>
		</div>
	</div>
	<div class="goods_info">
	    <h2>批发须知</h2>
		<div class="goods_info_body">
		<?php echo $rsProducts['Products_BriefDescription'];?>
		</div>
	</div>
	<div class="goods_ca">
	    <h2>看过此商品的人还看了</h2>
		<ul>
		<?php foreach($relatedProducts  as $key=>$item){?>
		<li> 
			<a href="<?php echo $pifa_url;?>product/<?=$item['Products_ID']?>/">
				<p class="img"><img src="<?=$base_url?><?=$item['ImgPath']?>" /></p>
				<p class="name">
					<?=$item['Products_Name']?>
				</p>
				<p class="price">￥<?php echo number_format($item['Products_PriceX'],2,'.','');?></p>
			</a> 
		</li>
		<?php }?>
		</ul>
	</div>
	<script type='text/javascript' src='/static/js/plugin/lazyload/jquery.scrollLoading.js'></script> 
	<script>
	    $(document).ready(function(){
			$("img").scrollLoading();
		});
	</script>
	<div class="container">
		<div class="row">
			<div class="modal"  role="dialog" id="seller-modal">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h5 class="modal-title" id="mySmallModalLabel">商家信息</h5>
						</div>
						<div class="modal-body">
							<ul class="seller_info">
							    <li>商家电话：<a href="tel:<?php echo $rsConfig['CallPhoneNumber'];?>"><?php echo $rsConfig['CallPhoneNumber'];?></a></li>
							</ul>
						</div>
					</div>
				</div>
				</div>
			</div>
	</div>
<?php require_once('footer.php'); ?>