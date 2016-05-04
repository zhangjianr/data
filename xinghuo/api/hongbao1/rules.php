<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
if(isset($_GET["UsersID"])){
	if(!strpos($_SERVER['REQUEST_URI'],"OpenID=")){
		$UsersID = $_GET["UsersID"];
		$rsConfig = $DB->GetRs("hongbao_config","*","where usersid='".$UsersID."'");
		$rsConfig['Description'] = str_replace('&quot;','"',$rsConfig['Description']);
		$rsConfig['Description'] = str_replace("&quot;","'",$rsConfig['Description']);
		$rsConfig['Description'] = str_replace('&gt;','>',$rsConfig['Description']);
		$rsConfig['Description'] = str_replace('&lt;','<',$rsConfig['Description']);
		if(!$rsConfig){
			echo '未开通抢红包';
			exit;
		}
	}else{
		header("location:/api/hongbao/rules.php?UsersID=".$_GET["UsersID"]."&wxref=mp.weixin.qq.com");
	}
}else{
	echo '缺少必要的参数';
	exit;
}

$rsUsers = $DB->GetRs("users","*","where Users_ID='".$UsersID."'");
$rsPay = $DB->GetRs("users_payconfig","*","where Users_ID='".$UsersID."' and PaymentWxpayEnabled=1");
$is_login = 1;
$owner = getOwner($DB,$UsersID);
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$owner = getOwner($DB,$UsersID);

require_once('skin/rules.php');
?>
