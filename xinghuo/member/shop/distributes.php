<?php
//ini_set("display_errors", "On");
require_once($_SERVER["DOCUMENT_ROOT"] . '/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/Framework/Ext/mysql.inc.php');

$base_url = base_url();

if (empty($_SESSION["Users_Account"])) {
	header("location:/member/login.php");
}

$rsConfig = shop_config($_SESSION["Users_ID"]);
$dis_title_level = Dis_Config::get_dis_pro_title($_SESSION['Users_ID']);

$channel_config=Channel_Config::get_dis_channel_type($_SESSION['Users_ID']);
//var_dump($channel_config);

if (isset($_GET["action"])) {
	if ($_GET["action"] == "del") {

		$rs = $DB->GetRs("shop_distribute_account", "*", "where Users_ID='" . $_SESSION["Users_ID"] . "' and Account_ID=" . $_GET["AccountID"]);

		if ($rs) {
			//针对于此平台的两次删除bug
			$Flag = $DB->Set("user", array("Is_Distribute" => 0), "where Users_ID='" . $_SESSION["Users_ID"] . "' and User_ID=" . $rs["User_ID"]);
		}

		$Flag = $DB->Del("shop_distribute_account", "Users_ID='" . $_SESSION["Users_ID"] . "' and Account_ID=" . $_GET["AccountID"]);
		if ($Flag) {
			echo '<script language="javascript">alert("删除成功");window.location="' . $_SERVER['HTTP_REFERER'] . '";</script>';
		} else {
			echo '<script language="javascript">alert("删除失败");history.back();</script>';
		}
		exit;
	}


	if ($_GET["action"] == "pass") {


		$Flag = $DB->Set("shop_distribute_account", array("Is_Audit" => 1), "where Users_ID='" . $_SESSION["Users_ID"] . "' and Account_ID=" . $_GET["AccountID"]);
		if ($Flag) {
			echo '<script language="javascript">alert("审核通过");window.location="' . $_SERVER['HTTP_REFERER'] . '";</script>';
		} else {
			echo '<script language="javascript">alert("操作失败");history.back();</script>';
		}
		exit;
	}

	if ($_GET["action"] == "disable") {
		$Flag = $DB->Set("shop_distribute_account", array("status" => 0), "where Users_ID='" . $_SESSION["Users_ID"] . "' and Account_ID=" . $_GET["AccountID"]);
		if ($Flag) {
			echo '<script language="javascript">alert("禁用成功");window.location="' . $_SERVER['HTTP_REFERER'] . '";</script>';
		} else {
			echo '<script language="javascript">alert("操作失败");history.back();</script>';
		}

		exit;
	}

	if ($_GET["action"] == "enable") {
		$Flag = $DB->Set("shop_distribute_account", array("status" => 1), "where Users_ID='" . $_SESSION["Users_ID"] . "' and Account_ID=" . $_GET["AccountID"]);
		if ($Flag) {
			echo '<script language="javascript">alert("启用成功");window.location="' . $_SERVER['HTTP_REFERER'] . '";</script>';
		} else {
			echo '<script language="javascript">alert("操作失败");history.back();</script>';
		}
		exit;
	}

	//开启分销代理
	if ($_GET["action"] == "enable_agent") {
		$Flag = Dis_Account::find($_GET['account_id'])->update(array('Enable_Agent' => 1));
		if ($Flag) {
			echo '<script language="javascript">alert("启用成功");window.location="' . $_SERVER['HTTP_REFERER'] . '";</script>';
		} else {
			echo '<script language="javascript">alert("操作失败");history.back();</script>';
		}
		exit;
	}

	//关闭代理
	if ($_GET["action"] == "disable_agent") {
		$Flag = Dis_Account::find($_GET['account_id'])->update(array('Enable_Agent' => 0));
		if ($Flag) {
			echo '<script language="javascript">alert("启用成功");window.location="' . $_SERVER['HTTP_REFERER'] . '";</script>';
		} else {
			echo '<script language="javascript">alert("操作失败");history.back();</script>';
		}
		exit;
	}
} else {

	$_SERVER['HTTP_REFERER'] = $_SERVER['REQUEST_URI'];
}


