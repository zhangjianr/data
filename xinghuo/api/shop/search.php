<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
$base_url = base_url();
$shop_url = shop_url();
if(!empty($_GET["UsersID"]))
{
	$UsersID=$_GET["UsersID"];
}else
{
	echo '缺少必要的参数';
	exit;
}

if(!empty($_SESSION[$UsersID."User_ID"])){
	$userexit = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."'");
	if(!$userexit){
		$_SESSION[$UsersID."User_ID"] = "";
	}	
}
$order_filter_base = $base_url.'api/shop/search.php?UsersID='.$UsersID;
$page_url = $base_url.'api/shop/search.php?UsersID='.$UsersID;

$owner = getOwner($DB,$UsersID);
if($owner['id'] != '0'){
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	$shop_url = $shop_url.$owner['id'].'/';
	$order_filter_base .= '&OwnerID='.$owner['id'];
	$page_url .= '&OwnerID='.$owner['id'];
}

require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');

$condition = "where Users_ID='".$UsersID."' and Products_SoldOut=0";

$position = '';
if(!empty($_GET["IsHot"])){
	$position = " &gt; 热卖商品";
	$condition .= " and Products_IsHot=1";
	$order_filter_base .= "&IsHot=1";
	$page_url .= "&IsHot=1";
}
if(!empty($_GET["IsNew"])){
	$position = " &gt; 最新商品";
	$condition .= " and Products_IsNew=1";
	$order_filter_base .= "&IsNew=1";
	$page_url .= "&IsNew=1";
}
if(!empty($_GET["kw"])){
	$position = ' &gt; 商品搜索 “<font style="color:#ff0000">'.$_GET["kw"].'</font>”';
	$condition .= " and Products_Name like '%".$_GET["kw"]."%'";
	$order_filter_base .= "&kw=".$_GET["kw"];
	$page_url .= "&kw=".$_GET["kw"];
}


$order_by = !empty($_GET['order_by'])?$_GET['order_by']:'sales';

if($order_by == 'sales'){
	$condition .= " order by Products_Sales desc";
}else if($order_by == 'price'){
	$condition .= " order by Products_PriceX asc";
}else if($order_by == 'comments'){
	$condition .= " order by Products_CreateTime desc";
}else{
	$condition .= " order by Products_ID desc";
}
$order_filter_base .= '&order_by=';
$page_url .= '&order_by='.$order_by.'&page=';
$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
//加入访问记录
$Data=array(
	"Users_ID"=>$UsersID,
	"S_Module"=>"shop",
	"S_CreateTime"=>time()
);
$DB->Add("statistics",$Data);
//调用模版
require_once('../share.php');
$share_title = $share_desc = "商品搜索";
$share_img = 'http://'.$_SERVER["HTTP_HOST"].'/static/api/images/cover_img/shop.jpg';
$share_link = '';
$C = $DB->GetRS("users","Users_Logo","where Users_ID='".$UsersID."'");
include($rsConfig['Skin_ID']."/search.php");
?>