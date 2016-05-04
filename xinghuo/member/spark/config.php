<?php 
$DB->showErr = false;
if (empty($_SESSION["Users_Account"])) {
	header("location:/member/login.php");
}
$item = $DB->GetRs("users", "Users_Sms", "where Users_ID='" . $_SESSION["Users_ID"] . "'");
$rsConfig = $DB->GetRs("spark_config", "*", "where Users_ID='" . $_SESSION["Users_ID"] . "'");

if ($_POST) {
	$_GPC["content"] = str_replace('"', '&quot;', $_GPC["content"]);
	$_GPC["content"] = str_replace("'", "&quot;", $_POST["content"]);
	$_GPC["content"] = str_replace('>', '&gt;', $_GPC["content"]);
	$_GPC["content"] = str_replace('<', '&lt;', $_GPC["content"]);
	$Data = array(
		"Users_ID" => $_SESSION["Users_ID"],
		"shareTitle" => $_GPC["shareTitle"],
		"isAuto" => $_GPC["isAuto"],
        "isShop" => $_GPC['isShop'],
		"shareLogo" => $_GPC["shareLogo"],
		"payMethod" => $_GPC['payMethod'],
		"sparkTitle" => $_GPC['sparkTitle'],
		"myTitle" => $_GPC['myTitle'],
		"content" => $_GPC["content"],
		"traAccount"=>trim($_GPC["traAccount"]),
		"traAppkey"=>trim($_GPC["traAppkey"]),
		"updateTime"=>time()	
	);
        if($_GPC['isShop'] == "1"){
            if(!empty($_GPC["nickNameId"])){
                $arr = checkUserName($_GPC["nickNameId"]);
                if($arr){
                    $Data["nickNameId"] = $_GPC["nickNameId"];
                }else{
                    echo '<script language="javascript">alert("设置失败,缺少参数");history.back();</script>';
                    exit; 
                }
                
            }else{
                echo '<script language="javascript">alert("设置失败,缺少参数");history.back();</script>';
                exit;
            }
        }
        if($_GPC['isUpDis']){
            $Data["isUpDis"] = $_GPC['isUpDis'];
        }else{
             $Data["isUpDis"] = 0;
        }
	if (empty($rsConfig)) {
		$flag = $DB->Add("spark_config", $Data);
	} else {
		$flag = $DB->Set("spark_config", $Data, "where Users_ID='" . $_SESSION["Users_ID"] . "'");
	}

	if ($flag === FALSE) {
		echo '<script language="javascript">alert("设置失败");history.back();</script>';
	} else {
		echo '<script language="javascript">alert("设置成功");window.location="config.php";</script>';
	}
	exit;
}
function checkUserName($name){
    global $DB;
    $arr = $DB->GetR("user","*"," where Users_ID = '" . $_SESSION["Users_ID"] . "' AND  User_No = '{$name}'");
    if($arr){
        return $arr;
    }else{
        return false;
    }
    exit;
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
<script type='text/javascript' src='/static/member/js/shop.js'></script>
<link rel="stylesheet" href="/third_party/kindeditor/themes/default/default.css" />
<script type='text/javascript' src="/third_party/kindeditor/kindeditor-min.js"></script>
<script type='text/javascript' src="/third_party/kindeditor/lang/zh_CN.js"></script>
<script>
KindEditor.ready(function(K) {
	var editor = K.editor({
		uploadJson : '/member/upload_json.php?TableField=web_article',
		fileManagerJson : '/member/file_manager_json.php',
		showRemote : true,
		allowFileManager : true,
	});
	K('#shareLogoUpload').click(function(){
		editor.loadPlugin('image', function(){
			editor.plugin.imageDialog({
				imageUrl : K('#shareLogo').val(),
				clickFn : function(url, title, width, height, border, align){
					K('#shareLogo').val(url);
					K('#shareLogoDetail').html('<img src="'+url+'" />');
					editor.hideDialog();
				}
			});
		});
	});
	KindEditor.ready(function(K) {
			K.create('textarea[name="content"]', {
				themeType : 'simple',
				filterMode : false,
				uploadJson : '/third_party/kindeditor/php/upload_json.php',
				fileManagerJson : '/third_party/kindeditor/php/file_manager_json.php',
				allowFileManager : true
			});
		});
})		
</script>
<style type="text/css">
#config_form img{width:100px; height:100px;}
.files {
    position: relative;
    display: inline-block;
    background: #D0EEFF;
    border: 1px solid #99D3F5;
    border-radius: 4px;
    padding: 4px 12px;
    overflow: hidden;
    color: #1E88C7;
    text-decoration: none;
    text-indent: 0;
    line-height: 20px;
}
.files input {
    position: absolute;
    font-size: 100px;
    right: 0;
    top: 0;
    opacity: 0;
}
.files:hover {
    background: #AADFFD;
    border-color: #78C3F3;
    color: #004974;
    text-decoration: none;
}
 #methods{ height: 32px;
    border: 1px solid #ddd;
    padding: 5px;
    vertical-align: middle;
    border-radius: 5px;}
</style>
</head>

<body>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->

