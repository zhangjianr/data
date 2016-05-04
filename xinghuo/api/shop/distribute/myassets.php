<?php
//ini_set("display_errors","On");
require_once($_SERVER["DOCUMENT_ROOT"] . '/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/Framework/Conn.php');

$base_url = base_url();
$shop_url = shop_url();
/* 分享页面初始化配置 */
$share_flag = 1;
$signature = '';

if (isset($_GET["UsersID"])) {
	$UsersID = $_GET["UsersID"];
} else {
	echo '缺少必要的参数';
	exit;
}

if (!empty($_SESSION[$UsersID . "User_ID"])) {
	$userexit = $DB->GetRs("user", "*", "where User_ID=" . $_SESSION[$UsersID . "User_ID"] . " and Users_ID='" . $UsersID . "'");
	if (!$userexit) {
		$_SESSION[$UsersID . "User_ID"] = "";
	}
}

if (empty($_SESSION[$UsersID . "User_ID"])) {
	$_SESSION[$UsersID . "HTTP_REFERER"] = "/api/" . $UsersID . "/shop/distribute/";
	header("location:/api/" . $UsersID . "/user/login/");
}
$User_ID = $_SESSION[$UsersID.'User_ID'];
$rsConfig = $DB->GetRs("shop_config", "*", "where Users_ID='" . $UsersID . "'");
$rsUser = $DB->GetRs("user", "*", "where User_ID=" . $_SESSION[$UsersID . "User_ID"]);
//判断代理类型
$disArea = array();
$Record_Type[0] = "分销下线";
$rsAccount =  Dis_Account::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$User_ID))->first()->toArray();//分销账号信息
if(!empty($rsAccount)){
	$disArea = Dis_Agent_Area::Multiwhere(array('Users_ID'=>$UsersID,"Account_ID"=>$rsAccount['Account_ID']))->get()->toArray();//地区代理信息
}
if(!empty($disArea)){
	$Record_Type[2] = "地区代理";
}
if($rsAccount != "0" && $rsAccount['Enable_Agent'] == "3"){
	$Record_Type[3] = "渠道代理";
}
if($rsAccount != "0" && $rsAccount['Enable_Agent'] == "1"){
	$Record_Type[3] = "渠道代理";
}
$Record_Type['all'] = "全部类型";

$dateMonth = array();
$curMonth = date("m");
for($i = $curMonth; $i > $curMonth - 6; $i--){
	$month = date("Y-m", mktime(0, 0, 0, $i,1));
	$dateMonth[mktime(0, 0, 0, $i,1)] = $month;
}
$getMonth = !empty($_GET['date']) ? intval($_GET['date']) : mktime(0, 0, 0, date("m"),1);
$getFilter = (in_array($_GET['filter'],array('all',0,1,2,3))) ? trim($_GET['filter']) : "all";
//初始化结束 开始读取数据
$resAgent = array();
$resDis = array();
$dateStart = $getMonth;
$dateEnd = mktime(0, 0, 0, date("m", $getMonth)+1, 1, date("Y", $getMonth));
if($getFilter == "all"){
	//读取代理商记录
	$condition1 = "a.Account_ID = '{$rsAccount['Account_ID']}' AND b.Order_CreateTime BETWEEN '{$dateStart}' AND '{$dateEnd}'";
	$resAgent =  $DB->query("SELECT * FROM `shop_dis_agent_rec` AS a left join user_order AS b ON a.Order_ID = b.Order_ID WHERE {$condition1}");
	$resAgent = $DB->toArray($resAgent);
	foreach($resAgent as $key=>$val){
		$tmp_user = User::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$val['User_ID']))->first();
		$resAgent[$key]['User_NickName'] = $tmp_user['User_NickName'];
		unset($tmp_user);
	}
	//读取分销记录
	$condition2 = "a.User_ID = '{$User_ID}' AND a.Record_CreateTime BETWEEN '{$dateStart}' AND '{$dateEnd}'";
	$resDis =  $DB->query("SELECT * FROM `shop_distribute_account_record` AS a left join shop_distribute_record AS b ON a.Ds_Record_ID = b.Record_ID WHERE {$condition2}");
	$resDis = $DB->toArray($resDis);
	foreach($resDis as $key=>$val){
		$tmp_user = User::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$val['Buyer_ID']))->first();
		$resDis[$key]['User_NickName'] = $tmp_user['User_NickName'];
		unset($tmp_user);
	}
}else if($getFilter == "0"){
	//读取分销记录
	$condition2 = "a.User_ID = '{$User_ID}' AND a.Record_CreateTime BETWEEN '{$dateStart}' AND '{$dateEnd}'";
	$resDis =  $DB->query("SELECT * FROM `shop_distribute_account_record` AS a left join shop_distribute_record AS b ON a.Ds_Record_ID = b.Record_ID WHERE {$condition2}");
	$resDis = $DB->toArray($resDis);
	foreach($resDis as $key=>$val){
		$tmp_user = User::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$val['Buyer_ID']))->first();
		$resDis[$key]['User_NickName'] = $tmp_user['User_NickName'];
		unset($tmp_user);
	}
}else if($getFilter == "1"){
	//读取代理商记录
	$condition1 = "a.Account_ID = '{$rsAccount['Account_ID']}' AND a.Record_Type = 1 AND b.Order_CreateTime BETWEEN '{$dateStart}' AND '{$dateEnd}'";
	$resAgent =  $DB->query("SELECT * FROM `shop_dis_agent_rec` AS a left join user_order AS b ON a.Order_ID = b.Order_ID WHERE {$condition1}");
	$resAgent = $DB->toArray($resAgent);
	foreach($resAgent as $key=>$val){
		$tmp_user = User::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$val['User_ID']))->first();
		$resAgent[$key]['User_NickName'] = $tmp_user['User_NickName'];
		unset($tmp_user);
	}
}else if($getFilter == "2"){
	//读取代理商记录
	$condition1 = "a.Account_ID = '{$rsAccount['Account_ID']}' AND a.Record_Type IN (2,3) AND b.Order_CreateTime BETWEEN '{$dateStart}' AND '{$dateEnd}'";
	$resAgent =  $DB->query("SELECT * FROM `shop_dis_agent_rec` AS a left join user_order AS b ON a.Order_ID = b.Order_ID WHERE {$condition1}");
	$resAgent = $DB->toArray($resAgent);
	foreach($resAgent as $key=>$val){
		$tmp_user = User::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$val['User_ID']))->first();
		$resAgent[$key]['User_NickName'] = $tmp_user['User_NickName'];
		unset($tmp_user);
	}
}else if($getFilter == "3"){
	//读取代理商记录
	$condition1 = "a.Account_ID = '{$rsAccount['Account_ID']}' AND a.Record_Type = 1 AND b.Order_CreateTime BETWEEN '{$dateStart}' AND '{$dateEnd}'";
	$resAgent =  $DB->query("SELECT * FROM `shop_dis_agent_rec` AS a left join user_order AS b ON a.Order_ID = b.Order_ID WHERE {$condition1}");
	$resAgent = $DB->toArray($resAgent);
	foreach($resAgent as $key=>$val){
		$tmp_user = User::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$val['User_ID']))->first();
		$resAgent[$key]['User_NickName'] = $tmp_user['User_NickName'];
		unset($tmp_user);
	}
}
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>资产明细</title>
	<link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
	<link href="/static/api/distribute/css/style.css" rel="stylesheet">
	<link href="/static/api/distribute/css/myorder.css" rel="stylesheet">
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="/static/js/jquery-1.11.1.min.js"></script>
	<script type='text/javascript' src='/static/api/js/global.js'></script>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
    
