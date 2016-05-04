<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/shipping.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/flow.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
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

if(isset($_GET["needcart"])){	
	$needcart=$_GET["needcart"];
}else{
	$needcart = 1;
}


$share_flag=0;
$signature="";

//用户已登录
if(!empty($_SESSION[$UsersID."User_ID"])){
	$userexit = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."'");
	if(!$userexit){
		$_SESSION[$UsersID."User_ID"] = "";
	}	
}
//用户没有登录
if(empty($_SESSION[$UsersID."User_ID"])){
	$_SESSION[$UsersID."HTTP_REFERER"]="/api/".$UsersID."/shop/cart/checkout/".$needcart."/";
	header("location:/api/".$UsersID."/user/login/");
}

$_SESSION[$UsersID."HTTP_REFERER"]="/api/".$UsersID."/shop/cart/checkout/".$needcart."/";

$User_ID  = $_SESSION[$UsersID."User_ID"];

if(!strpos($_SERVER['REQUEST_URI'],"mp.weixin.qq.com")){
	header("location:?wxref=mp.weixin.qq.com");
}

$rsConfig =  $DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$rsPay = $DB->GetRs("users_payconfig","Shipping,Delivery_AddressEnabled,Delivery_Address","where Users_ID='".$UsersID."'");

//获得用户地址
$Address_ID = !empty($_GET['AddressID'])?$_GET['AddressID']:0;
if($Address_ID == 0){
	$condition = "Where Users_ID = '".$UsersID."' And User_ID = ".$User_ID." And Address_Is_Default = 1";
}else{
	$condition = "Where Users_ID = '".$UsersID."' And User_ID = ".$User_ID." And Address_ID =".$Address_ID;
}

$rsAddress = $DB->GetRs('user_address','*',$condition);
$area_json = read_file($_SERVER["DOCUMENT_ROOT"].'/data/area.js');
$area_array = json_decode($area_json,TRUE);
$province_list = $area_array[0];

if($rsAddress){
	$Province = $province_list[$rsAddress['Address_Province']];
	$City = $area_array['0,'.$rsAddress['Address_Province']][$rsAddress['Address_City']];
	$Area = $area_array['0,'.$rsAddress['Address_Province'].','.$rsAddress['Address_City']][$rsAddress['Address_Area']];
}else{
	$_SESSION[$UsersID."From_Checkout"] = 1;
	header("location:/api/".$UsersID."/user/my/address/edit/");
}



if(empty($rsConfig['Default_Business'])||empty($rsConfig['Default_Shipping'])){
  echo '<p style="text-align:center;color:red;font-size:30px;"><br/><br/>基本运费信息没有设置，请联系管理员</p>';
  exit();
}

//获取前台可用的快递公司
$shipping_company_dropdown = array();
$shipping_company_dropdown = get_front_shiping_company_dropdown($UsersID,$rsConfig);
$Business = 'express';

//获取产品列表
$cart_key = get_cart_key($UsersID,0,$needcart);
$CartList=json_decode($_SESSION[$cart_key],true);

$Shipping_ID = !empty($rsConfig['Default_Shipping'])?$rsConfig['Default_Shipping']:0;

$Shipping_Name  = '';
if($Shipping_ID != 0){
	$Shipping_Name = $shipping_company_dropdown[$Shipping_ID];
}

$total_price =0;
$toal_shipping_fee = 0;

if(count($CartList)>0){
	
	if($rsAddress&&$rsConfig['NeedShipping'] == 1){
		$City_Code = $rsAddress['Address_City'];
	}else{
		$City_Code = 0;
	}

	$info = get_order_total_info($UsersID,$CartList,$rsConfig,$Shipping_ID,$City_Code);
	$total_price = $info['total'];
	$total_shipping_fee = $info['total_shipping_fee'];
}else{
	header("location:/api/".$UsersID."/shop/cart/");
	exit;
}


//获取优惠券
$coupon_info = get_useful_coupons($User_ID,$UsersID,$total_price);

/*商品信息汇总*/
$CartList = json_decode($_SESSION[$cart_key],true);
$total_price = $total_price + $info['total_shipping_fee'];

$owner = getOwner($DB,$UsersID);
if($owner['id'] != '0'){
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	$shop_url = $shop_url.$owner['id'].'/';
};


$Default_Business = $rsConfig['Default_Business'];	
$Default_Shipping = $rsConfig['Default_Shipping'];
$Business_List = array('express'=>'快递','common'=>'平邮');	

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
 <link href="/static/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/static/css/font-awesome.css" />
