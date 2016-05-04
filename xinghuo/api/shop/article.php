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



//获取文章
$rsArticle = $DB->getRs("shop_articles","*","where Users_ID='".$UsersID."' and Article_Status=1 and Article_ID=".$_GET['ID']);
if(!$rsArticle){
	echo "不存在该文章";
	exit;
}
$rsArticle["Article_Hits"]++;
$DB->Set("shop_articles",array("Article_Hits"=>$rsArticle["Article_Hits"]),"where Users_ID='".$UsersID."' and Article_Status=1 and Article_ID=".$_GET['ID']);

$rsArticle['Article_Content'] = str_replace('&quot;','"',$rsArticle['Article_Content']);
$rsArticle['Article_Content'] = str_replace("&quot;","'",$rsArticle['Article_Content']);
$rsArticle['Article_Content'] = str_replace('&gt;','>',$rsArticle['Article_Content']);
$rsArticle['Article_Content'] = str_replace('&lt;','<',$rsArticle['Article_Content']);
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=$rsArticle['Article_Title']?></title>
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

<body style="background:#FFF;">
<div class="wrap">
	<header class="bar bar-nav">
  <a href="javascript:history.back()" class="fa fa-2x fa-chevron-left grey pull-left"></a>
  <a href="/api/<?=$UsersID?>/shop/distribute/" class="fa fa-2x fa-sitemap grey pull-right"></a>
  <h1 class="title"><?=$rsArticle['Article_Title']?></h1>
  
</header>
<style type="text/css">
#description .contents img{max-width:100%}
</style>
<div class="wrap">
	 <div class="container">
     	<div class="row" id="article">
        	
            <div style="display: block;" id="description">
				<div class="contents" style="margin:0px 8px; box-sizing:border-box">
        			<?=$rsArticle['Article_Content']?>
				</div>
			</div>
        	
        	
        </div>
     </div>
</div>  	
  
    
</div>

<?php require_once('./distribute_footer.php');?> 
 
 
</body>
</html>
