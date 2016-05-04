<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/include/helper/distribute.php');
include $_SERVER["DOCUMENT_ROOT"] . '/include/library/traffic.class.php';

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
	$userexit = $DB->GetRs("user", "*", "where User_ID='{$_SESSION[$UsersID . "User_ID"]}' and Users_ID='{$UsersID}'");
	if (!$userexit) {
		$owner = getOwner($DB,$UsersID);
		$is_login=1;
		require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
	}
}

//获取本店配置
$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='{$UsersID}'");
$rsUser=$DB->GetRs("user","*","where User_ID= '{$_SESSION[$UsersID."User_ID"]}'");

//业务开始
if (!empty($_POST['op'])) {
	$sparkConfig=$DB->GetRs("spark_config","*","where Users_ID='".$UsersID."'");
	if(empty($sparkConfig['traAccount']) || empty($sparkConfig['traAppkey'])){
		rerurnData("商家流量接口配置错误，请联系商家客服", "", "error");
	}
	if($_POST['op'] == "check"){
		$traffic = new traffic();
		if($traffic === FALSE){
			rerurnData("商家流量接口配置错误，请联系商家客服", "", "error");
		}
		$mobileType = $traffic->getMobileType($_POST['mobile']);
		$resPackage = $DB->GetS("spark_traffic", "serviceId,package,packageName,priceX", "WHERE Users_ID='{$UsersID}' AND serviceId = '{$mobileType}' AND priceX > 0 ORDER BY serviceId ASC,packagePrice ASC");
		if(!empty($resPackage)){
			rerurnData($resPackage, $mobileType, "success");
		}else{
			rerurnData("商家暂无添加流量包", "", "error");
		}
	}else if($_POST['op'] == "submit"){
		if(empty($_POST['type']) || empty($_POST['package']) || empty($_POST['mobile'])){
			rerurnData("参数错误，请认真检查后提交", "", "error");
		}
		$type = trim($_POST['type']);$package = trim($_POST['package']);
		$trafficItem = $DB->GetRs("spark_traffic", "*", "WHERE Users_ID = '{$UsersID}' AND serviceId = '{$type}' AND package = '{$package}'");
		$orderData = array(
			"Users_ID"			=> $UsersID,
			"User_ID"			=> $_SESSION[$UsersID . "User_ID"],
			"mobile"			=> htmlspecialchars(trim($_POST["mobile"])),
			"trafficId"			=> $trafficItem['id'],
			"type"				=> $trafficItem['type'],
			"package"			=> $trafficItem['package'],
			"name"				=> $trafficItem['name'],
			"priceY"			=> $trafficItem['priceY'],
			"priceX"			=> $trafficItem['priceX'],
			"createtime"		=> time()
		);
		$FlagA = $DB->Add("spark_traffic_order", $orderData);
		$newId = $DB->insert_id();
		$orderId = "TRA".date("YmdHi",$orderData['createtime']).$newId;
		$FlagB=$DB->Set("spark_traffic_order", array("orderId"=>$orderId)," WHERE id='{$newId}'");
		if($FlagA && $FlagB){
			rerurnData("订单提交错误，请重试或联系客服", "/pay/wxpay2/pay.php?UsersID=" . $UsersID . "&orderId=" . $orderId, "success");
		}else{
			rerurnData("订单提交错误，请重试或联系客服", "", "error");
		}
	}
	rerurnData("请求错误，请检查手机号码，然后重试", "", "error");
}
function rerurnData($msg = "", $service = "", $type = "error") {
	echo json_encode(array("msg" => $msg, "service" => $service, "type" => $type));
	exit;
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
	<title>流量充值</title>
	<link rel="stylesheet" type="text/css" href="/static/spark/css/common.css?<?=$cacheVer?>"/>
	<link rel="stylesheet" type="text/css" href="/static/css/font-awesome.css?<?=$cacheVer?>">
	<link rel="stylesheet" type="text/css" href="/static/spark/css/app.css?<?=$cacheVer?>"/>
	<script src="/static/spark/js/zepto.min.js"></script>
	<script src="/static/spark/js/slide_left.js"></script>
	<script src="/static/spark/js/app.js"></script>
</head>
<body style="background:#F6F6F6;">
	<div class="commonHead" style="background: #0085d0;color:#fff">
		<i class="fa fa-angle-left commonBack"></i>
		<h3 class="text-overflow_1">流量充值</h3>
	</div>
	
	<img src="http://cz.liulianggo.com/static/img/banner0.png" width="100%" height="150px">
	
	<div class="traffic_mobile">
		<input type="num" name="mobile" value="" maxlength="11" autocomplete="off" />
		<span>手机号</span>
	</div>
	
	<div class="traffic_item clr">
		<dl>
			<dt>即时生效，月底失效</dt>
			<dd class="disabled" item="">10M</dd>
			<dd class="disabled">20M</dd>
			<dd class="disabled">30M</dd>
			<dd class="disabled">50M</dd>
			<dd class="disabled">70M</dd>
			<dd class="disabled">100M</dd>
			<dd class="disabled">1G</dd>
		</dl>
	</div>
	
	<div class="traffic_price clr">
		<h5>20M流量包</h5>
		<p>共需支付<span>3元</span></p>
	</div>
	
	<div class="traffic_submit">
		<button>立即支付</button>
		<p>充值注意事项充值注意事项充值注意事项充值注意事项充值注意事项充值注意事项充值注意事项充值注意事项充值注意事项充值注意事项充值注意事项充值注意事项充值注意事项充值注意事项</p>
	</div>
<?php require_once('../distribute_footer.php'); ?>
<script>
	var mobileServe = {"1":"中国移动", "2":"中国联通", "3":"中国电信"};
	var package = "";
	var pattern = /(13\d|14[57]|15[^4,\D]|17[678]|18\d)\d{8}|170[059]\d{7}/;
	var type = "";
	$(function(){
		$('input[name=mobile]').bind('input propertychange', function() {
			var mobile = $(this).val();
			if(mobile.length == "11" && pattern.test(mobile)){
				$.post(
					"",
					{op : "check", mobile : mobile},
					function(data){
						if(data.type == "success"){
							var tplHtml = "";
							for(var i in data.msg){
								tplHtml += '<dd price="'+data.msg[i]["priceX"]+'" onclick="selectItem(this, '+data.msg[i]["package"]+')">'+data.msg[i]["packageName"]+'</dd>';
							}
							$(".traffic_item dl dd").remove();
							$(".traffic_item dl").append(tplHtml);
							type = data.service;
							$(".traffic_mobile span").text(mobileServe[data.service]);
						}else{
							$(this).val("");
							$(this).focus();
							type = "";
							alert(data.msg);
							return;
						}
					},
					"json"
				)
			}else{
				$(".traffic_mobile span").text("手机号");
				$(".traffic_item dl dd").removeClass("active");
				$(".traffic_item dl dd").addClass("disabled");
				$(".traffic_price").hide();
			}
		});
		
		$(".traffic_submit button").click(function(){
			if($('input[name=mobile]').val() == "" || !pattern.test($('input[name=mobile]').val()) || type == ""){
				alert("请输入正确的11位手机号");
				return;
			}
			if(package == ""){
				alert("请选择流量包");
				return;
			}
			$.post(
				"",
				{op : "submit", type : type, package : package, mobile : $('input[name=mobile]').val()},
				function(data){
					if(data.type == "success"){
						window.location.href=data.service;
					}else{
						alert(data.msg);
						return;
					}
				},
				"json"
		)
		});
	});
	function selectItem(obj, item){
		if($(obj).hasClass("disabled")){
			return false;
		}
		package = item;
		$(".traffic_item dl dd").removeClass("active");
		$(obj).addClass("active");
		$(".traffic_price h5").text($(obj).text() + "流量包");
		$(".traffic_price p span").text($(obj).attr("price") + "元");
		$(".traffic_price").show();
	}
</script>
</body>
</html>