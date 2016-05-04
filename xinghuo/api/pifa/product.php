<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/lib_products.php');

//ini_set("display_errors","On");

$show_footer = true;

if(isset($_GET["UsersID"]))
{
	$UsersID=$_GET["UsersID"];
}else
{
	echo '缺少必要的参数';
	exit;
}

$base_url = base_url();
$pifa_url = $base_url.'api/'.$UsersID.'/pifa/';

if(isset($_GET["ProductID"]))
{
	$ProductsID=$_GET["ProductID"];
}else{
	echo '缺少必要的参数';
	exit;
}

$UserID = 0;
$Is_Distribute = 0;  //用户是否为分销会员

if(!empty($_SESSION[$UsersID."User_ID"])){
	$UserID = $_SESSION[$UsersID."User_ID"];
	$userexit = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."'");
	$Is_Distribute = $userexit['Is_Distribute'];
	
	if(!$userexit){
		$_SESSION[$UsersID."User_ID"] = "";
		$UserID = 0;
	}	
}

$rsConfig = $DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
//商城配置一股脑转换批发配置
$Config = $DB->GetRs("pifa_config","*","where Users_ID='".$UsersID."'");
$rsConfig['ShopName'] = $Config['PifaName'];
$rsConfig['NeedShipping'] = $Config['p_NeedShipping'];
$rsConfig['SendSms'] = $Config['p_SendSms'];
$rsConfig['MobilePhone'] = $Config['p_MobilePhone'];
$rsConfig['Commit_Check'] = $Config['p_Commit_Check'];
//获取此产品
$rsProducts = $DB->GetRs("pifa_products","*","where Users_ID='".$UsersID."' and Products_SoldOut=0 and Products_ID=".$ProductsID);
$JSON = json_decode($rsProducts['Products_JSON'],TRUE);
$price_rule = json_decode($rsProducts['Products_price_rule'],true);
$rsProducts  = handle_product($rsProducts);

//批发须知
$rsProducts["Products_BriefDescription"] = str_replace('&quot;','"',$rsProducts["Products_BriefDescription"]);
$rsProducts["Products_BriefDescription"] = str_replace("&quot;","'",$rsProducts["Products_BriefDescription"]);
$rsProducts["Products_BriefDescription"] = str_replace('&gt;','>',$rsProducts["Products_BriefDescription"]);
$rsProducts["Products_BriefDescription"] = str_replace('&lt;','<',$rsProducts["Products_BriefDescription"]);

//产品详情
$rsProducts["Products_Description"] = str_replace('&quot;','"',$rsProducts["Products_Description"]);
$rsProducts["Products_Description"] = str_replace("&quot;","'",$rsProducts["Products_Description"]);
$rsProducts["Products_Description"] = str_replace('&gt;','>',$rsProducts["Products_Description"]);
$rsProducts["Products_Description"] = str_replace('&lt;','<',$rsProducts["Products_Description"]);

$owner = getOwner($DB,$UsersID);
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$error_msg = pre_add_distribute_account($DB,$UsersID);
$owner = getOwner($DB,$UsersID);
if($owner['id'] != '0'){
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	$pifa_url = $pifa_url.$owner['id'].'/';
}


if(!$rsProducts){
	echo "此商品不存在或已下架";
	exit;
}

/*获取此商品thumb*/


$ImgPath = get_prodocut_cover_img($rsProducts);

$rsCommit=$DB->GetRs("user_order_commit","count(*) as num","where Users_ID='".$UsersID."' and Status=1 and MID='pifa' and Product_ID=".$ProductsID);
$commit = $rsCommit["num"];

//加入访问记录
$Data=array(
	"Users_ID"=>$UsersID,
	"S_Module"=>"pifa",
	"S_CreateTime"=>time()
);
$DB->Add("statistics",$Data);
//调用模版
$share_link = $pifa_url.'product/'.$ProductsID.'/';
require_once('../share.php');
$share_title = $rsProducts["Products_Name"];
if($owner['id'] != '0' && $rsConfig["Distribute_Customize"]==1){
	$share_desc = $owner['shop_announce'] ? $owner['shop_announce'] : $rsConfig["ShareIntro"];
	$share_img = strpos($owner['shop_logo'],"http://")>-1 ? $owner['shop_logo'] : 'http://'.$_SERVER["HTTP_HOST"].$owner['shop_logo'];
}else{
	$share_desc = $rsConfig["ShareIntro"];
	$share_img = strpos($rsConfig['ShareLogo'],"http://")>-1 ? $rsConfig['ShareLogo'] : 'http://'.$_SERVER["HTTP_HOST"].$rsConfig['ShareLogo'];
}


/*相关联产品，从此产品的分类中找出其他产品*/

$condition = "where Products_Category=".$rsProducts['Products_Category']." and Products_SoldOut=0 and Products_ID !=".$rsProducts['Products_ID'];
$condition .= " and Users_ID='".$UsersID."'";

$products = $DB->get('pifa_products','*',$condition,4);

$relatedProducts = handle_product_list($DB->toArray($products));
foreach($relatedProducts as $key => $val){
	$relatedProducts[$key]['Products_PriceX'] = json_decode($val['Products_price_rule'],true)[0][2];
}
$C = $DB->GetRS("users","Users_Company,Users_Logo","where Users_ID='".$UsersID."'");
$comment_aggregate = get_comment_aggregate($DB,$UsersID,$ProductsID);
include("skin/product.php");
?>