<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
if(isset($_GET["UsersID"])){
	if(!strpos($_SERVER['REQUEST_URI'],"OpenID=")){
		$UsersID = $_GET["UsersID"];
		$rsConfig = $DB->GetRs("hongbao_config","*","where usersid='".$UsersID."'");
		if(!$rsConfig){
			echo '未开通抢红包';
			exit;
		}
	}else{
		header("location:/api/hongbao/index.php?UsersID=".$_GET["UsersID"]."&wxref=mp.weixin.qq.com");
	}
}else{
	echo '缺少必要的参数';
	exit;
}
require_once('../share.php');
$rsUsers = $DB->GetRs("users","*","where Users_ID='".$UsersID."'");
$rsPay = $DB->GetRs("users_payconfig","*","where Users_ID='".$UsersID."' and PaymentWxpayEnabled=1");

$is_login = 1;
$owner = getOwner($DB,$UsersID);
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$owner = getOwner($DB,$UsersID);

$rank = $ranklist = array();
//排行榜
$rsRank = $DB->query("SELECT u.User_HeadImg,u.User_NickName,sum(za.money) as money,za.userid FROM hongbao_act za,`user` u where u.User_ID = za.userid and za.usersid='".$UsersID."' group by za.userid order by sum(za.money) desc");
$ranklist = $DB->toArray($rsRank);
foreach($ranklist as $key=>$item){
	if(empty($rank[$item["userid"]])){
		$rank[$item["userid"]] = $item;
	}else{
		continue;
	}
}
$people = count($rank);
$start = 1;
$time_diff = 0;
$actinfo = $DB->GetRs("hongbao_act","*","where usersid='".$UsersID."' and userid=".$_SESSION[$UsersID."User_ID"]." order by addtime desc");
if($actinfo && $rsConfig["pertime"]){
	$diff = time()-intval($rsConfig["pertime"])*60;
	if($actinfo["addtime"]>$diff){
		$start = 0;
		$time_diff = $actinfo["addtime"]+intval($rsConfig["pertime"])*60-time();
	}
}


require_once('skin/index.php');
?>
