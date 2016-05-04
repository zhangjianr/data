<?php require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
if(isset($_GET["UsersID"]))
{
	$UsersID=$_GET["UsersID"];
}else
{
	echo '缺少必要的参数';
	exit;
}
require_once('../share.php');
if(!strpos($_SERVER['REQUEST_URI'],"mp.weixin.qq.com")){
	header("location:?wxref=mp.weixin.qq.com");
}
$rsConfig=$DB->GetRs("web_config","*","where Users_ID='".$UsersID."'");
//加入访问记录
$Data=array(
	"Users_ID"=>$UsersID,
	"S_Module"=>"web",
	"S_CreateTime"=>time()
);
$DB->Add("statistics",$Data);
//客服
$KfIco = '';
$kfConfig=$DB->GetRs("kf_config","*","where Users_ID='".$UsersID."' and KF_IsWeb=1");
$KfIco = empty($kfConfig["KF_Icon"]) ? '' : $kfConfig["KF_Icon"];
//调用模版
$header_title = $share_title = $share_desc = "门店定位";

include($rsConfig['Skin_ID']."/lbs.php");
?>