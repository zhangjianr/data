<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}
if(isset($_GET["GiftID"])){
	$GiftID = $_GET["GiftID"];
}else{
	echo '缺少必要的参数';
	exit;
}

$is_login=1;
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$rsConfig=$DB->GetRs("user_config","*","where Users_ID='".$UsersID."'");
$rsUser=$DB->GetRs("user","*","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID."User_ID"]);

$rsGift = $DB->GetRs("user_gift","*","where Users_ID='".$UsersID."' and Gift_ID=".$GiftID);
$item = $DB->GetRs("user_gift_orders","*","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID."User_ID"]." and Gift_ID=".$GiftID." and Orders_Status<>4");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta content="telephone=no" name="format-detection" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>积分兑换</title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/api/css/user.css?t=<?php echo time();?>' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/api/js/global.js'></script>
</head>

<body>
<div id="gift_detail">
 <h1>项目详情</h1>
 <div class="detail_info">
  <div class="img"><img src="<?php echo $rsGift["Gift_ImgPath"];?>" /><span><?php echo $rsGift["Gift_Name"];?> ( 还剩<?php echo $rsGift["Gift_Qty"];?>份 )</span></div>
  <div class="btns">
	<?php if($item){?>
	<span>您已兑换</span>
	<?php }else{?>
		<?php if($rsUser["User_Integral"]>=$rsGift["Gift_Integral"]){?>
		<a href="/api/<?php echo $UsersID ?>/user/gift/order/<?php echo $rsGift["Gift_ID"];?>/">去兑换</a>
		<?php }else{?>
		<span>积分不足</span>
		<?php }?>
	<?php }?>
	<?php echo $rsGift["Gift_Integral"];?><font style="color:#999; font-size:12px;">&nbsp;积分</font>
  </div>
 </div>
 <div class="detail_decription">
  <?php echo htmlspecialchars_decode($rsGift["Gift_BriefDescription"],ENT_QUOTES)?>
 </div>
</div>
<?php require_once('../footer.php'); ?>
</body>
</html>