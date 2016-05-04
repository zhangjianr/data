<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Ext/virtual.func.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/order.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Ext/sms.func.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/pay_order.class.php');

$out_trade_no = $_GET['out_trade_no'];
if(strpos($out_trade_no,'gift')>-1){
	$OrderID = substr($out_trade_no,4);
	$rsOrder=$DB->GetRs("user_gift_orders","Users_ID,User_ID,Orders_Status","where Orders_ID='".$OrderID."'");
	$UsersID = $rsOrder["Users_ID"];
	$UserID = $rsOrder["User_ID"];
	$Status = $rsOrder["Orders_Status"]==0 ? 1 : 2;
	$OrderID = $out_trade_no;
	$url = '/api/'.$UsersID.'/user/gift/my/';
}else{
	$OrderID = substr($out_trade_no,10);
	$rsOrder=$DB->GetRs("user_order","Users_ID,User_ID,Order_Status","where Order_ID='".$OrderID."'");
	if(!$rsOrder){
		echo "订单不存在";
		exit;
	}
	$UsersID = $rsOrder["Users_ID"];
	$UserID = $rsOrder["User_ID"];
	$Status = $rsOrder["Order_Status"];
	$url = '/api/'.$UsersID.'/shop/member/status/'.$Status.'/';
}

$pay_order = new pay_order($DB,$OrderID);
$rsPay=$DB->GetRs("users_payconfig","*","where Users_ID='".$UsersID."'");
$rsUsers=$DB->GetRs("users","*","where Users_ID='".$UsersID."'");
$rsUser=$DB->GetRs("user","*","where Users_ID='".$UsersID."' and User_ID=".$UserID);
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
?>
<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php

$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {//验证成功
	if($Status==1){
		$data = $pay_order->pay_orders();
		if($data["status"]==1){
			echo "<script type='text/javascript'>window.location.href='".$data["url"]."';</script>";	
			exit;		
		}else{
			echo $data["msg"];
			exit;
		}
	}else{
		echo "<script type='text/javascript'>window.location.href='".$url."';</script>";	
		exit;
	}
}else {
    echo "验证失败";
}
?>
        <title>支付宝即时到账交易接口</title>
	</head>
    <body>
    </body>
</html>