<?php require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');

$base_url = base_url();
$shop_url = shop_url();
if(isset($_GET["UsersID"]))
{
	$UsersID=$_GET["UsersID"];
}else
{
	echo '缺少必要的参数';
	exit;
}

if(isset($_GET['CategoryID'])){
	$CategoryID = $_GET['CategoryID'];
	
}else{
	echo '缺少分类ID';
	exit();
}

if(!empty($_SESSION[$UsersID."User_ID"])){
	$userexit = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."'");
	if(!$userexit){
		$_SESSION[$UsersID."User_ID"] = "";
	}	
}

$order_filter_base = $base_url.'api/shop/category.php?UsersID='.$UsersID.'&CategoryID='.$CategoryID;
$page_url = $base_url.'api/shop/category.php?UsersID='.$UsersID.'&CategoryID='.$CategoryID;
$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$rsCategory=$DB->GetRs("shop_category","*","where Users_ID='".$UsersID."' and Category_ID=".$CategoryID);

$owner = getOwner($DB,$UsersID);
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$owner = getOwner($DB,$UsersID);

if($owner['id'] != '0'){
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	$shop_url = $shop_url.$owner['id'].'/';
	$order_filter_base .= '&OwnerID='.$owner['id'];
	$page_url .= '&OwnerID='.$owner['id'];
}
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');

if($rsCategory["Category_ParentID"]>0){
	$rsPCategory=$DB->GetRs("shop_category","*","where Users_ID='".$UsersID."' and Category_ID=".$rsCategory["Category_ParentID"]);
}
$condition = "where Users_ID='".$UsersID."' and Products_SoldOut=0";
if($CategoryID>0){
	if(empty($rsCategory["Category_ParentID"])){
		$CategoryList=array();
		$DB->Get("shop_category","*","where Users_ID='".$UsersID."' and Category_ParentID=".$CategoryID);
		while($v=$DB->fetch_assoc()){
			$CategoryList[]=$v["Category_ID"];
		}
		if(empty($CategoryList)){
			$condition .= " and Products_Category='".$CategoryID."'";
		}else{
			$CategoryList=implode(",",$CategoryList);
			$condition .= " and Products_Category in(".$CategoryList.")";
		}
	}else{
		$condition .= " and Products_Category='".$CategoryID."'";
	}
}
$order_by = !empty($_GET['order_by'])?$_GET['order_by']:'';

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
//加入访问记录
$Data=array(
	"Users_ID"=>$UsersID,
	"S_Module"=>"shop",
	"S_CreateTime"=>time()
);
$DB->Add("statistics",$Data);

//调用模版
$share_link = $shop_url.'category/'.$CategoryID.'/';
require_once('../share.php');
if($owner['id'] != '0' && $rsConfig["Distribute_Customize"]==1){
	$share_desc = $owner['shop_announce'] ? $owner['shop_announce'] : $rsConfig["ShareIntro"];
	$share_img = strpos($owner['shop_logo'],"http://")>-1 ? $owner['shop_logo'] : 'http://'.$_SERVER["HTTP_HOST"].$owner['shop_logo'];
}else{
	$share_desc = $rsConfig["ShareIntro"];
	$share_img = strpos($rsConfig['ShareLogo'],"http://")>-1 ? $rsConfig['ShareLogo'] : 'http://'.$_SERVER["HTTP_HOST"].$rsConfig['ShareLogo'];
}

$C = $DB->GetRS("users","Users_Logo","where Users_ID='".$UsersID."'");
include($rsConfig['Skin_ID']."/category.php");
?>