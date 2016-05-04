<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');

if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}

$base_url = base_url();
$pifa_url = $base_url.'api/'.$UsersID.'/pifa/';

$rsConfig = $DB->GetRs("shop_config", "*", "where Users_ID='" . $UsersID . "'");
//商城配置一股脑转换批发配置
$Config = $DB->GetRs("pifa_config","*","where Users_ID='".$UsersID."'");
$rsConfig['ShopName'] = $Config['PifaName'];
$rsConfig['NeedShipping'] = $Config['p_NeedShipping'];
$rsConfig['SendSms'] = $Config['p_SendSms'];
$rsConfig['MobilePhone'] = $Config['p_MobilePhone'];
$rsConfig['Commit_Check'] = $Config['p_Commit_Check'];

$share_flag=0;
$signature="";

$is_login=1;
$owner = getOwner($DB,$UsersID);//登录前
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$owner = getOwner($DB,$UsersID);//登录后
if($owner['id'] != '0'){
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	$pifa_url = $pifa_url.$owner['id'].'/';
}

if(!strpos($_SERVER['REQUEST_URI'],"mp.weixin.qq.com")){
	header("location:?wxref=mp.weixin.qq.com");
}
if(isset($_REQUEST["OrderID"]))
{
	$OrderID = $_REQUEST["OrderID"];
}else{
	echo '缺少必要的参数';
	exit;
}

$rsPay = $DB->GetRs("users_payconfig","*","where Users_ID='".$UsersID."'");
$rsOrder = $DB->GetRs("user_order","*","where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Order_Type='pifa' and Order_ID='".$OrderID."'");
$rsUser = $DB->GetRs("user","*","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID."User_ID"]);

$total = $rsOrder['Order_TotalPrice'];
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<meta content="telephone=no" name="format-detection" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $rsConfig["ShopName"] ?>付款</title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/api/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/api/shop/skin/default/css/style.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/api/js/global.js'></script>
<script type='text/javascript' src='/static/api/pifa/js/js.js'></script>
</head>

<body>
<div id="shop_page_contents">
	<div id="cover_layer"></div>
	<link href='/static/api/shop/skin/default/css/cart.css?t=<?php echo time();?>' rel='stylesheet' type='text/css' />
	<script language="javascript">$(document).ready(pifa_obj.payment_init);</script>
	<div id="payment">
		<div class="i-ture">
			<h1 class="t">订单提交成功！</h1>
			<div class="info"> 订 单 号：<?php echo date("Ymd",$rsOrder["Order_CreateTime"]).$rsOrder["Order_ID"] ?><br />
				订单总价：<span class="fc_red" id="Order_TotalPrice">￥<?php echo $rsOrder["Order_TotalPrice"]?><br/>
				</span>
				<?php if($rsOrder['Integral_Money'] > 0):?>
				已经用
				<?=$rsOrder['Integral_Consumption']?>
				个积分抵用了<span class="fc_red">
				<?=$rsOrder['Integral_Money']?>
				</span>元
				<?php endif; ?>
			</div>
		</div>
		<form id="payment_form" action="/api/<?php echo $UsersID ?>/pifa/cart/">
			<!-- 支付方式选择 begin  -->
			<div class="i-ture">
				<h1 class="t">选择支付方式</h1>
				<ul id="pay-btn-panel">
					<?php if(!empty($rsPay["PaymentWxpayEnabled"])){ ?>
					<li> <a href="javascript:void(0)" class="btn btn-default btn-pay direct_pay" id="wzf" data-value="微支付"><img  src="/static/api/shop/skin/default/wechat_logo.jpg" width="16px" height="16px"/>&nbsp;微信支付</a> </li>
					<?php }?>
					<?php if(!empty($rsPay["Payment_AlipayEnabled"])){ ?>
					<li> <a href="javascript:void(0)" class="btn btn-danger btn-pay direct_pay" id="zfb" data-value="支付宝"><img  src="/static/api/shop/skin/default/alipay.png" width="16px" height="16px"/>&nbsp;支付宝支付</a> </li>
					<?php if(!empty($rsPay["PaymentYeepayEnabled"])){?>
					<li style="display:none;"> <a href="javascript:void(0)" class="btn btn-danger btn-pay direct_pay" id="ybzf" data-value="易宝支付"><img  src="/static/api/shop/skin/default/yeepay.png" width="16px" height="16px"/>&nbsp;易宝支付</a> </li>
					<?php }?>
					<?php }?>
                    <?php if(!empty($rsPay["Payment_OfflineEnabled"])){?>
					<li> <a href="/api/<?=$UsersID?>/pifa/cart/complete_pay/huodao/<?=$OrderID?>/" class="btn btn-warning  btn-pay" id="out" data-value="线下支付"><img  src="/static/api/shop/skin/default/huodao.png" width="16px" height="16px"/>&nbsp;货到付款</a> </li>
					<?php }?>
					<li> <a href="/api/<?=$UsersID?>/pifa/cart/complete_pay/money/<?=$OrderID?>/" class="btn btn-warning  btn-pay" id="money" data-value="余额支付"><img  src="/static/api/shop/skin/default/money.jpg"  width="16px" height="16px"/>&nbsp;余额支付</a> </li>
				</ul>
			</div>
			<!-- 支付方式选择 end -->
			
			<input type="hidden" name="PaymentMethod" value="微支付"/>
			<input type="hidden" name="OrderID" value="<?php echo $rsOrder["Order_ID"] ?>" />
			<input type="hidden" name="total_price" value="<?php echo$rsOrder["Order_TotalPrice"] ?>" />
			<input type="hidden" name="action" value="paymentorder" />
			<input type="hidden" name="DefautlPaymentMethod" value="<?php echo $rsOrder["Order_PaymentMethod"] ?>" />
		</form>
	</div>
</div>
<div id="footer_points"></div>
<?php require_once('../footer.php'); ?>
</body>
</html>