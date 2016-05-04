<?php 
    //分类
	$rsCategory = $DB->get("pifa_category","Category_Name,Category_ID,Category_Img","where Users_ID='".$UsersID."' and Category_IndexShow=1 and Category_ParentID=0 order by Category_Index asc limit 0,4");
	$category_list = $DB->toArray($rsCategory);
	//推荐产品
	$rsProducts = $DB->get("pifa_products","Products_Name,Products_ID,Products_JSON,Products_price_rule,Products_Sales,Products_unit","where Users_ID='".$UsersID."' and Products_IsRecommend=1 and Products_SoldOut=0 order by Products_CreateTime desc");
	$c_products = handle_product_list($DB->toArray($rsProducts));
	foreach($c_products as $k => $item){
		$c_products[$k]['link'] = $pifa_url.'product/'.$item['Products_ID'].'/';	
	}
?>
<?php require_once('header.php'); ?>
	<script>
		$(document).ready(function(){
			pifa_obj.index_init();
		});
	</script>
    <div class="banner" style="display:none;"><img src="/static/api/pifa/images/banner.jpg"/></div>
	<ul class="menu" style="display:none;">
	<?php if(!empty($category_list)){?>
	<?php foreach($category_list as $k => $v){?>
	    <li class="cat"><a href="/api/<?php echo $UsersID;?>/pifa/category/<?php echo $v['Category_ID'];?>/"><img src="<?php echo $v['Category_Img'];?>"/><?php echo $v['Category_Name'];?></a></li>
	<?php }?>
	<?php }?>
	</ul>
	<?php if(!empty($c_products)){?>
	<?php foreach($c_products as $key => $val){
	    $price_rule = json_decode($val['Products_price_rule'],true);	
	?>
	<div class="products" <?php if($key==0){?>style="margin-top:0;"<?php }?>>
	    <div class="thumb_box">
	        <img class="thumb" data-url="<?php echo $val['ImgPath'];?>" src="/static/js/plugin/lazyload/grey.gif"/>
		</div>
		<div class="title_box"><span><b style="font-size:16px;"><?php echo $val['Products_Name'];?></b></span><a href="/api/<?php echo $UsersID;?>/pifa/product/<?php echo $val['Products_ID'];?>/">立即购买</a></div>
        <?php if(!empty($price_rule)){?>
		<ul class="interval">
		<?php foreach($price_rule as $k => $v){
		    $str0 = $v[0];
			$str1 = $v[1].$val['Products_unit'];
			if($v[0] === 0) $str0 = '不限';
			if($v[1] === 0) $str1 = '不限';	
		?>
		    <li><span class="i_left"><?php echo $str0;?>-<?php echo $str1;?></span><span class="i_right"><b><?php echo $v[2];?></b>元/<?php echo $val['Products_unit'];?></span></li>
	    <?php }?>
		</ul>
		<?php }?>
	</div>
	<?php }?>
	<?php }?>
	<script type='text/javascript' src='/static/js/plugin/lazyload/jquery.scrollLoading.js'></script> 
	<script>
	    $(document).ready(function(){
			$("img").scrollLoading();
		});
	</script>
<?php require_once('footer.php'); ?>