//获取分销账号列表

$builder = Dis_Account::where('Users_ID', $_SESSION["Users_ID"]);
$page_base = base_url('member/shop/distributes.php');

$url_param = array();
if (!empty($_GET["search"])) {
	$url_param['search'] = 1;

	if (!empty($_GET["Keyword"]) && strlen(trim($_GET["Keyword"])) > 0) {
		$Shop_Name = $_GET["Keyword"];
		$builder->where("Shop_Name", "like", '%' . $Shop_Name . '%');

		$url_param['Keyword'] = $Shop_Name;
	}

	if ($_GET['is_root'] == 1) {
		$builder->where('invite_id', 0);
	}
	$url_param['is_root'] = $_GET['is_root'];
}
$builder->orderBy('Account_CreateTime', 'desc');


$account_list = $builder->paginate(10);


$account_list->setPath(base_url('member/shop/distributes.php'));
if (!empty($url_param)) {
	$account_list->appends($url_param);
}

$page_links = $account_list->render();

//生成用户drop_down数组
$inviter_ids = $account_list->map(function($account) {
			return $account->invite_id;
		})->toArray();
$user_ids = $account_list->map(function($account) {
			return $account->User_ID;
		})->toArray();
$User_IDS = array_unique(array_merge($user_ids, $inviter_ids));
$users = User::whereIn('User_ID', $User_IDS);
$user_list = $users->get(array('User_ID', 'User_NickName'))->toArray();
$user_dropdown = array();
foreach ($user_list as $key => $user) {
	$user_dropdown[$user['User_ID']] = $user['User_NickName'];
}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <link href='/static/css/global.css' rel='stylesheet' type='text/css' />
        <link href='/static/css/bootstrap.min.css' rel='stylesheet' type='text/css' />
        <link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
        <link href='/static/member/css/area_content.css' rel='stylesheet' type='text/css' />
        <script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
        <script type='text/javascript' src='/static/js/jquery.form.js'></script>
        <script type='text/javascript' src='/static/js/bootstrap.min.js'></script>
        <script type='text/javascript' src='/static/member/js/global.js'></script>
        <link href="//cdn.bootcss.com/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
        <script src="//cdn.bootcss.com/select2/4.0.0/js/select2.min.js"></script>
        <script src="//cdn.bootcss.com/select2/4.0.0/js/i18n/zh-CN.js"></script>
    </head>

    <body>
    <!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
    <![endif]-->
        <style type="text/css">
            body, html{background:url(/static/member/images/main/main-bg.jpg) left top fixed no-repeat;}
        </style>
        <div id="iframe_page">
            <div class="iframe_content">
                <link href='/static/member/css/distribute.css' rel='stylesheet' type='text/css' />
                <script type='text/javascript' src='/static/member/js/shop.js'></script>
                <div class="r_nav">
                    <ul>
                        <li class="cur"> <a href="distributes.php">分销账号管理</a> </li>
                        <li class=""><a href="distribute_record.php">分销记录</a></li>
                        <li class=""><a href="withdraw_record.php">提现记录</a></li>
                        <li class=""><a href="distribute_title.php">爵位设置</a></li>
						<li class=""><a href="channel_config.php">渠道设置</a></li>
                        <li class=""><a href="withdraw_method.php">提现方法管理</a></li>
                    </ul>
                </div>
                <link href='/static/js/plugin/lean-modal/style.css' rel='stylesheet' type='text/css' />
                <script language="javascript">
					var base_url = '<?= $base_url ?>';
					$(document).ready(function () {
						shop_obj.distribute_init();
					});
                </script>
                <div id="update_post_tips"></div>
                <div>

                </div>
                <div id="user" class="r_con_wrap">

                    <form class="search" id="search_form" method="get" action="?">
                        <span> 关键字：</span>
                        <input type="text" name="Keyword" value="" class="form_input" size="15" />
                        <span>&nbsp;类型&nbsp;</span>
                        <select name="is_root">
                            <option value="0">&nbsp;&nbsp;全部&nbsp;&nbsp;</option>
							<?php if (!empty($_GET['is_root']) && $_GET['is_root'] == 1): ?>
								<option value="1" selected>&nbsp;&nbsp;根店&nbsp;&nbsp;</option></select>
						<?php else: ?>
							<option value="1">&nbsp;&nbsp;根店&nbsp;&nbsp;</option></select>
						<?php endif; ?>
                        &nbsp;&nbsp;
                        <input type="hidden" name="search" value="1" />
                        <input type="submit" class="search_btn" value=" 搜索 " />
						<a href="/member/user/user_dom.php" class="search_btn" style="padding: 3px 10px;border-radius: 5px;">修改上下级关系</a>
                    </form>
                    <table width="100%" align="center" border="0" cellpadding="5" cellspacing="0" class="r_con_table">
                        <thead>
                            <tr>
                                <td width="5%" nowrap="nowrap">序号</td>
                                <td width="5%" nowrap="nowrap">推荐人</td>
                                <td width="8%" nowrap="nowrap">店名</td>
                                <td width="5%" nowrap="nowrap">佣金余额</td>    
                                <td  width="5%" nowrap="nowrap">审核状态</td>
                                <td width="6%" nowrap="nowrap">总收入</td>
                                <td width="6%" nowrap="nowrap">销售额</td>
                                <td width="5%" nowrap="nowrap">爵位</td>
                                <td width="5%" nowrap="nowrap">类型</td>
                                <td width="5%" nowrap="nowrap">加入时间</td>
                                <td width="5%" nowrap="nowrap">状态</td>
                                <td width="8%" nowrap="nowrap" class="last"><strong>操作</strong></td>
                            </tr>
                        </thead>
                        <tbody>
							<?php
							$account_array = $account_list->toArray();
							foreach ($account_array['data'] as $key => $account) {
								?>


								<tr UserID="<?= $account['User_ID'] ?>">
									<td nowarp="nowrap"><?= $account['User_ID'] ?></td>  
									<td nowrap="nowraqp">

										<?php
										if ($account['invite_id'] == 0) {
											$inviter_name = '来自总店';
										} else {
											$inviter_name = !empty($user_dropdown[$account['invite_id']]) ? $user_dropdown[$account['invite_id']] : '信息缺失';
										}
										?>

										<span><?= $inviter_name ?></span>
									</td>
									<td nowarp="nowrap" field=1><?= $account['Shop_Name'] ?></td>
									<td nowarp="nowrap">&yen;<?= round_pad_zero($account['balance'], 2) ?></td>
									<td nowarp="nowrap"><?= $account['Is_Audit'] ? '已通过' : '未通过' ?></td>
									<td nowarp="nowrap">&yen;<?= round_pad_zero($account['Total_Income'], 2) ?></td>
									<td nowrap="nowrap">&yen;<?= round_pad_zero($account['Total_Sales'], 2) ?>元</td>
									<td nowrap="nowrap"><?= !empty($account['Professional_Title']) ? $dis_title_level[$account['Professional_Title']]['Name'] : '无'; ?></td>
									<td nowrap="nowrap">
										<?php  $arr = $DB->GetRs("shop_dis_agent_areas","*", " where Account_ID =" .$account['User_ID']);?>
										<?php if(empty($arr)):?>
										<?= !empty($account['Channel_Type']) ? (isset($channel_config[$account['Channel_Type']]['Name'])?$channel_config[$account['Channel_Type']]['Name']:'该类型已删除') : '普通分销商'; ?>
										<?php else:?>
										<?php echo $arr['area_name'];?>
										<?php endif;?>
									</td>
									<td nowrap="nowrap"><?php echo date("Y-m-d H:i:s", $account['Account_CreateTime']) ?></td>
									<td nowrap="nowrap">
										<?php if ($account['status'] == 1): ?>
											<img src="/static/member/images/ico/yes.gif"/>
										<?php else: ?>
											<img src="/static/member/images/ico/no.gif"/>
										<?php endif; ?>

									</td>
									<td nowrap="nowrap" class="last">
										<!-- 代理开关begin -->

										<?php if ($rsConfig['Dis_Agent_Type'] != 0): ?>
											<?php if ($rsConfig['Dis_Agent_Type'] == 1): ?>
												<?php if ($account['invite_id'] == 0): ?>
													<?php if ($account['Enable_Agent'] == 0): ?>
														<a href="<?= base_url('member/shop/distributes.php?action=enable_agent&account_id=' . $account['Account_ID']) ?>"/>开启代理</a>|
													<?php else: ?>
														<a href="<?= base_url('member/shop/distributes.php?action=disable_agent&account_id=' . $account['Account_ID']) ?>"/>关闭代理</a>|
													<?php endif; ?>
												<?php endif; ?>
											<?php elseif ($rsConfig['Dis_Agent_Type'] == 2) : ?>
												<a class="agent_info" agent-id="<?= $account['Account_ID'] ?>" href="javascript:void(0)"/>代理信息</a>|
											<?php elseif ($rsConfig['Dis_Agent_Type'] == 3) : ?>
												<a class="channel_info" agent-id="<?= $account['Account_ID'] ?>" href="javascript:void(0)" />渠道推广</a>|
											<?php elseif ($rsConfig['Dis_Agent_Type'] == 4) : ?>
											<a class="agent_info" agent-id="<?= $account['Account_ID'] ?>" href="javascript:void(0)"/>代理信息</a>|
											<a class="channel_info" agent-id="<?= $account['Account_ID'] ?>" href="javascript:void(0)" />渠道推广</a>|
											<?php endif; ?>
										<?php endif; ?>  
										<!-- 代理开关end -->  
										<?php if ($account['Is_Audit'] == 0): ?>
											<a href="?action=pass&AccountID=<?= $account['Account_ID'] ?>">通过</a>
										<?php endif; ?>
										<?php if ($account['status'] == 1): ?>
											<a href="?action=disable&AccountID=<?= $account['Account_ID'] ?>" onClick="if (!confirm('禁用后此分销商不可分销,你确定要禁用么？')) {
																return false
															}
															;"></a>
											<a href="ds_account_posterity.php?User_ID=<?= $account['User_ID'] ?>" >下属</a>
										</td>
									<?php else: ?>
								<a href="?action=enable&AccountID=<?= $account['Account_ID'] ?>" title="启用" >启用</a></td>
							<?php endif; ?>

							</tr>
						<?php } ?>

                        </tbody>
                    </table>

                    <div class="page"><?= $page_links ?></div>


                </div>
            </div>



        </div>
    </div>

    <!-- 代理信息modal begin -->
    <div class="container">
        <div class="row">

            <div class="modal"  role="dialog" id="agent-info-modal">
                <div class="modal-dialog">

                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h5 class="modal-title" id="mySmallModalLabel">代理信息</h5>
                        </div>
                        <div class="modal-body">
                            <p>正在加载中...</p>
                            <div class="clearfix"></div>
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-default" id="confirm_dis_area_agent_btn">确定</a>
                            <a class="btn btn-danger" id="cancel_shipping_btn close" data-dismiss="modal">取消</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- 代理信息modal end -->

    <!-- 一级锁定modal begin -->
    <div class="container">
        <div class="row">

            <div class="modal fade bs-example-modal-sm"  role="dialog" id="channel-info-modal">
                <div class="modal-dialog">

                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h5 class="modal-title" id="mySmallModalLabel">渠道推广</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">渠道类型</label>
								<input type="hidden" class="form-control" id="account_id" value="">
								<select class="form-control" id="channel-type">
								<option value="0">普通分销商</option>
								<?php foreach($channel_config as $k=>$v){?>
								<option value="<?=$k?>"><?=$v['Name']?></option>
								<?php }?>
								</select>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-default" id="confirm_channel_type_btn">确定</a>
                            <a class="btn btn-danger" id="cancel_shipping_btn close" data-dismiss="modal">取消</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- 代理信息modal end -->

</body>
</html>