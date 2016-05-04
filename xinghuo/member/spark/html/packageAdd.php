<?php defined('SYS_ACCESS') or exit('Access Denied'); ?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<title>添加套餐</title>
		<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
		<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
		<link href='/static/member/css/shop.css' rel='stylesheet' type='text/css' />
		
		<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
		<script type='text/javascript' src='/static/member/js/global.js'></script>
		
		<link rel="stylesheet" href="/third_party/kindeditor/themes/default/default.css" />
		<script type='text/javascript' src="/third_party/kindeditor/kindeditor-min.js"></script>
		<script type='text/javascript' src="/third_party/kindeditor/lang/zh_CN.js"></script>
		<script type='text/javascript' src='/static/member/js/shop.js'></script>
		
<style>
            .state1{
                color:#aaa;
            }
            .state2{
                color:red;
            }
            .state3{
                color:red;
            }
            .state4{
                color:green;
            }
        </style>
		
		<script>
			KindEditor.ready(function (K) {
				K.create('textarea[name="info"]', {
					themeType: 'simple',
					filterMode: false,
					uploadJson: '/member/upload_json.php?TableField=web_column&Users_ID=<?php echo $_SESSION["Users_ID"]; ?>',
					fileManagerJson: '/member/file_manager_json.php',
					allowFileManager: true,
				});
				var editor = K.editor({
					uploadJson: '/member/upload_json.php?TableField=web_article',
					fileManagerJson: '/member/file_manager_json.php',
					showRemote: true,
					allowFileManager: true,
				});
				K('#ImgUpload').click(function() {
					editor.loadPlugin('image', function() {
						editor.plugin.imageDialog({
							imageUrl : K('#thumb').val(),
							clickFn : function(url, title, width, height, border, align) {
								K('#thumb').val(url);
								K('#ImgDetail').html('<img src="'+url+'" width="100px" />');
								editor.hideDialog();
							}
						});
					});
				});
				
				K('#PicUpload').click(function(){
					if(K('#PicDetai').children().length>=5){
						alert('头像只能上传一张！');
						return;
					}
					editor.loadPlugin('image', function() {
						editor.plugin.imageDialog({
							clickFn : function(url, title, width, height, border, align) {
							  K('#PicDetai').append('<div><a href="'+url+'" target="_blank"><img src="'+url+'" /></a> <span onclick="delDom(this)">删除</span><input type="hidden" name="picture[]" value="'+url+'" /></div>');
								editor.hideDialog();
							}
						});
					});
				});
			})
			function delDom(obj){
				$(obj).parent().remove();
			}
		</script>
	</head>

	<body>
		<div id="iframe_page">
			<div class="iframe_content">
				
				<div class="r_nav">
					<ul>
						<li class="cur"><a href="package.php">套餐管理</a></li>
						<li class=""><a href="order.php">订单管理</a></li>
						<li class=""><a href="slide.php">幻灯管理</a></li>
                                                <li class=""><a href="rebate.php">返佣管理</a></li>
						<li class=""><a href="config.php">基本配置</a></li>
					</ul>
				</div>
				
				<div id="products" class="r_con_wrap">
					<form id="product_add_form" class="r_con_form" method="post" action="?op=post">
						<div class="rows">
							<label>级别名称</label>
							<span class="input">
								<input type="text" name="levelName" value="<?=$item['levelName']?>" class="form_input" size="35" />
								<font class="fc_red">*</font>
							</span>
							<div class="clear"></div>
						</div>
						
						<div class="rows">
							<label>购买金额</label>
							<span class="input">
								<input type="text" name="price" value="<?=$item['price']?>" class="form_input" size="35"/>
								<font class="fc_red">*</font>
							</span>
							<div class="clear"></div>
						</div>
						
						<div class="rows">
							<label>返佣设置</label>
							<span class="input">
								<table width="100%" id="attrTable">
									<tbody>
										<?php
											if(empty($item['rebate'])){
										?>
										<tr>
											<td>
												<a href="javascript:;" onclick="addLevel(this)">[+]第1代</a>
												返佣人数<input type="text" notnull="" name="rebate[1][num]" value="" id="people" size="10" />
												返佣金额<input type="text" notnull="" name="rebate[1][money]" value="" id="money" size="10" />
												<font class="tips">人数0表示不限制</font>
											</td>
										</tr>
										<?php
											}
											if(!empty($item['rebate'])):
											$rebate = json_decode($item['rebate'],TRUE);
											foreach($rebate as $key=>$val):
										?>
										<tr>
											<td>
												<?php if($key == 1): ?>
												<a href="javascript:;" onclick="addLevel(this)">[+]第<?=$key?>代</a>
												<?php else: ?>
												<a href="javascript:;" onclick="delLevel(this)">[-]第<?=$key?>代</a>
												<?php endif;?>
												返佣人数<input type="text" notnull=""  name="rebate[<?=$key?>][num]" id="people" value="<?=$val['num']?>" size="10" />
												返佣金额<input type="text" notnull="" name="rebate[<?=$key?>][money]" id="money" value="<?=$val['money']?>" size="10" />
												<font class="tips">人数0表示不限制</font>
											</td>
										</tr>
										<?php endforeach;endif;?>
									</tbody>
								</table>
							</span>
							<div class="clear"></div>
						</div>
						
						<div class="rows">
							<label>是否启用</label>
							<span class="input">
								<input type="checkbox" value="1" <?php echo ($item['status'] === "0")?"":"checked"; ?> name="status"  />  启用
							</span>
							<div class="clear"></div>
						</div>
						

						<div class="rows">
							<label></label>
							<span class="input">
								<input type="hidden" name="id" value="<?= $item["id"] ?>" />
								<input type="submit" class="btn_green" name="submit" id="submit" value="提交保存" />
								<a href="" class="btn_gray">返回</a></span>
							<div class="clear"></div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<script>
		var i = 1;
		var attrHtml = '<tr>\
							<td>\
								<a href="javascript:;" onclick="delLevel(this)">[-]第##代</a>\
								返佣人数 <input type="text" notnull="" name="rebate[##][num]" value="" size="10" />\
								返佣金额 <input type="text" notnull="" name="rebate[##][money]" value="" size="10" />\
								<font class="tips">人数0表示不限制</font>\
							</td>\
						</tr>';
		function addLevel(){
			++i;
			$("#attrTable tbody").append(attrHtml.replace(/##/g, i));
		}
		function delLevel(obj){
			var num = $(obj).parent().parent().index() + 1;
			var all = $("#attrTable tbody tr").length;
			if(num < all){
				alert("请按顺序删除，不能断层");
				return;
			}else{
				i--;
				$(obj).parent().parent().remove();
			}
		}
		</script>
	</body>
</html>