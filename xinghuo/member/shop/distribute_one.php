<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/include/helper/distribute.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/include/helper/url.php');


$base_url = base_url();

if (empty($_SESSION["Users_Account"])) {
	header("location:/member/login.php");
}
$UsersID = $_SESSION['Users_ID'];

//获取此合伙人晋级设置
$rsDsLvel = $DB->GetRs('shop_distribute_one', 'Pro_Title_Level', "where Users_ID='" . $_SESSION['Users_ID'] . "'");
$res_one = json_decode($rsDsLvel['Pro_Title_Level'], TRUE);

if ($_POST) {
	$Dis_List = array();
	$Dis_Pro_Title = $_POST['Dis_Pro_Title'];
	$data_one = array();
	foreach ($Dis_Pro_Title AS $dk=>$dval){
		if(empty($dval['Name']) || empty($dval['Saleroom']) || empty($dval['Group_Num']) || empty($dval['Bonus'])){
			unset($Dis_Pro_Title);
			break;
		}else{
			$data_one[$dk] = $dval;
		}
	}
	$data = array('Users_ID' => $UsersID, 'Pro_Title_Level' => json_encode($data_one, JSON_UNESCAPED_UNICODE));
	$Flag = $DB->Set('shop_distribute_one', $data, "where Users_ID='" . $UsersID . "'");
	echo '<script language="javascript">alert("编辑成功");history.back();</script>';
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
		<script type='text/javascript' src='/static/js/jquery.formatCurrency-1.4.0.js'></script>
		<script type='text/javascript' src='/static/member/js/global.js'></script>
		<link rel="stylesheet" href="/third_party/kindeditor/themes/default/default.css" />
		<script type='text/javascript' src="/third_party/kindeditor/kindeditor-min.js"></script>
		<script type='text/javascript' src="/third_party/kindeditor/lang/zh_CN.js"></script>
		<script type='text/javascript' src="/third_party/kindeditor/plugins/code/prettify.js"></script>
		<script type='text/javascript' src="/static/member/js/shop.js"></script>
		<script>

			var base_url = '<?= $base_url ?>';
			var Users_ID = '<?= $_SESSION['Users_ID'] ?>';

			KindEditor.ready(function (K) {

				editor = K.editor({
					uploadJson: '/member/upload_json.php?TableField=app_wedding',
					fileManagerJson: '/member/file_manager_json.php',
					showRemote: true,
					allowFileManager: true,
				});


				K('#ImgUpload_1').click(function () {

					editor.loadPlugin('image', function () {
						editor.plugin.imageDialog({
							imageUrl: K('#ImgPath_1').val(),
							clickFn: function (url, title, width, height, border, align) {
								K('#ImgPath_1').val(url);
								K('#ImgDetail_1').html('<img src="' + url + '" />');
								editor.hideDialog();
							}
						});
					});

				});

				K('#ImgUpload_2').click(function () {

					editor.loadPlugin('image', function () {
						editor.plugin.imageDialog({
							imageUrl: K('#ImgPath_2').val(),
							clickFn: function (url, title, width, height, border, align) {
								K('#ImgPath_2').val(url);
								K('#ImgDetail_2').html('<img src="' + url + '" />');
								editor.hideDialog();
							}
						});
					});

				});

				K('#ImgUpload_3').click(function () {

					editor.loadPlugin('image', function () {
						editor.plugin.imageDialog({
							imageUrl: K('#ImgPath_3').val(),
							clickFn: function (url, title, width, height, border, align) {
								K('#ImgPath_3').val(url);
								K('#ImgDetail_3').html('<img src="' + url + '" />');
								editor.hideDialog();
							}
						});
					});

				});

				K('#ImgUpload_4').click(function () {

					editor.loadPlugin('image', function () {
						editor.plugin.imageDialog({
							imageUrl: K('#ImgPath_4').val(),
							clickFn: function (url, title, width, height, border, align) {
								K('#ImgPath_4').val(url);
								K('#ImgDetail_4').html('<img src="' + url + '" />');
								editor.hideDialog();
							}
						});
					});

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

						<li class=""> <a href="distributes.php">分销账号管理</a> </li>
						<li class=""><a href="distribute_record.php">分销记录</a></li>
						<li class=""><a href="withdraw_record.php">提现记录</a></li>
						<li class=""><a href="distribute_title.php">爵位设置</a></li>
						<li class=""><a href="channel_config.php">渠道设置</a></li>
						<li class=""><a href="withdraw_method.php">提现方法管理</a></li>
						<li class="cur"><a href="distribute_one.php">合伙人晋级</a></li>
					</ul>
				</div>
				<link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
				<script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script>
				<script language="javascript">
			$(document).ready(function () {
				//	global_obj.config_form_init();
				shop_obj.dis_title_init();
			});
				</script>
				<div class="r_con_config r_con_wrap">

					<h1><strong>合伙人晋级设置</strong></h1>
					<span style="color:red;">(满足团队销售额或团队人数条件之一便可晋级,想设置几个级别，便可填写几条)</span>
					<div class="btn btn-success" onclick="add_one_form()">增加一行</div>

					<div id="distribute_title" class="r_con_config r_con_wrap">
						<form id="level_form" method="post" action="distribute_one.php">
							<table class="level_table" id="dis_pro_title_table" border="0" cellpadding="5" cellspacing="0">
								<thead>
									<tr>
										<td width="10%">序号</td>
										<td width="15%">称号名称</td>
										<td width="10%">晋级所需团队销售额<span style="color:red;">(仅可输入数字)</span></td>
										<td width="10%">晋级所需团队人数<span style="color:red;">(仅可输入数字)</span></td>
										<td width="5%">奖励额度<span style="color:red;">(%,占佣金的百分比)</span></td>

									</tr>
								</thead>
								<tbody id="dis_one_forms">
									
									<?php if (!empty($res_one)): ?>
										<?php foreach ($res_one AS $key=>$val) { ?>

											<tr  fieldtype="text">
												<td><?= $key ?></td>
												<td>
													<input class="form_input" value="<?= $val['Name'] ?>" name="Dis_Pro_Title[<?= $key ?>][Name]" type="text">
												</td>
												<td>
													<input class="form_input title_val" value="<?= $val['Saleroom'] ?>"  name="Dis_Pro_Title[<?= $key ?>][Saleroom]" type="text">
												</td>
												<td>
													<input class="form_input Group_Num" value="<?= !empty($val['Group_Num']) ? $val['Group_Num'] : '' ?>"  name="Dis_Pro_Title[<?= $key ?>][Group_Num]" type="text">
												</td>
												<td>
													<input class="form_input" value="<?= $val['Bonus'] ?>" name="Dis_Pro_Title[<?= $key ?>][Bonus]" type="text">
												</td>
											</tr>

										<?php } ?>
									<?php else: ?>
										<tr  fieldtype="text">
											<td>1</td>
											<td>
												<input class="form_input" value="" name="Dis_Pro_Title[1][Name]" type="text">
											</td>
											<td>
												<input class="form_input title_val" value=""  name="Dis_Pro_Title[1][Saleroom]" type="text">
											</td>
											<td>
												<input class="form_input Group_Num" value=""  name="Dis_Pro_Title[1][Group_Num]" type="text">
											</td>
											<td>
												<input class="form_input" value="" name="Dis_Pro_Title[1][Bonus]" type="text">
											</td>
										</tr>
									<?php endif;?>
								</tbody>
							</table>
							<div class="blank20"></div>
							<div class="submit">
								<input name="submit_button" value="提交保存" type="submit">
								<input name="reset_button"  value="清除内容" id="clear_form" type="button">
							</div>
							<input name="action" value="distribute_title" type="hidden">
						</form>
					</div>

				</div>
			</div>
		</div>
		<script>
			var i = <?php echo max(count($res_one),1); ?>;
			function add_one_form(){
				i++;
				var tpl = '<tr>\
								<td>'+i+'</td>\
								<td>\
									<input class="form_input" value="" name="Dis_Pro_Title['+i+'][Name]" type="text">\
								</td>\
								<td>\
									<input class="form_input title_val" value=""  name="Dis_Pro_Title['+i+'][Saleroom]" type="text">\
								</td>\
								<td>\
									<input class="form_input Group_Num" value=""  name="Dis_Pro_Title['+i+'][Group_Num]" type="text">\
								</td>\
								<td>\
									<input class="form_input" value="" name="Dis_Pro_Title['+i+'][Bonus]" type="text">\
								</td>\
							</tr>';
				$("#dis_one_forms").append(tpl);
			}
		</script>
	</body>
</html>