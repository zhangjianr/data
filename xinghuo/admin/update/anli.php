<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
if(empty($_SESSION["ADMINID"])){
	header("location:login.php");
}

$item=$DB->GetRs("anli","*","order by itemid desc");
$add = false;
if(!$item){
	$item["Descrition"] = "";
	$add = true;
}
if($_POST){
	$_POST['Descrition'] = str_replace('"','&quot;',$_POST['Descrition']);
	$_POST['Descrition'] = str_replace("'","&quot;",$_POST['Descrition']);
	$_POST['Descrition'] = str_replace('>','&gt;',$_POST['Descrition']);
	$_POST['Descrition'] = str_replace('<','&lt;',$_POST['Descrition']);
	$Data=array(
		"Descrition"=>$_POST["Descrition"],
	);
	if($add){
		$Data["CreateTime"] = time();
		$flag=$DB->Add("anli",$Data);
	}else{
		$flag=$DB->Set("anli",$Data,"where itemid=".$item["itemid"]);
	}
	
	if($flag){
		echo '<script language="javascript">alert("编辑成功！");window.open("anli.php","_self");</script>';
		exit();
	}else{
		echo '<script language="javascript">alert("编辑失败！");window.location="javascript:history.back()";</script>';
		exit();
	}
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/admin/css/main.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/admin/js/global.js'></script>
<link rel="stylesheet" href="/third_party/kindeditor/themes/default/default.css" />
<script charset="utf-8" src="/third_party/kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="/third_party/kindeditor/lang/zh_CN.js"></script>
<script>
    KindEditor.ready(function(K) {
        K.create('textarea[name="Descrition"]', {
            themeType : 'simple',
			filterMode : false,
            uploadJson : '/third_party/kindeditor/php/upload_json.php',
            fileManagerJson : '/third_party/kindeditor/php/file_manager_json.php',
            allowFileManager : true
        });
    });
</script>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->
</head>
<body>
<div id="iframe_page">
  <div class="iframe_content">
	<div class="r_nav">
	  <ul>
        <li class="cur"><a href="anli.php">优秀案例</a></li>
      </ul>
	</div>
    <div class="r_con_wrap">
		<form class="r_con_form" method="post" action="?">
        	
            <div class="rows">
                <label>详细内容</label>
                <span class="input">
                    <textarea name="Descrition" style="width:100%;height:400px;visibility:hidden;"><?php echo $item["Descrition"];?></textarea>
                </span>
                <div class="clear"></div>
            </div>

            <div class="rows">
                <label></label>
                <span class="input"><input type="submit" name="Submit" value="确定" class="submit">
                  <input type="reset" value="重置"></span>
                <div class="clear"></div>
            </div>
        </form>
    </div>
  </div>
</div>
</body>
</html>