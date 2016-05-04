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
  $UsersID = $_GET["UsersID"];
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

if(empty($_SESSION[$UsersID."User_ID"]))
{
  $_SESSION[$UsersID."HTTP_REFERER"]="/api/".$UsersID."/shop/distribute/";
  header("location:/api/".$UsersID."/user/login/");
}

if(!strpos($_SERVER['REQUEST_URI'],"mp.weixin.qq.com")){
  header("location:?wxref=mp.weixin.qq.com");
}


//银行账号列表
$rsMethods = $DB->Get("shop_user_withdraw_methods","*","where Users_ID='".$UsersID."' and User_ID= '".$_SESSION[$UsersID.'User_ID']."'");
$method_list = $DB->toArray($rsMethods);

$rsConfig =$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");

?>


<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>用户提现</title>
 <link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
    <link href="/static/api/distribute/css/style.css" rel="stylesheet">
     <link href="/static/api/distribute/css/cards.css" rel="stylesheet">
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/static/js/jquery-1.11.1.min.js"></script>
    <script type='text/javascript' src='/static/api/js/global.js'></script>
    <script src="/static/api/distribute/js/distribute.js"></script>
     <script language="javascript">
	

	var base_url = '<?=$base_url?>';
	var UsersID = '<?=$UsersID?>';
	
	$(document).ready(distribute_obj.bank_card_manage);

</script>


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>

<body>

<header class="bar bar-nav">
  <a href="javascript:history.back()" class="fa fa-2x fa-chevron-left grey pull-left"></a>
  <a href="/api/<?=$UsersID?>/shop/distribute/" class="fa fa-2x fa-sitemap grey pull-right"></a>
  <h1 class="title">我的提现方式</h1>
  
</header>

<div class="wrap">
 <div class="container">
    
  
  <div id="bank-card-list" class="row">
    <?php foreach($method_list as $key=>$item):?>
    <div class="item">
    
       <h1><i class="fa fa-credit-card grey"></i>&nbsp;&nbsp;<?=$item['Method_Name']?></h1>
       <p>
	   <span style="float:left"><?=$item['Account_Name']?></span><br/>
	   <?=$item['Account_Val']?></p>
       <?php if($item['Method_Type'] == 'bank_card'):?>
            <p><?=$item['Bank_Position']?></p>
       <?php endif;?>
       <p>
       
       <span style="float:right"><a class="remove-card"  data-method-id="<?=$item['User_Method_ID']?>" href="javascript:void(0)"><i class="red fa fa-remove"></i></a></span>
       </p>
    </div>
     <?php endforeach; ?>
  </div>
  </div>
</div>

 
<?php require_once('../distribute_footer.php');?> 
 
 
</body>
</html>

