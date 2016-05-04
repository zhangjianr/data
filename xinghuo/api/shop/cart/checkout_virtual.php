<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/flow.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/smarty.php');

$base_url = base_url();
$shop_url = shop_url();
//设置smarty
$smarty->left_delimiter = "{{";
$smarty->right_delimiter = "}}";
$template_dir = $_SERVER["DOCUMENT_ROOT"].'/api/shop/html';
$smarty->template_dir = $template_dir;

if(isset($_GET["UsersID"])){
		$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}

$share_flag=0;
$signature="";

if(!empty($_SESSION[$UsersID."User_ID"])){
	$userexit = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."'");
	if(!$userexit){
		$_SESSION[$UsersID."User_ID"] = "";
	}
		
}

if(empty($_SESSION[$UsersID."User_ID"])){
	$_SESSION[$UsersID."HTTP_REFERER"]="/api/".$UsersID."/shop/cart/checkout_virtual/";
	header("location:/api/".$UsersID."/user/login/");
}

$User_ID  = $_SESSION[$UsersID."User_ID"];

if(!strpos($_SERVER['REQUEST_URI'],"mp.weixin.qq.com")){
	header("location:?wxref=mp.weixin.qq.com");
}

$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$rsPay = $DB->GetRs("users_payconfig","Shipping,Delivery_AddressEnabled,Delivery_Address","where Users_ID='".$UsersID."'");

$CartList = json_decode($_SESSION[$UsersID."Virtual"],true);

$total_price = 0;
if(count($CartList)>0){

	$City_Code = 0;
	$info = get_order_total_info($UsersID,$CartList,$rsConfig,0,0);
	$total_price = $info['total'];
	
	
}else{
	header("location:/api/".$UsersID."/shop/cart/");
	exit;
}

//获取优惠券
$coupon_info = get_useful_coupons($User_ID,$UsersID,$total_price);

/*商品信息汇总*/
$total_price = $total_price + $info['total_shipping_fee'];

/*分销所需信息*/
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
<title>订单确认页面</title>

<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/api/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/api/shop/skin/default/style.css?t=<?=time()?>' rel='stylesheet' type='text/css' />
<link href="/static/css/bootstrap.css" rel="stylesheet" />
<link rel="stylesheet" href="/static/css/font-awesome.css" />
<link rel="stylesheet" href="/static/api/shop/skin/default/css/tao_checkout.css" />
	
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/api/js/global.js?t=<?=time()?>'></script>
<script type='text/javascript' src='/static/api/shop/js/flow.js?t=<?=time()?>'></script>
<script type='text/javascript'>
	var base_url = '<?=$base_url?>';
	var Users_ID = '<?=$UsersID?>';
	
	$(document).ready(function(){
		flow_obj.checkout_init();
	});
</script>
</head>

<body >
	
<header class="bar bar-nav">
        <a href="javascript:history.go(-1)" class="pull-left"><img src="/static/api/shop/skin/default/images/black_arrow_left.png" /></a>
        <a href="/api/<?=$UsersID?>/shop/cart/" class="pull-right"><img src="/static/api/shop/skin/default/images/cart_two_points.png" /></a>
        <h1 class="title" id="page_title">提交订单 </h1>
