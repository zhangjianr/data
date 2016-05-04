<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
$base_url = base_url();
$shop_url = shop_url();

if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}
$share_flag=0;
$signature="";
if(!strpos($_SERVER['REQUEST_URI'],"mp.weixin.qq.com")){
	header("location:?wxref=mp.weixin.qq.com");
}
$rsConfig=$DB->GetRs("shop_config","Skin_ID,ShopName,Distribute_Type,CallEnable","where Users_ID='".$UsersID."'");

$owner = getOwner($DB,$UsersID);
if($owner['id'] != '0'){
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	$shop_url = $shop_url.$owner['id'].'/';
};

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<meta content="telephone=no" name="format-detection" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $rsConfig["ShopName"] ?>购物车</title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/api/css/global.css?t=<?php echo time();?>' rel='stylesheet' type='text/css' />
<link href='/static/api/shop/skin/default/css/style.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/api/js/global.js'></script>
<script type='text/javascript' src='/static/api/shop/js/shop.js'></script>
<script language="javascript">$(document).ready(shop_obj.page_init);</script>
<link href="/static/api/distribute/css/style.css" rel="stylesheet">
</head>

<body>
<div id="shop_page_contents">
  <div id="cover_layer"></div>
  <link href='/static/api/shop/skin/default/css/cart.css?t=<?php echo time();?>' rel='stylesheet' type='text/css' />
<?php if(empty($_SESSION[$UsersID."CartList"])){?>
  <div id="cart">
    <div class="empty"> <img src="/static/api/shop/skin/default/images/cart.png" /><br />
      购物车空的，赶快去逛逛吧！ </div>
  </div>
<?php }else{?>
  <div id="cart"> 
    <script language="javascript">$(document).ready(shop_obj.cart_init);</script>
    <form id="cart_form" action="/api/<?php echo $UsersID ?>/shop/cart/">
<?php $CartList=json_decode($_SESSION[$UsersID."CartList"],true);
$total=0;

foreach($CartList as $key=>$value){
	
	$i=0;
	foreach($value as $k=>$v){
		$total+=$v["Qty"]*$v["ProductsPriceX"];
		echo '<div class="item">
        <div class="del">
          <div CartID="'.$key.'_'.$i.'"><img src="/static/api/shop/skin/default/images/del.gif" /></div>
        </div>
        <div class="img"><a href="/api/'.$UsersID.'/shop/products/'.$key.'/"><img src="'.$v["ImgPath"].'" width="100" height="100"></a></div>
        <dl class="info">
          <dd class="name"><a href="/api/'.$UsersID.'/shop/products/'.$key.'/">'.$v["ProductsName"].'</a></dd>
          <dd class="price">价格:<span>￥'.$v["ProductsPriceX"].'</span></dd>';
		foreach($v["Property"] as $Attr_ID=>$Attr){
			
			echo '<dd>'.$Attr['Name'].': '.$Attr['Value'].'</dd>';
		}
        echo '<dd class="sub_total" ProId="'.$i.'" id="c_'.$i.'"> 数量:
            <input type="text" name="Qty[]" value="'.$v["Qty"].'" maxlength="3" pattern="[0-9]*" />
            <span>小计:<span class="fc_red"></span></span>
            <input type="hidden" name="CartID[]" value="'.$key.'_'.$i.'" />
          </dd>
        </dl>
        <div class="clear"></div>
      </div>';
		$i++;
	}
}?>
      <div class="total">商品总价:<span class="fc_red"></span></div>
      <div class="checkout">
        <input type="button" value="去结算" />
      </div>
      <input type="hidden" name="action" value="update" />
    </form>
  </div>
<?php }?>
</div>
<div id="footer_points"></div>
<?php require_once('../distribute_footer.php'); ?>
</body>
</html>