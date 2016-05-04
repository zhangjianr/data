<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
if(empty($_SESSION["Users_ID"])){
	header("location:../login.php");
}

//删除操作
if(isset($_GET['act']) && $_GET['act']='delete'){
	if($DB->del('users_employee','id='.$_GET['id'])){
		echo '<script language="javascript">alert("删除成功");window.location="employee_list.php";</script>';
	}else{
		echo '<script language="javascript">alert("删除失败");window.location="javascript:history.back()";</script>';
	}
}

//获取区域
$area_json = read_file($_SERVER["DOCUMENT_ROOT"].'/data/area.js');
$area_array = json_decode($area_json,TRUE);
$province_list = $area_array[0];

//搜索员工
$Keywords = "";
$lists=array();
$condition = "where users_account='{$_SESSION['Users_Account']}'";
if(!empty($_GET['Keywords'])){
	$Keywords = trim($_GET['Keywords']);
	$condition .= " AND (employee_name like '%{$Keywords}%' OR employee_login_name like '%{$Keywords}%') ";
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
			<form class="search" id="search_form" method="get" action="">
				关键字：
				<input name="Keywords" value="<?php echo $Keywords;?>" type="text" class="form_input" size="30"/>
				<input type="submit" class="search_btn" value="搜索" style="width: 80px;"/>
			</form>
			<br/>
			<div style="line-height: 35px;background: #3AA0EB;width: 120px;text-align: center;color: #fff;border-radius: 5px;">
				<a href="employee_add.php" style="color:#fff">
					添加员工
				</a>
			</div>
			<div class="b10">
			</div>
			<table border="0" cellpadding="5" cellspacing="0" class="r_con_table">
				<thead>
					<tr>
						<td nowrap="nowrap" width="6%">
							ID
						</td>
						<td nowrap="nowrap">
							姓名
						</td>
						<td nowrap="nowrap">
							账号
						</td>
						<td nowrap="nowrap">
							角色
						</td>
						<td nowrap="nowrap">
							管理地区
						</td>
						<td nowrap="nowrap">
							添加时间
						</td>
						<td nowrap="nowrap" class="last">
							操作
						</td>
					</tr>
				</thead>
				<tbody>
					<?php
						$DB->getPage("users_employee","*",$condition." order by id desc",10);
						while($r=$DB->fetch_assoc()){
							$lists[] = $r;
						}
						foreach($lists as $t){
							$roleName = '暂无角色';
							$roles = $DB->GetRs("users_roles","role","where id=".$t["role_id"]);
							if($roles){
								$roleName=$roles["role"];
							}
					?>
						<tr>
							<td nowrap="nowrap">
								<?php echo $t["id"] ?>
							</td>
							<td nowrap="nowrap">
								<?php echo $t["employee_name"];?>
							</td>
							<td nowrap="nowrap">
								<?php echo $t["employee_login_name"];?>
							</td>
							<td nowrap="nowrap">
								<?php echo $roleName; ?>
							</td>
							<td nowrap="nowrap">
								<?php
									if($t["isAbleArea"] == "1" && is_numeric($t["loc_province"])){
										$Province = '';
										if(!empty($t["loc_province"])){
											$Province = $province_list[$t["loc_province"]];
										}
										$City = '';
										if(!empty($t["loc_city"])){
											$City = " - ".$area_array['0,'.$t["loc_province"]][$t["loc_city"]];
										}

										$Area = '';
										if(!empty($t["loc_town"])){
											$Area = " - ".$area_array['0,'.$t["loc_province"].','.$t["loc_city"]][$t["loc_town"]];
										}
										echo $Province.$City.$Area;
									}else{
										echo '<span style="color:red;">全部区域</span>';
									}
									
								?>
							</td>
							<td nowrap="nowrap">
								<?php echo date("Y-m-d H:i:s",$t[ "create_time"]); ?>
							</td>
							<td class="last" nowrap="nowrap">
								<a href="employee_edit.php?id=<?php echo $t["id"];?>">
									<img src="/static/admin/images/ico/mod.gif" align="absmiddle" alt="修改"
									title="修改" />
								</a>
								&nbsp;
								<a href="?act=delete&id=<?php echo $t["id"]; ?>" title="删除"
								onClick="if(!confirm('删除后不可恢复，继续吗？')){return false};">
									<img src="/static/admin/images/ico/del.gif" align="absmiddle" alt="删除"
									title="删除" />
								</a>
							</td>
						</tr>
						<?php } ?>
				</tbody>
			</table>
			<div class="blank20">
			</div>
			<?php $DB->showPage(); ?>
		</div>

	</div>
</div>
</body>
</html>