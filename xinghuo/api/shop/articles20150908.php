<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');

$base_url = base_url();
$shop_url = shop_url();
/*分享页面初始化配置*/
$share_flag = 1;
$signature = '';

if(isset($_GET["UsersID"])){
  $UsersID=$_GET["UsersID"];
}else{
  echo '缺少必要的参数';
  exit;
}

if(isset($_GET["CategoryID"])){
  $CategoryID=$_GET["CategoryID"];
}else{
  $CategoryID=0;
}

if(!empty($_SESSION[$UsersID."User_ID"])){
  $userexit = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."'");
  if(!$userexit){
    $_SESSION[$UsersID."User_ID"] = "";
  } 
}
$rsConfig = $DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$is_login = 1;
$owner = getOwner($DB,$UsersID);
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$owner = getOwner($DB,$UsersID);

if($owner['id'] != '0'){
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	$shop_url = $shop_url.$owner['id'].'/';
	//$order_filter_base .= '&OwnerID='.$owner['id'];
	//$page_url .= '&OwnerID='.$owner['id'];
}
//获取配置信息

$condition = "where Users_ID='".$UsersID."' and Article_Status=1";
if($CategoryID>0){
	$condition .= " and Category_ID=".$CategoryID;
}
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>常见问题</title>
 <link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
    <link href="/static/api/distribute/css/style.css" rel="stylesheet">
     <link href="/static/api/distribute/css/article.css" rel="stylesheet">
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/static/js/jquery-1.11.1.min.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>

<body>
<div class="wrap">
  <header class="bar bar-nav">
	<a href="javascript:history.back()" class="fa fa-2x fa-chevron-left grey pull-left"></a>
	<a href="/api/<?=$UsersID?>/shop/distribute/" class="fa fa-2x fa-sitemap grey pull-right"></a>
	<h1 class="title">常见问题</h1>
  </header>
  <div class="wrap">
	 <div class="container">
          	
        <div id="articles" class="row">    
       		<ul class="list-group">
			 <?php
				$i=0;
				$DB->getPage("shop_articles","*",$condition,$pageSize=10);
				while($item=$DB->fetch_assoc()){
					$i++;
			 ?>
             
			 <li class="list-group-item"><a href="<?=$shop_url?>article/<?=$item['Article_ID']?>/"><?php echo $i;?>、<?=$item['Article_Title']?></a></li>
			 <?php }?>
	   </ul>
    	</div>
     </div>
	 <?php $DB->showWechatPage($shop_url.'articles/'.($CategoryID>0 ? $CategoryID.'/' : '')); ?>
  </div>
</div>
<?php require_once('./distribute_footer.php');?> 
</body>
</html>
