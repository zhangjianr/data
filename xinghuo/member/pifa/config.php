<?php
$DB->showErr=false;
if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}
$item = $DB->GetRs("users","Users_Sms","where Users_ID='".$_SESSION["Users_ID"]."'");
$rsConfig = $DB->GetRs("pifa_config","*","where Users_ID='".$_SESSION["Users_ID"]."'");
$json=$DB->GetRs("wechat_material","*","where Users_ID='".$_SESSION["Users_ID"]."' and Material_Table='pifa' and Material_TableID=0 and Material_Display=0");
if(empty($json)){
	$Material=array(
		"Title"=>"批发商城",
		"ImgPath"=>"/static/api/images/cover_img/pifa.jpg",
		"TextContents"=>"",
		"Url"=>"/api/".$_SESSION["Users_ID"]."/pifa/"
	);
	
	$Data=array(
		"Users_ID"=>$_SESSION["Users_ID"],
		"Material_Table"=>"pifa",
		"Material_TableID"=>0,
		"Material_Display"=>0,
		"Material_Type"=>0,
		"Material_Json"=>json_encode($Material,JSON_UNESCAPED_UNICODE),
		"Material_CreateTime"=>time()
	);
	$DB->Add("wechat_material",$Data);
	$MaterialID = $DB->insert_id();
	$rsMaterial = $Material;
}else{
	$rsMaterial = json_decode($json['Material_Json'],true);
}
$rsKeyword = $DB->GetRs("wechat_keyword_reply","*","where Users_ID='".$_SESSION["Users_ID"]."' and Reply_Table='pifa' and Reply_TableID=0 and Reply_Display=0");
if(empty($rsKeyword)){
	$MaterialID = empty($json['Material_Json'])?$MaterialID:$json['Material_Json'];
	$Data=array(
		"Users_ID"=>$_SESSION["Users_ID"],
		"Reply_Table"=>"pifa",
		"Reply_TableID"=>0,
		"Reply_Display"=>0,
		"Reply_Keywords"=>"批发商城",
		"Reply_PatternMethod"=>0,
		"Reply_MsgType"=>1,
		"Reply_MaterialID"=>$MaterialID,
		"Reply_CreateTime"=>time()
	);
	$DB->Add("wechat_keyword_reply",$Data);
	$rsKeyword=$Data;
}

if($_POST)
{
	//开始事务定义
	$flag=true;
	$msg="";
	mysql_query("begin");
	$Data=array(
	    "Users_ID"=>$_SESSION["Users_ID"],
		"PifaName"=>$_POST["PifaName"],
		"p_NeedShipping"=>isset($_POST["NeedShipping"])?$_POST["NeedShipping"]:0,
		"p_SendSms"=>isset($_POST["SendSms"])?$_POST["SendSms"]:0,
		"p_MobilePhone"=>empty($_POST["MobilePhone"])?'':$_POST["MobilePhone"],
		"p_Commit_Check"=>empty($_POST["CommitCheck"])?0:$_POST["CommitCheck"],
	);
	if(empty($rsConfig)){
		$Set=$DB->Add("pifa_config",$Data);
	}else{
		$Set=$DB->Set("pifa_config",$Data,"where Users_ID='".$_SESSION["Users_ID"]."'");
	}
    $flag=$flag&&$Set;
	$Data=array(
		"Reply_Keywords"=>$_POST["Keywords"],
		"Reply_PatternMethod"=>isset($_POST["PatternMethod"])?$_POST["PatternMethod"]:0
	);
	
    $Set=$DB->Set("wechat_keyword_reply",$Data,"where Users_ID='".$_SESSION["Users_ID"]."' and Reply_Table='pifa' and Reply_TableID=0 and Reply_Display=0");	
	
	$flag=$flag&&$Set;
	$Material=array(
		"Title"=>$_POST["Title"],
		"ImgPath"=>$_POST["ImgPath"],
		"TextContents"=>"",
		"Url"=>"/api/".$_SESSION["Users_ID"]."/pifa/"
	);
	$Data=array(
		"Material_Json"=>json_encode($Material,JSON_UNESCAPED_UNICODE)
	);

	$Set=$DB->Set("wechat_material",$Data,"where Users_ID='".$_SESSION["Users_ID"]."' and Material_Table='pifa' and Material_TableID=0 and Material_Display=0");
	$flag=$flag&&$Set;
	if($flag)
	{
		mysql_query("commit");
		echo '<script language="javascript">alert("设置成功");window.location="config.php";</script>';
	}else
	{
		mysql_query("roolback");
		echo '<script language="javascript">alert("设置失败");history.back();</script>';
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
<script type='text/javascript' src='/static/member/js/pifa.js'></script>
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
	
	K('#ReplyImgUpload').click(function(){
		editor.loadPlugin('image', function(){
			editor.plugin.imageDialog({
				imageUrl : K('#ReplyImgPath').val(),
				clickFn : function(url, title, width, height, border, align){
					K('#ReplyImgPath').val(url);
					K('#ReplyImgDetail').html('<img src="'+url+'" />');
					editor.hideDialog();
				}
			});
		});
	});
})
</script>
<style type="text/css">
#config_form img {
	width: 100px;
	height: 100px;
}
</style>
</head>

