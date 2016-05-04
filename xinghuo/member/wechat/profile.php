<?php 
if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}
if($_POST)
{
	$rsUsers = $DB->GetRs("users","*","where Users_ID='".$_SESSION["Users_ID"]."'");
	if(empty($_POST["OldPassword"])){
		echo '<script language="javascript">alert("请输入原密码！");window.location="javascript:history.back()";</script>';
	}
	if(md5($_POST["OldPassword"])!=$rsUsers["Users_Password"]){
		echo '<script language="javascript">alert("原密码不正确！");window.location="javascript:history.back()";</script>';
	}
	if(empty($_POST["NewPassword"])){
		echo '<script language="javascript">alert("请输入新密码！");window.location="javascript:history.back()";</script>';
	}
	if($_POST["NewPassword"]!=$_POST["ConfirmPassword"]){
		echo '<script language="javascript">alert("新密码与确认密码不一致！");window.location="javascript:history.back()";</script>';
	}	
	$Data=array(
		"Users_Password"=>md5($_POST['NewPassword'])
	);
	$Flag=$DB->Set("users",$Data,"where Users_ID='".$_SESSION["Users_ID"]."'");
	if($Flag){
		session_unset();
		echo '<script language="javascript">alert("修改成功，请重新登陆");window.location="/member/login.php";</script>';
	}else{
		echo '<script language="javascript">alert("修改失败！");window.location="/member/login.php";</script>';
	}
	
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
    <link href='/static/member/css/account.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/member/js/account.js'></script> 
    <script language="javascript">$(document).ready(account_obj.profile_init);</script>
    <div class="r_nav">
      <ul>
        <li class="cur"><a href="profile.php">修改密码</a></li>
      </ul>
    </div>
    <div id="profile" class="r_con_wrap">
      <form id="profile_form" class="r_con_form" action="?" method="post">
        <div class="rows">
          <label>旧密码</label>
          <span class="input">
          <input name="OldPassword" id="OldPassword" value="" type="password" class="form_input" size="40" maxlength="20" notnull>
          </span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label>新密码</label>
          <span class="input">
          <input name="NewPassword" id="NewPassword" value="" type="password" class="form_input" size="40" maxlength="20" notnull>
          </span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label>确认密码</label>
          <span class="input">
          <input name="ConfirmPassword" id="ConfirmPassword" value="" type="password" class="form_input" size="40" maxlength="20" notnull>
          </span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label></label>
          <span class="input">
          <input type="submit" class="btn_green" name="submit_button" value="修改密码" />
          </span>
          <div class="clear"></div>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>