</head>

<body>
<header class="bar bar-nav">
	<a href="javascript:history.back()" class="fa fa-2x fa-chevron-left grey pull-left"></a>
	<a href="/api/<?=$UsersID?>/shop/distribute/" class="fa fa-2x fa-sitemap grey pull-right"></a>
	<h1 class="title">资产明细</h1>
</header>

<div class="wrap">
	<div class="container">
        <div class="row">
			<ul id="distribute-brief-info">
				<li class="item" onclick="showUl(0)"><?=$Record_Type[$getFilter]?><i class="fa fa-angle-down"></i></li>
				<li class="item" onclick="showUl(1)"><?=date("Y-m", $getMonth)?>月份<i class="fa fa-angle-down"></i></li>
				<li class="clearfix"></li>
				<div class="item_ul">
					<ul id="filter">
						<?php foreach($Record_Type as $key=>$val): ?>
						<li data-filter="<?=$key?>" onclick="window.location.href='/api/shop/distribute/myassets.php?UsersID=<?=$UsersID?>&filter=<?=$key?>&date=<?=$getMonth?>'"><?=$val?></li>
						<?php endforeach; ?>
					</ul>
					<ul id="month">
						<?php foreach($dateMonth as $key=>$val): ?>
						<li data-month="<?=$key?>" onclick="window.location.href='/api/shop/distribute/myassets.php?UsersID=<?=$UsersID?>&filter=<?=$getFilter?>&date=<?=$key?>'"><?=$val?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</ul>
			<table class="table table-bordered" style="background: #fff;margin-top: 10px;">
				<thead>
					<tr>
						<th>日期</th>
						<th style="width:70px;">购买者</th>
						<th>金额</th>
						<th>收益</th>
						<th>状态</th>
					</tr>
				</thead>
					<?php if(empty($resAgent) && empty($resDis)): ?>
					<tr>
						<th colspan="6" style="text-align: center;">没有记录！</th>
					</tr>
					<?php endif; ?>
					<?php foreach($resAgent as $key=>$val): ?>
					<tr>
						<th scope="row"><?php echo date("m-d",$val['Order_CreateTime']); ?></th>
						<td><?=$val['User_NickName']?></td>
						<td><?=$val['Order_TotalPrice']?></td>
						<td><?=$val['Record_Money']?></td>
						<td><?php if($val['Order_Status'] == "4"){echo "已完成";}else{echo "进行中";}?></td>
					</tr>
					<?php endforeach;?>
					<?php foreach($resDis as $key=>$val): ?>
					<tr>
						<th scope="row"><?php echo date("m-d",$val['Record_CreateTime']); ?></th>
						<td><?=$val['User_NickName']?></td>
						<td><?php echo $val['Product_Price']*$val['Qty']; ?></td>
						<td><?=$val['Record_Money']?></td>
						<td><?php if($val['status'] == "1"){echo "已完成";}else{echo "进行中";}?></td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
			<?php
				//echo $page_link;
			?>
        </div>
    </div>
</div>

<script>
function showUl(data){
	if($('.item_ul ul').eq(data).css("display") == "block"){
		$('.item_ul ul').eq(data).hide();
		return;
	}
	$(".item_ul ul").each(function(){
		$(this).hide();
	});
	$('.item_ul ul').eq(data).show();
}
</script>
<?php require_once('../distribute_footer.php');?>
</body>
</html>