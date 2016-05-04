<?php defined('SYS_ACCESS') or exit('Access Denied'); ?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<title>添加幻灯</title>
		<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
		<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
		<link href='/static/member/css/shop.css' rel='stylesheet' type='text/css' />
		
		<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
		<script type='text/javascript' src='/static/member/js/global.js'></script>
		
		<link rel="stylesheet" href="/third_party/kindeditor/themes/default/default.css" />
		<script type='text/javascript' src="/third_party/kindeditor/kindeditor-min.js"></script>
		<script type='text/javascript' src="/third_party/kindeditor/lang/zh_CN.js"></script>
		<script type='text/javascript' src='/static/member/js/shop.js'></script>
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
						<li class=""><a href="package.php">套餐管理</a></li>
						<li class=""><a href="order.php">订单管理</a></li>
						<li class="cur"><a href="slide.php">幻灯管理</a></li>
                                                <li class=""><a href="rebate.php">返佣管理</a></li>
						<li class=""><a href="config.php">基本配置</a></li>
					</ul>
				</div>
				
				<div id="products" class="r_con_wrap">
					<form id="product_add_form" class="r_con_form" method="post" action="?op=post">
						<div class="rows">
							<label>幻灯名称</label>
							<span class="input">
								<input type="text" name="title" value="<?=$item['title']?>" class="form_input" size="35"/>
								<font class="fc_red">*</font>
							</span>
							<div class="clear"></div>
						</div>
						<div class="rows">
							<label>缩略图</label>
							<span class="input">
								<span class="upload_file">
									<div>
										<div class="up_input">
											<input id="ImgUpload" name="ImgUpload" type="button" style="width:80px" value="上传图片">
											<input type="hidden" id="thumb" name="thumb" value="<?=$item['thumb']?>" />
										</div>
										<div class="tips">建议:200x200</div>
										<div class="clear"></div>
									</div>
									<div class="img" id="ImgDetail">
										<?php if(!empty($item['thumb'])){ ?><img src="<?=$item['thumb']?>" width="100px" /><?php }?>
									</div>
								</span>
							</span>
							<div class="clear"></div>
						</div>
						
						<div class="rows">
							<label>排序</label>
							<span class="input">
								<input type="text" name="displayOrder" value="<?=$item['displayOrder']?>" class="form_input" size="35" />
								<font class="fc_red">*</font>
							</span>
							<div class="clear"></div>
						</div>
						<div class="rows">
							<label>链接</label>
							<span class="input">
								<input type="text" name="url" value="<?=$item['url']?>" class="form_input" size="35" />
								<font class="fc_red">*</font>
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
								<input type="submit" class="btn_green" name="submit" value="提交保存" />
								<a href="" class="btn_gray">返回</a></span>
							<div class="clear"></div>
						</div>
					</form>
				</div>
			</div>
		</div>
		
	</body>
</html>