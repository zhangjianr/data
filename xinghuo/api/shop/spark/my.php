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
$show_logo = !empty($rsUser['User_HeadImg']) ? $rsUser['User_HeadImg'] : '/static/api/images/user/face.jpg';

$orderInfo = $DB->GetR("spark_user AS a LEFT JOIN spark_order AS b ON a.User_ID = b.User_ID", "a.*, b.orderId", " WHERE a.Users_ID='{$UsersID}' AND a.User_ID='{$_SESSION[$UsersID . "User_ID"]}' AND b.payCode = 1 ORDER BY b.id ASC");

if(empty($orderInfo)){
	exit('<script>alert("您好没有加入，请先购买产品再进入！");window.location.href="'.$shop_url.'spark/index/";</script>');
}
if(!empty($orderInfo) && $orderInfo['status'] == "0"){
	include $_SERVER["DOCUMENT_ROOT"].'/include/observers/spark.class.php';
	$spark = new spark($DB, $orderInfo['orderId']);
	$resSpark = $spark->start();
}
$userName = empty($orderInfo['realName']) ? $rsUser['User_NickName'] : $orderInfo['realName'];

//获取下级
$resTree = getTree($DB, $UsersID, $_SESSION[$UsersID . "User_ID"]);
$resMoney = $DB->GetS("spark_logs", "sum(money) as money,level", "WHERE Users_ID='{$UsersID}' AND User_ID='{$_SESSION[$UsersID . "User_ID"]}' GROUP BY level");
foreach ($resMoney as $key => $value) {
	$Money[$value['level']] = $value['money'];
}

$sparkConfig=$DB->GetRs("spark_config","*","where Users_ID='".$UsersID."'");
if($sparkConfig['isShop'] == "1"){
    if(empty($orderInfo["intCode"])){
        intCode($orderInfo['User_ID'],$UsersID);
    }
}
//邀请码
function intCode($uid,$UsersID){
   global $DB;
   $a = mt_rand(10000000, 99999999);
   $arr = $DB->GetR("spark_user","*"," where Users_ID='{$UsersID}' AND intCode='{$a}'");
   if(empty($arr)){
       $DB->Set("spark_user",array("intCode"=>$a)," where Users_ID='{$UsersID}' AND User_ID='{$uid}'");
   }else{
       intCode($uid,$UsersID);
   }
}
$code = $DB->GetR("spark_user","*"," where Users_ID='{$UsersID}'  AND User_ID='{$orderInfo['User_ID']}'");
?>
<!doctype html>
<html class="no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="email=no"/>
	<title><?php echo !empty($sparkConfig['myTitle']) ? $sparkConfig['myTitle'] : "家族中心";?></title>
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
		<h3 class="text-overflow_1"><?php echo !empty($sparkConfig['myTitle']) ? $sparkConfig['myTitle'] : "家族中心";?></h3>
	</div>
	<div class="my_header flex">
		<div class="my_logo"><img src="<?=$show_logo?>" /></div>
		<div class="flex_1">
			<h3 class="text-overflow_1">姓名：<?=$userName?></h3>
			<p class="text-overflow_1">关注时间：<?php echo date("Y-m-d H:i",$rsUser['User_CreateTime']); ?></p>
			<p class="text-overflow_1">会员ID：<?=$rsUser['User_No']?></p>
			<p class="text-overflow_1">您的头衔：<?=$orderInfo['packageLevelName']?></p>
		</div>
	</div>
	<div class="my_content">
		<ul>
			<li class="clr">
				<i class="fa fa-users" style="color: #0085d0;"></i>
				<h4>家族成员</h4>
				<i class="fa fa-angle-down"></i>
			</li>
			<div class="spark_level">
				<a href="<?=$shop_url?>spark/level/1/"><p>一级<span><input value="<?php echo count($resTree[1]);?>" readonly="readonly" /></span></p></a>
				<a href="<?=$shop_url?>spark/level/2/"><p>二级<span><input value="<?php echo count($resTree[2]);?>" readonly="readonly" /></span></p></a>
				<a href="<?=$shop_url?>spark/level/3/"><p>三级<span><input value="<?php echo count($resTree[3]);?>" readonly="readonly" /></span></p></a>
			</div>
			<li class="clr">
				<i class="fa fa-bar-chart" style="color: #FF0033;"></i>
				<h4>红包专区</h4>
				<i class="fa fa-angle-down"></i>
			</li>
			<div class="spark_money">
				<a href="<?=$shop_url?>spark/cash/"><p>一级<span><input value="<?= empty($Money[1]) ? 0 : $Money[1]?>" readonly="readonly" /></span></p></a>
				<a href="<?=$shop_url?>spark/cash/"><p>二级<span><input value="<?= empty($Money[2]) ? 0 : $Money[2]?>" readonly="readonly" /></span></p></a>
				<a href="<?=$shop_url?>spark/cash/"><p>三级<span><input value="<?= empty($Money[3]) ? 0 : $Money[3]?>" readonly="readonly" /></span></p></a>
			</div>
			<li class="clr" onclick="window.location.href='<?=$shop_url?>distribute/qrcodehb/'">
				<i class="fa fa-share-alt" style="color: #FF9933;"></i>
				<h4>我的推广海报</h4>
				<i class="fa fa-angle-right"></i>
			</li>
                        <?php if($sparkConfig['isShop'] == "1"):?>
                        <li class="clr">
                            <i class="fa fa-share-alt" style="color: #FF9933;"></i>
                            <h4>我的邀请码:</h4><span style="margin-left:15px;"><?php echo $code["intCode"];?></span>
				<i class="fa fa-angle-right"></i>
                        </li>
                        <?php endif;?>
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