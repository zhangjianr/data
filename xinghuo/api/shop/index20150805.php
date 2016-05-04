<?php
//ini_set("display_errors","On");
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');

$base_url = base_url();
$shop_url = shop_url();

if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
	$_SESSION[$UsersID."HTTP_REFERER"]="/api/shop/index.php?UsersID=".$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}

$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");

$owner = getOwner($DB,$UsersID);
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$error_msg = pre_add_distribute_account($DB,$UsersID);
$owner = getOwner($DB,$UsersID);
$share_name = '';
if($owner['id'] != '0'){
	$share_name = $rsConfig["ShopName"];
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];	
	$shop_url = $shop_url.$owner['id'].'/';
};

$rsSkin=$DB->GetRs("shop_home","*","where Users_ID='".$UsersID."' and Skin_ID=".$rsConfig['Skin_ID']);
//加入访问记录
$Data=array(
	"Users_ID"=>$UsersID,
	"S_Module"=>"shop",
	"S_CreateTime"=>time()
);

$DB->Add("statistics",$Data);
//调用模版
$share_link = $shop_url;
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

include($rsConfig['Skin_ID']."/index.php");
?>