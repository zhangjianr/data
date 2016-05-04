<?php
if(empty($_SESSION["Users_Account"])){
	header("location:/member/login.php");
}

$item = $DB->GetRs("user_integral_rules","*","where Users_ID='".$_SESSION["Users_ID"]."'");
if($_POST){
	$_POST['BriefDescription'] = htmlspecialchars($_POST['BriefDescription'], ENT_QUOTES);
	$Data=array(
		"Rules"=>$_POST['BriefDescription']
	);
	if($item){
		$Flag=$DB->Set("user_integral_rules",$Data,"where Users_ID='".$_SESSION["Users_ID"]."'");
	}else{
		$Data["Users_ID"] = $_SESSION["Users_ID"];
		$Flag=$DB->Add("user_integral_rules",$Data);
	}
	if($Flag){
		echo '<script language="javascript">alert("设置成功");window.location="integral_rule.php";</script>';
	}else{
		echo '<script language="javascript">alert("设置失败");history.back();</script>';
	}
	exit;
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
<link rel="stylesheet" href="/third_party/kindeditor/themes/default/default.css" />
<script type='text/javascript' src="/third_party/kindeditor/kindeditor-min.js"></script>
<script type='text/javascript' src="/third_party/kindeditor/lang/zh_CN.js"></script>
<script>
KindEditor.ready(function(K) {
	K.create('textarea[name="BriefDescription"]', {
		themeType : 'simple',
		filterMode : false,
		uploadJson : '/member/upload_json.php?TableField=user_gift',
		fileManagerJson : '/member/file_manager_json.php',
		allowFileManager : true,
		items : [
			'source', '|', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
			'removeformat', 'undo', 'redo', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|', 'emoticons', 'image', 'link' , '|', 'preview']
	});
})
</script>
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
        <li> <a href="gift_orders.php">礼品兑换</a>
          <dl>
            <dd class="first"><a href="gift.php">礼品管理</a></dd>
            <dd class=""><a href="gift_orders.php">兑换订单管理</a></dd>
          </dl>
        </li>
        <li class=""><a href="business_password.php">商家密码设置</a></li>
        <li class=""><a href="message.php">消息发布管理</a></li>
      </ul>
    </div>
    <div id="gift" class="r_con_wrap">
      <link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
      <script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script> 
      <script language="javascript">$(document).ready(user_obj.gift_edit_init);</script>
      <form id="rules_form" class="r_con_form" method="post" action="?">
        <div class="rows">
          <label>积分规则</label>
          <span class="input">
          <textarea name="BriefDescription" class="briefdesc" style="width:500px; height:300px;"><?php echo empty($item["Rules"]) ? '' : $item["Rules"];?></textarea>
          </span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label></label>
          <span class="input">
          <input type="submit" class="btn_green" name="submit_button" value="提交保存" /></span>
          <div class="clear"></div>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>