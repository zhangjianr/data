<?php
//商品退货
//ini_set("display_errors","On");
error_reporting(E_ALL); 
 
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/smarty.php');

if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
	$_SESSION[$UsersID."is_kan"] = 1;
}else{
	echo '缺少必要的参数';
	exit;
}

/*分享页面初始化配置*/
$share_flag = 1;
$signature = '';


$base_url = base_url();
$shop_url = shop_url();
$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$OrderID = $_GET['OrderID'];
$rsOrder=$DB->GetRs("user_order","*","where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Order_ID='".$OrderID."'");
$Shipping=json_decode($rsOrder["Order_Shipping"],true);
$CartList=json_decode($rsOrder["Order_CartList"],true);
$amount = $fee = 0;

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<meta content="telephone=no" name="format-detection" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>申请退货 - 个人中心</title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/api/shop/skin/default/css/style.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/api/js/global.js'></script>
<script type='text/javascript' src='/static/api/shop/js/shop.js'></script>
<script language="javascript">$(document).ready(shop_obj.backup_init);$(document).ready(shop_obj.page_init);</script>
</head>

<body>
<div id="shop_page_contents">
  <div id="cover_layer"></div>
  <link href='/static/api/shop/skin/default/css/member.css?t=<?php echo time();?>' rel='stylesheet' type='text/css' />
  
  <div id="order_detail">
    <div class="item">
      <ul>
        <li>订单编号：<?php echo date("Ymd",$rsOrder["Order_CreateTime"]).$rsOrder["Order_ID"] ?></li>
        <li>订单时间: <?php echo date("Y-m-d H:i:s",$rsOrder["Order_CreateTime"]) ?></li>
     
      </ul>
    </div>
    <div class="item">
      <ul>
        <li>收货地址: <?php echo $rsOrder["Address_Province"].$rsOrder["Address_City"].$rsOrder["Address_Area"].$rsOrder["Address_Detailed"].'【'.$rsOrder["Address_Name"].'，'.$rsOrder["Address_Mobile"].'】' ?></li>
        <li>配送方式: <?php echo empty($Shipping)?"":$Shipping["Express"] ?><strong class="fc_red">
		<?php if(empty($Shipping["Price"])){?>
			免运费 
		<?php }else{
			$fee = $Shipping["Price"];
		?>
			 ￥<?php echo $Shipping["Price"];?>
		<?php }?>
		
		</strong></li>
        <?php echo empty($rsOrder["Order_ShippingID"])?"":"<li>快递单号:".$rsOrder["Order_ShippingID"]."</li> " ?>
      </ul>
    </div>
<?php if(!empty($rsOrder["Order_PaymentMethod"])){ ?>
    <div class="item">
      <ul>
        <li>支付方式: <?php echo $rsOrder["Order_PaymentMethod"] ?></li>
        <?php if($rsOrder["Order_PaymentMethod"]=="线下支付"){ ?><li>支付信息: <?php echo $rsOrder["Order_PaymentInfo"] ?><a href="/api/<?php echo $UsersID ?>/shop/cart/payment/<?php echo $rsOrder["Order_ID"] ?>/" class="red"><strong>修改支付信息</strong></a></li><?php }?>
      </ul>
    </div>
<?php }?>
    <div class="item">
	<form action="<?=$base_url?>api/<?=$UsersID?>/shop/member/" name="apply_form" id="apply_form" />	
         <input type="hidden" name="action" value="apply_backup" />
         <input type="hidden"  name="Order_ID" value="<?=$rsOrder["Order_ID"]?>" />
    <?php foreach($CartList as $key=>$value): ?>
    	<?php foreach($value as $k=>$v):?>
     
           <input type="hidden" name="Products_Name[<?=$key?>]" value="<?=$v["ProductsName"]?>" />
           <input type="hidden" name="Products_Price[<?=$key?>]" value="<?=$v["ProductsPriceX"]?>" />
  		   <input type="hidden" name="Products_Image[<?=$key?>]" value="<?=$v["ImgPath"]?>" />      
           <div class="pro" >
	    
        <div class="img"><a href="/api/<?=$UsersID?>/shop/products/<?=$key?>/?wxref=mp.weixin.qq.com"><img src="<?=$v["ImgPath"]?>" height="100" width="100"></a></div>
			<dl class="info">
				<dd class="name"><a href="/api/<?=$UsersID?>/shop/products/<?=$key?>/?wxref=mp.weixin.qq.com"><?=$v["ProductsName"]?></a></dd>
				<dd>价格:￥<?=$v["ProductsPriceX"]?>×<?=$v["Qty"]?>=￥<?=$v["ProductsPriceX"]*$v["Qty"]?></dd>
            	<dd> <input type="checkbox" name="Products_ID[]" value="<?=$key?>"/></dd>	    
            </dl>
			
           
            <div class="clear"></div>
        </div>
        
      		<div class="backup_reason" id="backup_reason_<?=$key?>">
        	<dl>
        <dd>退货数量<font class="fc_red">*</font>
          <input class="back_num"  type="text" qty="<?=$v['Qty']?>" name="backup_num[<?=$key?>]" size="3" notnull />
          
        </dd>
        
        <dd>退货原因<br>
          <textarea name="reason[<?=$key?>]" value=""  class="score_textarea back_attr_<?=$key?>" notnull ></textarea>
        </dd>
    
      </dl>
        </div>
    
    	<?php endforeach; ?>
    <?php endforeach; ?>

     	</form>
    <div class="backup"><a id="apply_backup" href="javascript:void()">申请退货</a></div>
   
  </div>
  </div>
  
</div>
<?php
 	require_once('../distribute_footer.php');
 ?>
</body>
</html>



