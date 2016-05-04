<?php
if (empty($_SESSION["Users_Account"])) {
	header("location:/member/login.php");
}
$rsConfig = $DB->GetRs("user_config", "UserLevel", "where Users_ID='{$_SESSION["Users_ID"]}'");

function generateTree($items) {
	$tree = array();
	foreach ($items as $item) {
		if (isset($items[$item['Owner_Id']])) {
			$items[$item['Owner_Id']]['children'][] = &$items[$item['id']];
		} else {
			$tree[] = &$items[$item['id']];
		}
	}
	return $tree;
}
if (empty($rsConfig)) {
	header("location:config.php");
} else {
	if(isset($_GET['operation'])) {
		$data = array();
		switch ($_GET['operation']) {
			case "get_node":
				$node = isset($_GET['id']) ? $_GET['id'] : 0;
				$temp = $DB->GetS("user","User_ID AS id, Owner_Id, User_Name, User_NickName","where Users_ID = '{$_SESSION["Users_ID"]}'");
				foreach($temp as $key=>$val){
					if(!empty($val['User_NickName'])){
						$val["text"] = $val['User_NickName'];
					}else{
						$val["text"] = $val['User_Name'];
					}
					$userData[$val['id']] = $val;
				}
				$data[] = array('id' => "0", 'text' => "总店", "state"=>array("opened"=>true), 'children' => generateTree($userData), "type"=>"root");
				break;
			case "move_node":
				$id = intval($_GET['id']);
				$parent = intval($_GET['parent']);
				if(!empty($id)){
					$flagA = $DB->Set("user", array("Owner_Id"=>$parent),"where User_ID = '{$id}'");
					$flagB = $DB->Set("shop_distribute_account", array("invite_id"=>$parent),"where User_ID = '{$id}'");
					if($flagA && $flagB){
						return TRUE;
					}
				}
			default:
				$data = "error";
				break;
		}
		exit(json_encode($data));
	}
}
?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>会员关系</title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
<link href='/static/member/css/distribute.css' rel='stylesheet' type='text/css' />
<link rel="stylesheet" href="/static/spark/css/jstree/style.min.css" />
<script type='text/javascript' src='http://libs.useso.com/js/jquery/2.1.1/jquery.min.js'></script>
<script src="/static/spark/js/jstree.min.js"></script>
</head>
<body>
	<div id="iframe_page">
		<div class="iframe_content">
			<div class="r_nav">
				<ul>
					<li class="cur"> <a href="/member/shop/distributes.php">分销账号管理</a> </li>
					<li class=""><a href="/member/shop/distribute_record.php">分销记录</a></li>
					<li class=""><a href="/member/shop/withdraw_record.php">提现记录</a></li>
					<li class=""><a href="/member/shop/distribute_title.php">爵位设置</a></li>
					<li class=""><a href="/member/shop/channel_config.php">渠道设置</a></li>
					<li class=""><a href="/member/shop/withdraw_method.php">提现方法管理</a></li>
				</ul>
			</div>
			
			<div id="user" class="r_con_config r_con_wrap">
				<div class="search" style="height: 30px;">
					<input type="text" id="keyword" name="keyword" value="" style="width: 200px;height: 30px;border-radius: 5px;margin:0 10px;float: left;" placeholder="请输入关键词搜索"/>
					<p style="line-height: 30px;margin: 0px;float: left;font-size: 14px;">输入关键词自动搜索，可直接拖动操作，请谨慎！！！</p>
					<div class="clear"></div>
				</div>
				<div id="container"></div>
			</div>
		</div>
	</div>
<script>
$(function() {
	$('#container')
		.jstree({
			'core' : {
				'data' : {
					'url' : '?operation=get_node',
					"dataType" : "json",
					'data' : function (node) {
						return { 'id' : node.id };
					}
				},
				'check_callback' : true,
				'themes' : {
					'responsive' : false
				}
			},
			"types" : {
				"#" : { "max_children" : -1, "max_depth" : -1, "valid_children" : -1 },
				"root" : { "icon" : "/static/spark/i/iconfont-tree.png", "valid_children" : ["default"] },
				"default" : {"icon" : "/static/spark/i/iconfont-user.png","valid_children" : ["default","file"] },
				"file" : { "icon" : "glyphicon glyphicon-file", "valid_children" : [] }
			},
			'force_text' : true,
			'plugins' : ['state','dnd','wholerow','search', "types"]
		})
		.on('delete_node.jstree', function (e, data) {
			$.get('?operation=delete_node', { 'id' : data.node.id })
				.fail(function () {
					data.instance.refresh();
				});
		})
		.on('create_node.jstree', function (e, data) {
			$.get('?operation=create_node', { 'id' : data.node.parent, 'position' : data.position, 'text' : data.node.text })
				.done(function (d) {
					data.instance.set_id(data.node, d.id);
				})
				.fail(function () {
					data.instance.refresh();
				});
		})
		.on('rename_node.jstree', function (e, data) {
			$.get('?operation=rename_node', { 'id' : data.node.id, 'text' : data.text })
				.fail(function () {
					data.instance.refresh();
				});
		})
		.on('move_node.jstree', function (e, data) {
			if(!confirm("确定移动吗，不能回撤？")){
				data.instance.refresh();
				return;
			}
			$.get('?operation=move_node', { 'id' : data.node.id, 'parent' : data.parent, 'position' : data.position })
				.fail(function () {
					data.instance.refresh();
				});
		})
		.on('copy_node.jstree', function (e, data) {
			$.get('?operation=copy_node', { 'id' : data.original.id, 'parent' : data.parent, 'position' : data.position })
				.always(function () {
					data.instance.refresh();
				});
		})
		.on('changed.jstree', function (e, data) {
			if(data && data.selected && data.selected.length) {
				$.get('?operation=get_content&id=' + data.selected.join(':'), function (d) {
					$('#data .default').html(d.content).show();
				});
			}
			else {
				$('#data .content').hide();
				$('#data .default').html('Select a file from the tree.').show();
			}
		})
		.on('search.jstree', function (e, data) {
			$('#container').jstree(true).deselect_all()
			$('#container').jstree(true).select_node(data.res);
		})
		
	$('#keyword').bind('input propertychange', function () {
		if($(this).val().length >= 1){
			$('#container').jstree(true).search($(this).val());
		}
	});
});
</script>
</body>
</html>


