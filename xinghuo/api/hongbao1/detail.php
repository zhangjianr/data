<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');

if(isset($_GET["UsersID"])){
	
	if(!strpos($_SERVER['REQUEST_URI'],"OpenID=")){
		if(!strpos($_GET["UsersID"],"_")){
			echo '缺少必要的参数';
			exit;
		}else{//help friend
			$arr = explode("_",$_GET["UsersID"]);
			$UsersID = $arr[0];
			$actid = $arr[1];
		}
		$_SESSION[$UsersID."HTTP_REFERER"]="/api/hongbao/detail.php?UsersID=".$_GET["UsersID"];
		$rsConfig = $DB->GetRs("hongbao_config","*","where usersid='".$UsersID."'");
		if(!$rsConfig){
			echo '未开通抢红包';
			exit;
		}
	}else{
		header("location:/api/hongbao/detail.php?UsersID=".$_GET["UsersID"]."&wxref=mp.weixin.qq.com");
	}
}else{
	echo '缺少必要的参数';
	exit;
}
require_once('../share.php');
$actinfo = $DB->GetRs("hongbao_act","*","where usersid='".$UsersID."' and actid=$actid");
if(!$actinfo){
	echo '该红包不存在';
	exit;
}

$rsUsers = $DB->GetRs("users","*","where Users_ID='".$UsersID."'");

$is_login = 1;
$owner = getOwner($DB,$UsersID);
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$owner = getOwner($DB,$UsersID);

$rsPay = $DB->GetRs("users_payconfig","*","where Users_ID='".$UsersID."' and PaymentWxpayEnabled=1");
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
$chai_pass = 0;
if($actinfo["status"]==1){
	$chai_pass = 1;
}
if($actinfo["userid"]==$_SESSION[$UsersID."User_ID"]){//我的红包
	if($chai_pass==1){
		echo '<script type="text/javascript">alert("该红包已拆启");window.location.href="/api/'.$UsersID.'/hongbao/mycenter/";</script>';
		exit;
	}
	if($actinfo["expire"] >= $actinfo["friend"]){
		$chai = 1;
		$diff = 0;
	}else{
		$chai = 0;
		$diff = $actinfo["friend"] - $actinfo["expire"];
	}	
	require_once('skin/detail.php');
}else{
	$people = count($rank);
	$myrecord = 0;
	$rsRecord = $DB->GetRs("hongbao_record","count(*) as num","where usersid='".$UsersID."' and actid=$actid and userid=".$_SESSION[$UsersID."User_ID"]);
	if($rsRecord["num"]>0){
		$myrecord = 1;
	}
	require_once('skin/help.php');
}
?>
