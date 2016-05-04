<?php
$Dwidth = array('640','157','157','157','157','640','640','640','640');
$DHeight = array('309','137','137','137','137','160','160','160','160');
$Home_Json=json_decode($rsSkin['Home_Json'],true);
for($no=1;$no<=9;$no++){
	$json[$no-1]=array(
		"ContentsType"=>$no==1?"1":"0",
		"Title"=>$no==1?json_encode($Home_Json[$no-1]['Title']):$Home_Json[$no-1]['Title'],
		"ImgPath"=>$no==1?json_encode($Home_Json[$no-1]['ImgPath']):$Home_Json[$no-1]['ImgPath'],
		"Url"=>$no==1?json_encode($Home_Json[$no-1]['Url']):$Home_Json[$no-1]['Url'],
		"Postion"=>"t0".$no,
		"Width"=>$Dwidth[$no-1],
		"Height"=>$DHeight[$no-1],
		"NeedLink"=>"1"
	);
}

$rsCategory = $DB->get("shop_category","Category_Name,Category_ID,Category_Img","where Users_ID='".$UsersID."' and Category_IndexShow=1 and Category_ParentID=0 order by Category_Index asc ");
$category_list = $DB->toArray($rsCategory);

//获取新品
$rsNewProducts = $DB->get("shop_products","Products_Name,Products_ID,Products_JSON,Products_PriceX,Products_Sales","where Users_ID='".$UsersID."' and Products_IsNew=1 and Products_SoldOut=0 order by Products_CreateTime asc");
$new_products = handle_product_list($DB->toArray($rsNewProducts));

$ANNOUNCE = $rsConfig["ShopAnnounce"];

function get_products_bycate($db,$catid){
	$CategoryList = array();
	$CategoryList[] = $catid;
	$db->Get("shop_category","*","where Category_ParentID=".$catid);
	while($v=$db->fetch_assoc()){
		$CategoryList[]=$v["Category_ID"];
	}
	$new_products = array();
	$rsNewProducts = $db->get("shop_products","Products_Name,Products_ID,Products_JSON,Products_PriceX,Products_PriceY,Products_Count,Products_Sales","where Products_SoldOut=0 and Products_IsRecommend=1 and Products_Category in(".implode(",",$CategoryList).") order by Products_CreateTime asc");
	$new_products = handle_product_list($db->toArray($rsNewProducts));
	return $new_products;
}
?>
<?php require_once('top.php');?> 
<body>
<?php
ad($UsersID, 1, 1);
?>
<div id="shop_page_contents">
 <div id="cover_layer"></div>
 <link href='/static/api/shop/skin/<?php echo $rsConfig['Skin_ID'];?>/page.css?t=<?php echo time();?>' rel='stylesheet' type='text/css' />
 <link href='/static/api/shop/skin/<?php echo $rsConfig['Skin_ID'];?>/page_media.css' rel='stylesheet' type='text/css' />
 <link href='/static/js/plugin/flexslider/flexslider.css' rel='stylesheet' type='text/css' />
 <script type='text/javascript' src='/static/js/plugin/flexslider/flexslider.js'></script>
 <script type='text/javascript' src='/static/api/shop/js/index.js'></script>
 <script language="javascript">
  var shop_skin_data=<?php echo json_encode($json) ?>;
  $(document).ready(index_obj.index_init);
 </script>
    <div id="shop_skin_index">
		<div class="shop_skin_index_list banner" rel="edit-t01">
			<div class="img"></div>
		</div>
		<div class="search">
			<form action="/api/shop/search.php" method="get">
				<input type="text" name="kw" class="input" value="" placeholder="输入商品名称..." />
				<input type="hidden" name="UsersID" value="<?php echo $UsersID;?>" />
				<input type="hidden" name="OwnerID" value="<?php echo $owner['id'] != '0' ? $owner['id'] : '';?>" />
				<input type="submit" class="submit" value=" " />
			</form>
		</div>
		<div class="box">
			<div class="shop_skin_index_list rol bor" rel="edit-t02">
				<div class="img"></div>
				<div class="text"></div>
			</div>
			<div class="shop_skin_index_list rol bor" rel="edit-t03">
				<div class="img"></div>
				<div class="text"></div>
			</div>
			<div class="shop_skin_index_list rol bor" rel="edit-t04">
				<div class="img"></div>
				<div class="text"></div>
			</div>
			<div class="shop_skin_index_list rol" rel="edit-t05">
				<div class="img"></div>
				<div class="text"></div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="shop_skin_index_list ad" rel="edit-t06">
		    <div class="img"></div>
		</div>
		<div class="shop_skin_index_list ad" rel="edit-t07">
			<div class="img"></div>
		</div>
		<!--产品列表开始-->
		 <?php foreach($category_list as $t){?>
		 <div class="index_products">
		  <h2><span><a href="<?php echo $shop_url;?>category/<?php echo $t["Category_ID"];?>/">更多&gt;&gt;</a></span><?php echo $t["Category_Name"]?></h2>
		  <ul>
		   <?php
		   $recommend_products = get_products_bycate($DB,$t["Category_ID"]);
		   $i=0;
		   foreach($recommend_products as $key=>$item):
		   $i++;
		   ?>
		   <li>
			<div <?php if($i%2==0) echo 'class="border_r"';?>>
			 <p><a href="<?=$shop_url?>products/<?=$item['Products_ID']?>/"><img class="product-image" width="80%" data-url="<?=$item['ImgPath']?>" src="/static/js/plugin/lazyload/grey.gif"/></a><p>
			 <p class="products_title"><?=$item['Products_Name']?></p>
			 <p class="products_price">&nbsp;&yen;<?=$item['Products_PriceX']?>&nbsp;<i><?=$item['Products_PriceY']?></i></p>
			</div>
		   </li>
		   <?php if($i%2==0){?>
		   <div class="clear"></div>   
		   <?php }?>
		   <?php endforeach;?>
		   <div class="clear"></div> 
		   </ul>
		 </div>
		 <?php }?>			
		<!--产品列表结束-->
		<div class="shop_skin_index_list ad" rel="edit-t08">
			<div class="img"></div>
		</div>
		<div class="shop_skin_index_list ad" rel="edit-t09">
			<div class="img"></div>
		</div>
	</div>
</div>
<?php require_once('distribute_footer.php'); ?>
<!--懒加载--> 
<script type='text/javascript' src='/static/js/plugin/lazyload/jquery.scrollLoading.js'></script> 
<script language="javascript">
	$("img").scrollLoading();
</script>
</body>
</html>