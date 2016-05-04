<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');

if(isset($_GET["UsersID"])){
  $UsersID = $_GET["UsersID"];
}else{
  echo '缺少必要的参数';
  exit;
}

$base_url = base_url();
$shop_url = shop_url();
/*分享页面初始化配置*/
$share_flag = 1;
$signature = '';

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
$rsConfig =$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$rsUser = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]);
if($_POST){
	$data = array(
		"User_Name" => htmlspecialchars(trim($_POST['User_Name'])),
		"User_Mobile" => htmlspecialchars(trim($_POST['User_Mobile']))
	);
	$flag = $DB->Set("user",$data,"where User_ID=".$_SESSION[$UsersID."User_ID"]);
	if($flag !== false){
		$toUrl = "/api/".$UsersID."/shop/distribute/";
		echo '<script language="javascript">alert("设置成功");window.location="'.$toUrl.'";</script>';
	}
}
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>填写分销资料</title>
	<link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
	<link href="/static/api/distribute/css/style.css" rel="stylesheet">
	<link href="/static/api/distribute/css/apply_distribute.css" rel="stylesheet">
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="/static/js/jquery-1.11.1.min.js"></script>
	<script type='text/javascript' src='/static/js/jquery.validate.js'></script>
	<script type='text/javascript' src='/static/api/js/global.js'></script>
	<script src="/static/api/distribute/js/distribute.js"></script>
  
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script language="javascript">
	jQuery.extend(jQuery.validator.messages, {  
       	 	required: "必须填写",  
			email: "请输入正确格式的电子邮件",  
			url: "请输入合法的网址",  
			date: "请输入合法的日期",  
			dateISO: "请输入合法的日期 (ISO).",  
			number: "请输入合法的数字",  
			digits: "只能输入整数",  
			creditcard: "请输入合法的信用卡号",  
			equalTo: "请再次输入相同的值",  
			accept: "请输入拥有合法后缀名的字符串",  
			maxlength: jQuery.validator.format("请输入一个长度最多是 {0} 的字符串"),  
			minlength: jQuery.validator.format("请输入一个长度最少是 {0} 的字符串"),  
			rangelength: jQuery.validator.format("请输入一个长度介于 {0} 和 {1} 之间的字符串"),  
			range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),  
			max: jQuery.validator.format("请输入一个最大为 {0} 的值"),  
			min: jQuery.validator.format("请输入一个最小为 {0} 的值")  
	});
	var base_url = '<?=$base_url?>';
	var UsersID = '<?=$UsersID?>';
	$(document).ready(distribute_obj.apply_distribute);
</script>

</head>

<body>
<div class="wrap">
	<div class="container">
		<div class="row">
			<div class="apply-image">
			<img  width="100%" src="<?php echo $rsConfig["ApplyBanner"] ? $rsConfig["ApplyBanner"] : '/static/api/distribute/images/apply_distribute.png';?>" />
			</div>
		</div>
		<div class="row apply_title">
			<ul class="list-group" id="apply_form_panel">
				<form action="/api/<?=$UsersID?>/shop/distribute/profile/" method="post" class="form-horizontal" id="join-distribute-form"/>
					<input type="hidden" name="action"  value="join"/>
					<li class="list-group-item">
						<label>姓名</label>&nbsp;&nbsp;
						<input type="text" name="User_Name"  value="<?=$rsUser['User_Name']?>" placeholder="请输入你您的姓名" />
					</li>

					<li class="list-group-item">
						<label>手机</label>&nbsp;&nbsp;
						<input type="text" name="User_Mobile" value="<?=$rsUser['User_Mobile']?>" placeholder="请输入您的手机号码" />
					</li>

					<li class="list-group-item  text-center" style="margin:0px; padding:10px 0px">
						<button type="submit" class="btn btn-default">补全资料</button>
					</li>
				</form>
			</ul>
		</div>
	</div>
</div>

<?php require_once('../distribute_footer.php');?> 
 
</body>
</html>

