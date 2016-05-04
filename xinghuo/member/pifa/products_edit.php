<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/lib_products.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/shipping.php');

if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}
$ProductsID=empty($_REQUEST['ProductsID'])?0:$_REQUEST['ProductsID'];
$rsProducts = $DB->GetRs("pifa_Products","*","where Users_ID='".$_SESSION["Users_ID"]."' and Products_ID=".$ProductsID);


$JSON=json_decode($rsProducts['Products_JSON'],true);

$distribute_list = json_decode($rsProducts['Products_Distributes'],true);  //分佣金额列表

$price_rule = json_decode($rsProducts['Products_price_rule'],true);  //价格区间

if(!isset($JSON["Wholesale"][0]["Qty"])) $JSON["Wholesale"] = array();

//计算物流模板数量
$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Template_Status = 1 ";
$rsShippingTemplates = $DB->Get("shop_shipping_template","*",$condition);
$Templates = $DB->toArray($rsShippingTemplates);
$ShippingNum = count($Templates);
//获取有物流模板的物流公司
$ShippingIDS = '';
if($ShippingNum > 0 ){
	foreach($Templates as $key=>$item){
		$Shipping_ID_List[] = $item['Shipping_ID'];
	}  
    $ShippingIDS = implode(',',$Shipping_ID_List);
	
	$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Shipping_Status = 1 And Shipping_ID in (". $ShippingIDS.")";
	$rsCompanies = $DB->Get("shop_shipping_company","Shipping_ID,Shipping_Name",$condition);
	$Company_List = $DB->toArray($rsCompanies);
}else{
    $Company_List = array();
}


