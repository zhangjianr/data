<?php
/*	权限验证
*	$path 当前地址
*/


function check_right($path,$file,$file_all){
	global $DB;
	
	$my_dir = str_replace(dirname(dirname($path)).'/','',dirname($path));
	$my_file = basename($path, '.php');
	
	//商家权限
	$rsUsers = $DB->GetRs("users","*","where Users_ID='".$_SESSION["Users_ID"]."'");
	$Users_Right = json_decode($rsUsers['Users_Right'],true)?json_decode($rsUsers['Users_Right'],true):array();
	foreach ($Users_Right as $key=>$val){
		foreach($val as $k=>$v){
			$right[$key][$v] = $file_all[$key][$v];
		}
	}
	$my_users_right = array_merge($file,$right);
	//var_dump($my_users_right);

	//员工权限
	$role = $DB->GetRs("users_roles","*","where id='".$_SESSION["role_id"]."'");
	$employee_right = json_decode($role['role_right'],true)?json_decode($role['role_right'],true):array();
	foreach ($employee_right as $key=>$val){
		foreach($val as $k=>$v){
			$employee_rights[$key][$v] = $file_all[$key][$v];
		}
	}
	//var_dump($employee_rights);
	//例外
	if($my_dir == 'wechat'){
		if($my_file == 'renewal_record'){
			$my_dir = 'buy_record';
		}else{
			$my_dir = 'weixin';
		}
	}
	if($my_dir == 'shop'){
		if($my_dir == 'shop' && $my_file == 'shipping'){
			$my_dir = 'weixin';
		}else{
			foreach($file as $k=>$v){
				if(isset($file[$k][$my_file])){
					$my_dir = $k;
					break;
				}
			}
		}
	}
	
	//调度
	if(isset($file_all[$my_dir])){
		$moudle = $my_dir;
		$action = $my_file;
	}else{
		$action = $my_dir;
		if($action == 'scratch'){
			$action = 'sctrach';
		}
		foreach($file_all as $k=>$v){
			if(isset($file_all[$k][$action])){
				$moudle = $k;
				break;
			}else{
				$moudle = '';
			}
		}
	}
	//echo '标题'.$moudle.'--操作'.$action;
	
	//检测
	$style1 = "style='width:100%;text-align:center;background-color:#F1F2F7;height:500;line-height:500px;margin:0px;'";
	$num = rand(-30,30);

	$style2 = "style='border:3px solid red;width:400px;height:100px;line-height:100px;margin:200px auto;color:red;transform:rotate({$num}deg);-ms-transform:rotate({$num}deg);-moz-transform:rotate({$num}deg);-webkit-transform:rotate({$num}deg);-o-transform:rotate({$num}deg);'";
	//var_dump($employee_rights);
	
	if(isset($file_all[$moudle][$action])){
		if(!empty($my_users_right[$moudle])){
			if(!empty($employee_rights[$moudle])){
				if(!isset($employee_rights[$moudle][$action])){
					echo "<div class='un_access' ".$style1."><div ".$style2.">您暂未开通<b style='font-size:25px;'>".$file_all[$moudle][$action]."</b>权限,请联系管理员</div></div>";
					exit;
				}
			}else{
				echo "<div class='un_access' ".$style1."><div ".$style2.">您暂未开通<b style='font-size:25px;'>".$file_all[$moudle][$moudle]."</b>权限,请联系管理员</div></div>";
				exit;
			}
		}else{
			//echo $action.'++';
			 //var_dump($my_users_right[$moudle]);
			echo "<div class='un_access' ".$style1."><div ".$style2.">您暂未开通<b style='font-size:25px;'>".$file_all[$moudle][$moudle]."</b>权限,请联系管理员</div></div>";
			exit;
		}
	}
}
?>