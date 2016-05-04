<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/lib_products.php');

//ini_set("display_errors","On");

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


if(isset($_GET["ProductsID"]))
{
	$ProductsID=$_GET["ProductsID"];
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

$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
//获取此产品
$rsProducts = $DB->GetRs("shop_products","*","where Users_ID='".$UsersID."' and Products_SoldOut=0 and Products_ID=".$ProductsID);
if(!$rsProducts){
	echo "此商品不存在或已下架";
	exit;
}
$JSON = json_decode($rsProducts['Products_JSON'],TRUE);

$rsProducts  = handle_product($rsProducts);


//产品详情
$rsProducts["Products_Description"] = str_replace('&quot;','"',$rsProducts["Products_Description"]);
$rsProducts["Products_Description"] = str_replace("&quot;","'",$rsProducts["Products_Description"]);
$rsProducts["Products_Description"] = str_replace('&gt;','>',$rsProducts["Products_Description"]);
$rsProducts["Products_Description"] = str_replace('&lt;','<',$rsProducts["Products_Description"]);

//查看此用户是否有未领取优惠券
$un_accept_coupon_num = 1;
if(!empty($_SESSION[$UsersID.'User_ID'])){
	$DB->query("SELECT COUNT(*) as num FROM user_coupon WHERE Users_ID='".$UsersID."' and Coupon_StartTime<".time()." and Coupon_EndTime>".time()." and user_coupon.Coupon_ID NOT IN ( SELECT Coupon_ID FROM user_coupon_record WHERE Users_ID='".$UsersID."' and User_ID = ".$_SESSION[$UsersID."User_ID"]." ) order by Coupon_CreateTime desc");
	$rs_unaccept_count = $DB->fetch_assoc();

	$un_accept_coupon_num = $rs_unaccept_count['num']; 
}


/*若用户已经登陆，判断此商品是否被当前登陆用户收藏*/
$rsProducts['Products_IsFavourite'] = 0;

if(!empty($_SESSION[$UsersID."User_ID"])){
	$rsUser = $DB->GetRs("user","User_Level","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID']);
	$rsFavourites = $DB->getRs('user_favourite_products',"Products_ID","where User_ID='".$_SESSION[$UsersID."User_ID"]."' and Products_ID=".$ProductsID);
		
	if($rsFavourites != FALSE){
		$rsProducts['Products_IsFavourite'] = 1;
	}

}


//获取登录用户的用户级别及其是否对应优惠价

$rsUserConfig = $DB->GetRs("User_Config","UserLevel","where Users_ID='".$UsersID."'");
$discount_list = json_decode($rsUserConfig["UserLevel"],TRUE);

$cur_price = $rsProducts['Products_PriceX'];

if(!empty($discount_list)){
if(count($discount_list) >1 ){
	//计算出此商品的各级会员价
	foreach($discount_list as $key=>$item){
		if(empty($item['Discount'])){
			$item['Discount'] = 0;
		}
		$discount_price = $rsProducts['Products_PriceX']*(1-$item['Discount']/100);
		$discount_price = getFloatValue($discount_price,2);
		$discount_list[$key]['price'] =  $discount_price;
		$cur = 0;

		if(!empty($_SESSION[$UsersID.'User_ID'])){
			if($rsUser['User_Level'] == $key){
				$cur = 1;
				$cur_price = $discount_price;
			}else{
				$cur = 0;
			}
		}
		
		$discount_list[$key]['cur'] =  $cur;
	}

	array_shift($discount_list);
}else{
	array_shift($discount_list);
}

}

//必选属性价格
$properties = get_product_properties($ProductsID);  // 获得商品的规格和属性

$no_attr_price = $cur_price;
if(!empty($properties['spe'])){
	$specification = $properties['spe'];
	foreach($specification as $Attr_ID=>$item){
		if($item['Attr_Type'] == 1){
			foreach($item['Values'] as $k=>$v){
				if($k == 0){
					$cur_price += $v['price'];
				}
				
			}
		}
	}
}else{
	$specification = array();
}



/*获取本店满多少减多少条件*/
$man_list = json_decode($rsConfig['Man'],true);

$owner = getOwner($DB,$UsersID);
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$error_msg = pre_add_distribute_account($DB,$UsersID);
$owner = getOwner($DB,$UsersID);
if($owner['id'] != '0'){
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	$shop_url = $shop_url.$owner['id'].'/';
}

/*获取此商品thumb*/


$ImgPath = get_prodocut_cover_img($rsProducts);

$rsCommit=$DB->GetRs("user_order_commit","count(*) as num","where Users_ID='".$UsersID."' and Status=1 and Product_ID=".$ProductsID);
$commit = $rsCommit["num"];

//加入访问记录
$Data=array(
	"Users_ID"=>$UsersID,
	"S_Module"=>"shop",
	"S_CreateTime"=>time()
);
$DB->Add("statistics",$Data);
//调用模版
$share_link = $shop_url.'products_virtual/'.$ProductsID.'/';
require_once('../share.php');
$share_title = $rsProducts["Products_Name"];
if($owner['id'] != '0' && $rsConfig["Distribute_Customize"]==1){
	$share_desc = $owner['shop_announce'] ? $owner['shop_announce'] : $rsConfig["ShareIntro"];
	$share_img = strpos($owner['shop_logo'],"http://")>-1 ? $owner['shop_logo'] : 'http://'.$_SERVER["HTTP_HOST"].$owner['shop_logo'];
}else{
	$share_desc = $rsConfig["ShareIntro"];
	$share_img = strpos($rsConfig['ShareLogo'],"http://")>-1 ? $rsConfig['ShareLogo'] : 'http://'.$_SERVER["HTTP_HOST"].$rsConfig['ShareLogo'];
}

/*相关联产品，从此产品的分类中找出三个其他产品*/

$condition = "where Products_Category=".$rsProducts['Products_Category']." and Products_SoldOut=0 and Products_ID !=".$rsProducts['Products_ID'];
$condition .= " and Users_ID='".$UsersID."'";

$products = $DB->get('shop_products','*',$condition,3);

$relatedProducts = handle_product_list($DB->toArray($products));
$C = $DB->GetRS("users","Users_Company,Users_Logo","where Users_ID='".$UsersID."'");

$comment_aggregate = get_comment_aggregate($DB,$UsersID,$ProductsID);

$is_ad = 0;
include($rsConfig['Skin_ID']."/products_virtual.php");

?>