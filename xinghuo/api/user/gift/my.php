<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}
$_SESSION[$UsersID."HTTP_REFERER"]="/api/user/gift/gift_my.php?UsersID=".$UsersID;
$is_login=1;
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');

$rsConfig=$DB->GetRs("user_config","*","where Users_ID='".$UsersID."'");
$rsUser=$DB->GetRs("user","*","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID."User_ID"]);
$_STATUS_SHIPPING = array('<font style="color:#FF0000">待付款</font>','<font style="color:#03A84E">待发货</font>','<font style="color:#F60">待收货</font>','<font style="color:blue">已领取</font>','<font style="color:#999; text-decoration:line-through">&nbsp;已取消&nbsp;</font>');
$_STATUS = array('','<font style="color:#FF0000">未领取</font>','','<font style="color:blue">已领取</font>');
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
<script type='text/javascript' src='/static/api/js/user.js'></script>
<script type='text/javascript'>
	var ajax_url = '/api/<?php echo $UsersID;?>/user/gift/ajax/';
	$(document).ready(function(){
		user_obj.gift_init();
	});
</script>
</head>

<body>
<div id="gift">
  <div class="my_integral"><span class="inte">我的积分： <font style="font-size:16px;"><?php echo $rsUser["User_Integral"];?></font> 分</span><span><a href="/api/<?php echo $UsersID;?>/user/rules/">积分规则&gt;&gt;</a></span><div class="clear"></div></div>
  <div class="t_list"> <a href="/api/<?php echo $UsersID ?>/user/gift/my/" class="c">我兑换的礼品</a> <a href="/api/<?php echo $UsersID ?>/user/gift/">积分兑换礼品</a> </div>
  <?php
	  $DB->get("user_gift`,`user_gift_orders","user_gift.Gift_Name,user_gift.Gift_ImgPath,user_gift.Gift_BriefDescription,user_gift_orders.Orders_ID,user_gift_orders.Orders_Status,user_gift_orders.Orders_Code,user_gift_orders.Orders_IsShipping,user_gift_orders.Orders_TotalPrice,user_gift_orders.Orders_Shipping,user_gift_orders.Orders_ShippingID,user_gift_orders.Orders_FinishTime","where user_gift.Gift_ID=user_gift_orders.Gift_ID and user_gift.Users_ID='".$UsersID."' and user_gift_orders.User_ID=".$_SESSION[$UsersID."User_ID"]." and user_gift_orders.Orders_Status<4 order by user_gift_orders.Orders_ID desc");
	  while($rsGift=$DB->fetch_assoc()){
	  echo '<div class="item">
    <h1 style="font-size:14px;">【'.$rsGift['Gift_Name'].'】</h1>
    <div class="d">
		<img src="'.$rsGift['Gift_ImgPath'].'" />
		<div class="others">';
	 if($rsGift["Orders_IsShipping"]==0){
		 echo '<p class="status">'.$_STATUS[$rsGift["Orders_Status"]].'</p>';
	 }else{
		 echo '<p class="status">'.$_STATUS_SHIPPING[$rsGift["Orders_Status"]].'</p>';
		 if($rsGift["Orders_Status"]==0){
			 echo '<a class="btns pay" href="/api/'.$UsersID.'/user/gift/payment/'.$rsGift["Orders_ID"].'/">付&nbsp;款</a>';
			 echo '<a class="btns concel" href="javascript:void(0);" ret="'.$rsGift["Orders_ID"].'">取&nbsp;消</a>';
		 }elseif($rsGift["Orders_Status"]==2){
			 echo '<a class="btns recieve" href="javascript:void(0);" ret="'.$rsGift["Orders_ID"].'">收&nbsp;货</a>';
		 }
	 }
	 echo '
		</div>
		<div class="clear"></div>
	</div>';
	if($rsGift["Orders_Status"]==2){
		$Shipping=json_decode($rsGift["Orders_Shipping"],true);
		if(!empty($Shipping["Express"])){
			echo '<h2 style="text-align:left; text-indent:5px; font-weight:normal">'.$Shipping["Express"].($rsGift["Orders_ShippingID"] ? '&nbsp;&nbsp;&nbsp;单号：'.$rsGift["Orders_ShippingID"] : '').'</h2>';
		}
	}
	
	if($rsGift["Orders_IsShipping"]==0){
		echo '<h2 style="text-align:left; text-indent:5px; font-weight:normal">兑换码：'.$rsGift["Orders_Code"].'</h2>';
	}
	echo '
  </div>';
	  }
  ?>
</div>
<?php require_once('../footer.php'); ?>
</body>
</html>