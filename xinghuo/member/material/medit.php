<?php 
if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}
$MaterialID=empty($_REQUEST['MaterialID'])?0:$_REQUEST['MaterialID'];
$rsMaterial=$DB->GetRs("wechat_material","*","where Users_ID='".$_SESSION["Users_ID"]."' and Material_ID=".$MaterialID." and Material_TableID=0 and Material_Display=1");
$json=json_decode($rsMaterial['Material_Json'],true);
if($_POST)
{
	foreach($_POST["Title"] as $key=>$value)
	{
		$Material[$key]=array(
			"Title"=>$_POST["Title"][$key],
			"Url"=>$_POST['Url'][$key],
			"ImgPath"=>$_POST["ImgPath"][$key]
		);
	}
	
	$Data=array(
		"Material_Json"=>json_encode($Material,JSON_UNESCAPED_UNICODE)
	);
	$Flag=$DB->Set("wechat_material",$Data,"where Users_ID='".$_SESSION["Users_ID"]."' and Material_ID=".$MaterialID." and Material_TableID=0 and Material_Display=1");
	if($Flag)
	{
		echo '<script language="javascript">alert("修改成功");window.location="index.php";</script>';
	}else
	{
		echo '<script language="javascript">alert("修改失败");history.back();</script>';
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
        <li class="cur"><a href="./?m=material&a=index">图文消息管理</a></li>
        <li class=""><a href="./?m=material&a=url">自定义URL</a></li>
        <li class=""><a href="./?m=material&a=url_list">系统URL查询</a></li>
      </ul>
    </div>
    <div id="material" class="r_con_wrap">
      <form id="material_form" method="post" action="medit.php">
        <script language="javascript">$(document).ready(material_obj.material_multi_init);</script>
        <div class="m_lefter multi">
          <div class="time"><?php echo date("Y-m-d",$rsMaterial['Material_CreateTime']) ?></div>
<?php foreach($json as $k=>$v){?>
          <div class="<?php echo $k?"list":"first" ?>" id="multi_msg_<?php echo $k ?>">
            <div class="info">
              <div class="img"><img src='<?php echo $v['ImgPath'] ?>' /></div>
              <div class="title"><?php echo $v['Title'] ?></div>
            </div>
            <div class="control"> <a href="#mod"><img src="/static/member/images/ico/mod.gif" /></a> </div>
            <input type="hidden" name="Title[]" value="<?php echo $v['Title'] ?>" />
            <input type="hidden" name="Url[]" value="<?php echo $v['Url'] ?>" />
            <input type="hidden" name="ImgPath[]" value="<?php echo $v['ImgPath'] ?>" />
          </div>
<?php }?>
          <div class="add"><a href="#add"><img src="/static/member/images/ico/add.gif" align="absmiddle" /> 增加一条</a></div>
        </div>
        <div class="m_righter">
          <div class="mod_form">
            <div class="jt"><img src="/static/member/images/material/jt.gif" /></div>
            <div class="m_form"> <span class="fc_red">*</span> 标题<br />
              <div class="input">
                <input name="inputTitle" value="" type="text" notnull />
              </div>
              <div class="blank9"></div>
              <span class="fc_red">*</span> 封面图片 <span class="tips">大图尺寸建议：<span class="big_img_size_tips">640*360px</span></span><br />
              <div class="blank6"></div>
              <div>
                <input name="ImgUpload" id="ImgUpload" type="file" />
              </div>
              <div class="blank3"></div>
              <span class="fc_red">*</span> 链接页面<br />
              <div class="input">
                <select name='inputUrl'>
                  <option value=''>--请选择--</option>
                  <optgroup label='---------------系统业务模块---------------'></optgroup>
                  <?php $DB->get("wechat_material","Material_ID,Material_Table,Material_Json","where Users_ID='".$_SESSION["Users_ID"]."' and Material_Table<>'0' and Material_TableID=0 and Material_Display=0 order by Material_ID desc");
				while($rsMaterial=$DB->fetch_assoc()){
					$Material_Json=json_decode($rsMaterial['Material_Json'],true);
					echo '<option value="/api/'.$_SESSION["Users_ID"].'/'.$rsMaterial['Material_Table'].'/">'.$Material_Json['Title'].'</option>';
				}?>
                 
                  <optgroup label="-----------微商城产品分类页面-----------"></optgroup>
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
          <a href="#" onclick="history.go(-1);" class="btn_gray">返回</a></div>
        <input type="hidden" name="MaterialID" value="<?php echo $MaterialID ?>" />
      </form>
    </div>
  </div>
</div>
</body>
</html>