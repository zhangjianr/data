<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/include/helper/distribute.php');

/* 分享页面初始化配置 */
$share_flag = 1;
$signature = '';
$shop_url = shop_url();
if (isset($_GET["UsersID"])) {
	$UsersID = $_GET["UsersID"];
} else {
	exit('缺少必要的参数');
}
if (!empty($_SESSION[$UsersID . "User_ID"])) {
	$userexit = $DB->GetRs("user", "*", "where User_ID=" . $_SESSION[$UsersID . "User_ID"] . " and Users_ID='" . $UsersID . "'");
	if (!$userexit) {
		$_SESSION[$UsersID . "User_ID"] = "";
	}
}
$owner = getOwner($DB,$UsersID);
$is_login=1;
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');

//获取本店配置
$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$rsUser=$DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]);

//业务开始

$sparkConfig=$DB->GetRs("spark_config","*","where Users_ID='".$UsersID."'");
?>
<!doctype html>
<html class="no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="email=no"/>
	<title>用户协议</title>
	<link rel="stylesheet" type="text/css" href="/static/spark/css/common.css?<?=$cacheVer?>"/>
	<link rel="stylesheet" type="text/css" href="/static/css/font-awesome.css?<?=$cacheVer?>">
	<link rel="stylesheet" type="text/css" href="/static/spark/css/app.css?<?=$cacheVer?>"/>
	<script src="/static/spark/js/zepto.min.js"></script>
	<script src="/static/spark/js/slide_left.js"></script>
	<script src="/static/spark/js/app.js"></script>
</head>
<body style="background:#F6F6F6;">
	<div class="commonHead" style="background: #0085d0;color:#fff">
		<i class="fa fa-angle-left commonBack"></i>
		<h3 class="text-overflow_1">用户协议</h3>
	</div>
	<div style="padding: 10px;"><?=  htmlspecialchars_decode($sparkConfig['content'])?></div>
</body>
</html>