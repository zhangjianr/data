<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/lib_products.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/smarty.php');

//设置smarty
$smarty->left_delimiter = "{{";
$smarty->right_delimiter = "}}";
$template_dir = $_SERVER["DOCUMENT_ROOT"].'/member/shop/html';
$smarty->template_dir = $template_dir;


if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}

//支付方式列表
$Pay_List =  get_enabled_pays($DB,$_SESSION['Users_ID']);

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

//取商店设置
$rsConfig = $DB->GetRs("shop_config","*","where Users_ID='".$_SESSION["Users_ID"]."'");
$Dis_Levels=json_decode($rsConfig['Dis_Level'],true);

if($_POST)
{
	
	if(!isset($_POST["JSON"])){
		echo '<script language="javascript">alert("请上传商品图片");history.back();</script>';
		exit;
	}
	
	$_POST['Description'] = str_replace('"','&quot;',$_POST['Description']);
	$_POST['Description'] = str_replace("'","&quot;",$_POST['Description']);
	$_POST['Description'] = str_replace('>','&gt;',$_POST['Description']);
	$_POST['Description'] = str_replace('<','&lt;',$_POST['Description']);


	$Data=array(
		"Products_Name"=>$_POST['Name'],
		"Products_Category"=>empty($_POST['Category'])?"0":$_POST['Category'],
		"Products_Type"=>empty($_POST["TypeID"]) ?  0: $_POST["TypeID"],
		"Products_PriceY"=>empty($_POST['PriceY'])?"0":$_POST['PriceY'],
		"Products_PriceX"=>empty($_POST['PriceX'])?"0":$_POST['PriceX'],
		"Products_Profit"=>empty($_POST['Products_Profit'])?"0":$_POST['Products_Profit'],
		"Channel_Profit"=>empty($_POST['Channel_Profit'])?"0":$_POST['Channel_Profit'],
		"Distribute_Profit"=>empty($_POST['Distribute_Profit'])?"0":$_POST['Distribute_Profit'],
		"Products_Distributes"=>empty($_POST['Distribute'])?"":json_encode($_POST['Distribute'],JSON_UNESCAPED_UNICODE),
		"Products_JSON"=>json_encode((isset($_POST["JSON"])?$_POST["JSON"]:array()),JSON_UNESCAPED_UNICODE),
		"Products_BriefDescription"=>$_POST['BriefDescription'],
		"Products_SoldOut"=>isset($_POST["SoldOut"])?$_POST["SoldOut"]:0,
		"Products_IsNew"=>isset($_POST["IsNew"])?$_POST["IsNew"]:0,
		"Products_IsHot"=>isset($_POST["IsHot"])?$_POST["IsHot"]:0,
		"Products_IsRecommend"=>isset($_POST["IsRecommend"])?$_POST["IsRecommend"]:0,
		"Products_IsShippingFree"=>$_POST["Products_IsShippingFree"],
		"Products_IsVirtual"=>isset($_POST["IsVirtual"])?$_POST["IsVirtual"]:0,
		"Products_IsRecieve"=>isset($_POST["IsRecieve"])?$_POST["IsRecieve"]:0,
		"Products_Description"=>$_POST['Description'],
		"Products_Count"=>empty($_POST["Count"]) ? 10000 : intval($_POST["Count"]),
		"Products_CreateTime"=>time(),
		"Products_Weight"=>$_POST['Products_Weight'],
		"Users_ID"=>$_SESSION["Users_ID"],
		"Shipping_Free_Company"=>isset($_POST["Shipping_Free_Company"])?intval($_POST["Shipping_Free_Company"]):0
	);
	
  	
	$Flag=$DB->Add("shop_products",$Data);
	$product_id = mysql_insert_id();
	 //处理产品属性 
	if(strlen($_POST['TypeID'])>0){
		deal_with_attr($product_id);
	}
	 
	 
	$product_url = base_url().'api/'.$_SESSION['Users_ID'].'/shop/products/'.$product_id.'/';
	$qrcode = array(
		"Products_Qrcode"=>generate_qrcode($product_url),
	);
	
    $condition = "where Users_ID = '".$_SESSION["Users_ID"]."' and Products_ID=".$product_id;
	
	$DB->set("shop_products",$qrcode,$condition);
	
	if($Flag)
	{
		echo '<script language="javascript">alert("添加成功");window.location="products.php";</script>';
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
<title>微易宝</title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/member/js/global.js'></script>
<script type='text/javascript' src='/static/member/js/products_attr_helper.js'></script>
<link rel="stylesheet" href="/third_party/kindeditor/themes/default/default.css" />
<script type='text/javascript' src="/third_party/kindeditor/kindeditor-min.js"></script>
<script type='text/javascript' src="/third_party/kindeditor/lang/zh_CN.js"></script>
<script type='text/javascript' src='/static/member/js/shop.js'></script>
<script>

	var Browser = new Object();
	
$(document).ready(shop_obj.products_init);
KindEditor.ready(function(K) {
	K.create('textarea[name="Description"]', {
		themeType : 'simple',
		filterMode : false,
		uploadJson : '/member/upload_json.php?TableField=web_column&Users_ID=<?php echo $_SESSION["Users_ID"];?>',
		fileManagerJson : '/member/file_manager_json.php',
		allowFileManager : true,
		
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

$(document).ready(shop_obj.products_add_init);

</script>
</head>

<body>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->

<div id="iframe_page">
  <div class="iframe_content">
    <link href='/static/member/css/shop.css' rel='stylesheet' type='text/css' />
    
    
    <div class="r_nav">
      <ul>
        <li class="cur"><a href="products.php">产品列表</a></li>
        <li class=""><a href="category.php">产品分类</a></li>
        <li class=""><a href="shop_attr.php">产品属性</a></li>
        <li class=""><a href="commit.php">产品评论</a></li>
      </ul>
    </div>
    <div id="products" class="r_con_wrap">
      <link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
      <script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script>
      <form id="product_add_form" class="r_con_form" method="post" action="products_add.php">
        <div class="rows">
          <label>产品名称</label>
          <span class="input">
          <input type="text" name="Name" value="" class="form_input" size="35" maxlength="100" notnull />
          <font class="fc_red">*</font></span>
          <div class="clear"></div>
        </div>
        
        <div class="rows">
          <label>隶属分类</label>
          <span class="input">
          <select name='Category'>
            <option value=''>--请选择--</option>
            <?php
$DB->get("shop_category","*","where Users_ID='".$_SESSION["Users_ID"]."' and Category_ParentID=0 order by Category_Index asc");
$ParentCategory=array();
$i=1;
while($rsPCategory=$DB->fetch_assoc()){
	$ParentCategory[$i]=$rsPCategory;
	$i++;
}
foreach($ParentCategory as $key=>$value){
	$DB->get("shop_category","*","where Users_ID='".$_SESSION["Users_ID"]."' and Category_ParentID=".$value["Category_ID"]." order by Category_Index asc");
	if($DB->num_rows()>0){
		echo '<optgroup label="'.$value["Category_Name"].'">';
		while($rsCategory=$DB->fetch_assoc()){
			echo '<option value="'.$rsCategory["Category_ID"].'">'.$rsCategory["Category_Name"].'</option>';
		}
		echo '</optgroup>';
	}else{
		echo '<option value="'.$value["Category_ID"].'">'.$value["Category_Name"].'</option>';
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
          <input type="text" name="Count" value="10000" class="form_input" size="5" maxlength="10" /> <span class="tips">&nbsp;注:若不限则填写10000.</span>
          </span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label>产品价格</label>
          <span class="input price"> 原价:￥
          <input type="text" name="PriceY" value="" class="form_input" size="5" maxlength="10" />
          现价:￥
          <input type="text" name="PriceX" value="" class="form_input" size="5" maxlength="10" />
          </span>
          <div class="clear"></div>
        </div>
        
         <div class="rows">
          <label>分销总拨比</label>
          <span class="input price">
          <span>%</span>
          <input type="text" name="Products_Profit" value="" class="form_input" size="5" maxlength="10" notnull />
          <span>(占产品现价的百分比，用于分销返利额计算)</span>
          </span>
          <div class="clear"></div>
        </div>
         <div class="rows">
		  <label>拨比分配</label>
		  <span class="input price">
		  分销拨比
          <span>%</span>
          <input type="text" name="Distribute_Profit" value="" class="form_input" size="5" maxlength="10" notnull />
          
		  渠道拨比
		  <span>%</span>
          <input type="text" name="Channel_Profit" value="" class="form_input" size="5" maxlength="10" notnull />
          <span>(均为占利润比率)</span>
          </span>
          <div class="clear"></div>
        </div>
        <div class="rows">
        	<label>佣金返利</label>
            <span class="input">
            	<table id="wholesale_price_list" class="item_data_table" border="0" cellpadding="3" cellspacing="0">
       <?php foreach ($Dis_Levels AS $key=>$val) { ?>
           <tr>
              <td><?=$key+1?>级&nbsp;&nbsp;%
                <input name="Distribute[<?=$key?>]" value="" class="form_input" size="5" maxlength="10" type="text">
					(产品分销的百分比)
           </td>
		    </tr>
		<?php } ?>
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
          <div class="img" id="PicDetail"></div>
          </span>
          <div class="clear"></div>
        </div>
          <div class="rows">
          <label>简短介绍</label>
          <span class="input">
          <textarea name="BriefDescription" class="briefdesc"></textarea>
          </span>
          <div class="clear"></div>
        </div>
        
        <div class="rows" id="type_html">
           <label>产品类型：</label>
           <span class="input">
           <select name="TypeID" style="width:180px;" id="Type_ID" notnull>
            <option value="">请选择类型</option>
               <?php
				$DB->get("shop_product_type","*","where Users_ID='".$_SESSION["Users_ID"]."' order by Type_Index asc");
				while($rsType= $DB->fetch_assoc()){
					echo '<option value="'.$rsType["Type_ID"].'"'.'>'.$rsType["Type_Name"].'</option>';
				}
			  ?>
              <option value="0">其他</option>
           </select>
           <font class="fc_red">*</font></span>
           <div class="clear"></div>
        </div>
        
        <div class="rows">
          <label>产品属性</label>
          <span class="input" id="attrs">
           
          </span>
          <div class="clear"></div>
  
        </div>
        
        <div id="propertys" display="none"></div> 
        <div class="rows">
          <label>产品重量</label>
          <span class="input">
         <input type="text" name="Products_Weight" value="" notnull class="form_input" size="5" />&nbsp;&nbsp;千克
          </span>
          <div class="clear"></div>
        </div>
        <div class="rows">
        	<label>运费计算方式</label>
            <span class="input">   
          
            
              <?php if($ShippingNum >0 ): ?>
                &nbsp;&nbsp;<input type="radio" value="1"  name="Products_IsShippingFree"  /> 免运费&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;<input type="radio"   value="0"   name="Products_IsShippingFree" checked /> 物流模板
              <?php else: ?>
               &nbsp;&nbsp;<input type="radio" value="1" checked="checked"name="Products_IsShippingFree"  checked /> 免运费&nbsp;&nbsp;&nbsp;&nbsp;
              没有可用的物流模板
              <?php endif;?>
            </span>
            <div class="clear"></div>
        </div>
        
    
        <div class="rows" id="free_shipping_company" style="display:none">
            <label>指定免运费快递公司</label>		
            <span class="input">
            <select name="Shipping_Free_Company" notnull>
             	  <option value="">请选择</option> 
              	  <option value="0" >全部</option>
                  <?php foreach($Company_List as $key=>$item):?>
                  <option value="<?=$item['Shipping_ID']?>"  ><?=$item['Shipping_Name']?></option>
				  <?php endforeach; ?>
              </select> 
            </span>     
            <div class="clear"></div>   
        </div>
    
        
        
        <div class="rows">
          <label>其他属性</label>
          <span class="input attr"> 下架:
          <input type="checkbox" value="1" name="SoldOut" />&nbsp;|&nbsp;
          新品:
          <input type="checkbox" value="1" name="IsNew" />&nbsp;|&nbsp;
          热卖:
          <input type="checkbox" value="1" name="IsHot" />&nbsp;|&nbsp;
          推荐:
         
          <input type="checkbox" value="1" name="IsShippingFree" />&nbsp;|&nbsp;
          虚拟产品(电子券消费形式):
          <input type="checkbox" value="1" name="IsVirtual" />&nbsp;|&nbsp;
		  虚拟产品(订单付款后即刻完成):
          <input type="checkbox" value="1" name="IsRecieve" />
          </span>
          <div class="clear"></div>
        </div>
        
        
        <div class="rows">
          <label>详细介绍</label>
          <span class="input">
          <textarea class="ckeditor" name="Description" style="width:600px; height:300px;"></textarea>
          </span>
          <div class="clear"></div>
        </div>
        <div class="rows">
          <label></label>
          <span class="input">
          <input type="submit" class="btn_green" name="submit_button" value="提交保存" />
          <a href="" class="btn_gray">返回</a></span>
          <div class="clear"></div>
        </div>

         <input type="hidden" id="UsersID" value="<?=$_SESSION["Users_ID"]?>" />
        <input type="hidden" id="ProductsID" value="0">        
      </form>
    </div>
  </div>
</div>
</body>
</html>