<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
/*分享页面初始化配置*/
$share_flag = 1;
$signature = '';

if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}

$base_url = base_url();
$pifa_url = $base_url.'api/'.$UsersID.'/pifa/';

if(empty($_SESSION[$UsersID."User_ID"]))
{
	$_SESSION[$UsersID."HTTP_REFERER"]="/api/".$UsersID."/pifa/member/";
	header("location:/api/".$UsersID."/user/login/");
}
$Status=empty($_GET["Status"])?0:$_GET["Status"];
if(!strpos($_SERVER['REQUEST_URI'],"mp.weixin.qq.com")){
	header("location:?wxref=mp.weixin.qq.com");
}

$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
//商城配置一股脑转换批发配置
$Config = $DB->GetRs("pifa_config","*","where Users_ID='".$UsersID."'");
$rsConfig['ShopName'] = $Config['PifaName'];
$rsConfig['NeedShipping'] = $Config['p_NeedShipping'];
$rsConfig['SendSms'] = $Config['p_SendSms'];
$rsConfig['MobilePhone'] = $Config['p_MobilePhone'];
$rsConfig['Commit_Check'] = $Config['p_Commit_Check'];

$Status  = !empty($_GET['status'])?$_GET['status']:0;

//获取退货单
$condition = "where Users_ID='".$UsersID."'";
$condition .= " and User_ID=".$_SESSION[$UsersID.'User_ID'];
$condition .= " and Back_Status=".$Status ;

$rsBackList = $DB->Get("user_back_order","*",$condition);
$back_list = $DB->toArray($rsBackList);

//获取退货单中所包含产品

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<meta content="telephone=no" name="format-detection" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>个人中心</title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/api/shop/skin/default/css/style.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/api/js/global.js'></script>
<script type='text/javascript' src='/static/api/pifa/js/js.js'></script>
</head>
<body>
<div id="shop_page_contents">
	<div id="cover_layer"></div>
	<link href='/static/api/shop/skin/default/css/member.css' rel='stylesheet' type='text/css' />
	<ul id="member_nav">
		<li class="<?php echo $Status==0?"cur":"" ?>"><a href="/api/<?php echo $UsersID ?>/pifa/member/backup/status/0/">申请中</a></li>
		<li class="<?php echo $Status==1?"cur":"" ?>"><a href="/api/<?php echo $UsersID ?>/pifa/member/backup/status/1/">已批准</a></li>
		<li class="<?php echo $Status==2?"cur":"" ?>"><a href="/api/<?php echo $UsersID ?>/pifa/member/backup/status/2/">退货中</a></li>
		<li class="<?php echo $Status==3?"cur":"" ?>"><a href="/api/<?php echo $UsersID ?>/pifa/member/backup/status/3/">已退货</a></li>
	</ul>
	<div id="order_list">
		<?php foreach($back_list as $key=>$item):?>
		<?php $Product_list = json_decode($item['Back_Json'],TRUE)?>
		<div class="item">
			<h1> 退货单号：<a href="/api/<?=$UsersID?>/pifa/member/backup/detail/<?=$item['Back_ID']?>/?wxref=mp.weixin.qq.com">
				<?=$item['Back_Sn']?>
				</a> </h1>
			<table class="bordered">
				<thead>
					<tr>
						<th>#</th>
						<th>商品名</th>
						<th>数量</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($Product_list as $key=>$product):?>
					<tr>
						<td><?=$key?></td>
						<td><?=$product['Products_Name']?></td>
						<td><?=$product['back_num']?></td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
		<?php endforeach;?>
	</div>
</div>
<?php require_once('../distribute_footer.php');?>