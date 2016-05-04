<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
$base_url = base_url();

if(isset($_GET["UsersID"]))
{
	$UsersID=$_GET["UsersID"];
}else
{
	echo '缺少必要的参数';
	exit;
}


if(empty($_SESSION[$UsersID."User_ID"]))
{
  $_SESSION[$UsersID."HTTP_REFERER"]="/api/".$UsersID."/shop/distribute/";
  header("location:/api/".$UsersID."/user/login/");
}


if(isset($_GET["UsersID"])){
  $UsersID=$_GET["UsersID"];
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

$shop_url = shop_url();

$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");

$owner = getOwner($DB,$UsersID);
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$owner = getOwner($DB,$UsersID);
if($owner['id'] != '0'){
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	$shop_url = $shop_url.$owner['id'].'/';
}

if(!empty($_GET["ProductID"])){
  $ProductsID = $_GET["ProductID"];	
  $rsProducts = $DB->GetRs("shop_products","*","where Users_ID='".$UsersID."' and Products_SoldOut=0 and Products_ID=".$ProductsID);
  if(!$rsProducts){
	  	echo '无此产品';
		exit();
  }else{
  	$product = handle_product($rsProducts);
  }

}else{
	echo '缺少必要的参数产品ID';
}

$ds_acount = $DB->GetRs("shop_distribute_account","*","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID."User_ID"]);

require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/weixin_qrcode.class.php');
$weixin_qrcode = new weixin_qrcode($DB,$UsersID);
$qrcode_path = $weixin_qrcode->get_qrcode("products_".$owner['id']."_".$ProductsID);

$ds_list = json_decode($product['Products_Distributes']);

$ds_money = $product['Products_PriceX']*$product['Products_Profit']*$ds_list[0]/10000;  //分销佣金

$share_link = $shop_url.'products/'.$ProductsID.'/';
require_once('../../share.php');
$share_title = $product['Products_Name'];
$share_desc = $product['Products_Name'];
$share_img =  $product['ImgPath'];


?>
<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=$product['Products_Name']?></title>
 <link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
    <link href="/static/api/distribute/css/style.css" rel="stylesheet">
     <link href="/static/api/distribute/css/goods_distribute.css" rel="stylesheet">
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/static/js/jquery-1.11.1.min.js"></script>
    <script type='text/javascript' src='/static/api/js/global.js?t=<?php echo time();?>'></script>
	
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>

<body>
<div class="wrap">
	<div class="container">
    	<h4 class="row text-center">分销商品</h4>
   </div>
   
   <?php if($ds_acount['status']): ?>
   		<div class="container">
        <div class="row" id="distribute_brief">
           <div class="col-xs-1"><span class="fa  golden fa-usd fa-3x"></span></div>
           <div class="col-xs-5"><p>分销佣金<span class="red"><?=$ds_money?></span>元<br/>
           已销售<span class="red"><?=$product['Products_Sales']?></span>件</p>
           </div>
        </div>
    
   	<div class="row">
      <div class="activity-image">
      	<img  width="100%" src="<?=$product['ImgPath']?>"/>
     	 <a class="deadline" href="">
        	<span class="product_name"><?=$product['Products_Name']?></span><br/>
        	<span class="price golden">&yen;<?=$product['Products_PriceX']?></span>&nbsp;&nbsp;&nbsp;&nbsp;
    	</a>
      </div>
    </div>
    
  
   </div>
   		<div class="container"> 
        <div class="row text-center">
        	
        	<img class="qrcode_image" src="<?=$qrcode_path?>" style="max-width:100%"/>
            
        </div>
      
  
    </div>
    	<footer class="footer">
      <div class="container">
      	<div class="button-panel">
     		 <button class="btn btn-default" id="distribute-btn">分销此产品</button>
      	</div>
      </div>
      
    </footer>
   <?php else: ?>
          <div class="container">
          		<div class="row">
                	<p>&nbsp;&nbsp;&nbsp;&nbsp;您的分销账号已被禁用,&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$share_link?>" class="red">返回</a></p>
                    
                </div>
          </div>
   <?php endif; ?>
</div>

 <?php require_once('../distribute_footer.php');?>    
 </body>
</html>
