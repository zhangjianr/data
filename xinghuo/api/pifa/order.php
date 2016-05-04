<?php 
//ini_set("display_errors","On");
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/shipping.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/flow.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
if(isset($_REQUEST["UsersID"])){	
	$UsersID=$_REQUEST["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}
$base_url = base_url();
$pifa_url = $base_url.'api/'.$UsersID.'/pifa/';

$is_login = 1;

$owner = getOwner($DB,$UsersID);
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$error_msg = pre_add_distribute_account($DB,$UsersID);
$owner = getOwner($DB,$UsersID);

if(isset($_REQUEST["ProductID"]))
{
	$ProductsID=$_REQUEST["ProductID"];
}else{
	echo '缺少必要的参数';
	exit;
}
//获取此产品
$rsProducts = $DB->GetRs("pifa_products","*","where Users_ID='".$UsersID."' and Products_SoldOut=0 and Products_ID=".$ProductsID);
$JSON = json_decode($rsProducts['Products_JSON'],TRUE);//图片信息
$price_rule = json_decode($rsProducts['Products_price_rule'],true);//价格区间信息
$rsProducts  = handle_product($rsProducts);
//最低批发数
$qty = $price_rule[0][0];
//第一个批发价格区间的价格
$cur_price = $price_rule[0][2];


$_SESSION[$UsersID."HTTP_REFERER"]="/api/".$UsersID."/pifa/order/".$ProductsID."/";

$User_ID  = $_SESSION[$UsersID."User_ID"];

//总价计算（最低总价）
$base_total_price = $qty * $cur_price;

$rsConfig =  $DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
//商城配置一股脑转换批发配置
$Config = $DB->GetRs("pifa_config","*","where Users_ID='".$UsersID."'");
$rsConfig['ShopName'] = $Config['PifaName'];
$rsConfig['NeedShipping'] = $Config['p_NeedShipping'];
$rsConfig['SendSms'] = $Config['p_SendSms'];
$rsConfig['MobilePhone'] = $Config['p_MobilePhone'];
$rsConfig['Commit_Check'] = $Config['p_Commit_Check'];
//支付配置
$rsPay = $DB->GetRs("users_payconfig","*","where Users_ID='".$UsersID."'");

//获得用户地址
$Address_ID = !empty($_GET['AddressID'])?$_GET['AddressID']:0;
if($Address_ID == 0){
	$condition = "Where Users_ID = '".$UsersID."' And User_ID = ".$User_ID." And Address_Is_Default = 1";
}else{
	$condition = "Where Users_ID = '".$UsersID."' And User_ID = ".$User_ID." And Address_ID =".$Address_ID;
}

$rsAddress = $DB->GetRs('user_address','*',$condition);
$area_json = read_file($_SERVER["DOCUMENT_ROOT"].'/data/area.js');
$area_array = json_decode($area_json,TRUE);
$province_list = $area_array[0];

if($rsAddress){
	$Province = $province_list[$rsAddress['Address_Province']];
	$City = $area_array['0,'.$rsAddress['Address_Province']][$rsAddress['Address_City']];
	$Area = $area_array['0,'.$rsAddress['Address_Province'].','.$rsAddress['Address_City']][$rsAddress['Address_Area']];
}else{
	$_SESSION[$UsersID."From_Checkout"] = 1;
	header("location:/api/".$UsersID."/user/my/address/edit/");
}

if(empty($rsConfig['Default_Business'])||empty($rsConfig['Default_Shipping'])){
	echo '<p style="text-align:center;color:red;font-size:30px;"><br/><br/>基本运费信息没有设置，请联系管理员</p>';
	exit();
}

//获取前台可用的快递公司
$shipping_company_dropdown = get_front_shiping_company_dropdown($UsersID,$rsConfig);
$Business = 'express';

$Shipping_Name = $shipping_company_dropdown[$rsConfig['Default_Shipping']];
if($rsAddress){
	$City_Code = $rsAddress['Address_City'];
}else{
	$City_Code = 0;
}
//运费计算
$total_shipping_fee = 0;
if($rsConfig['NeedShipping'] == 1 && $City_Code != 0){ //商城是否支持物流运费（$City_Code必须）
	$shipping_money = $base_total_price;
	$weight = $qty * $rsProducts['Products_Weight'];
	$Products_Info = array('qty'=>$qty,'weight'=>$weight,'money'=>$shipping_money);
	$total_shipping_fee = get_shipping_fee($UsersID,$rsConfig['Default_Shipping'],$Business,$City_Code,$rsConfig,$Products_Info);		
}
$total_price = $base_total_price + $total_shipping_fee;

if($owner['id'] != '0'){
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	$pifa_url = $pifa_url.$owner['id'].'/';
};
$share_link = $pifa_url.'order/'.$ProductsID.'/';
require_once('../share.php');
//$Default_Business = $rsConfig['Default_Business'];	
$Default_Shipping = $rsConfig['Default_Shipping'];
//$Business_List = array('express'=>'快递','common'=>'平邮');	
include("skin/order.php");
?>