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


//获取此用户分销账号信息
$rsAccount  = $DB->GetRs("shop_distribute_account","*","where Users_ID='".$UsersID."' and User_ID= '".$_SESSION[$UsersID.'User_ID']."'");
$rsConfig = $DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");

$before_edit_shop_name = $rsAccount['Shop_Name'];

if($_POST){
	
	
	$data = array();
	if($rsConfig['Distribute_Customize'] == 1){
		$data["Shop_Name"] = $_POST["Shop_Name"];
	}
	

	$data["Shop_Announce"] = $_POST["Shop_Announce"];
	$data["Is_Regeposter"] = 1;
	
	$condition = "Where Users_ID = '".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID'];	
	$Flag = $DB->Set('shop_distribute_account',$data,$condition);
	
	if($Flag){
		 
		 $owner = getOwner($DB,$UsersID);
		 if($owner['id'] != '0'){
			header("location:/api/".$UsersID."/shop/");
		 }
		  
	}
}

?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>修改资料</title>
 <link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
    <link href="/static/api/distribute/css/style.css" rel="stylesheet">
     <link href="/static/api/distribute/css/edit_info.css" rel="stylesheet">
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/static/js/jquery-1.11.1.min.js"></script>
	<script src="/static/api/distribute/js/distribute.js"></script>
    <script type="text/javascript">
		$(document).ready(function(){
			distribute_obj.init();
		});
    </script>
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
    	<div class="row page-title">
           <h4>&nbsp;&nbsp;&nbsp;&nbsp;修改资料</h4>
        </div>
		<div class="row">
        	<ul class="list-group" id="edit_info_panel">
  <form method="post" action="/api/<?=$UsersID?>/shop/distribute/edit_shop/"  id="edit_shop_form">
  <?php if($rsConfig['Distribute_Customize'] == 1): ?>
  <li class="list-group-item" >
  	 <label>店名</label>&nbsp;&nbsp;<input type="text" name="Shop_Name" value="<?=$rsAccount['Shop_Name']?>" placeholder="请输入您的店名" />
  </li>
  <?php endif;?>
  
	<li class="list-group-item" >
    <label id="annoce-label">自定<br />义分<br />享语&nbsp;&nbsp;&nbsp;&nbsp;</label>
    
    <textarea id="annoce-content" name="Shop_Announce"><?=$rsAccount['Shop_Announce']?></textarea>
  
  	
  </li>
 	<li class="list-group-item  text-center">

     <input type="submit" value="修改资料" class="btn btn-default" id="submit-btn"/>
  </li>


 </form>
     
</ul>
        
        </div>

    </div>
    
  	
  
    
</div>

<?php require_once('../distribute_footer.php');?> 
 
 
</body>
</html>
