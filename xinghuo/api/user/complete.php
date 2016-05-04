<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}
if(!isset($_SESSION[$UsersID."User_ID"])){
	echo '缺少必要的参数';
	exit;
}

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
<title>会员完善资料</title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/api/css/user.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/api/js/global.js'></script>
<script type='text/javascript' src='/static/api/js/user.js'></script>
</head>
<style>
html,body{background:#FFF}
</style>
<body>
<div id="complete_header">
  <div class="headerimg"><img src="<?php echo $rsUser["User_HeadImg"];?>" /><br /><?php echo $rsUser["User_NickName"]?></div>
</div>
<script language="javascript">$(document).ready(user_obj.user_complete_init);</script>
<div id="complete">
  <form id="complete_form">
    <h2>完善资料</h2>
	<div class="input">
      <input type="text" name="Name" value="<?php echo $rsUser["User_NickName"];?>" maxlength="10" placeholder="您的姓名(必填)" notnull />
    </div>    
	<div class="input">
      <input type="tel" name="Mobile" value="" maxlength="11" placeholder="手机号码(必填)" pattern="[0-9]*" notnull />
    </div>    
    <div class="submit">
      <input type="button" class="submit_btn" value="提交" />      
      <input type="hidden" name="action" value="complete" />
	  <input type="hidden" id="httphref" value="<?php echo $_SESSION[$UsersID."HTTP_REFERER"];?>" />
	</div>
  </form>
</div>
</body>
</html>