<body>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->

<div id="iframe_page">
	<div class="iframe_content">
		<link href='/static/member/css/pifa.css' rel='stylesheet' type='text/css' />
		<div class="r_nav">
			<ul>
				<li class="cur"><a href="config.php">基本设置</a></li>
				<li><a href="products.php">商品管理</a></li>
				<li><a href="category.php">商品分类</a></li>
				<li><a href="orders.php">订单管理</a></li>
				<li ><a href="commit.php">评论管理</a></li>
			</ul>	
		</div>
		<link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
		<script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script> 
		<script language="javascript">$(document).ready(function(){
		global_obj.config_form_init();
		pifa_obj.confirm_form_init();
	});</script>
		<div class="r_con_config r_con_wrap">
			<form id="config_form" action="config.php" method="post">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="50%" valign="top"><h1><span class="fc_red">*</span> <strong>批发商城名称</strong></h1>
							<input type="text" class="input" name="PifaName" value="<?php echo $rsConfig["PifaName"] ?>" maxlength="30" notnull /></td>
						<td width="50%" valign="top"><h1><strong>需要物流</strong><span class="tips">（关闭后下订单无需填写收货地址）</span></h1>
							<div class="input">
								<input type="checkbox" name="NeedShipping" value="1"<?php echo empty($rsConfig["p_NeedShipping"])?"":" checked"; ?> />
								<span class="tips">如果您提供的是本地化服务，无需物流，请关闭</span></div></td>
					</tr>
					<tr>
						<td width="50%" valign="top"><h1><strong>订单手机短信通知</strong>
								<input type="checkbox" name="SendSms" value="1"<?php echo empty($rsConfig["p_SendSms"])?"":" checked"; ?> />
								<span class="tips">启用（填接收短信的手机号）</span></h1>
							<input type="text" class="input" name="MobilePhone" style="width:120px" value="<?php echo $rsConfig["p_MobilePhone"] ?>" maxlength="11" />
							<span class="tips"> 短信剩余 <font style="color:red"><?php echo $item["Users_Sms"];?></font> 条&nbsp;&nbsp;<a href="/member/sms/sms_add.php" style="color:#F60; text-decoration:underline">点击购买</a></span></td>
						<td width="50%" valign="top"><h1><strong>评论审核</strong><span class="tips">（关闭后客户评论可不经过审核直接显示在前台页面）</span></h1>
						  <div class="input">
							<input type="checkbox" name="CommitCheck" value="1"<?php echo empty($rsConfig["p_Commit_Check"])?"":" checked"; ?> /> <span class="tips">关闭</span>
						  </div>
						</td>
					</tr>
				</table>
				<table align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td><h1><strong>触发信息设置</strong></h1>
							<div class="reply_msg">
								<div class="m_left"> <span class="fc_red">*</span> 触发关键词<span class="tips_key">（有多个关键词请用 <font style="color:red">"|"</font> 隔开）</span><br />
									<input type="text" class="input" name="Keywords" value="<?php echo $rsKeyword["Reply_Keywords"] ?>" maxlength="100" notnull />
									<br />
									<br />
									<br />
									<span class="fc_red">*</span> 匹配模式<br />
									<div class="input">
										<input type="radio" name="PatternMethod" value="0"<?php echo empty($rsKeyword["Reply_PatternMethod"])?" checked":""; ?> />
										精确匹配<span class="tips">（输入的文字和此关键词一样才触发）</span></div>
									<div class="input">
										<input type="radio" name="PatternMethod" value="1"<?php echo $rsKeyword["Reply_PatternMethod"]==1?" checked":""; ?> />
										模糊匹配<span class="tips">（输入的文字包含此关键词就触发）</span></div>
									<br />
									<br />
									<span class="fc_red">*</span> 图文消息标题<br />
									<input type="text" class="input" name="Title" value="<?php echo $rsMaterial["Title"] ?>" maxlength="100" notnull />
								</div>
								<div class="m_right"> <span class="fc_red">*</span> 图文消息封面<span class="tips">（大图尺寸建议：640*360px）</span><br />
									<div class="file">
										<input name="ReplyImgUpload" id="ReplyImgUpload" type="button" style="width:80px;" value="上传图片" />
									</div>
									<br />
									<div class="img" id="ReplyImgDetail"> <?php echo $rsMaterial["ImgPath"] ? '<img src="'.$rsMaterial["ImgPath"].'" />' : '';?> </div>
								</div>
								<div class="clear"></div>
							</div>
							<input type="hidden" id="ReplyImgPath" name="ImgPath" value="<?php echo $rsMaterial["ImgPath"] ?>" /></td>
					</tr>
				</table>
				<div class="submit">
					<input type="submit" name="submit_button" value="提交保存" />
				</div>
			</form>
		</div>
	</div>
</div>
</body>
</html>