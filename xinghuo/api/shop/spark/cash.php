<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/include/helper/distribute.php');
include $_SERVER["DOCUMENT_ROOT"] . '/include/library/weixin_pay_red.class.php';
include $_SERVER["DOCUMENT_ROOT"] . '/include/library/weixin_pay.class.php';

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
$rsConfig=$DB->GetRs("spark_config","*","where Users_ID='".$UsersID."'");
if(isset($_POST['op']) && $_POST['op'] == "cash"){
	$id = intval($_POST['id']);
	empty($id) ? message("参数不正确", "", "error") : "";
	
	//读取收益记录
	$resItem = $DB->GetR("spark_logs AS a LEFT JOIN user AS b ON a.User_ID = b.User_ID", "a.*, b.User_OpenID, b.User_Name", "WHERE a.Users_ID='{$UsersID}' AND a.User_ID='{$_SESSION[$UsersID."User_ID"]}' AND a.id='{$id}'");
	empty($resItem) ? message("记录不存在", "", "error") : "";
	($resItem['status'] > 0 && $resItem['status'] < 5) ? message("已经领取过了", "", "error") : "";
	($resItem['status'] == 9) ? message("正在审核，请耐心等待", "", "error") : "";
	
	if($rsConfig["payMethod"] == "1"){
		$Flag = $DB->Set("spark_logs","status=9","WHERE Users_ID='{$UsersID}' AND id='{$id}'");
		message("您的申请已提交请耐心等待结果");
	}else if($rsConfig["payMethod"] == "2"){
		$data = array(
			"Users_ID" => $UsersID,
			"Record_Sn" => $resItem['orderId'],
			"openid" => $resItem['User_OpenID'],
			"Record_Money" => $resItem['money']
		);
		if($resItem['money'] > "200"){
			message("红包金额不能超过200元，请联系客服手动发放", "", "success");
		}
		$pay = new weixin_pay_red($data);
		$payResult = $pay->startPay();
		if($payResult === TRUE){
			$Flag = $DB->Set("spark_logs","status=2","WHERE Users_ID='{$UsersID}' AND id='{$id}'");
			message("红包已发放，请注意查收".$payResult['return_msg'], "", "success");
		}else{
			message("领取失败：微信官方提示以下错误，如有疑问请咨询客服或稍后重试。\n【".$payResult['return_msg']."】", "", "error");
		}
	}else if($rsConfig["payMethod"] == "3"){
		$data = array(
			"Users_ID" => $UsersID,
			"Record_Sn" => $resItem['orderId'],
			"openid" => $resItem['User_OpenID'],
			"Record_Money" => $resItem['money'],
			"realname" => $resItem['User_Name'],
		);
		$pay = new weixin_pay($data);
		$payResult = $pay->startPay();
		if($payResult === TRUE){
			$Flag = $DB->Set("spark_logs","status=3","WHERE Users_ID='{$UsersID}' AND id='{$id}'");
			message("红包已发放，请在微信钱包零钱里面查看", "", "success");
		}else{
			message("领取失败：微信官方提示以下错误，如有疑问请咨询客服或稍后重试。\n【".$payResult['return_msg']."】", "", "error");
		}
	}else{
		message("商家尚未设置提现方法，请联系客服");
	}
}
//业务开始
$resMoney = $DB->GetS("spark_logs AS a LEFT JOIN user AS b ON a.buy_User_ID = b.User_ID", "a.*, b.User_NickName, b.User_HeadImg", "WHERE a.Users_ID='{$UsersID}' AND a.User_ID='{$_SESSION[$UsersID."User_ID"]}' ORDER BY a.createtime DESC");
?>
<!doctype html>
<html class="no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="email=no"/>
	<title>红包专区</title>
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
		<h3 class="">红包专区</h3>
	</div>
	<div class="level_content">
		<?php if(empty($resMoney)):?>
			<div class="zl_empty_data">
				<i class="fa fa-search"></i>
				<p>暂无数据</p>
			</div>
		<?php endif;?>
		<ul>
			<?php
				foreach($resMoney as $key=>$val):
					$User_HeadImg = empty($val['User_HeadImg']) ? "https://open.weixin.qq.com/zh_CN/htmledition/res/assets/res-design-download/icon64_appwx_logo.png" : $val['User_HeadImg'];
					$userName = empty($val['realName']) ? $val['User_NickName'] : $val['realName'];
					$userWechet = empty($val['nickName']) ? "暂无" : $val['nickName'];
					$userMobile = empty($val['mobile']) ? "暂无" : $val['mobile'];
			?>
			<li class="flex">
				<img src="<?=$User_HeadImg?>" />
				<div class="flex_1">
					<p class="text-overflow_1">昵称：<?=$userName?></p>
					<p>下属层级：<?=$val['level']?></p>
					<p>订单号：<?=$val['orderId']?></p>
					<p class="text-overflow_1">支付时间：<?php echo date("Y-m-d H:i",$val['createtime']);?></p>
				</div>
				<?php if($val['status'] > "0" && $val['status'] < "5"):?>
				<div class="cash_money disabled">
					<h6>￥<?=$val['money']?></h6>
					<h6>已领取</h6>
				</div>
				<?php elseif($val['status'] == "9"): ?>
				<div class="cash_money wait">
					<h6>￥<?=$val['money']?></h6>
					<h6>正在审核</h6>
				</div>
				<?php else: ?>
				<div class="cash_money"  onclick="getCash(<?=$val['id']?>,this)">
					<h6>￥<?=$val['money']?></h6>
					<h6>立即领取</h6>
				</div>
				<?php endif; ?>
					
			</li>
			<?php endforeach;?>
		</ul>
	</div>
<?php require_once('../distribute_footer.php'); ?>
	<script>
		function getCash(id, obj){
			$.post(
				"",
				{id : id, op : "cash"},
				function(data){
					if(data.type == "success"){
						$(obj).addClass("disabled");
						$(obj).removeAttr("onclick");
						$(obj).find("h6").eq(1).text("已领取");
						alert(data.msg);
					}else{
						alert(data.msg);
						location.reload();
					}
				},
				"json"
			);
		}
	</script>
</body>
</html>