<div id="iframe_page">
	<div class="iframe_content">
		<div class="r_nav">
			<ul>
				<li class=""><a href="package.php">套餐管理</a></li>
				<li class=""><a href="order.php">订单管理</a></li>
				<li class=""><a href="slide.php">幻灯管理</a></li>
				<li class=""><a href="rebate.php">返佣管理</a></li>
				<li class=""><a href="traffic.php">购买流量包</a></li>
				<li class="cur"><a href="config.php">基本配置</a></li>
			</ul>
		</div>
		<div class="r_con_config r_con_wrap">
			<form id="config_form" action="config.php" method="post" enctype="multipart/form-data">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<h1><strong>分享页面标题</strong></h1>
							<input type="text" class="input" name="shareTitle" value="<?php echo $rsConfig["shareTitle"] ?>" maxlength="30" notnull />
						</td>
					</tr>

					<tr>
						<td>
							<h1><strong>自定义分享图片</strong></h1>
							<div id="card_style">
								<div class="file">
									<span class="tips">&nbsp;&nbsp;尺寸建议：100*100px</span><br /><br />
									<input name="shareLogoUpload" id="shareLogoUpload" type="button" style="width:80px;" value="上传图片" /><br /><br />
									<div class="img" id="shareLogoDetail">
										<?php if(!empty($rsConfig['shareLogo'])): ?>
											<img src="<?=$rsConfig['shareLogo']?>" />
										<?php endif;?>
									</div>
									<input type="hidden" id="shareLogo" name="shareLogo" value="<?php echo $rsConfig && $rsConfig['shareLogo'] <> '' ? $rsConfig['shareLogo'] : '' ?>" />
								</div>
								<div class="clear"></div>
							</div>               
						</td>
					</tr>
					<tr>
						<td>
							<h1><strong>购买产品包条件</strong><span class="tips">（打开后下必须有推荐人才能购买）</span></h1>
							<select name="isShop" id="isShop" class="js-example-basic-multiple">
							  <option value="0" <?=$rsConfig['isShop']==0?"selected":""?> id="is1">-默认关闭-</option>
							  <option value="1" <?=$rsConfig['isShop']==1?"selected":""?> id="is2">打开才能购买</option>
							</select>
						</td>
					</tr>
                                        
                    <tr>
						<td>
							<h1><strong>打开购买产品包条件后填写</strong><span class="tips">（填写会员编号）</span></h1>
							<input type="text" class="input" name="nickNameId" value="<?php echo $rsConfig["nickNameId"] ?>" placeholder="nickNameId"/>
						</td>
					</tr>

					<tr>
						<td>
							<h1><strong>自动返佣</strong><span class="tips">（关闭后下返佣需要手工审核）</span></h1>
							<div class="input">
								<input type="checkbox" name="isAuto" value="1"<?php echo empty($rsConfig["isAuto"]) ? "" : " checked"; ?> />
								<span class="tips">微信企业付款，请保证账户有足够金额</span>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<h1><strong>返佣付款方式：</strong></h1>
							<select name="payMethod"  id="methods" class="js-example-basic-multiple">
							  <option value="0" <?=$rsConfig['payMethod']==0?"selected":""?>>-请选择-</option>
							  <option value="1" <?=$rsConfig['payMethod']==1?"selected":""?>>个人审核</option>
							  <option value="2" <?=$rsConfig['payMethod']==2?"selected":""?>>微信红包</option>
							  <option value="3" <?=$rsConfig['payMethod']==3?"selected":""?>>企业付款</option>
							</select>

						</td>
					</tr>
                                        
                    <tr>
						<td>
							<h1><strong>升级返佣</strong><span class="tips">（下级升级是否返佣）</span></h1>
							<div class="input">
								<input type="checkbox" name="isUpDis" value="1"<?php echo empty($rsConfig["isUpDis"]) ? "" : " checked"; ?> />
								<span class="tips">默认升级返佣</span>
							</div>
						</td>
					</tr>
					
					<tr>
						<td>
							<h1><strong>手机短信账户</strong><span class="tips"></span></h1>
							<input type="text" class="input" name="traAccount" value="<?php echo $rsConfig["traAccount"] ?>" />
						</td>
					</tr><tr>
						<td>
							<h1><strong>手机短信接口key</strong><span class="tips"></span></h1>
							<input type="text" class="input" name="traAppkey" value="<?php echo $rsConfig["traAppkey"] ?>" />
						</td>
					</tr>
					

					<tr>
						<td>
							<h1><strong>星火草原首页标题</strong><span class="tips">（文字过长会被隐藏）</span></h1>
							<input type="text" class="input" name="sparkTitle" value="<?php echo $rsConfig["sparkTitle"] ?>" />
						</td>
					</tr>
					<tr>
						<td>
							<h1><strong>家族中心页面标题</strong><span class="tips">（文字过长会被隐藏）</span></h1>
							<input type="text" class="input" name="myTitle" value="<?php echo $rsConfig["myTitle"] ?>" />
						</td>
					</tr>
					<!--协议添加-->
					<tr>
						<td>
							<h1><strong>用户协议:</strong></h1>
							<textarea name="content" style="width:70%;height:400px;visibility:hidden;"><?php echo $rsConfig["content"];?></textarea>
						</td>
					</tr>
				</table>
				<div class="submit">
					<input type="submit" name="submit_button" value="提交保存" />
				</div>
		  </form>
		</div>
	</div>
</div>
<script>
$("#file").change(function(){
	var a =$(this).val();
	$("#filename").html(a);
});
</script>
</body>
</html>