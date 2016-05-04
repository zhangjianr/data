<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');

$base_url = base_url();
$shop_url = shop_url();

if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}
if(isset($_GET["ProductsID"])){
	$ProductsID=$_GET["ProductsID"];
}else{
	echo '缺少必要的参数';
	exit;
}

if(!empty($_SESSION[$UsersID."User_ID"])){
	$userexit = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."'");
	if(!$userexit){
		$_SESSION[$UsersID."User_ID"] = "";
	}	
}

$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
//获取此产品
$rsProducts=$DB->GetRs("shop_products","*","where Users_ID='".$UsersID."' and Products_SoldOut=0 and Products_ID=".$ProductsID);
if(!$rsProducts){
	echo "暂无此信息！";
	exit;
}
$owner = getOwner($DB,$UsersID);
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$owner = getOwner($DB,$UsersID);
if($owner['id'] != '0'){
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	$shop_url = $shop_url.$owner['id'].'/';
};
//加入访问记录
$Data=array(
	"Users_ID"=>$UsersID,
	"S_Module"=>"shop",
	"S_CreateTime"=>time()
);
$DB->Add("statistics",$Data);
//调用模版
$header_title = '全部评论 - '.$rsProducts["Products_Name"].' - '.$rsConfig["ShopName"];
$share_link = $shop_url.'commit/'.$ProductsID.'/';
require_once('../share.php');
$share_title = $share_desc = '全部评论 - '.$rsProducts["Products_Name"].' - '.$rsConfig["ShopName"];
$share_img = 'http://'.$_SERVER["HTTP_HOST"].'/static/api/images/cover_img/shop.jpg';
if(isset($JSON["ImgPath"])){
	$share_img = 'http://'.$_SERVER["HTTP_HOST"].$JSON["ImgPath"][0];
}

$C = $DB->GetRS("users","Users_Logo","where Users_ID='".$UsersID."'");
include($rsConfig['Skin_ID']."/commitlist.php");
?>