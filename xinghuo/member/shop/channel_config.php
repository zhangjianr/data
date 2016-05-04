<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');


$base_url = base_url();

if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}
//获取分销商称号配置

if($_POST)
{	
//print_r($_POST);exit;
    
	$Channel_List = array();
	$Channel_Type =  $_POST['Channel_Type'];
	
	foreach($Channel_Type['Name'] as $key=>$item){
		$Channel_List[$key+1]['Name'] = $item;
		$Channel_List[$key+1]['Bonus'] = $Channel_Type['Bonus'][$key];
       // $Channel_List[$key+1]['ImgPath'] = $Channel_Type['ImgPath'][$key];
		$Channel_List[$key+1]['Group_Num'] = $Channel_Type['Group_Num'][$key];
		$Channel_List[$key+1]['Group_Sum'] = $Channel_Type['Group_Sum'][$key];
		$Channel_List[$key+1]['Self'] = $Channel_Type['Self'][$key];
		$Channel_List[$key+1]['Direct_Num'] = $Channel_Type['Direct_Num'][$key];
	}
	
	//print_r($Channel_List);
	if($_POST['operation'] == 'add'){
		add_channel_type($DB,$_SESSION['Users_ID'],$Channel_List,$_POST['Depth']);
	}else{
		set_channel_type($DB,$_SESSION['Users_ID'],$Channel_List,$_POST['Depth']);
	}
	
}

//获取此用户爵位设置
$rsDsLvel = $DB->GetRs('shop_channel_config', 'Channel_Type,Depth', "where Users_ID='" . $_SESSION['Users_ID'] . "'");

$exist = false;
$channel_level = array();
if($rsDsLvel){
	$exist = TRUE;
	$channel_level = get_channel_type($DB,$_SESSION['Users_ID'],'back');
}

$count=count($channel_level);


?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/js/jquery.formatCurrency-1.4.0.js'></script>
<script type='text/javascript' src='/static/member/js/global.js'></script>
<link rel="stylesheet" href="/third_party/kindeditor/themes/default/default.css" />
<script type='text/javascript' src="/third_party/kindeditor/kindeditor-min.js"></script>
<script type='text/javascript' src="/third_party/kindeditor/lang/zh_CN.js"></script>
<script type='text/javascript' src="/third_party/kindeditor/plugins/code/prettify.js"></script>
<script type='text/javascript' src="/static/member/js/shop.js"></script>
<script>

var base_url  = '<?=$base_url?>';
var Users_ID = '<?=$_SESSION['Users_ID']?>';

