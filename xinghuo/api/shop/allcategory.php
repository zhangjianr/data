<?php 
//加载数据库类
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
//加载无限分类树处理类
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/General_tree.php');
//加载基本帮助函数
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');

$base_url = base_url();

if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}


$shop_url = $base_url.'api/'.$UsersID.'/shop/';

if(!empty($_SESSION[$UsersID."User_ID"])){
	$userexit = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."'");
	if(!$userexit){
		$_SESSION[$UsersID."User_ID"] = "";
	}	
}

if(!strpos($_SERVER['REQUEST_URI'],"mp.weixin.qq.com")){
	header("location:?wxref=mp.weixin.qq.com");
}

$Select_Cat_ID = !empty($_GET['categoryID'])?$_GET['categoryID']:0;

$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$rsCategory=$DB->Get("shop_category","Category_ID,Category_Name,Category_ParentID","where Users_ID='".$UsersID."' order by Category_Index desc");
$CategoryList = $DB->toArray($rsCategory);

//实例化通用树类
if(count($CategoryList) >0){
	$param = array('result'=>$CategoryList,'fields'=>array('Category_ID','Category_ParentID'));
	$generalTree = new General_tree($param);
	//生成分类树
	$categoryTree = $generalTree->leaf();
}else{
	$categoryTree = array();
}
//生成一级分类列表
$DB->get("shop_category","Category_Name,Category_ID","where Users_ID='".$UsersID."' and Category_ParentID=0 order by Category_Index asc");
		$ParentCategory=array();
		$i=1;
		while($rsPCategory=$DB->fetch_assoc()){
			$ParentCategory[$i]=$rsPCategory;
			$i++;
		}

$owner = getOwner($DB,$UsersID);
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');	
$owner = getOwner($DB,$UsersID);
if($owner['id'] != '0'){
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	$shop_url = $shop_url.$owner['id'].'/';
}	

//加入访问记录
$Data=array(
	"Users_ID"=>$UsersID,
	"S_Module"=>"shop",
	"S_CreateTime"=>time()
);
$DB->Add("statistics",$Data);
//调用模版
require_once('../share.php');
$C = $DB->GetRS("users","Users_Logo","where Users_ID='".$UsersID."'");
$share_title = $share_desc = "全部分类";
$share_img = 'http://'.$_SERVER["HTTP_HOST"].'/static/api/images/cover_img/shop.jpg';
$share_link = '';
include($rsConfig['Skin_ID']."/allcategory.php");

?>