if($_POST){
	if(!isset($_POST["JSON"])){
		echo '<script language="javascript">alert("请上传商品图片");history.back();</script>';
		exit;
	}
	//安全过滤
	$_POST['BriefDescription'] = str_replace('"','&quot;',$_POST['BriefDescription']);
	$_POST['BriefDescription'] = str_replace("'","&quot;",$_POST['BriefDescription']);
	$_POST['BriefDescription'] = str_replace('>','&gt;',$_POST['BriefDescription']);
	$_POST['BriefDescription'] = str_replace('<','&lt;',$_POST['BriefDescription']);
	
	$_POST['Description'] = str_replace('"','&quot;',$_POST['Description']);
	$_POST['Description'] = str_replace("'","&quot;",$_POST['Description']);
	$_POST['Description'] = str_replace('>','&gt;',$_POST['Description']);
	$_POST['Description'] = str_replace('<','&lt;',$_POST['Description']);
	//组装价格区间
	$price_rule = array();
	$price = $_POST['price'];
	$numX = $_POST['numX'];
	$numY = $_POST['numY'];
	if(!empty($price)){
		foreach($price as $k => $v){
			if(!empty($v)){
				if(empty($numX[$k])){
					$numX[$k] = 0;
				}
				if(empty($numY[$k])){
					$numY[$k] = 0;
				}
				//检测数据合法性
				if(isset($numY[$k-1])){
					if(($numX[$k]-1) != $numY[$k-1]){
						echo '<script language="javascript">alert("价格区间数据格式有错误！");history.back();</script>';exit;
					}
				}
				$price_rule[$k][] = $numX[$k];
				$price_rule[$k][] = $numY[$k];
				$price_rule[$k][] = $price[$k];
			}	
		}
	}

	$price_rule_json = json_encode($price_rule,JSON_UNESCAPED_UNICODE);
 
	$Data=array(
		"Products_Name"=>$_POST['Name'],
		"Products_Category"=>empty($_POST['Category'])?"0":$_POST['Category'],
		"Products_price_rule"=>$price_rule_json,
		"Products_Profit"=>empty($_POST['Products_Profit'])?"0":$_POST['Products_Profit'],
		"Products_Distributes"=>empty($_POST['Distribute'])?"":json_encode($_POST['Distribute'],JSON_UNESCAPED_UNICODE),
		"Products_JSON"=>json_encode((isset($_POST["JSON"])?$_POST["JSON"]:array()),JSON_UNESCAPED_UNICODE),
		"Products_BriefDescription"=>$_POST['BriefDescription'],
		"Products_SoldOut"=>isset($_POST["SoldOut"])?$_POST["SoldOut"]:0,
		"Products_IsNew"=>isset($_POST["IsNew"])?$_POST["IsNew"]:0,
		"Products_IsHot"=>isset($_POST["IsHot"])?$_POST["IsHot"]:0,
		"Products_IsRecommend"=>isset($_POST["IsRecommend"])?$_POST["IsRecommend"]:0,
		"Products_IsShippingFree"=>isset($_POST["Products_IsShippingFree"])?$_POST["Products_IsShippingFree"]:0,
		"Products_Description"=>$_POST['Description'],
		"Products_Count"=>empty($_POST["Count"]) ? 10000 : intval($_POST["Count"]),
		"Products_Weight"=>$_POST['Products_Weight'],
		"Shipping_Free_Company"=>isset($_POST["Shipping_Free_Company"])?intval($_POST["Shipping_Free_Company"]):0,
		"Products_unit"=>empty($_POST["unit"])?'件':$_POST["unit"],
	);

	
	$Flag=$DB->Set("pifa_Products",$Data,"where Users_ID='".$_SESSION["Users_ID"]."' and Products_ID=".$ProductsID);
	if($Flag)
	{
		echo '<script language="javascript">alert("修改成功");window.location="products.php";</script>';
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
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/member/js/global.js'></script>
<script type='text/javascript' src='/static/member/js/products_attr_helper.js'></script>
<link rel="stylesheet" href="/third_party/kindeditor/themes/default/default.css" />
<script type='text/javascript' src="/third_party/kindeditor/kindeditor-min.js"></script>
<script type='text/javascript' src="/third_party/kindeditor/lang/zh_CN.js"></script>
<script>


KindEditor.ready(function(K) {
	K.create('textarea[name="Description"],textarea[name="BriefDescription"]', {
		themeType : 'simple',
		filterMode : false,
		uploadJson : '/member/upload_json.php?TableField=web_column&Users_ID=<?php echo $_SESSION["Users_ID"];?>',
		fileManagerJson : '/member/file_manager_json.php',
		allowFileManager : true,
		items : [
		'source', '|', 'fullscreen', 'undo', 'redo', 'print', 'cut', 'copy', 'paste',
		'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
		'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
		'superscript', '|', 'selectall', '-',
		'title', 'fontname', 'fontsize', '|', 'textcolor', 'bgcolor', 'bold',
		'italic', 'underline', 'strikethrough', 'removeformat', '|', 'image',
		'flash', 'media', 'advtable', 'hr', 'emoticons', 'link', 'unlink'
		],
	});
	var editor = K.editor({
		uploadJson : '/member/upload_json.php?TableField=web_article',
		fileManagerJson : '/member/file_manager_json.php',
		showRemote : true,
		allowFileManager : true,
	});
	K('#ImgUpload').click(function(){
		if(K('#PicDetail').children().length>=5){
			alert('您上传的图片数量已经超过5张，不能再上传！');
			return;
		}
		editor.loadPlugin('image', function() {
			editor.plugin.imageDialog({
				clickFn : function(url, title, width, height, border, align) {
					K('#PicDetail').append('<div><a href="'+url+'" target="_blank"><img src="'+url+'" /></a> <span>删除</span><input type="hidden" name="JSON[ImgPath][]" value="'+url+'" /></div>');
					editor.hideDialog();
				}
			});
		});
	});
	
	K('#PicDetail div span').click(function(){
		K(this).parent().remove();
	});
})
function insertRow(){
	var newrow=document.getElementById('wholesale_price_list').insertRow(-1);
	newcell=newrow.insertCell(-1);
	newcell.innerHTML='数量： <input type="text" name="JSON[Wholesale]['+(document.getElementById('wholesale_price_list').rows.length-2)+'][Qty]" value="" class="form_input" size="5" maxlength="3" /> 价格：￥ <input type="text" name="JSON[Wholesale]['+(document.getElementById('wholesale_price_list').rows.length-2)+'][Price]" value="" class="form_input" size="5" maxlength="10" /><a href="javascript:;" onclick="document.getElementById(\'wholesale_price_list\').deleteRow(this.parentNode.parentNode.rowIndex);"> <img src="/static/member/images/ico/del.gif" hspace="5" /></a>';
}
</script>
</head>

<body>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->

<div id="iframe_page">
	<div class="iframe_content">
		<link href='/static/member/css/pifa.css' rel='stylesheet' type='text/css' />
		<script type='text/javascript' src='/static/member/js/pifa.js'></script> 
		<script type='text/javascript'>
	
    	$(document).ready(pifa_obj.products_edit_init);
    </script>
		<div class="r_nav">
			<ul>
				<li><a href="config.php">基本设置</a></li>
				<li class="cur"><a href="products.php">商品管理</a></li>
				<li><a href="category.php">商品分类</a></li>
				<li><a href="orders.php">订单管理</a></li>
				<li ><a href="commit.php">评论管理</a></li>
			</ul>
		</div>
		<div id="products" class="r_con_wrap">
			<link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
			<script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script>
			<form class="r_con_form" id="product_edit_form" method="post" action="products_edit.php">
				<div class="rows">
					<label>产品名称</label>
					<span class="input">
					<input type="text" name="Name" value="<?php echo $rsProducts["Products_Name"] ?>" class="form_input" size="35" maxlength="100" notnull />
					<font class="fc_red">*</font></span>
					<div class="clear"></div>
				</div>
				<div class="rows">
					<label>隶属分类</label>
					<span class="input">
					<select name='Category'>
						<option value=''>--请选择--</option>
						<?php
$DB->get("pifa_category","*","where Users_ID='".$_SESSION["Users_ID"]."' and Category_ParentID=0 order by Category_Index asc");
$ParentCategory=array();
$i=1;
while($rsPCategory=$DB->fetch_assoc()){
	$ParentCategory[$i]=$rsPCategory;
	$i++;
}
foreach($ParentCategory as $key=>$value){
	$DB->get("pifa_category","*","where Users_ID='".$_SESSION["Users_ID"]."' and Category_ParentID=".$value["Category_ID"]." order by Category_Index asc");
	if($DB->num_rows()>0){
		echo '<optgroup label="'.$value["Category_Name"].'">';
		while($rsCategory=$DB->fetch_assoc()){
			echo '<option value="'.$rsCategory["Category_ID"].'"'.($rsCategory["Category_ID"]==$rsProducts["Products_Category"]?" selected":"").'>'.$rsCategory["Category_Name"].'</option>';
		}
		echo '</optgroup>';
	}else{
		echo '<option value="'.$value["Category_ID"].'"'.($value["Category_ID"]==$rsProducts["Products_Category"]?" selected":"").'>'.$value["Category_Name"].'</option>';
	}
}
?>
					</select>
					</span>
					<div class="clear"></div>
				</div>
				<div class="rows">
					<label>库存</label>
					<span class="input">
					<input type="text" name="Count" value="<?php echo $rsProducts["Products_Count"] ?>" class="form_input" size="5" maxlength="10" />
					<span class="tips">&nbsp;注:若不限则填写10000.</span> </span>
					<div class="clear"></div>
				</div>
				<div class="rows">
					<label>产品单位</label>
					<span class="input">
					<input type="text" name="unit" value="<?=$rsProducts["Products_unit"]?>" notnull class="form_input" size="5" />
					</span>
					<div class="clear"></div>
				</div>
				<div class="rows">
					<label>价格区间</label>
					<span class="input"> 
					<a id="add_range_law" class="red" href="javascript:void(0);">添加</a>
					<ul id="range_box">
					<?php if(!empty($price_rule)){?>
					<?php foreach($price_rule as $k => $v){?>
					<li>
					数量:
					<input type="text" name="numX[]" value="<?php echo $v[0];?>" class="form_input" size="5" maxlength="10" />
					-
					<input type="text" name="numY[]" value="<?php echo $v[1];?>" class="form_input" size="5" maxlength="10" />
					价格:
					<input type="text" name="price[]" value="<?php echo $v[2];?>" class="form_input" size="5" maxlength="10" />￥&nbsp;<a><img hspace="5" src="/static/member/images/ico/del.gif"></a>
					</li>
					<?php }?>
					<?php }?>
					</ul>
					</span>
					<div class="clear"></div>
				</div>
				
				<!-- 产品利润begin -->
				<div class="rows">
					<label>分销总拨比</label>
					<span class="input price"> <span>%</span>
					<input type="text" name="Products_Profit" value="<?=$rsProducts["Products_Profit"]?>" class="form_input" size="5" maxlength="10" notnull />
					<span>(占产品现价的百分比，用于分销返利额计算)</span> </span>
					<div class="clear"></div>
				</div>
				<!-- 产品利润end -->
				<div class="rows">
					<label>佣金返利</label>
					<span class="input">
					<table id="wholesale_price_list" class="item_data_table" border="0" cellpadding="3" cellspacing="0">
						<tr>
							<td>一级&nbsp;&nbsp;%
								<input name="Distribute[0]" value="<?=$distribute_list[0]?>" class="form_input" size="5" maxlength="10" type="text">
								(产品利润的百分比) </td>
						<tr>
							<td>二级&nbsp;&nbsp;%
								<input name="Distribute[1]" value="<?=$distribute_list[1]?>" class="form_input" size="5" maxlength="10" type="text">
								(产品利润的百分比) </td>
						</tr>
						<tr>
							<td>三级&nbsp;&nbsp;%
								<input name="Distribute[2]" value="<?=$distribute_list[2]?>" class="form_input" size="5" maxlength="10" type="text">
								(产品利润的百分比) </td>
						</tr>
							</tr>
						
						<tr>
					</table>
					</span>
					<div class="clear"></div>
				</div>
				<div class="rows">
					<label>产品图片</label>
					<span class="input"> <span class="upload_file">
					<div>
						<div class="up_input">
							<input type="button" id="ImgUpload" value="添加图片" style="width:80px;" />
						</div>
						<div class="tips">共可上传<span id="pic_count">5</span>张图片，图片大小建议：640*640像素</div>
						<div class="clear"></div>
					</div>
					</span>
					<div class="img" id="PicDetail">
						<?php if(isset($JSON["ImgPath"])){
			foreach($JSON["ImgPath"] as $key=>$value){?>
						<div><a target="_blank" href="<?php echo $value ?>"> <img src="<?php echo $value ?>"></a><span>删除</span>
							<input type="hidden" name="JSON[ImgPath][]" value="<?php echo $value ?>">
						</div>
						<?php }
			}?>
					</div>
					</span>
					<div class="clear"></div>
				</div>
				<div class="rows">
					<label>批发须知</label>
					<span class="input">
					<textarea name="BriefDescription" class="ckeditor" style="width:600px; height:300px;"><?php echo $rsProducts["Products_BriefDescription"] ?></textarea>
					</span>
					<div class="clear"></div>
				</div>
				<div class="rows">
					<label>产品重量（单件）</label>
					<span class="input">
					<input type="text" name="Products_Weight" value="<?=$rsProducts["Products_Weight"]?>" notnull class="form_input" size="5" />
					&nbsp;&nbsp;千克 </span>
					<div class="clear"></div>
				</div>
				<div class="rows">
					<label>运费计算方式</label>
					<span class="input">
					<?php if($ShippingNum >0 ): ?>
					&nbsp;&nbsp;
					<input type="radio" value="1" <?=$rsProducts['Products_IsShippingFree']?'checked':''?> name="Products_IsShippingFree"  />
					免运费&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;
					<input type="radio"  value="0"  <?=$rsProducts['Products_IsShippingFree']?'':'checked'?> name="Products_IsShippingFree"  />
					物流模板
					&nbsp;&nbsp;
					<?php else: ?>
					&nbsp;&nbsp;
					<input type="radio" value="1" checked="checked"name="Products_IsShippingFree" <?php echo empty($rsProducts["Products_IsShippingFree"])?"":" checked" ?> />
					免运费&nbsp;&nbsp;&nbsp;&nbsp;
					没有可用的物流模板
					<?php endif;?>
					</span>
					<div class="clear"></div>
				</div>
				<?php 
			$display = ($rsProducts['Products_IsShippingFree'] == 1)?'block':'none';
		?>
				<div class="rows" id="free_shipping_company" style="display:<?=$display?>">
					<label>指定免运费快递公司</label>
					<span class="input">
					<select name="Shipping_Free_Company" notnull>
						<option value="">请选择</option>
						<option value="0" <?=($rsProducts['Shipping_Free_Company'] == 0)?'selected':''?> >全部</option>
						<?php foreach($Company_List as $key=>$item):?>
						<option value="<?=$item['Shipping_ID']?>" <?=($rsProducts['Shipping_Free_Company'] == $item['Shipping_ID'])?'selected':''?> >
						<?=$item['Shipping_Name']?>
						</option>
						<?php endforeach; ?>
					</select>
					</span>
					<div class="clear"></div>
				</div>
				<div class="rows">
					<label>其他属性</label>
					<span class="input attr"> 下架:
					<input type="checkbox" value="1" name="SoldOut" <?php echo empty($rsProducts["Products_SoldOut"])?"":" checked" ?> />
					&nbsp;|&nbsp;
					新品:
					<input type="checkbox" value="1" name="IsNew" <?php echo empty($rsProducts["Products_IsNew"])?"":" checked" ?> />
					&nbsp;|&nbsp;
					热卖:
					<input type="checkbox" value="1" name="IsHot" <?php echo empty($rsProducts["Products_IsHot"])?"":" checked" ?> />
					&nbsp;|&nbsp;
					推荐:
					<input type="checkbox" value="1" name="IsRecommend" <?php echo empty($rsProducts["Products_IsRecommend"])?"":" checked" ?> />
					</span>
					<div class="clear"></div>
				</div>
				<div class="rows">
					<label>产品详情</label>
					<span class="input">
					<textarea class="ckeditor" name="Description" style="width:600px; height:300px;"><?php echo $rsProducts["Products_Description"] ?></textarea>
					</span>
					<div class="clear"></div>
				</div>
				<div class="rows">
					<label></label>
					<span class="input">
					<input type="hidden" id="UsersID" value="<?=$_SESSION["Users_ID"]?>" />
					<input type="hidden" name="ProductsID" id="ProductsID"  value="<?php echo $rsProducts["Products_ID"] ?>">
					<input type="submit" class="btn_green" name="submit_button" value="提交保存" />
					<a href="" class="btn_gray">返回</a></span>
					<div class="clear"></div>
				</div>
			</form>
		</div>
	</div>
</div>
</body>
</html>