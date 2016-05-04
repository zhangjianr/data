<?php defined('SYS_ACCESS') or exit('Access Denied'); ?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<title>套餐管理</title>
		<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
		<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
		<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
		<script type='text/javascript' src='/static/member/js/global.js'></script>
		<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script><![endif]-->
	</head>
	<body>
		<div id="iframe_page">
			<div class="iframe_content">
				<link href='/static/member/css/shop.css' rel='stylesheet' type='text/css' />
				<script type='text/javascript' src='/static/member/js/shop.js'></script>
				<div class="r_nav">
					<ul>
						<li class="cur"><a href="package.php">套餐管理</a></li>
						<li class=""><a href="order.php">订单管理</a></li>
						<li class=""><a href="slide.php">幻灯管理</a></li>
						<li class=""><a href="rebate.php">返佣管理</a></li>
						<li class=""><a href="traffic.php">购买流量包</a></li>
						<li class=""><a href="config.php">基本配置</a></li>
					</ul>
				</div>
				<div id="products" class="r_con_wrap">
					<div class="control_btn">
						<a href="?op=post" class="btn_green btn_w_120">添加套餐</a>
						<a href="#search" class="btn_green btn_w_120">套餐搜索</a>
					</div>
					<form class="search" method="get" action="products.php">
						关键词：<input type="text" name="Keyword" value="" class="form_input" size="15" />
						<input type="submit" class="search_btn" value="搜索" />
					</form>
					<table width="100%" align="center" border="0" cellpadding="5" cellspacing="0" class="r_con_table">
						<thead>
							<tr>
								<td width="8%" nowrap="nowrap">序号</td>
								<td width="8%" nowrap="nowrap">套餐名称</td>
								<td width="8%" nowrap="nowrap">价格</td>
								<td width="8%" nowrap="nowrap">等级名称</td>
								<td width="15%" nowrap="nowrap">状态</td>
								<td width="10%" nowrap="nowrap">创建时间</td>
								<td width="22%" nowrap="nowrap" class="last">操作</td>
							</tr>
						</thead>
						<tbody>
							<?php
								$lists = array();
								//$condition = "";
								$DB->getPage("spark_package", "*", $condition, 10);

								while ($r = $DB->fetch_assoc()) {
									$lists[] = $r;
								}
								foreach ($lists as $k => $rsProducts) {
							?>
							<tr>
								<td nowrap="nowrap"><?=$k+1 ?></td>
								<td><?php echo $rsProducts["levelName"] ?></td>
								<td><?php echo $rsProducts["price"] ?></td>
								<td><?php echo $rsProducts["levelName"] ?></td>
								<td><?php echo $rsProducts["status"] ?></td>
								<td><?php echo date("Y-m-d H:s:i", $rsProducts["createtime"]); ?></td>
								<td class="last" nowrap="nowrap">
									<a href="package.php?op=post&id=<?=$rsProducts["id"] ?>">
										<img src="/static/member/images/ico/mod.gif" align="absmiddle" alt="修改" /></a>
									<a href="package.php?op=delete&id=<?=$rsProducts["id"] ?>" onClick="if (!confirm('删除后不可恢复，继续吗？')) { 	return false;};">
										<img src="/static/member/images/ico/del.gif" align="absmiddle" alt="删除" />
									</a>
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