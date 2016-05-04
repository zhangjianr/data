<?php
 
if(empty($_SESSION["Users_ID"])){
	header("location:../login.php");
}
$DB->showErr=false;
	/* if(empty($_GET['id']) && empty($_POST['id'])){
		echo '<script language="javascript">alert("参数错误");window.location="javascript:history.back()";</script>';
	} */
	if(!empty($_GET['id'])){
		$employee = $DB->GetRs("users_employee","*","where id='".$_GET['id']."' and users_account='".$_SESSION["Users_Account"]."'");
	}
	$Province = !empty($employee['loc_province'])?$employee['loc_province']:0;
	$City = !empty($employee['loc_city'])?$employee['loc_city']:0;
	$Area = !empty($employee['loc_town'])?$employee['loc_town']:0;
	
	if(isset($_GET['act']) && $_GET['act']='delete'){
		if($DB->del('users_employee','id='.$_GET['ids'])){
			echo '<script language="javascript">alert("删除成功");window.location="employee_edit.php";</script>';
		}else{
			echo '<script language="javascript">alert("删除失败");window.location="javascript:history.back()";</script>';
		}
	}
	if(!empty($_POST)){
			if(empty($_POST["id"])){
				echo '<script language="javascript">alert("请选择员工");window.location="javascript:history.back()";</script>';
				exit();
			}
			if(empty($_POST["employee_name"])){
				echo '<script language="javascript">alert("登录帐号不能为空！");window.location="javascript:history.back()";</script>';
				exit();
			}
			/* if(empty($_POST["employee_passA"]) || empty($_POST["employee_passB"])){
				echo '<script language="javascript">alert("登录密码和确认密码都必须填写！");window.location="javascript:history.back()";</script>';
				exit();
			} */
			/* if($_POST["employee_passA"]!=$_POST["employee_passB"]){
				echo '<script language="javascript">alert("登录密码和确认密码不一致，请修改！");window.location="javascript:history.back()";</script>';
				exit();
			} */
			
			$Data = array(
				'employee_name'=>$_POST['employee_name'],
				'employee_login_name'=>$_POST['employee_login_name'],
				'employee_expiretime'=>strtotime($_POST['employee_expiretime']),
				'role_id'=>$_POST['role_id'],
				'status'=>$_POST['status'],
				'employee_note'=>$_POST['employee_note'],
				'update_time'=>time(),
				'isAbleArea'=>intval($_POST['isAbleArea'])
			);
			if($Data['isAbleArea'] == "1"){
				$Data['loc_province'] = intval($_POST['loc_province']);
				$Data['loc_city'] = intval($_POST['loc_city']);
				$Data['loc_town'] = intval($_POST['loc_town']);
				if(empty($Data['loc_province'])){
					echo '<script language="javascript">alert("请设置管理区域！");window.location="javascript:history.back()";</script>';
					exit();
				}
			}else{
				$Data['loc_province'] = "";
				$Data['loc_city'] = "";
				$Data['loc_town'] = "";
			}
			if(!empty($_POST["employee_passA"]) && !empty($_POST["employee_passB"])){
				$Data['employee_pass'] = md5($_POST['employee_passA']);
			}
			//var_dump($Data);
			if($DB->set('users_employee',$Data,' where id='.$_POST['id'].' and users_account="'.$_SESSION['Users_Account'].'"')){
				echo '<script language="javascript">alert("修改成功");window.location="javascript:history.back()";</script>';
			}else{
				echo mysql_error();
				echo '<script language="javascript">alert("修改失败");window.location="employee.php";</script>';
			}
	}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/admin/css/main.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/admin/js/global.js'></script>
<script charset="utf-8" src="/third_party/My97DatePicker/WdatePicker.js"></script>

