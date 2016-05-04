<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/include/helper/distribute.php');

/* 分享页面初始化配置 */
$share_flag = 1;
$signature = '';
$shop_url = shop_url();
if (isset($_GET["UsersID"])) {
	$UsersID = $_GET["UsersID"];
} else {
	exit('缺少必要的参数');
}
if (!empty($_SESSION[$UsersID . "User_ID"])) {
	$userexit = $DB->GetRs("user", "*", "where User_ID=" . $_SESSION[$UsersID . "User_ID"] . " and Users_ID='" . $UsersID . "'");
	if (!$userexit) {
		$_SESSION[$UsersID . "User_ID"] = "";
	}
}
$owner = getOwner($DB,$UsersID);
$is_login=1;
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');

//获取本店配置
$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$rsUser=$DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]);

//业务开始
//获取下级
$User_ID = !empty($_GET['User_ID']) ? trim($_GET['User_ID']) : $_SESSION[$UsersID . "User_ID"];
$resTree = getTree($DB, $UsersID, $User_ID);
$resMoney = $DB->GetS("spark_logs", "sum(money) as money,level", "WHERE Users_ID='{$UsersID}' AND User_ID='{$_SESSION[$UsersID . "User_ID"]}' GROUP BY level");
$level = !empty($_GET['level']) ? intval($_GET['level']) : 1;
foreach ($resMoney as $key => $value) {
	$Money[$value['level']] = $value['money'];
}
?>
<!doctype html>
<html class="no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="email=no"/>
	<title>我的家族</title>
	<link rel="stylesheet" type="text/css" href="/static/spark/css/common.css?<?=$cacheVer?>"/>
	<link rel="stylesheet" type="text/css" href="/static/css/font-awesome.css?<?=$cacheVer?>">
	<link rel="stylesheet" type="text/css" href="/static/spark/css/app.css?<?=$cacheVer?>"/>
	<script src="/static/spark/js/zepto.min.js"></script>
	<script src="/static/spark/js/slide_left.js"></script>
	<script src="/static/spark/js/app.js"></script>
</head>
<body style="background:#eee;">
	<div class="commonHead" style="background: #0085d0;color:#fff">
		<i class="fa fa-angle-left commonBack"></i>
		<h3 class="">我的家族</h3>
	</div>
	<div class="zl_select">
		<div class="zl_select_span">
			<a href="<?=$shop_url?>spark/level/1/<?=$User_ID?>/"><span class="<?php if($level == "1"):?>active<?php endif;?>">一级</span></a>
			<a href="<?=$shop_url?>spark/level/2/<?=$User_ID?>/"><span class="<?php if($level == "2"):?>active<?php endif;?>">二级</span></a>
			<a href="<?=$shop_url?>spark/level/3/<?=$User_ID?>/"><span class="<?php if($level == "3"):?>active<?php endif;?>">三级</span></a>
		</div>
	</div>
	<div class="level_content">
		<?php if(empty($resTree[$level])):?>
			<div class="zl_empty_data">
				<i class="fa fa-search"></i>
				<p>暂无数据</p>
			</div>
		<?php endif;?>
		<ul>
			<?php
				foreach($resTree[$level] as $key=>$val):
					$res = $DB->GetR("user AS a LEFT JOIN spark_order AS b ON a.User_ID = b.User_ID", "a.User_ID, a.User_NickName, a.User_HeadImg, a.User_CreateTime, b.realName,b.nickName, b.mobile", "WHERE a.Users_ID='{$UsersID}' AND a.User_ID='{$val['User_ID']} AND b.payCode = 1'");
					$User_HeadImg = empty($res['User_HeadImg']) ? "https://open.weixin.qq.com/zh_CN/htmledition/res/assets/res-design-download/icon64_appwx_logo.png" : $res['User_HeadImg'];
					$userName = empty($res['realName']) ? $res['User_NickName'] : $res['realName'];
					$userWechet = empty($res['nickName']) ? "暂无" : $res['nickName'];
					$userMobile = empty($res['mobile']) ? "暂无" : $res['mobile'];
			?>
			<li class="flex" onclick="window.location.href='<?=$shop_url?>spark/level/1/<?=$res['User_ID']?>/'">
				<img src="<?=$User_HeadImg?>" />
				<div class="flex_1">
					<p>昵称：<?=$userName?></p>
					<p>微信号：<?=$userWechet?></p>
					<p>手机号：<?=$userMobile?></p>
					<p>加入时间：<?php echo date("Y-m-d H:i",$res['User_CreateTime']);?></p>
				</div>
			</li>
			<?php endforeach;?>
		</ul>
	</div>
<?php require_once('../distribute_footer.php'); ?>
	<script>
		$(function(){
			$(".my_content ul li").eq(0).click(function(){
				if($(".spark_level").css("display") !== "none"){
					$(this).find("i").eq(1).removeClass("fa-angle-up").addClass("fa-angle-down");
					$(".spark_level").hide();
				}else{
					$(this).find("i").eq(1).removeClass("fa-angle-down").addClass("fa-angle-up");
					$(".spark_level").show();
				}
			});
			
			$(".my_content ul li").eq(1).click(function(){
				if($(".spark_money").css("display") !== "none"){
					$(this).find("i").eq(1).removeClass("fa-angle-up").addClass("fa-angle-down");
					$(".spark_money").hide();
				}else{
					$(this).find("i").eq(1).removeClass("fa-angle-down").addClass("fa-angle-up");
					$(".spark_money").show();
				}
			});
		});
	</script>
</body>
</html>