</header>
    
    
    <div id="wrap">
    	<!-- 地址信息简述begin -->
        <div class="container">
        	<div class="row">
            
            </div>
        </div>
        
     	<div class="b15"></div>   
     
        <!-- 地址信息简述end-->
        <form id="checkout_form" action="/api/<?=$UsersID?>/shop/cart/">

        <input type="hidden" name="action" value="checkout" />	
       
         
        
        <!-- 产品列表begin-->
        <div id="product-list" class="container">
        
            <?php foreach($CartList as $Products_ID=>$Product_List):?>
            <?php
			    $condition = "where Users_ID = '".$UsersID."' and Products_ID =".$Products_ID;
				$rsProduct = $DB->getRs('shop_products','Products_Shipping,Products_Business,Products_Count,Products_IsShippingFree,Products_IsRecieve',$condition);
				$recieve = 0;
				if($rsProduct["Products_IsRecieve"]== 1){
					$recieve = 1;
				}
			?>
            <!-- 购物车中产品attr begin -->
            <input type="hidden" id="Products_Count_<?=$Products_ID?>" value="<?=$rsProduct['Products_Count']?>" />
            <input type="hidden" id="IsShippingFree_<?=$Products_ID?>" value="<?=$rsProduct['Products_IsShippingFree']?>" />
         
           <!-- 购物车中产品attr end -->                 
               	<?php foreach($Product_List as $key=>$product): ?>
                	
                    <div class="product">
              	
                <div class="simple-info row">
               	 <dl>
                   <dd class="col-xs-2"><img src="<?=$product['ImgPath']?>" class="thumb"/></dd>
                   <dd class="col-xs-6"><h5><?=$product['ProductsName']?></h5>
                  
                   	 <dl class="option">
					 <?php 
					 if(!empty($product["Property"])){
                     	foreach($product["Property"] as $Attr_ID=>$Attr){
							echo '<dd>'.$Attr['Name'].': '.$Attr['Value'].'</dd>';
						}
					 }
                     ?>
                     </dl>
                  
                   </dd>
                   <dd class="col-xs-2 price"> <span class="orange">&yen;<?=$product["ProductsPriceX"]?></span></dd>
              	   <div class="clearfix"></div>
                 </dl>
     			</div>
                
                <div class="row">
                <div class="qty_container">
                    	<span class="pull-left">&nbsp;&nbsp;购买数量</span>
                    	<div class="qty_selector pull-right">
                            <a class="btn  btn-default" name="minus">-</a>
                            <input id="qty_<?=$Products_ID?>_<?=$key?>" class="qty_value" type="text" value="<?=$product['Qty']?>" size="2"/>  
                            <a class="btn btn-default" name="add">+</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?php 
						$Sub_Weight = $product['ProductsWeight']*$product['Qty']; 
                    	$Sub_Total = $product["ProductsPriceX"]*$product['Qty']; 
                     	$Sub_Qty = $product['Qty']; 
					?>	
                    
					
            	</div>
                <!-- 产品小计开始 -->
                <div class="sub_total row">
                <p>
                   共&nbsp;<span class="red" id="subtotal_qty_<?=$Products_ID?>_<?=$key?>"><?=$product['Qty']?></span>&nbsp;件商品&nbsp;&nbsp;<span class="orange" id="subtotal_price_<?=$Products_ID?>_<?=$key?>">&yen;<?=$Sub_Total?>元</span>
                </p>
                </div>
                <!-- 产品小计结束 -->
            </div>
            	    	
            	<?php endforeach; ?>
                
			<?php endforeach;?>
            
        
        </div>
        
        <!-- 产品列表end-->
        
        <!-- 优惠券begin -->
      
		    <?php if($coupon_info['num'] >0):?>
        		<div class="b15"></div>
        		<div class="container">
        		<div class="row" id="coupon-list">
	        		<?=build_coupon_html($smarty,$coupon_info)?>
            	</div>
        		</div>
        	<?php endif; ?>
        <!-- 优惠券end -->
        
        <!-- 订单备注begin -->
        <div class="container">
         	 
			 <?php if($recieve==1):?>
            	<input type="hidden" name="Mobile" value="<?php echo !empty($_SESSION[$UsersID."User_Mobile"]) ? $_SESSION[$UsersID."User_Mobile"] : ''; ?>" />
            <?php else: ?>
             <div class="row order_extra_info" >
                <h5 class="t">接收短信手机</h5>
                   <input type="text"  id="Mobile_Input" name="Mobile" value="<?php echo !empty($_SESSION[$UsersID."User_Mobile"]) ? $_SESSION[$UsersID."User_Mobile"] : ''; ?>" pattern="[0-9]*" notnull />
             </div>
			<?php endif; ?>
            
            
            <div class="row order_extra_info">
            	<h5>订单备注信息</h5>
               <textarea name="Remark" placeholder="选填，填写您对本订单的特殊需求，如送货时间等"></textarea>
            </div>
            
            
         </div>
        <!-- 订单备注end -->
        <br/>
        <!--- 订单汇总信息begin -->
        <div class="container">
			
        	<div class="row" >
	        	<ul class="list-group">
				
					<?php if($info['man_flag']):?>
						<li class="list-group-item" >
							满多少减多少活动&nbsp;&nbsp;减去<span class="red" id="reduce_txt">&yen;<?=$info['reduce']?></span>
						</li>
					<?php endif;?>
					<li class="list-group-item">
					<p class="pull-right" id="order_total_info">合计<span id="total_price_txt" class="red">&yen;<?=$total_price?></span></p>
					<div class="clearfix"></div>
					</li>
				</ul>
				<input type="hidden" id="total_price" name="total_price" value="<?=$total_price?>"/>
               
                <div class="clearfix"></div>
            </div>
			<br/>
            <div class="row" id="btn-panel">
            	<button class="shop-btn btn-orange pull-right" id="submit-btn" type="button">提交</button>
            	<div class="clearfix"></div>
            </div>
        </div>
        <!-- 订单汇总信息end -->
        <!--edit by xpc-->
      	   <input type="hidden" id="coupon_value" value="0"/>
           <input type="hidden" name="virtual" id="virtual" value="1" />
	  	   <input type="hidden" name="recieve" value="<?php echo $recieve;?>" />
        <!--edit by xpc end-->
    	</form>
    </div>
    
    <!-- 属性选择内容begin -->
   
    <!-- 快递方式选择begin -->
    
    <!-- 快递公司选择end -->
   <?php require_once('../footer.php'); ?>
   

	</body>

</html>