<link rel="stylesheet" href="/static/api/shop/skin/default/css/tao_checkout.css" />
	
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/js/bootstrap.min.js'></script>
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
        <?php if($rsConfig['NeedShipping'] == 1):?>
        	<div id="receiver-info" class="row">
           		<dl>
                	<dd class="col-xs-1"><a href="javascript:history.go(-1)"><img src="/static/api/shop/skin/default/images/map_maker.png" /></a></dd>
                 <dd class="col-xs-9"><p>
				 <?php if($rsAddress):?>
                    收货人:<?=$rsAddress['Address_Name']?>&nbsp;&nbsp;&nbsp;&nbsp;<?=$rsAddress['Address_Mobile']?><br/>
						  所在地区:<?=$Province.'&nbsp;&nbsp;'.$City.'&nbsp;&nbsp;'.$Area?><br/>
						  详细地址:<?=$rsAddress['Address_Detailed']?>
                  	<input type="hidden" id="City_Code" value="<?=$rsAddress['Address_City']?>"/>
				  <?php else: ?>
                      请去添加收货地址<br/> 
                  <?php endif; ?>
                  
                  </p></dd>        
                    <dd class="col-xs-1"><a href="/api/<?=$UsersID?>/user/my/address/<?=$rsAddress['Address_ID'] ?>/"><img src="/static/api/shop/skin/default/images/arrow_right.png"/></a></dd>
                </dl>
            </div>
    	<?php else:?>  
             <p class="row" style="text-align:center;"><br/>此商城无需物流</p>
		<?php endif ?>
        </div>
     	<div class="b15"></div>   
     
        <!-- 地址信息简述end-->
        <form id="checkout_form" action="<?=$base_url?>api/<?=$UsersID?>/shop/cart/">
        <input type="hidden" name="AddressID" value="<?=$rsAddress['Address_ID'] ?>" />	
        <input type="hidden" name="action" value="checkout" />	
        <input type="hidden" name="Need_Shipping" value="<?=$rsConfig['NeedShipping']?>" />	
        
        <!-- 产品列表begin-->
        <div id="product-list" class="container">
        
            <?php foreach($CartList as $Products_ID=>$Product_List):?>
            <?php
			    $condition = "where Users_ID = '".$UsersID."' and Products_ID =".$Products_ID;
				$rsProduct = $DB->getRs('shop_products','Products_Shipping,Products_Business,Products_Count,Products_IsShippingFree',$condition);
			
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
                   <dd class="col-xs-6"><h4><?=$product['ProductsName']?></h4>
                    
                    
                    <?php if($product['IsShippingFree'] == 1):?>
                    	<?php if($product['Shipping_Free_Company'] == 0):?>
                        <span class="red">免运费</span>	
                        <?php else:?>
                        <span class="red">
                        	<?=$shipping_company_dropdown[$product['Shipping_Free_Company']]?>免运费
                        </span>
						<?php endif; ?>
                    	
					<?php endif;?>   
                                 
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
        
        <!-- 配送方式begin-->
        <div class="container">
        	<div class="row" id="shipping_method">
             &nbsp;&nbsp;配送方式 
               <a href="javascript:void(0)" class="pull-right"><img width="25px" height="25px" src="/static/api/shop/skin/default/images/arrow_right.png"/></a>
               
             
                  <a  class="pull-right">&nbsp;&nbsp;
                  <span id="shipping_name"><?=$Shipping_Name?></span>&nbsp;&nbsp;
                  
                        <span id="total_shipping_fee_txt">
							<?php if($total_shipping_fee  == 0 ):?>
								免运费
                        	<?php else:?>
                            	<?=$total_shipping_fee?>元
							<?php endif; ?>
                        </span>&nbsp;&nbsp;</a>
                        
            </div>
        </div>
        <!-- 配送方式end -->
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
					
					 <?php if(!empty($rsConfig['Integral_Convert'])): ?>	
						<li  class="list-group-item">
						   此次购物可获得<span id="total_integral" class="red"><?=$total_price/abs($rsConfig['Integral_Convert'])?></span>个积分
						</li>
					 <?php endif;?>
					  
					<li class="list-group-item">
					<p class="pull-right" id="order_total_info">合计<span id="total_price_txt" class="red">&yen;<?=$total_price?></span></p>
					<div class="clearfix"></div>
					</li>
				</ul>
                <input type="hidden" id="virtual" value="0"/>
                <input type="hidden" id="coupon_value" value="0"/>
				<input type="hidden" id="total_price" name="total_price" value="<?=$total_price?>"/>
                <input type="hidden" name="Order_Shipping[Express]"  id="Order_Shipping_Express" value="<?=$Shipping_Name?>"/>
                <input type="hidden" id="total_shipping_fee" name="Order_Shipping[Price]" value="<?=$total_shipping_fee?>"/>
				<input type="hidden" id="needcart" name="needcart" value="<?=$needcart?>"/>
                <div class="clearfix"></div>
            </div>
			<br/>
            <div class="row" id="btn-panel">
            	<button type="button" class="shop-btn btn-orange pull-right"   id="submit-btn">提交</button>
                
            	<div class="clearfix"></div>
            </div>
        </div>
        <!-- 订单汇总信息end -->
    	
        
        <!-- 快递公司选择begin -->
    	<div class="container">
       		<div class="row">

		<div class="modal"  role="dialog" id="shipping-modal">
  <div class="modal-dialog modal-sm">
    
    	<div class="modal-content">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h5 class="modal-title" id="mySmallModalLabel">配送方式</h5>
        </div>
        <div class="modal-body">
            <dl id="shipping-company-list"> 
             <?php foreach($shipping_company_dropdown as  $key=>$item):?>
              <dd><div class="pull-left shipping-company-name"><?=$item?></div>
         	 	<div class="pull-right"><input  type="radio" shipping_name="<?=$item?>" <?=($Default_Shipping == $key)?'checked':'';?>  name="Shiping_ID" value="<?=$key?>" /></div>
             	<div class="clearfix"></div>
              </dd>
			 
   	          <?php endforeach;?>
            </dl>
            <div class="clearfix"></div>
        </div>
        <div class="modal-footer">
       	<a class="pull-left modal-btn" id="confirm_shipping_btn">确定</a>
        <a class="pull-right modal-btn" id="cancel_shipping_btn">取消</a>
        </div>
      </div>
  </div>
</div>
    
            </div>
        </div>
        <!-- 快递公司选择end -->
        <input type="hidden"  id="trigger_cart_id" value=""/> 
        </form>
    </div>
    
   <?php require_once('../footer.php'); ?>
   

	</body>

</html>