<script type="text/javascript" src="/static/js/location.js"></script>
<script type="text/javascript" src="/static/js/area.js"></script>
<script type='text/javascript' src="/static/js/select2.js"></script>
<link rel="stylesheet" href="/static/css/select2.css"/>
<script type='text/javascript' src="/static/js/select2_locale_zh-CN.js"></script>
<style>
.right_top{font-size:14px; font-weight:bold; height:36px; line-height:36px; padding-left:15px; background:#fff}
.right_ul{padding-left:5px; padding-top:10px; background:#fff; list-style:none; margin:0px}
.right_ul li{height:28px; line-height:28px;}
</style>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->
</head>
<body>
<div id="iframe_page">
  <div class="iframe_content">
	<div class="r_nav">
		<ul>
			<li><a href="roles.php">创建角色</a></li>
			<li><a href="role_edit.php">角色信息</a></li>
			<li><a href="employee_add.php">添加员工</a></li>
			<li class="cur"><a href="employee_list.php">员工信息</a></li>
      </ul>
	</div>
    <div class="r_con_wrap">
	 <form class="r_con_form" method="post" action="?">
	 <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
       <td valign="top">
			
			<div class="rows">
                <label>员工称呼</label>
                <span class="input"><input type="text" name="employee_name" class="form_input" value='<?php echo !empty($employee['employee_name'])?$employee['employee_name']:''?>'/> <font class="fc_red">*</font></span>
                <div class="clear"></div>
            </div>
            <div class="rows">
                <label>登录帐号</label>
                <span class="input">
				<input type="text" name="employee_login_name" class="form_input" value='<?php echo !empty($employee['employee_login_name'])?$employee['employee_login_name']:''?>'/> <font class="fc_red">*</font>
				<input type="hidden" name="id" class="form_input" value='<?php echo !empty($employee['id'])?$employee['id']:'';?>'/>
				</span>
                <div class="clear"></div>
            </div>
            <div class="rows">
                <label>登录密码</label>
                <span class="input"><input type="password" name="employee_passA" class="form_input" /> <font class="fc_red">*</font></span>
                <div class="clear"></div>
            </div>
            
            <div class="rows">
                <label>到期时间</label>
                <span class="input">
                    <input type="text" name="employee_expiretime" style="Width:150px;" value="<?php echo !empty($employee['employee_expiretime'])?date("Y-m-d H:i:s",$employee['employee_expiretime']):''?>" onClick="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" readonly>
                </span>
                <div class="clear"></div>
            </div>
            <div class="rows">
                <label>权限</label>
                <span class="input">
                    <select name='role_id'>
						<option value=''>选择角色</option>
						<?php 	$condition = "where users_account='{$_SESSION['Users_Account']}'";
								$my_all_roles = $DB->Get('users_roles','',$condition);
								$my_role = array();
								while($r = $DB->fetch_assoc()){
									$my_role[] = $r;
								}
						?>
						<?php if(!empty($my_role)){
							$role_id = !empty($employee['role_id'])?$employee['role_id']:'';
							foreach($my_role as $key=>$val){
								$str = '';
								if($val['id'] == $role_id) $str = "selected";
								echo "<option value='".$val['id']."'".$str.">".$val['role']."</option>";
							}
						}?>				
					</select>
                </span>
                <div class="clear"></div>
            </div>
			
			<div class="rows">
                <label>管理区域</label>
                <span class="input">
                    <label><input name="isAbleArea" type="radio" value="0" <?php if(empty($employee['isAbleArea'])){echo "checked";}?> >全部区域</label>
                    <label><input name="isAbleArea" type="radio" value="1" <?php if($employee['isAbleArea'] == "1"){echo "checked";}?>>设置区域</label>
                </span>
                <div class="clear"></div>
            </div>
			<div class="rows" id="select_area" <?php if(empty($employee['isAbleArea'])){echo 'style="display:none"';}?>>
                <label>设置区域</label>
                <span class="input">
					<select id="loc_province" name="loc_province" style="width:150px;"></select>
					<select id="loc_city" name="loc_city" style="width:150px; margin-left: 10px"></select>
					<select id="loc_town" name="loc_town" style="width:150px;margin-left: 10px"></select>
                </span>
                <div class="clear"></div>
            </div>
			
			<div class="rows">
                <label>是否启用</label>
                <span class="input">
                    <label><input name="status" type="radio" value="1" <?php if(!empty($employee['status']) && $employee['status'] == 1) echo "checked";?> checked>启用</label>
                    <label><input name="status" type="radio" value="0" <?php if(isset($employee['status']) && $employee['status'] == 0) echo "checked";?>>禁用</label>
                </span>
                <div class="clear"></div>
            </div>
            
            <div class="rows">
                <label>描述</label>
                <span class="input"><textarea id="employee_note" name="employee_note" rows="5" style="width:200px"><?php echo !empty($employee['employee_note'])?$employee['employee_note']:''?></textarea></span>
                <div class="clear"></div>
            </div>
            <div class="rows">
                <label></label>
                <span class="input"><input type="submit" name="Submit" value="修改" class="submit">
				<?php echo $str = empty($employee['id'])?"<input type='reset' value='重置'></span>":"<a href='?act=delete&ids=".$employee['id']."' style='line-height:30px;height:30px;background-color:#DDD;border-radius:5px;width:60px;display:inline-block;cursor:pointer;text-align:center;'>删除</a>";?>
                  
                <div class="clear"></div>
            </div>
         
		</td>
		<td width="10">&nbsp;</td>
		<td width="440" style="border-left:1px #dddddd solid; padding:10px" valign="top">
         <div class="right_top">员工列表(点击修改)</div>
		
			<ul class="right_ul" style='margin-left:30px;'>
			<?php 
			
				$condition = "where users_account='{$_SESSION['Users_Account']}'";
				$my_all_roles = $DB->Get('users_roles','*',$condition);
				
				$my_role = array();
				while($r = $DB->fetch_assoc($my_all_roles)){
					$my_role[] = $r;
				}
			
				$emp = $DB->Get('users_employee','*',"where users_account='".$_SESSION['Users_Account']."'");
				echo mysql_error();
				$my_employee = array();
				while($r = $DB->fetch_assoc($emp)){
					$my_employee[] = $r;
				}
				$role = "无";
				if(!empty($my_employee)){
					foreach($my_employee as $key=>$val){
						foreach($my_role as $k=>$v){
							if($v['id'] == $val['role_id']){
								$role = $v['role'];
							}
						}
						echo '<li style="border-bottom:1px solid #DDD;"><a href="employee_edit.php?id='.$val['id'].'"><span style="display:inline-block;width:120px;margin-left:5px;">称呼：'.$val['employee_name'].'</span><span style="display:inline-block;width:120px;margin-left:5px;">账号：'.$val['employee_login_name'].' </span><span style="display:inline-block;width:120px;margin-left:5px;">角色：'.$role.'</span></a></li>';
					}
				}
			?>
		</ul>
        </td>
	   </tr>
      </table>
      </form>	  
    </div>
  </div>
</div>

<script>
$(function(){
	showLocation(<?=$Province?>,<?=$City?>,<?=$Area?>);
	$("input[name=isAbleArea]").click(function(){
		var isAbleArea = $(this).val();
		if(isAbleArea == 1){
			$("#select_area").show();
		}else{
			$("#select_area").hide();
		}
	});
})
function showLocation(province , city , town) {
	
	var loc	= new Location();
	var title	= ['选择省份' , '选择市' , '选择县/区'];
	$.each(title , function(k , v) {
		title[k]	= '<option value="">'+v+'</option>';
	})
	
	$('#loc_province').append(title[0]);
	$('#loc_city').append(title[1]);
	$('#loc_town').append(title[2]);
	
	$("#loc_province,#loc_city,#loc_town").select2();
	$('#loc_province').change(function() {
		$('#loc_city').empty();
		$('#loc_city').append(title[1]);
		loc.fillOption('loc_city' , '0,'+$('#loc_province').val());
		$('#loc_city').change()
		//$('input[@name=location_id]').val($(this).val());
	})
	
	$('#loc_city').change(function() {
		$('#loc_town').empty();
		$('#loc_town').append(title[2]);
		loc.fillOption('loc_town' , '0,' + $('#loc_province').val() + ',' + $('#loc_city').val());
		//$('input[@name=location_id]').val($(this).val());
	})
	
	$('#loc_town').change(function() {
		$('input[@name=location_id]').val($(this).val());
	})
	
	if (province) {
		loc.fillOption('loc_province' , '0' , province);
		$('#loc_province').change();
		if (city) {
			loc.fillOption('loc_city' , '0,'+province , city);
			$('#loc_city').change();
			if (town) {
				loc.fillOption('loc_town' , '0,'+province+','+city , town);
			}
		}
		
	} else {
		loc.fillOption('loc_province' , '0');
	}
		
}
</script>
</body>
</html>