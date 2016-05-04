<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/pay_order.class.php');
if(isset($_GET["UsersID"]) && isset($_GET["orderId"])){
	$UsersID = trim($_GET["UsersID"]);
	$orderId = trim($_GET["orderId"]);
}else{
	exit('缺少必要的参数');
}
$rsPay=$DB->GetRs("users_payconfig","*","where Users_ID='{$UsersID}'");
$rsUsers=$DB->GetRs("users","*","where Users_ID='{$UsersID}'");
$pay_order = new pay_order($DB,$orderId);

if (strpos($orderId, 'TRA') > -1) {
	$rsOrder = $DB->GetRs("spark_traffic_order", "*", "where Users_ID='{$UsersID}' and User_ID='{$_SESSION[$UsersID . "User_ID"]}' and orderId='{$orderId}'");
	if($rsOrder['payCode'] != 0){
		exit('<script>alert("该订单已支付，请勿重复付款！");history.go(-1);</script>');
	}
	$payinfo = $pay_order->get_pay_traffic();
}else if (strpos($orderId, 'SPARK') > -1){
	$rsOrder = $DB->GetRs("spark_order", "*", "where Users_ID='{$UsersID}' and User_ID='{$_SESSION[$UsersID . "User_ID"]}' and orderId='{$orderId}'");
	if($rsOrder['payCode'] != 0){
		exit('<script>alert("该订单已支付，请勿重复付款！");history.go(-1);</script>');
	}
	$payinfo = $pay_order->get_pay_package();
}
$pay_fee = $payinfo["total_fee"];
$pay_total = strval(floatval($pay_fee)*100);
$pay_orderno = strval($payinfo["out_trade_no"]);
$pay_subject = $payinfo["subject"];

include_once("WxPay.pub.config.php");
include_once("WxPayPubHelper.php");
$jsApi = new JsApi_pub();
if (!isset($_GET['code'])){
	$url = $jsApi->createOauthUrlForCode(JS_API_CALL_URL_PACKAGE);
	Header("Location: $url");
}else{
	$code = $_GET['code'];
	$jsApi->setCode($code);
}
$openid = $jsApi->getOpenid();
$unifiedOrder = new UnifiedOrder_pub();
$unifiedOrder->setParameter("openid","$openid");
$unifiedOrder->setParameter("body","$pay_subject");
$unifiedOrder->setParameter("out_trade_no","$pay_orderno");
$unifiedOrder->setParameter("total_fee","$pay_total");
$unifiedOrder->setParameter("notify_url",NOTIFY_URL_PACKAGE);
$unifiedOrder->setParameter("trade_type","JSAPI");
$prepay_id = $unifiedOrder->getPrepayId();

$jsApi->setPrepayId($prepay_id);
$jsApiParameters = $jsApi->getParameters();
?>
<!DOCTYPE html>
<html class="no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="email=no"/>
	<title>微信安全支付</title>
	<link rel="stylesheet" type="text/css" href="/static/spark/css/common.css?<?=$cacheVer?>"/>
	<link rel="stylesheet" type="text/css" href="/static/spark/css/app.css?<?=$cacheVer?>"/>
	<script type="text/javascript">
		var lock = false;
		function jsApiCall(){
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				<?php echo $jsApiParameters; ?>,
				function(res){
					WeixinJSBridge.log(res.err_msg);
					if(res.err_msg=='get_brand_wcpay_request:ok'){
						window.location.href = 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/pay/wxpay2/notify.php?orderId=<?php echo $orderId; ?>';
					}
				}
			);
		}

		function callpay(){
			if(lock){
				return;
			}
			lock = true;
			if (typeof WeixinJSBridge == "undefined"){
				if( document.addEventListener ){
					document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
				}else if (document.attachEvent){
					document.attachEvent('WeixinJSBridgeReady', jsApiCall);
					document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
				}
			}else{
				jsApiCall();
			}
		}
		callpay();
	</script>
</head>
	<body style="background: #dddddd;">
		<div id="payment">
			<div class="i-ture">
				<h1 class="t">订单提交成功！</h1>
				<div class="info">
					订 单 号：<?php echo $rsOrder["orderId"]; ?><br />
					订单总价：<span class="fc_red" id="Order_TotalPrice">￥<?php echo $pay_fee ?><br/></span>
				</div>
			</div>
			<!-- 支付方式选择 begin  -->
			<div class="i-ture">
				<h1 class="t">支付方式</h1>

				<ul id="pay-btn-panel">
					<li>
						<a href="javascript:void(0)" onclick="callpay()" class="btn btn-default btn-pay direct_pay" id="wzf" data-value="微支付">
							<img  src="/static/api/shop/skin/default/wechat_logo.jpg" width="16px" height="16px"/>&nbsp;微信支付
						</a>
					</li>
				</ul>
			</div>
		</div>
	</body>
</html>