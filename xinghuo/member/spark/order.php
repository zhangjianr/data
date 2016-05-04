<?php 
if (empty($_SESSION["Users_Account"])) {
	header("location:/member/login.php");
}

$condition = "where Users_ID='{$_SESSION["Users_ID"]}' AND payCode = 1";
if(isset($_GET["search"])){
	if($_GET["search"]==1){
		if(!empty($_GET["Keyword"])){
			$condition .= " and realName like '%".$_GET["Keyword"]."%' or mobile like '%".$_GET["Keyword"]."%'";
		}
		
		if(!empty($_GET["AccTime_S"])){
			$condition .= " and createtime>=".strtotime($_GET["AccTime_S"]);
		}
		if(!empty($_GET["AccTime_E"])){
			$condition .= " and createtime<=".strtotime($_GET["AccTime_E"]);
		}
	}
}
$condition .= " order by createtime desc";

if (isset($_GET["action"])) {
	if ($_GET["action"] == "set_read") {
		$Flag = $DB->Set("user_order", "Order_IsRead=1", "where Users_ID='" . $_SESSION["Users_ID"] . "' and Order_ID=" . $_GET["OrderID"]);
		$Data = array("ret" => 1);
		echo json_encode($Data, JSON_UNESCAPED_UNICODE);
		exit;
	} elseif ($_GET["action"] == "is_not_read") {
		$Flag = $DB->Set("user_order", "Order_IsRead=1", "where Users_ID='" . $_SESSION["Users_ID"] . "' and Order_ID=" . $_GET["OrderID"]);
		$Data = array(
			"ret" => 1,
			"msg" => ""
		);

		echo json_encode($Data, JSON_UNESCAPED_UNICODE);
		exit;
	}
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
<script type='text/javascript' src='/static/member/js/global.js'></script>
<script type='text/javascript' src='/static/js/plugin/laydate/laydate.js'></script>
<link href='/static/css/bootstrap.min.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/bootstrap.min.js'></script>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->
<script>
$(function(){
	$("#search_form .output_btns").click(function(){

		window.location='../shop/output.php?'+$('#search_form').serialize()+'&type=spark_order_list';
	});
});
</script>
<style>
.page .pre, .page .next, .page .nopre, .page .nonext {width:60px!important;}
.output_btns{background:#1584D5; color:white; border:none; height:22px; line-height:22px; width:80px;}
.laydate_body .laydate_top{height: 30px;}
.laydate_body .laydate_bottom{height: 30px;}
</style>
</head>

<body>
<div id="iframe_page">
	<div class="iframe_content">
		<link href='/static/member/css/shop.css' rel='stylesheet' type='text/css' />
		<script type='text/javascript' src='/static/member/js/shop.js'></script>
		<div class="r_nav">
			<ul>
				<li class=""><a href="package.php">套餐管理</a></li>
				<li class="cur"><a href="order.php">订单管理</a></li>
				<li class=""><a href="slide.php">幻灯管理</a></li>
				<li class=""><a href="rebate.php">返佣管理</a></li>
				<li class=""><a href="traffic.php">购买流量包</a></li>
				<li class=""><a href="config.php">基本配置</a></li>
			</ul>
		</div>
		<div id="orders" class="r_con_wrap">
			<form class="search" id="search_form" method="get" action="?">
				搜索：
				<input type="text" name="Keyword" value="<?php echo empty($_GET['Keyword']) ? '' : $_GET['Keyword'] ?>" placeholder="姓名或者手机号" class="form_input" size="20" />
				时间：
				<input type="text" class="input" name="AccTime_S" value="<?php echo!empty($_GET['AccTime_S']) ? $_GET['AccTime_S'] : "" ?>" id="AccTime_S" maxlength="20" />
				-
				<input type="text" class="input" name="AccTime_E" value="<?php echo!empty($_GET['AccTime_E']) ? $_GET['AccTime_E'] : "" ?>" id="AccTime_E" maxlength="20" />
				<input type="hidden" value="1" name="search" />
				<input type="submit" class="search_btn" value="搜索" />
				<input type="button" class="output_btns" value="导出" />
			</form>
			<form name="form1" method="post" action="?">
				<table border="0" cellpadding="5" cellspacing="0" class="r_con_table" id="order_list">
					<thead>
						<tr>
							<td width="5%" nowrap="nowrap">序号</td>
							<td width="10%" nowrap="nowrap">姓名</td>
							<td width="5%" nowrap="nowrap">微信号</td>
							<td width="5%" nowrap="nowrap">订单编号</td>
							<td width="10%" nowrap="nowrap">手机号</td>
							<td width="17%" nowrap="nowrap">分销记录</td>
							<td width="9%" nowrap="nowrap">金额</td>
							<td width="9%" nowrap="nowrap">产品包等级名称</td>
							<td width="8%" nowrap="nowrap">付款状态</td>
							<td width="7%" nowrap="nowrap">时间</td>
							<td width="10%" nowrap="nowrap" class="last">操作</td>
						</tr>
					</thead>
				<tbody>
					<?php
						$i = 0;
						$lists = array();
						$DB->getPage("spark_order", "*", $condition, 10);
						$Order_Status = array("未支付", "已支付");
						while ($r = $DB->fetch_assoc()) {
							$lists[] = $r;
						}
						foreach ($lists as $k => $rsOrder) {
					?>
					<tr>
						<td nowrap="nowrap"><?php echo $i + 1 ?></td>
						<td nowrap="nowrap"><?php echo empty($rsOrder["realName"]) ? "暂无！" : $rsOrder['realName'] ?></td>
						<td nowrap="nowrap"><?php echo empty($rsOrder["nickName"]) ? "暂无！" : $rsOrder['nickName'] ?></td>
						<td nowrap="nowrap"><?php echo empty($rsOrder["orderId"]) ? "暂无！" : $rsOrder['orderId'] ?></td>
						<td nowrap="nowrap"><?php echo empty($rsOrder["mobile"]) ? "暂无！" : $rsOrder['mobile'] ?></td>
						<td nowrap="nowrap">
						<?php 
						if(empty($rsOrder["disInfo"])){
							echo "暂无！";
						}else{
							$json_data = json_decode($rsOrder["disInfo"], true);
							foreach ($json_data as $key => $val){
								$name = $DB->GetRs("spark_order", "*", " WHERE Users_ID='".$_SESSION['Users_ID']."' AND User_ID=".$val['User_ID']);
								$relname = $name['realName'];
								echo $key."级".$relname."&nbsp;&nbsp;返佣：".$val['Money']."<br/>";
							}
						}
						?>
						</td>
						<td nowrap="nowrap"><?php echo empty($rsOrder["price"])?"暂无！":$rsOrder['price'] ?></td>
						<td nowrap="nowrap"><?php echo  empty($rsOrder["packageLevelName"])?"暂无！":$rsOrder["packageLevelName"] ?></td>
						<td nowrap="nowrap"><?php echo $Order_Status[$rsOrder["payCode"]] ?></td>
						<td nowrap="nowrap"><?php echo date("Y-m-d H:i:s",$rsOrder["createtime"]) ?></td>
						<td class="last" nowrap="nowrap">
							<a href="orders_view.php?OrderID=<?php echo $rsOrder["id"] ?>">
								<img src="/static/member/images/ico/view.gif" align="absmiddle" title="查看详情" />
							</a>
						</td>
					</tr>
					<?php
						$i++;
						}
					?>
				</tbody>
			</table>
		</form>
		<div class="blank20"></div>
		<?php $DB->showPage(); ?>
		</div>
	</div>
</div>
<script>
var start = {
    elem: '#AccTime_S',
    format: 'YYYY-MM-DD hh:mm:ss',
    istime: true,
    istoday: true,
    choose: function(datas){
         end.min = datas; //开始日选好后，重置结束日的最小日期
         end.start = datas //将结束日的初始值设定为开始日
    }
};
var end = {
    elem: '#AccTime_E',
    format: 'YYYY-MM-DD hh:mm:ss',
    max: laydate.now(),
    istime: true,
    istoday: true,
    choose: function(datas){
        start.max = datas; //结束日选好后，重置开始日的最大日期
    }
};
laydate(start);
laydate(end);
</script>
</body>
</html>