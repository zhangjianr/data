<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}
require_once('../share.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$rsConfig=$DB->GetRs("web_config","*","where Users_ID='".$UsersID."'");
$rsSkin=$DB->GetRs("web_home","*","where Users_ID='".$UsersID."' and Skin_ID=".$rsConfig['Skin_ID']);
//加入访问记录
$Data=array(
	"Users_ID"=>$UsersID,
	"S_Module"=>"web",
	"S_CreateTime"=>time()
);
$DB->Add("statistics",$Data);
//调用模版
$KfIco = '';
$kfConfig=$DB->GetRs("kf_config","*","where Users_ID='".$UsersID."' and KF_IsWeb=1");
$KfIco = empty($kfConfig["KF_Icon"]) ? '' : $kfConfig["KF_Icon"];
$header_title = $share_title = $share_desc = $rsConfig["SiteName"];
include($rsConfig['Skin_ID']."/index.php");
?>