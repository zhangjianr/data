<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/verifycode/verifycode.class.php');

if(isset($_SESSION['user_type'])){
	if(isset($_SESSION['employee_id']) && isset($_SESSION['employee_id'])){
		header("Location:/member/");
	}
}else{
	if(isset($_SESSION["Users_ID"])){
		header("Location:/member/");
	}
}

if(isset($_GET["action"])){

	if($_GET["action"] == "verifycode" && isset($_GET["t"])){
		wzwcode::$useNoise = true;
		wzwcode::$useCurve = true;
		wzwcode::entry();
	}
}

if($_POST){
	//开始事务定义
	$Flag=true;
	$msg="";
	mysql_query("begin");
	$verifycode = wzwcode::check($_POST["VerifyCode"]);

	if(!$verifycode){
		$Data=array(
			'status'=>4
		);
	}else{
		$employee = false;
		if(!empty($_POST['Account'])){
			if(isset($_POST['login_type']) && $_POST['login_type'] == 'employee'){
					$employee = true;
					$employee_users=$DB->GetRs("users_employee","*","where employee_login_name='".$_POST['Account']."' and status='1' and employee_pass='".md5($_POST["Password"])."'");
					
					if($employee_users){
						$role=$DB->GetRs("users_roles","*","where id='".$employee_users["role_id"]."' and status='1'");
							$Data=array(
								'status'=>1
							);
							$_SESSION['user_type'] = 'employee';
							$_SESSION['employee_id'] = $employee_users['id'];
							$_SESSION['employee_name'] = $employee_users['employee_name'];
							$_SESSION['role_id'] = $employee_users['role_id'];
							//$_SESSION['role_name'] = $role['role'];
							//$_SESSION['role_right'] = $role['role_right'];
					}else{
						$Data=array(
							'status'=>3
						);
						echo json_encode(empty($Data)?array('status'=>0,'msg'=>'请勿非法操作！'):$Data,JSON_UNESCAPED_UNICODE);
						exit;
					}
					$rsUsers=$DB->GetRs("users","","where Users_Account='".$employee_users["users_account"]."'");
				}else{
					$rsUsers=$DB->GetRs("users","","where Users_Account='".$_POST["Account"]."' and Users_Password='".md5($_POST["Password"])."'");
				}
		}else{
			$Data=array(
					'status'=>0
				);
		}

		if($rsUsers){
			if($rsUsers["Users_Status"]==0){
				$Data=array(
					'status'=>0
				);
			}else{
				if($rsUsers["Users_ExpireDate"]<time()){
					$Data=array(
						'status'=>2
					);
				}else{
					$Data=array(
						'status'=>1
					);
					$_SESSION["Users_ID"]=$rsUsers["Users_ID"];
					$_SESSION["Users_WechatToken"]=$rsUsers["Users_WechatToken"];
					$_SESSION["Users_Account"]=$rsUsers["Users_Account"];
					
				}
			}
		}else{
			$Data=array(
				'status'=>3
			);
		}
		if($Flag){
			mysql_query("commit");
		}else{
			mysql_query("roolback");
		}
	}
	echo json_encode(empty($Data)?array('status'=>0,'msg'=>'请勿非法操作！'):$Data,JSON_UNESCAPED_UNICODE);
	exit;
}

?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $SiteName;?></title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/member/js/global.js'></script>
</head>

<body>
<link href='/static/member/css/login.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/member/js/account.js'></script>
<script language="javascript">$(document).ready(account_obj.login_init);$(document).ready(account_obj.verifycode_init);</script>
<div class="login_box">
  <div class="tab_box">
    <h2 class="" onclick="window.location.href='/member/login.php';">登陆</h2>
    <div class="clear"></div>
    <div class="login_con tab_con_1">
        <form>
		    账&nbsp&nbsp&nbsp号：<input type="text" id="Account" name="Account" value="" class="name" /><label><input type="checkbox" style='width:20px;height:20px;margin-left:10px;' name="login_type" value="employee" />管理登陆</label></br>
		    
		   <!-- <label>管理账号：<input type="text" id="imployee" name="employee_login_name" value="" class="name" /><label style='color:red;margin-left:5px;'>无管理员可不填 </label></label><br>-->
			</label>密&nbsp&nbsp&nbsp码：<input type="password" id="Password" name="Password" value="" class="password" /></label>
			<p><img class="verifyimg" id="verifyimg" />验证码：<input type="text" name="VerifyCode" id="VerifyCode" value="" placeholder="验证码" class="verifycode"/>
				<div class="clear"></div>
			</p>
			<input type="submit" value="登陆" class="login_btn">
			<p class="findpwd"><a href="/member/findpwd.php">忘记密码?</a></p>
		</form>
        <div class="login_msg"></div>
	</div>
  </div>
  <div class="alpha"></div>
</div>
</body>
</html>