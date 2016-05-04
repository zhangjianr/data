<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}

$rsRules=$DB->GetRs("user_integral_rules","*","where Users_ID='".$UsersID."'");
$rsUser=$DB->GetRs("user","*","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID."User_ID"]);
if(!$rsRules){
	$content = "暂无内容";
}else{
	$content = htmlspecialchars_decode($rsRules["Rules"],ENT_QUOTES);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta content="telephone=no" name="format-detection" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/api/css/user.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/api/js/global.js'></script>
</head>

<body style="background:#FFF">
<div id="rules">
	<h2><span onClick="history.go(-1);"></span>积分规则</h2>
    <div class="description">
    	<?php echo $content;?>
    </div>
</div>
<?php require_once('footer.php'); ?>
</body>
</html>