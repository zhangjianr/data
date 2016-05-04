<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}
$_SESSION[$UsersID."HTTP_REFERER"]="/api/".$UsersID."/user/gift/";

$is_login=1;
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');

$rsConfig=$DB->GetRs("user_config","*","where Users_ID='".$UsersID."'");
$rsUser=$DB->GetRs("user","*","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID."User_ID"]);
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
</head>

<body>
<script language="javascript">$(document).ready(user_obj.gift_init);</script>
<div id="gift">
  <div class="my_integral"><span class="inte">我的积分： <font style="font-size:16px;"><?php echo $rsUser["User_Integral"];?></font> 分</span><span><a href="/api/<?php echo $UsersID;?>/user/rules/">积分规则&gt;&gt;</a></span><div class="clear"></div></div>
  <div class="t_list"> <a href="/api/<?php echo $UsersID ?>/user/gift/my/">我兑换的礼品</a> <a href="/api/<?php echo $UsersID ?>/user/gift/" class="c">积分兑换礼品</a> </div>
  <?php
	  $DB->query("SELECT Gift_ID,Gift_Name,Gift_ImgPath,Gift_Integral,Gift_Qty FROM user_gift WHERE Users_ID='".$UsersID."' and Gift_Qty>0 order by Gift_MyOrder asc");
	  while($rsGift=$DB->fetch_assoc()){
		  echo '<div class="item"><a href="/api/'.$UsersID.'/user/gift/detail/'.$rsGift["Gift_ID"].'/">
    <h1>【'.$rsGift['Gift_Name'].'】</h1>
    <div class="p"><div class="img"><img src="'.$rsGift['Gift_ImgPath'].'" /></div>
      <div class="get">详情</div>
    </div></a>
    <h2>兑换需<span>'.$rsGift['Gift_Integral'].'</span>积分，还剩<span>'.$rsGift['Gift_Qty'].'</span>件</h2>
  </div>';
	  }
  ?>
</div>
<?php require_once('../footer.php'); ?>
</body>
</html>