<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/smarty.php');

//设置smarty
$smarty->left_delimiter = "{{";
$smarty->right_delimiter = "}}";

$template_dir = $_SERVER["DOCUMENT_ROOT"].'/api/kanjia/skin/1';
$smarty->template_dir = $template_dir;

$base_url = base_url();

if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}

if(isset($_GET["ProductID"])){
	$ProductID = $_GET["ProductID"];

}else{
	echo '缺少必要的参数:产品ID';
	exit;
}
require_once('../share.php');
//获取指定产品信息
$condition = "where Users_ID = '".$UsersID."' and Products_ID='".$ProductID."'";
$product = $DB->GetRs('shop_products',"*",$condition);

$product["Products_Description"] = str_replace('&quot;','"',$product["Products_Description"]);
$product["Products_Description"] = str_replace("&quot;","'",$product["Products_Description"]);
$product["Products_Description"] = str_replace('&gt;','>',$product["Products_Description"]);
$product["Products_Description"] = str_replace('&lt;','<',$product["Products_Description"]);

//获得此产品评论
$condition = "where Users_ID = '".$UsersID."' and Product_ID='".$ProductID."'";
$rsCommits = $DB->Get('user_order_commit',"*",$condition);

$commit_list = $DB->toArray($rsCommits);



if(!$product){
	echo 'NO THIS PRODUCT';
	exit();
}


//通用变量赋值
$smarty->assign('base_url',$base_url);
$smarty->assign('UsersID',$UsersID);
$smarty->assign('public',$base_url.'/static/api/kanjia/');
$smarty->assign('kanjia_url',$base_url.'api/'.$UsersID.'/kanjia/');
$smarty->assign('title','产品详情');

//分享变量
$smarty->assign('share_flag',($share_flag==1 && $signature<>""));
if($share_flag){
	$JSON=json_decode($product['Products_JSON'],true);
	$img_url = empty($JSON["ImgPath"]) ? '' : $JSON["ImgPath"][0];
	$smarty->assign('signature',$signature);
	$smarty->assign('appId',$share_user["Users_WechatAppId"]);
	$smarty->assign('timestamp',$timestamp);
	$smarty->assign('noncestr',$noncestr);
	$smarty->assign('url',$url);
	$smarty->assign('desc',str_replace(array("\r\n", "\r", "\n"), "", $product["Products_BriefDescription"]));
	$smarty->assign('img_url',$base_url.$img_url);
	$smarty->assign('link','');
}
//本页变量
$smarty->assign('product',$product);
$smarty->assign('commit_list',$commit_list);
$smarty->display('detail.html');