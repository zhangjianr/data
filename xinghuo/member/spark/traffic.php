<?php
if (empty($_SESSION["Users_Account"])) {
	header("location:/member/login.php");
}
$serveName = array("1"=>"中国移动", "2"=>"中国联通", "3"=>"中国电信");
include $_SERVER["DOCUMENT_ROOT"] . '/include/library/traffic.class.php';
$resPackage = $DB->GetS("spark_traffic", "*", "WHERE Users_ID='{$_SESSION["Users_ID"]}' ORDER BY serviceId ASC,packagePrice ASC");
if(empty($resPackage)){
	$traffic = new traffic($_SESSION["Users_ID"]);
	$package1 = $traffic->getPackage(1);
        $package2 = $traffic->getPackage(2);
        $package3 = $traffic->getPackage(3);
        $packageAll = array_merge($package1,$package2,$package3);
	foreach($packageAll as $key=>$val){
		$packageData = array("Users_ID"=>$_SESSION["Users_ID"], "serviceId"=>$val['Type'], "package"=>$val['Package'], "packageName"=>$val['Name'], "packagePrice"=>$val['Price']);
		$DB->Add("spark_traffic",$packageData);
	}
	$resPackage = $DB->GetS("spark_traffic", "*", "WHERE Users_ID='{$_SESSION["Users_ID"]}' ORDER BY serviceId ASC,packagePrice ASC");
}
if ($_POST['serve']) {
	$serve = $_GPC['serve'];
        if($serve == "0"){
            $serve = "1,2,3";
        }
        $resPackage = $DB->GetS("spark_traffic", "*", "WHERE Users_ID='{$_SESSION["Users_ID"]}' AND serviceId in( {$serve}) ORDER BY serviceId ASC,packagePrice ASC");
}
if (isset($_GPC['op']) && $_GPC['op'] == "ajaxData"){
	$id = intval($_GPC['id']);
	$item = $_GPC['item'];
	$data = $_GPC['data'];
	$Data = array(
		$item => $data
	);
	$flag = $DB->Set("spark_traffic", $Data, "WHERE Users_ID='{$_SESSION["Users_ID"]}' AND id = '{$id}'");
	if($flag === FALSE){
		message("提交失败", "", "error");
	}else{
		message("提交成功", "", "success");
	}
	
}
function message($msg = "", $redirectUrl = "", $type = "error") {
	echo json_encode(array("msg" => $msg, "redirectUrl" => $redirectUrl, "type" => $type));
	exit;
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>购买星火流量包</title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
<link href="/static/member/css/shop.css" rel="stylesheet" type="text/css">
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/member/js/global.js'></script>
<script type='text/javascript' src='/static/member/js/shop.js'></script>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->
</head>
<body>
<div id="iframe_page">
	<div class="iframe_content">
		<div class="r_nav">
			<ul>
				<li class=""><a href="package.php">套餐管理</a></li>
				<li class=""><a href="order.php">订单管理</a></li>
				<li class=""><a href="slide.php">幻灯管理</a></li>
				<li class=""><a href="rebate.php">返佣管理</a></li>
				<li class="cur"><a href="traffic.php">购买流量包</a></li>
				<li class="cur"><a href="traOrder.php">流量包订单</a></li>
				<li class=""><a href="config.php">基本配置</a></li>
			</ul>
		</div>
		<div id="products" class="r_con_wrap">
			<form class="search" method="post" action="traffic.php" style="display: block;">
				选择服务商：
				<select name="serve" style="width: 100px;">
                                        <option value="0" <?php if($_POST['serve'] ==0) echo selected; ?>>全部</option>
					<option value="1" <?php if($_POST['serve'] ==1) echo selected; ?>>中国移动</option>
					<option value="2" <?php if($_POST['serve'] ==2) echo selected; ?>>中国联通</option>
					<option value="3" <?php if($_POST['serve'] ==3) echo selected; ?>>中国电信</option>
				</select>
				<input type="hidden" name="search" value="0">
				<input type="submit" class="search_btn" value="查看编辑" style="margin-left: 10px;width: 80px;">
			</form>
			
			<div id ="demo"><table border="1">
					<thead>
						<tr>
							<th width="10%">服务商</th>
							<th width="10%">套餐名称</th>
							<th width="10%">价格</th>
							<th width="20%">销售价格</th>
							<th width="15%">利润率(%)</th>
							<th width="15%">1级分销(%)</th>
							<th width="15%">2级分销(%)</th>
							<th width="15%">3级分销(%)</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($resPackage as $key=>$val):
							$val['profit'] = empty($val['profit']) ? "0" : $val['profit'];
							$val['one'] = empty($val['one']) ? "0" : $val['one'];
							$val['two'] = empty($val['two']) ? "0" : $val['two'];
							$val['three'] = empty($val['three']) ? "0" : $val['three'];
						?>
						<tr>
							<td><?=$serveName[$val['serviceId']]?></td>
							<td><?=$val['packageName']?></td>
							<td><?=$val['packagePrice']?></td>
							<td><span onclick="edit(this)" id="<?=$val['id']?>" item="priceX" data="<?=$val['priceX']?>"><?=$val['priceX']?></span></td>
							<td><span onclick="edit(this)" id="<?=$val['id']?>" item="profit" data="<?=$val['profit']?>"><?=$val['profit']?></span></td>
							<td><span onclick="edit(this)" id="<?=$val['id']?>" item="one" data="<?=$val['one']?>"><?=$val['one']?></span></td>
							<td><span onclick="edit(this)" id="<?=$val['id']?>" item="two" data="<?=$val['two']?>"><?=$val['two']?></span></td>
							<td><span onclick="edit(this)" id="<?=$val['id']?>" item="three" data="<?=$val['three']?>"><?=$val['three']?></span></td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<style>
	#demo table{
		border-collapse: collapse;
		width: 100%;
		text-align: center;
		line-height: 25px;
		border-color: #AEAEAE;
		table-layout: fixed;
	}
	#demo table tr td span{
		display: block;
	}
</style>
<script>
function edit(obj){
	id = $(obj).attr("id");
	item = $(obj).attr("item");
	oldData = $(obj).attr("data");
	itemWidth = $(obj).width() - 5;
	$(obj).hide().after('<input type="text" name="'+item+'" value="'+oldData+'" style="width:'+itemWidth+'px"/>');
	$("input[name="+item+"]").focus().select().bind("blur", function(){
		newData = $(this).val();
		if(newData == oldData){
			$("input[name="+item+"]").remove();
			$(obj).show();
			return false;
		}
		if(!confirm("确认修改吗？")){
			$("input[name="+item+"]").remove();
			$(obj).show();
			return false;
		}
		$.post(
			"",
			{op : "ajaxData", id : id, item : item, data : newData},
			function(data){
				if(data.type === "success"){
					$("input[name="+item+"]").remove();
					$(obj).text(newData).attr("data", newData).show();
				}else{
					alert(data.msg);
					location.reload();
					return;
				}
			},
			"json"
		);
	});
}
</script>
</body>
</html>