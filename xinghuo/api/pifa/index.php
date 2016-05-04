<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
//ini_set("display_errors","On");
$show_footer = true;
if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
	$_SESSION[$UsersID."HTTP_REFERER"]="/api/shop/index.php?UsersID=".$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}

$base_url = base_url();
$pifa_url = $base_url.'api/'.$UsersID.'/pifa/';

$rsConfig = shop_config($UsersID);
//商城配置一股脑转换批发配置
$Config = $DB->GetRs("pifa_config","*","where Users_ID='".$UsersID."'");
$rsConfig['ShopName'] = $Config['PifaName'];
$rsConfig['NeedShipping'] = $Config['p_NeedShipping'];
$rsConfig['SendSms'] = $Config['p_SendSms'];
$rsConfig['MobilePhone'] = $Config['p_MobilePhone'];
$rsConfig['Commit_Check'] = $Config['p_Commit_Check'];

$owner = get_owner($rsConfig,$UsersID);//获取店主

require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$error_msg = pre_add_distribute_account($rsConfig,$UsersID);

$owner = get_owner($rsConfig,$UsersID);
$share_name = '';
if($owner['id'] != '0'){
	$share_name = $rsConfig["ShopName"];
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];	
	$pifa_url = $pifa_url.$owner['id'].'/';
};
//加入访问记录
$Data=array(
	"Users_ID"=>$UsersID,
	"S_Module"=>"pifa",
	"S_CreateTime"=>time()
);

$DB->Add("statistics",$Data);
//调用模版
$share_link = $pifa_url;
require_once('../share.php');

if($owner['id'] != '0' && $rsConfig["Distribute_Customize"]==1){
	$share_title = $rsConfig["ShopName"];
	$share_desc = $owner['shop_announce'] ? $owner['shop_announce'] : $rsConfig["ShareIntro"];
	$share_img = strpos($owner['shop_logo'],"http://")>-1 ? $owner['shop_logo'] : 'http://'.$_SERVER["HTTP_HOST"].$owner['shop_logo'];
}else{
	$share_title = $share_name;
	$share_desc = $rsConfig["ShareIntro"];
	$share_img = strpos($rsConfig['ShareLogo'],"http://")>-1 ? $rsConfig['ShareLogo'] : 'http://'.$_SERVER["HTTP_HOST"].$rsConfig['ShareLogo'];
}


$C = $DB->GetRS("users","Users_Logo","where Users_ID='".$UsersID."'");
$show_support = true;

require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/weixin_message.class.php');
$weixin_message = new weixin_message($DB,$UsersID,0);
$weixin_message->sendordernotice();

include("skin/index.php");

?>