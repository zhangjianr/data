<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
$base_url = base_url();
$shop_url = shop_url();

if (isset($_GET["UsersID"]) || empty($_GET['orderId'])) {
	$UsersID = $_GET["UsersID"];
	$orderId = intval($_GET['orderId']);
} else {
	exit("缺少必要的参数");
}
$is_login = 1;
require_once($_SERVER["DOCUMENT_ROOT"] . '/include/library/wechatuser.php');

$rsConfig = $DB->GetRs("shop_config", "*", "where Users_ID='{$UsersID}'");
$rsPay = $DB->GetRs("users_payconfig", "*", "where Users_ID='" . $UsersID . "'");

//获取订单数据
$rsOrder = $DB->GetRs("spark_order", "*", "where Users_ID='{$UsersID}' and User_ID='{$_SESSION[$UsersID . "User_ID"]}' and id='{$orderId}'");
$total = $rsOrder['price'];

if ($rsPay["PaymentWxpayType"] == 1) {
	$payUrl = "/pay/wxpay2/pay.php?UsersID=" . $UsersID . "_" . $orderId;
} else {
	$payUrl = "/pay/wxpay/pay.php?UsersID=" . $UsersID . "&OrderID=" . $orderId;
}
?>
<!DOCTYPE html>
<html class="no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="email=no"/>
	<title><?php echo $rsConfig["ShopName"] ?>付款</title>
	<link rel="stylesheet" type="text/css" href="/static/spark/css/common.css?<?=$cacheVer?>"/>
	<link rel="stylesheet" type="text/css" href="/static/spark/css/app.css?<?=$cacheVer?>"/>
	<script src="/static/spark/js/zepto.min.js"></script>
	<script src="/static/spark/js/slide_left.js"></script>
	<script src="/static/spark/js/app.js"></script>
</head>
	<body style="background: #dddddd;">
		<div id="payment">
			<div class="i-ture">
				<h1 class="t">订单提交成功！</h1>
				<div class="info"> 订 单 号：<?php echo date("Ymd", $rsOrder["createtime"]) . $rsOrder["id"] ?><br />
					订单总价：<span class="fc_red" id="Order_TotalPrice">￥<?php echo $rsOrder["price"] ?><br/></span>
				</div>
			</div>
			<!-- 支付方式选择 begin  -->
			<div class="i-ture">
				<h1 class="t">选择支付方式</h1>

				<ul id="pay-btn-panel">
					<?php if (!empty($rsPay["PaymentWxpayEnabled"])) { ?>
					<li>
						<a href="<?=$payUrl?>" class="btn btn-default btn-pay direct_pay" id="wzf" data-value="微支付">
							<img  src="/static/api/shop/skin/default/wechat_logo.jpg" width="16px" height="16px"/>&nbsp;微信支付
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</body>
</html>