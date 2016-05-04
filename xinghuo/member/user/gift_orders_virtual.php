<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/shipping.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');

if(empty($_SESSION["Users_Account"])){
	header("location:/member/login.php");
}

if($_POST){
	$OrderID = $_POST["OrderID"];
	$Data=array(
		"Orders_Status"=>3,
		"Orders_FinishTime"=>time()
	);
	$Flag=$DB->Set("user_gift_orders",$Data,"where Users_ID='".$_SESSION["Users_ID"]."' and Orders_ID=".$OrderID);
	if($Flag){
		echo '<script language="javascript">alert("操作成功");window.location="gift_orders.php";</script>';
	}else{
		echo '<script language="javascript">alert("操作失败");history.back();</script>';
	}
}else{
	if(isset($_GET["code"])){
		$code = $_GET["code"];
	}else{
		echo '缺少必要的参数';
		exit;
	}
	$gift_orders = $DB->GetRs("user_gift_orders","*","where Orders_Code='".$code."'");
	if(!$gift_orders){
		echo '<script language="javascript">alert("此订单不存在");history.back();</script>';
		exit;
	}
	if($gift_orders["Orders_IsShipping"]==1){
		echo '<script language="javascript">alert("此订单不能验证");history.back();</script>';
		exit;
	}
	if($gift_orders["Orders_Status"]==3){
		echo '<script language="javascript">alert("此电子券已兑换，不能重复验证");history.back();</script>';
		exit;
	}
	if($gift_orders['Gift_ID']){
		$gift = $DB->GetRs("user_gift","*","where Gift_ID=".$gift_orders['Gift_ID']."");
	}
	$_STATUS_SHIPPING = array('<font style="color:#FF0000">待付款</font>','<font style="color:#03A84E">待发货</font>','<font style="color:#F60">待收货</font>','<font style="color:blue">已领取</font>','<font style="color:#999; text-decoration:line-through">&nbsp;已取消&nbsp;</font>');
	$_STATUS = array('','<font style="color:#FF0000">未领取</font>','','<font style="color:blue">已领取</font>');
}


?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/member/js/global.js'></script>
</head>

<body>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->
<style type="text/css">
body, html{background:url(/static/member/images/main/main-bg.jpg) left top fixed no-repeat;}
</style>
<div id="iframe_page">
  <div class="iframe_content">
    <link href='/static/member/css/user.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/member/js/user.js'></script>
    <div class="r_nav">
      <ul>
        <li class=""><a href="config.php">基本设置</a>
          <dl>
            <dd class="first"><a href="lbs.php">一键导航设置</a></dd>
          </dl>
        </li>
        <li class=""> <a href="user_list.php">会员管理</a>
          <dl>
            <dd class="first"><a href="user_level.php">会员等级设置</a></dd>
            <dd class=""><a href="user_profile.php">会员注册资料</a></dd>
            <dd class=""><a href="card_benefits.php">会员权利说明</a></dd>
            <dd class=""><a href="user_list.php">会员管理</a></dd>
          </dl>
        </li>
        <li class=""> <a href="card_config.php">会员卡设置</a></li>
        <li class=""> <a href="coupon_config.php">优惠券</a>
          <dl>
            <dd class="first"><a href="coupon_config.php">优惠券设置</a></dd>
            <dd class=""><a href="coupon_list.php">优惠券管理</a></dd>
            <dd class=""><a href="coupon_list_logs.php">优惠券使用记录</a></dd>
          </dl>
        </li>
        <li class="cur"> <a href="gift_orders.php">礼品兑换</a>
          <dl>
            <dd class="first"><a href="gift.php">礼品管理</a></dd>
            <dd class=""><a href="gift_orders.php">兑换订单管理</a></dd>
          </dl>
        </li>
        <li class=""><a href="business_password.php">商家密码设置</a></li>
        <li class=""><a href="message.php">消息发布管理</a></li>
      </ul>
    </div>
    <div id="gift_orders" class="r_con_wrap">
     <form id="orders_mod_form" method="post" action="?" class="r_con_form">
        <div class="rows">
          <label>礼品产品</label>
          <span class="input"><span class="tips"><?php echo $gift['Gift_Name'];?></span></span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label>礼品图片</label>
          <span class="input"><img src="<?php echo $gift['Gift_ImgPath'];?>" width="300" /></span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label>用户手机</label>
          <span class="input"><span class="tips"><?php echo $gift_orders['Address_Mobile'];?></span></span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label>状态</label>
          <span class="input"><span class="tips">
		  <?php
            echo $_STATUS[$gift_orders["Orders_Status"]];
		 ?>
          </span></span>
          <div class="clear"></div>
        </div>
        
        <div class="rows">
          <label></label>
          <span class="input">
          	<input type="submit" class="btn_green" name="submit_button" value="确认兑换" />
          </span>
          <div class="clear"></div>
        </div>
        <input type="hidden" name="OrderID" value="<?php echo $gift_orders["Orders_ID"];?>" />
      </form>
    </div>
  </div>
</div>
</body>
</html>