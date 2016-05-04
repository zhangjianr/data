<?php 
if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}
if($_POST)
{
	if(empty($_POST['Title'])){
		echo '<script language="javascript">alert("请填写标题");history.go(-1);</script>';exit;
	}
	if(empty($_POST['ImgPath'])){
		echo '<script language="javascript">alert("请上传封面图片");history.go(-1);</script>';exit;
	}
	if(empty($_POST['Url'])){
		echo '<script language="javascript">alert("请选择要链接的页面");history.go(-1);</script>';exit;
	}
	$_POST['TextContents'] = str_replace("\r\n","<br />",$_POST['TextContents']);
	$_POST['TextContents'] = str_replace("\n","<br />",$_POST['TextContents']);
	$Material=array(
		"Title"=>$_POST["Title"],
		"ImgPath"=>$_POST["ImgPath"],
		"TextContents"=>$_POST['TextContents'],
		"Url"=>$_POST['Url']
	);
	$Data=array(
		"Material_Type"=>0,
		"Material_Table"=>0,
		"Material_TableID"=>0,
		"Material_Display"=>1,
		"Users_ID"=>$_SESSION["Users_ID"],
		"Material_Json"=>json_encode($Material,JSON_UNESCAPED_UNICODE),
		"Material_CreateTime"=>time()		
	);
	$Flag=$DB->Add("wechat_material",$Data);
	if($Flag)
	{
		echo '<script language="javascript">alert("添加成功");window.location="index.php";</script>';
	}else
	{
		echo '<script language="javascript">alert("保存失败");history.back();</script>';
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
<script type="text/javascript" src="/third_party/uploadify/jquery.uploadify.min.js"></script>
<link href="/third_party/uploadify/uploadify.css" rel="stylesheet" type="text/css">
</head>

<body>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->
<style type="text/css">
body, html{background:url(/static/member/images/main/main-bg.jpg) left top fixed no-repeat;}
</style>
<div id="iframe_page">
  <div class="iframe_content">
    <link href='/static/member/css/material.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/member/js/material.js'></script>
    <div class="r_nav">
      <ul>
        <li class="cur"><a href="index.php">图文消息管理</a></li>
        <li class=""><a href="url.php">自定义URL</a></li>
        <li class=""><a href="sysurl.php">系统URL查询</a></li>
      </ul>
    </div>
    <link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script>
    <div id="material" class="r_con_wrap">
      <form method="post" action="add.php" id="material_form">
        <script language="javascript">$(document).ready(material_obj.material_one_init);</script>
        <div class="m_lefter one">
          <div class="title">消息标题</div>
          <div>2014-01-13</div>
          <div class="img" id="ImgDetail">封面图片</div>
          <div class="txt"></div>
        </div>
        <div class="m_righter">
          <div class="mod_form">
            <div class="jt"><img src="/static/member/images/material/jt.gif" /></div>
            <div class="m_form"> <span class="fc_red">*</span> 标题<br />
              <div class="input">
                <input name="Title" value="" type="text" />
              </div>
              <div class="blank20"></div>
              <span class="fc_red">*</span> 封面图片 <span class="tips">大图尺寸建议：640*360px</span><br />
              <div class="blank6"></div>
              <div>
                <input id="ImgUpload" name="ImgUpload" type="file">
                <input type="hidden" id="ImgPath" name="ImgPath" value="" />
              </div>
              <div class="blank12"></div>
              简短介绍<br />
              <div>
                <textarea name="TextContents"></textarea>
              </div>
              <div class="blank20"></div>
              <span class="fc_red">*</span> 链接页面<br />
              <div class="input">
                <select name='Url'>
                 <option value=''>--请选择--</option>
				<optgroup label='---------------系统业务模块---------------'></optgroup>
                <?php $DB->get("wechat_material","Material_ID,Material_Table,Material_Json","where Users_ID='".$_SESSION["Users_ID"]."' and Material_Table<>'0' and Material_TableID=0 and Material_Display=0 order by Material_ID asc");
				while($rsMaterial=$DB->fetch_assoc()){
					$Material_Json=json_decode($rsMaterial['Material_Json'],true);
					echo '<option value="/api/'.$_SESSION["Users_ID"].'/'.$rsMaterial['Material_Table'].'/">'.$Material_Json['Title'].'</option>';
				}?>
            
				<optgroup label="------------------微商城产品分类页面------------------"></optgroup>
				<?php $DB->get("shop_category","*","where Users_ID='".$_SESSION["Users_ID"]."' and Category_ParentID=0 order by Category_Index asc");
				$ParentCategory=array();
				$i=1;
				while($rsPCategory=$DB->fetch_assoc()){
					$ParentCategory[$i]=$rsPCategory;
					$i++;
				}
				foreach($ParentCategory as $key=>$value){
					$DB->get("shop_category","*","where Users_ID='".$_SESSION["Users_ID"]."' and Category_ParentID=".$value["Category_ID"]." order by Category_Index asc");
					if($DB->num_rows()>0){
						echo '<option value="/api/'.$_SESSION["Users_ID"].'/shop/category/'.$value["Category_ID"].'/">'.$value["Category_Name"].'</option>';
						while($rsCategory=$DB->fetch_assoc()){
							echo '<option value="/api/'.$_SESSION["Users_ID"].'/shop/category/'.$rsCategory["Category_ID"].'/">&nbsp;&nbsp;├'.$rsCategory["Category_Name"].'</option>';
						}
					}else{
						echo '<option value="/api/'.$_SESSION["Users_ID"].'/shop/category/'.$value["Category_ID"].'/">'.$value["Category_Name"].'</option>';
					}
				}?>
			 
				<optgroup label="------------------自定义URL------------------"></optgroup>
				<?php $DB->get("wechat_url","*","where Users_ID='".$_SESSION["Users_ID"]."'");
				  while($rsUrl=$DB->fetch_assoc()){
					  echo '<option value="'.$rsUrl['Url_Value'].'">'.$rsUrl['Url_Name'].'('.$rsUrl['Url_Value'].')</option>';
				  }?>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="clear"></div>
        <div class="button">
          <input type="submit" class="btn_green" name="submit_button" value="提交保存" />
          <a href="index.php" class="btn_gray">返回</a></div>
      </form>
    </div>
  </div>
</div>
</body>
</html>