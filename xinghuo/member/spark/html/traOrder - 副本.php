<?php

?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<title>流量包订单管理</title>
		<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
		<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
		<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
		<script type='text/javascript' src='/static/member/js/global.js'></script>
		<script type='text/javascript' src='/static/member/js/shop.js'></script>
		<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script><![endif]-->
	</head>
	<body>
		<div id="iframe_page">
			<div class="iframe_content">
				<link href='/static/member/css/shop.css' rel='stylesheet' type='text/css' />
				<script type='text/javascript' src='/static/member/js/shop.js'></script>
				<div class="r_nav">
					<ul>
						<li class=""><a href="package.php">套餐管理</a></li>
						<li class=""><a href="order.php">订单管理</a></li>
						<li class=""><a href="slide.php">幻灯管理</a></li>
						<li class=""><a href="rebate.php">返佣管理</a></li>
						<li class=""><a href="traffic.php">购买流量包</a></li>
						<li class="cur"><a href="traOrder.php">流量包订单</a></li>
						<li class=""><a href="config.php">基本配置</a></li>
					</ul>
				</div>
				<div id="order" class="r_con_wrap">
<!--					<div class="control_btn">
						<a href="?op=post" class="btn_green btn_w_120">添加套餐</a>
						<a href="#search" class="btn_green btn_w_120">手机号搜索</a>
					</div>-->
				<form class="search" id="search_form" method="get" action="traOrder.php">
						搜索：
						<input type="text" name="Keyword" value="<?php echo empty($_GET['Keyword']) ? '' : $_GET['Keyword'] ?>" placeholder="订单号或者手机号" class="form_input" size="20" />
<!--						时间
						<input type="text" class="input" name="AccTime_S" value="<?php echo!empty($_GET['AccTime_S']) ? $_GET['AccTime_S'] : "" ?>" id="AccTime_S" maxlength="20" />
						-
						<input type="text" class="input" name="AccTime_E" value="<?php echo!empty($_GET['AccTime_E']) ? $_GET['AccTime_E'] : "" ?>" id="AccTime_E" maxlength="20" />-->
						<input type="hidden" value="1" name="search" />
						<input type="submit" class="search_btn" value="搜索" />
<!--						<input type="button" class="output_btns" value="导出" />-->
					</form>
					<table width="100%" align="center" border="0" cellpadding="5" cellspacing="0" class="r_con_table">
						<thead>
							<tr>
								<td width="8%" nowrap="nowrap">序号</td>
								<td width="8%" nowrap="nowrap">订单号</td>
								<td width="8%" nowrap="nowrap">充值手机号</td>
								<td width="8%" nowrap="nowrap">流量包信息</td>
								<td width="15%" nowrap="nowrap">价格</td>
								<td width="15%" nowrap="nowrap">交易编码</td>
								<td width="10%" nowrap="nowrap">创建时间</td>
								<td width="22%" nowrap="nowrap" class="last">分销信息</td>
							</tr>
						</thead>
						<tbody>
							<?php
								$lists = array();
								//$condition = "";
								$DB->getPage("spark_traffic_order", "*", $condition, 10);

								while ($r = $DB->fetch_assoc()) {
									$lists[] = $r;
								}
								$tra_status = array(
									"1"=>"移动",
									"2"=>"联通",
									"3"=>"电信"
								);
								foreach ($lists as $k => $order) {
							?>
							<tr>
								<td nowrap="nowrap"><?=$k+1 ?></td>
								<td><?php echo $order["orderId"] ?></td>
								<td><?php echo $order["mobile"] ?></td>
								<td>
									<?php 
										$traffic = $DB->GetR("spark_traffic","*"," where Users_ID='".$_SESSION['Users_ID']."'  and id='{$order['trafficId']}'");
										if(!empty($traffic)){
											echo "流量包名称：".$traffic['packageName']."<br/>";
											echo "服务商：".$tra_status[$traffic['serviceId']];
										}
									?>
								</td>
								<td><?php echo $order["priceX"] ?></td>
								<td><?php echo $order["transaction_id"] ?></td>
								<td><?php echo date("Y-m-d H:s:i", $order["createtime"]); ?></td>
								<td class="last" nowrap="nowrap">
									<?php 
									$arr = json_decode($order['disinfo'],true);
									foreach($arr as $k=>$v){
										$user = $DB->GetR("user","*"," where Users_ID='".$_SESSION['Users_ID']."' and User_ID ='{$v['User_ID']}'");
										echo "用户：".$user['User_NickName']."得到".$v['money']."元<br/>";
									}
									?>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<div class="blank20"></div>
					<?php $DB->showPage(); ?>
				</div>
			</div>
		</div>
	</body>
</html>
