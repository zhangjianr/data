<?php 
if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}
require_once('vertify.php');
if($_POST){	
	$Flag=true;
	$msg="";
	mysql_query("begin");
	$_POST['Description'] = str_replace('"','&quot;',$_POST['Description']);
	$_POST['Description'] = str_replace("'","&quot;",$_POST['Description']);
	$_POST['Description'] = str_replace('>','&gt;',$_POST['Description']);
	$_POST['Description'] = str_replace('<','&lt;',$_POST['Description']);
	$Data=array(
		"Article_Title"=>$_POST['Title'],
		"Article_Index"=>$_POST['Index'] ? intval($_POST['Index']) : 0,
		"Column_ID"=>$_POST['Column_ID'],
		"Article_ImgPath"=>$_POST['ImgPath'],
		"Article_Link"=>empty($_POST['Link'])?0:$_POST['Link'],
		"Article_LinkUrl"=>$_POST["LinkUrl"],
		"Article_BriefDescription"=>$_POST['BriefDescription'],
		"Article_Description"=>$_POST['Description'],
		"Users_ID"=>$_SESSION["Users_ID"],
		"Article_CreateTime"=>time()
	);
	$Add=$DB->Add("web_article",$Data);
	$TableID=$DB->insert_id();
	$Flag=$Flag&&$Add;
	
	$Material=array(
		"Title"=>$_POST["Title"],
		"ImgPath"=>$_POST["FPath"],
		"TextContents"=>$_POST["BriefDescription"],
		"Url"=>"/api/".$_SESSION["Users_ID"]."/web/article/".$TableID."/"
	);
	$Data=array(
		"Users_ID"=>$_SESSION["Users_ID"],
		"Material_Table"=>"web",
		"Material_TableID"=>$TableID,
		"Material_Display"=>0,
		"Material_Type"=>0,
		"Material_Json"=>json_encode($Material,JSON_UNESCAPED_UNICODE),
		"Material_CreateTime"=>time()
	);
	$Add=$DB->Add("wechat_material",$Data);
	$MaterialID=$DB->insert_id();
	$Flag=$Flag&&$Add;
	
	$Data=array(
		"Users_ID"=>$_SESSION["Users_ID"],
		"Reply_Table"=>"web",
		"Reply_TableID"=>$TableID,
		"Reply_Display"=>0,
		"Reply_Keywords"=>$_POST["Keywords"],
		"Reply_PatternMethod"=>0,
		"Reply_MsgType"=>1,
		"Reply_MaterialID"=>$MaterialID,
		"Reply_CreateTime"=>time()
	);
	$Add=$DB->Add("wechat_keyword_reply",$Data);
	$Flag=$Flag&&$Add;
	
	if($Flag){
		mysql_query("commit");
		echo '<script language="javascript">alert("添加成功");window.location="article.php";</script>';
	}else{
		mysql_query("roolback");
		echo '<script language="javascript">alert("添加失败");history.back();</script>';
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
<link rel="stylesheet" href="/third_party/kindeditor/themes/default/default.css" />
<script type='text/javascript' src="/third_party/kindeditor/kindeditor-min.js"></script>
<script type='text/javascript' src="/third_party/kindeditor/lang/zh_CN.js"></script>
<script>
    KindEditor.ready(function(K) {
        K.create('textarea[name="Description"]', {
            themeType : 'simple',
			filterMode : false,
            uploadJson : '/member/upload_json.php?TableField=web_column',
            fileManagerJson : '/member/file_manager_json.php',
            allowFileManager : true,
			items : [
				'source', '|', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
				'removeformat', 'undo', 'redo', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|', 'emoticons', 'image', 'link' , '|', 'preview']
        });
    });
	KindEditor.ready(function(K){
		var editor = K.editor({
			uploadJson : '/member/upload_json.php?TableField=web_article',
            fileManagerJson : '/member/file_manager_json.php',
			showRemote : true,
            allowFileManager : true,
		});
		K('#ImgUpload').click(function() {
			editor.loadPlugin('image', function() {
				editor.plugin.imageDialog({
					imageUrl : K('#ImgPath').val(),
					clickFn : function(url, title, width, height, border, align) {
						K('#ImgPath').val(url);
						K('#ImgDetail').html('<img src="'+url+'" />');
						editor.hideDialog();
					}
				});
			});
		});
		K('#FUpload').click(function() {
			editor.loadPlugin('image', function() {
				editor.plugin.imageDialog({
					imageUrl : K('#FPath').val(),
					clickFn : function(url, title, width, height, border, align) {
						K('#FPath').val(url);
						K('#FDetail').html('<img src="'+url+'" />');
						editor.hideDialog();
					}
				});
			});
		});
	})
</script>
</head>

<body>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->

<div id="iframe_page">
  <div class="iframe_content">
    <link href='/static/member/css/web.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/member/js/web.js'></script>
    <div class="r_nav">
      <ul>
        <li class=""><a href="config.php">基本设置</a></li>
        <li class=""><a href="skin.php">风格设置</a></li>
        <li class=""><a href="home.php">首页设置</a></li>
        <li class=""><a href="column.php">栏目管理</a></li>
        <li class="cur"><a href="article.php">内容管理</a></li>
        <li class=""><a href="lbs.php">一键导航</a></li>
      </ul>
    </div>
    <div id="column" class="r_con_wrap">
      <link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
      <script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script>
      <script language="javascript">//$(document).ready(web_obj.column_article_init);</script>
      <form class="r_con_form" method="post" action="article_add.php">
	    <div class="rows">
          <label>排序</label>
          <span class="input">
          <input name="Index" value="" type="text" class="form_input" size="10" >
          越大越靠后</span>
          <div class="clear"></div>
        </div>
		<div class="rows">
          <label>触发关键词</label>
          <span class="input">
           <input name="Keywords" value="" type="text" class="form_input" size="40" maxlength="50"><span class="tips">&nbsp;匹配方式为“<font class="fc_red">精确匹配</font>”</span>
          </span>
          <div class="clear"></div>
        </div>
		<div class="rows">
          <label>图文消息封面</label>
          <span class="input"> <span class="upload_file">
          <div>
            <div class="up_input">
              <input type="button" id="FUpload" value="添加图片" style="width:80px;" />
            </div>
            <div class="tips">图片建议尺寸：640*360px</div>
            <div class="clear"></div>
          </div>
          <div class="img" id="FDetail"></div>
          </span> </span>
          <div class="clear"></div>
		  <input type="hidden" id="FPath" name="FPath" value="" />
        </div>
		
        <div class="rows">
          <label>内容标题</label>
          <span class="input">
          <input name="Title" value="" type="text" class="form_input" size="40" maxlength="50" notnull>
          <font class="fc_red">*</font></span>
          <div class="clear"></div>
        </div>		
        <div class="rows">
          <label>隶属栏目</label>
          <span class="input">
          <select name="Column_ID" notnull>
			<?php
			$DB->Get("web_column","*","where Users_ID='".$_SESSION["Users_ID"]."' and Column_ParentID=0 order by Column_Index asc");
			$Columns = array();
			while($r=$DB->fetch_assoc()){
				$Columns[] = $r;
			}
			foreach($Columns as $Column){
				echo '<option value="'.$Column['Column_ID'].'">'.$Column['Column_Name'].'</option>';
				$DB->Get("web_column","*","where Users_ID='".$_SESSION["Users_ID"]."' and Column_ParentID=".$Column['Column_ID']." order by Column_Index asc");
				while($item=$DB->fetch_assoc()){
					echo '<option value="'.$item['Column_ID'].'"> └ '.$item['Column_Name'].'</option>';
				}
			}?>
          </select>
          <font class="fc_red">*</font></span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label>上传图片</label>
          <span class="input"> <span class="upload_file">
          <div>
            <div class="up_input">
              <input id="ImgUpload" name="ImgUpload" type="button" style="width:80px" value="上传图片">
                <input type="hidden" id="ImgPath" name="ImgPath" value="" />
            </div>
            <div class="tips">大图尺寸建议：420*300px</div>
            <div class="clear"></div>
          </div>
          <div class="img" id="ImgDetail"></div>
          </span> </span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label>页面链接</label>
          <span class="input opt">
          <input type="checkbox" value="1" name="Link"  />
          <span id="LinkUrl_span">
          <select name='LinkUrl'>
            <option value=''>--请选择--</option>
            <optgroup label='---------------系统业务模块---------------'></optgroup>
            <?php $DB->getPage("wechat_material","Material_ID,Material_Table,Material_Json","where Users_ID='".$_SESSION["Users_ID"]."' and Material_Table<>'0' and Material_TableID=0 and Material_Display=0 order by Material_ID desc");
				while($Material=$DB->fetch_assoc()){
					$Material_Json=json_decode($Material['Material_Json'],true);
					echo '<option value="/api/'.$_SESSION["Users_ID"].'/'.$Material['Material_Table'].'/">'.$Material_Json['Title'].'</option>';
				}?>
            <optgroup label="---------------微官网二级页面---------------"></optgroup>
            <?php $DB->get("web_column","*","where Users_ID='".$_SESSION["Users_ID"]."' order by Column_Index asc");
				  $ColumnList=array();
				  $i=1;
				  while($Column=$DB->fetch_assoc()){
					  $ColumnList[$i]=$Column;
					  $i++;
				  }
				foreach($ColumnList as $key=>$value){
					echo '<option value="/api/'.$_SESSION["Users_ID"].'/web/column/'.$value['Column_ID'].'/">'.$value['Column_Name'].'</option>';
					$result = mysql_query("select * from web_article where Users_ID='".$_SESSION["Users_ID"]."' and Column_ID=".$value['Column_ID']);
					while($Article = mysql_fetch_assoc($result)){
						echo '<option value="/api/'.$_SESSION["Users_ID"].'/web/article/'.$Article["Article_ID"].'/">&nbsp;&nbsp;├'.$Article["Article_Title"].'</option>';
					}
				}?>
            <option value="/api/<?php echo $_SESSION["Users_ID"] ?>/web/lbs/">一键导航(LBS)</option>
            
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
            }
            ?>
			
			
            <optgroup label="------------------自定义URL------------------"></optgroup>
            <?php $DB->get("wechat_url","*","where Users_ID='".$_SESSION["Users_ID"]."'");
				  while($Url=$DB->fetch_assoc()){
					  echo '<option value="'.$Url['Url_Value'].'">'.$Url['Url_Name'].'('.$Url['Url_Value'].')</option>';
				  }?>
          </select>
          </span> </span>
          <div class="clear"></div>
        </div>
        <div class="rows" id="BriefDescription_rows">
          <label>简短介绍</label>
          <span class="input">
          <textarea class="txetarea" name="BriefDescription"></textarea>
          </span>
          <div class="clear"></div>
        </div>
        <div class="rows" id="Description_rows">
          <label>详细内容</label>
          <span class="input">
          <textarea name="Description" style="width:100%;height:400px;visibility:hidden;"></textarea>
          </span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label></label>
          <span class="input">
          <input type="submit" class="btn_green" value="提交保存" name="submit_btn">
          <a href="?m=web&a=column" class="btn_gray">返回</a></span>
          <div class="clear"></div>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>