KindEditor.ready(function(K) {
    
	editor = K.editor({
        uploadJson : '/member/upload_json.php?TableField=app_wedding',
        fileManagerJson : '/member/file_manager_json.php',
        showRemote : true,
        allowFileManager : true,
    });


	K('#ImgUpload_1').click(function(){	
	
        editor.loadPlugin('image', function(){
            editor.plugin.imageDialog({
                imageUrl : K('#ImgPath_1').val(),
                clickFn : function(url, title, width, height, border, align){
                    K('#ImgPath_1').val(url);
                    K('#ImgDetail_1').html('<img src="'+url+'" />');
                    editor.hideDialog();
                }
            });
        });
	
    });
	
	K('#ImgUpload_2').click(function(){	
	
        editor.loadPlugin('image', function(){
            editor.plugin.imageDialog({
                imageUrl : K('#ImgPath_2').val(),
                clickFn : function(url, title, width, height, border, align){
                    K('#ImgPath_2').val(url);
                    K('#ImgDetail_2').html('<img src="'+url+'" />');
                    editor.hideDialog();
                }
            });
        });
	
    });
	
	K('#ImgUpload_3').click(function(){	
	
        editor.loadPlugin('image', function(){
            editor.plugin.imageDialog({
                imageUrl : K('#ImgPath_3').val(),
                clickFn : function(url, title, width, height, border, align){
                    K('#ImgPath_3').val(url);
                    K('#ImgDetail_3').html('<img src="'+url+'" />');
                    editor.hideDialog();
                }
            });
        });
	
    });
	
	K('#ImgUpload_4').click(function(){	
	
        editor.loadPlugin('image', function(){
            editor.plugin.imageDialog({
                imageUrl : K('#ImgPath_4').val(),
                clickFn : function(url, title, width, height, border, align){
                    K('#ImgPath_4').val(url);
                    K('#ImgDetail_4').html('<img src="'+url+'" />');
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
<style type="text/css">
body, html{background:url(/static/member/images/main/main-bg.jpg) left top fixed no-repeat;}
</style>
<div id="iframe_page">
  <div class="iframe_content">
    <link href='/static/member/css/user.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/member/js/user.js'></script>
    <div class="r_nav">
     <ul>
       
        <li class=""> <a href="distributes.php">分销账号管理</a> </li>
        <li class=""><a href="distribute_record.php">分销记录</a></li>
        <li class=""><a href="withdraw_record.php">提现记录</a></li>   
        <li class=""><a href="distribute_title.php">爵位设置</a></li>
        <li class="cur"><a href="channel_config.php">渠道设置</a></li>
        <li class=""><a href="withdraw_method.php">提现方法管理</a></li>
      </ul>
    </div>
    <link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script> 
    <script language="javascript">
	$(document).ready(function(){
		//	global_obj.config_form_init();
		shop_obj.dis_channel_init();
	});
    </script>
    <div class="r_con_config r_con_wrap">
 
	 <h1><strong>渠道设置</strong></h1>
	 <span style="color:red;"></span>
  
   <div id="distribute_title" class="r_con_config r_con_wrap">
  
    <form id="level_form" method="post" action="channel_config.php">
		   <?php if($exist): ?>
                 <input type="hidden" name="operation" value="set" />
		   <?php else: ?>
           		 <input type="hidden" name="operation" value="add" />
		   <?php endif;?>
		   <table class="level_table" id="Channel_Type_table" border="0" cellpadding="5" cellspacing="0">
		   <tr>
		   <td>团队计算深度</td><td><input class="form_input" value="<?=$rsDsLvel['Depth']?>" name="Depth"</td>
		   </tr>
		   </table>
		    <div class="btn btn-success" onclick="add_one_form()">增加一行</div>
	   <table class="level_table" id="Channel_Type_table" border="0" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <td width="10%">序号</td>
                    <td width="15%">类型名称</td>
                    <td width="15%">比例</td>
                    <td width="40%">升级条件</td>
                </tr>
            </thead>
            <tbody id="dis_one_forms">
			
	
			<?php for($i=1;$i<=$count;$i++){ ?>
			
				<tr  fieldtype="text" class="level_<?=$i?>">
                    <td><?=$i?></td>
                    <td>
                        <input class="form_input" value="<?=$channel_level[$i]['Name']?>" name="Channel_Type[Name][]" 
						<?php if($i == 1):?>
						notnull
						<?php endif;?>

						type="text">
                    </td>
                    <td>
                        <input class="form_input title_val" value="<?=$channel_level[$i]['Bonus']?>"  name="Channel_Type[Bonus][]"  <?php if($i == 1):?>
						notnull
						<?php endif;?> type="text">
                    </td>
                    <td>
					自消费累计达到<input class="form_input Self" value="<?=!empty($channel_level[$i]['Self'])?$channel_level[$i]['Self']:''?>"  name="Channel_Type[Self][]" type="text" size=10></br>
					直接推荐分销商数<input class="form_input Direct_Num" value="<?=!empty($channel_level[$i]['Direct_Num'])?$channel_level[$i]['Direct_Num']:''?>"  name="Channel_Type[Direct_Num][]" type="text" size=10></br>
					团队分销商达到<input class="form_input Group_Num" value="<?=!empty($channel_level[$i]['Group_Num'])?$channel_level[$i]['Group_Num']:''?>"  name="Channel_Type[Group_Num][]" type="text" size=10></br>
                  团队消费额达到<input class="form_input Group_Sum" value="<?=!empty($channel_level[$i]['Group_Sum'])?$channel_level[$i]['Group_Sum']:''?>"  name="Channel_Type[Group_Sum][]" type="text" size=10>
                    </td>
                   
                 <td> 
                        <?php if ($i==$count):?>
								<a class="level_del" href="javascript:void(0);" level_id="<?=$i?>">
									<img src="/static/member/images/ico/del.gif">
								</a>
						<?php endif?>
                    </td>
                  
				</tr>
	
			<?php } ?>
      	
            </tbody>
        </table>
        <div class="blank20"></div>
        <div class="submit">
            <input name="submit_button" value="提交保存" type="submit">
 	   </div>
        <input name="action" value="distribute_title" type="hidden">
    </form>
  </div>
             
    </div>
  </div>
</div>
<script>
				var i = <?=$count?>;
				function add_one_form(){
				i++;
				var tpl = '<tr class="level_'+i+'">\
								<td>'+i+'</td>\
								<td>\
									<input class="form_input" value="" name="Channel_Type[Name][]" type="text">\
								</td>\
								<td>\
									<input class="form_input title_val" value=""  name="Channel_Type[Bonus][]" type="text">\
								</td>\
								<td>\
									自消费累计达到<input class="form_input Group_Num" value=""  name="Channel_Type[Self][]" type="text" size=10></br>\
									直接推荐分销商数<input class="form_input Group_Num" value=""  name="Channel_Type[Direct_Num][]" type="text" size=10></br>\
									团队分销商达到<input class="form_input Group_Num" value=""  name="Channel_Type[Group_Num][]" type="text" size=10></br>\
									团队消费额达到<input class="form_input Group_Sum" value=""  name="Channel_Type[Group_Sum][]" type="text" size=10>\
								</td>\
								<td>\
								</td>\
							</tr>';
				$("#dis_one_forms").append(tpl);
			}
		</script>
</body>
</html>