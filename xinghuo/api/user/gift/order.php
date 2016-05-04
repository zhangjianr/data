<?php 
//ini_set("display_errors","On");
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

$is_login=1;
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$User_ID = $_SESSION[$UsersID."User_ID"];

if(isset($_GET["GiftID"])){	
	$GiftID=$_GET["GiftID"];
}else{
	echo '缺少必要的参数';
	exit;
}

$rsGift = $DB->GetRs("user_gift","*","where Users_ID='".$UsersID."' and Gift_ID=".$GiftID);
if(!$rsGift){
	echo "该礼品不存在";
	exit;
}
if($rsGift["Gift_Shipping"]){
	$rsConfig = $DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
	$rsPay = $DB->GetRs("users_payconfig","Shipping,Delivery_AddressEnabled,Delivery_Address","where Users_ID='".$UsersID."'");
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
		$_SESSION[$UsersID."HTTP_REFERER"] = '/api/'.$UsersID.'/user/gift/order/'.$GiftID.'/';
		$_SESSION[$UsersID."From_Checkout"] = 1;
		header("location:/api/".$UsersID."/user/my/address/edit/");
	}
	
	if(empty($rsConfig['Default_Business'])||empty($rsConfig['Default_Shipping'])){
	  echo '<p style="text-align:center;color:red;font-size:30px;"><br/><br/>基本运费信息没有设置，请联系管理员</p>';
	  exit();
	}
	
	//获取前台可用的快递公司
	$shipping_company_dropdown = get_front_shiping_company_dropdown($UsersID,$rsConfig);
	$Business = 'express';
	//获取产品列表
	
	$Shipping_ID = !empty($rsConfig['Default_Shipping'])?$rsConfig['Default_Shipping']:0;
	
	$Shipping_Name  = '';
	if($Shipping_ID != 0){
		$Shipping_Name = $shipping_company_dropdown[$Shipping_ID];
	}
	
	if($rsAddress){
		$City_Code = $rsAddress['Address_City'];
	}else{
		$City_Code = 0;
	}
	
	//get_order_total_info
	if($rsGift["Gift_FreeShipping"] == 0){
		$info = get_gift_shipping_fee($UsersID,$rsConfig,$Shipping_ID,$City_Code);
		$total_shipping_fee = $info['total_shipping_fee'];
	}else{
		$total_shipping_fee = 0;
	}
	
	$Default_Business = $rsConfig['Default_Business'];	
	$Default_Shipping = $rsConfig['Default_Shipping'];
	$Business_List = array('express'=>'快递','common'=>'平邮');
}
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
<link href="/static/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/static/css/font-awesome.css" />
<link rel="stylesheet" href="/static/api/shop/skin/default/css/tao_checkout.css" />
<link href='/static/api/css/user.css?t=<?php echo time();?>' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/js/bootstrap.min.js'></script>
<script type='text/javascript' src='/static/api/js/global.js?t=<?=time()?>'></script>
<script type='text/javascript' src='/static/api/js/user.js?t=<?=time()?>'></script>
<script type='text/javascript'>
	var base_url = '<?=$base_url?>';
	var Users_ID = '<?=$UsersID?>';
	var FreeShipping = <?php echo $rsGift["Gift_FreeShipping"];?>;
	$(document).ready(function(){
		user_obj.gift_checkout_init();
	});
</script>
</head>

<body style="background:#FFF">	
<header class="bar bar-nav">
        <a href="javascript:history.go(-1)" class="pull-left"><img src="/static/api/shop/skin/default/images/black_arrow_left.png" /></a>
        <h1 class="title" id="page_title">积分兑换 </h1>
</header>
    
    
    <div id="wrap">
    	<!-- 地址信息简述begin -->
        <div class="container">
        <?php if($rsGift['Gift_Shipping'] == 1):?>
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
		<?php endif ?>
        </div>
     	<div class="b15"></div>   
     
        <!-- 地址信息简述end-->
        <form id="checkout_form" action="<?=$base_url?>api/<?=$UsersID?>/user/gift/ajax/">
        <input type="hidden" name="GiftID" value="<?php echo $GiftID;?>" />
        <input type="hidden" name="isshipping" value="<?php echo $rsGift["Gift_Shipping"];?>" />
        <input type="hidden" name="action" value="gift_change" />
        <!-- 产品列表begin-->
        <div class="gift_order_info">
        	<h1>礼品信息</h1>
        	<img src="<?php echo $rsGift['Gift_ImgPath'];?>" />
            <ul>
             <li><?php echo $rsGift['Gift_Name'];?></li>
             <li class="integral"><?=$rsGift["Gift_Integral"]?><font style="font-size:12px; color:#999">&nbsp;积分</font></li>
            </ul>
            <div class="clear"></div>
        </div>
        
        <?php if($rsGift["Gift_Shipping"]==1){?>
        <input type="hidden" name="AddressID" value="<?=$rsAddress['Address_ID'] ?>" />
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
        
        <!--- 订单汇总信息begin -->
        <div class="container">
			<?php if($rsGift["Gift_FreeShipping"] == 0){?>
        	<div class="row" >
	        	<ul class="list-group">
					<li class="list-group-item">
					<p class="pull-right" id="order_total_info">合计：<span id="total_price_txt" class="red">&yen;<?=$total_shipping_fee?></span></p>
					<div class="clearfix"></div>
					</li>
				</ul>	
                <div class="clearfix"></div>
            </div>
			<?php }?>
			<br/>
			<input type="hidden" name="Order_Shipping[Express]"  id="Order_Shipping_Express" value="<?=$Shipping_Name?>"/>	
			<input type="hidden" id="total_price" name="total_price" value="<?=$total_shipping_fee?>"/>
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
        <?php }else{?>
		<div class="b15"></div>
		<div class="container">
			<input type="text" name="Mobile" value="" style="display:block; width:95%; height:36px; text-indent:15px; line-height:36px; border:1px #dfdfdf solid; margin:15px auto;" maxlength="11" placeholder="兑换人手机号码" pattern="[0-9]*"  notnull />
            <div class="row" id="btn-panel">
            	<button type="button" class="shop-btn btn-orange pull-right"   id="submit-btn">提交</button>
                
            	<div class="clearfix"></div>
            </div>
        </div>
		<?php }?>
        </form>
    </div>
    
   <?php require_once('../footer.php'); ?>
   

	</body>

</html>
