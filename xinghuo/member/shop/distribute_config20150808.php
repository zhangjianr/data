<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');


if(empty($_SESSION["Users_Account"])){
	header("location:/member/login.php");
}

if(isset($_GET["action"])){
	if($_GET["action"] == 'get_product'){
		$cate_id = $_GET['cate_id'];
	    $keyword = $_GET['keyword'];
	    $condition = "where Users_ID = '".$_SESSION['Users_ID']."'";
	    if(strlen($cate_id)>0){
			$rsCategory=$DB->GetRs("shop_category","*","where Users_ID='".$_SESSION['Users_ID']."' and Category_ID=".$cate_id);
			if(empty($rsCategory["Category_ParentID"])){
				$CategoryList=array();
				$DB->Get("shop_category","*","where Users_ID='".$_SESSION['Users_ID']."' and Category_ParentID=".$cate_id);
				while($v=$DB->fetch_assoc()){
					$CategoryList[]=$v["Category_ID"];
				}
				if(empty($CategoryList)){
					$condition .= " and Products_Category='".$cate_id."'";
				}else{
					$CategoryList=implode(",",$CategoryList);
					$condition .= " and Products_Category in(".$CategoryList.")";
				}
			}else{
				$condition .= " and Products_Category='".$cate_id."'";
			}
	   }
	   
	   if(strlen($keyword)>0){
			$condition .= " and Products_Name like '%".$_GET["keyword"]."%'";	
	   }
	   
	   $rsProducts = $DB->Get("shop_products",'Products_ID,Products_Name,Products_PriceX',$condition);
	   $product_list = $DB->toArray($rsProducts);
	   $option_list = '';
	   foreach($product_list as $v){
		   $option_list .= '<option value="'.$v['Products_ID'].'">'.$v['Products_Name'].'---'.$v['Products_PriceX'].'</option>';
	   }
	   echo $option_list;
	   exit;
	}
}

if($_POST){
	$Menu = array();
	if($_POST['Type']==3){
		if(!empty($_POST['MName'])){
			foreach($_POST['MName'] as $k=>$v){
				if($v){
					$Menu[] = array(
						"name"=>$v,
						"order"=>empty($_POST['MOrder'][$k]) ? 0 : $_POST['MOrder'][$k],
						"link"=>empty($_POST['MLink'][$k]) ? '' : $_POST['MLink'][$k]
					);
				}
			}
		}
	}
	
	$shop_config = Shop_Config::find($_SESSION["Users_ID"]);
	
	$shop_config->Distribute_Type = $_POST['Type'];
	$shop_config->Distribute_Limit = empty($_POST['Limit'][$_POST['Type']]) ? 0 : $_POST['Limit'][$_POST['Type']];
	
	$shop_config->Withdraw_Type = $_POST['DType'];
	$shop_config->Withdraw_Limit =empty($_POST['DLimit'][$_POST['DType']]) ? 0 : $_POST['DLimit'][$_POST['DType']];
	$shop_config->Withdraw_PerLimit = empty($_POST['PerLimit']) ? 0 : $_POST['PerLimit'];
	
	$shop_config->Distribute_Customize = $_POST['Customize'];
	$shop_config->HIncomelist_Open = $_POST['HIncomelist_Open'];
	$shop_config->H_Incomelist_Limit = !empty($_POST['H_Incomelist_Limit'])?$_POST['H_Incomelist_Limit']:0;
	$shop_config->QrcodeBg = $_POST['QrcodeBg'];
	$shop_config->ApplyBanner = $_POST['ApplyBanner'];
	$shop_config->Dis_Agent_Type = $_POST['Dis_Agent_Type'];
	
	if($_POST['Dis_Agent_Type'] == 0){
		$Agent_Rate = '';
	}elseif($_POST['Dis_Agent_Type'] == 1){
		$Agent_Rate = $_POST['Agent_Rate'];
	}elseif($_POST['Dis_Agent_Type'] == 2){
		$Agent_Rate = json_encode($_POST['Agent_Rate'],JSON_UNESCAPED_UNICODE);		
	}
	
	$shop_config->Agent_Rate = $Agent_Rate;
	if($_POST['Type']==3){
		$shop_config->DiyMenu = empty($Menu) ? '' : json_encode($Menu,JSON_UNESCAPED_UNICODE);
	}
	
	$Flag = $shop_config->save();
	
	if($Flag){
		echo '<script language="javascript">alert("设置成功");window.location="distribute_config.php";</script>';
	}else{
		echo '<script language="javascript">alert("保存失败");history.back();</script>';
	}
	exit;
}else{
	$rsConfig = $DB->GetRs("shop_config","*","where Users_ID='".$_SESSION["Users_ID"]."'");
	//获取分类列表
	$DB->get("shop_category","*","where Users_ID='".$_SESSION["Users_ID"]."' and Category_ParentID=0 order by Category_Index asc");
	$ParentCategory=array();
	
	$i=1;
	while($rsPCategory=$DB->fetch_assoc()){
	  $ParentCategory[$i]=$rsPCategory;
	  $i++;
	}
	
	$category_list = array(); 
	foreach($ParentCategory as $key=>$value){
	  $DB->get("shop_category","*","where Users_ID='".$_SESSION["Users_ID"]."' and Category_ParentID=".$value["Category_ID"]." order by Category_Index asc");
	  if($DB->num_rows()>0){
		$category_list[$value["Category_ID"]]['name'] = $value["Category_Name"];
		while($rsCategory=$DB->fetch_assoc()){
		   $category_list[$value["Category_ID"]]['children'][$rsCategory['Category_ID']] = $rsCategory['Category_Name'];
		}
	   
	  }else{
		  $category_list[$value["Category_ID"]]['name'] = $value["Category_Name"];
			$category_list[$value["Category_ID"]]['children'] = array();
	  }
	}
	$product_name = $dproduct_name = '';
	$MenuList = array();
	if($rsConfig["Distribute_Type"]==3){
		$p = $DB->GetRs("shop_products","Products_Name","where Users_ID='".$_SESSION["Users_ID"]."' and Products_ID=".$rsConfig["Distribute_Limit"]);
		$product_name = $p["Products_Name"];
	}
	
	if($rsConfig["Withdraw_Type"]==2){
		$p = $DB->GetRs("shop_products","Products_Name","where Users_ID='".$_SESSION["Users_ID"]."' and Products_ID=".$rsConfig["Withdraw_Limit"]);
		$dproduct_name = $p["Products_Name"];
	}
	
	if($rsConfig["DiyMenu"]){
		$MenuList = $rsConfig["DiyMenu"] ? json_decode($rsConfig['DiyMenu'],true) : array();
	}
}

function UrlList($url){
	global $DB;
	$rsConfig = $DB->GetRs("shop_config","Distribute_Type,Distribute_Limit","where Users_ID='".$_SESSION["Users_ID"]."'");
	echo '<option value="">--请选择--</option>
	<optgroup label="------------------系统业务模块------------------"></optgroup>';
	$DB->get("wechat_material","Material_ID,Material_Table,Material_Json","where Users_ID='".$_SESSION["Users_ID"]."' and Material_Table<>'0' and Material_TableID=0 and Material_Display=0 order by Material_ID desc");
	while($rsMaterial=$DB->fetch_assoc()){
		$Material_Json=json_decode($rsMaterial['Material_Json'],true);
		echo '<option value="/api/'.$_SESSION["Users_ID"].'/'.$rsMaterial['Material_Table'].'/"'.($url=='/api/'.$_SESSION["Users_ID"].'/'.$rsMaterial['Material_Table'].'/' ? ' selected' : '').'>'.$Material_Json['Title'].'</option>';
	}
	if($rsConfig["Distribute_Type"]==3){
		echo '<optgroup label="------------------分销门槛购买产品------------------"></optgroup>';
		$p = $DB->GetRs("shop_products","Products_Name,Products_ID","where Users_ID='".$_SESSION["Users_ID"]."' and Products_ID=".$rsConfig["Distribute_Limit"]);
		echo '<option value="/api/'.$_SESSION["Users_ID"].'/shop/products_virtual/'.$p["Products_ID"].'/"'.($url=='/api/'.$_SESSION["Users_ID"].'/shop/products_virtual/'.$p["Products_ID"].'/' ? ' selected' : '').'>'.$p["Products_Name"].'</option>';
	}
	echo '<optgroup label="------------------微商城产品分类页面------------------"></optgroup>';
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
			echo '<option value="/api/'.$_SESSION["Users_ID"].'/shop/category/'.$value["Category_ID"].'/"'.($url=='/api/'.$_SESSION["Users_ID"].'/shop/category/'.$value["Category_ID"].'/' ? ' selected' : '').'>'.$value["Category_Name"].'</option>';
			while($rsCategory=$DB->fetch_assoc()){
				echo '<option value="/api/'.$_SESSION["Users_ID"].'/shop/category/'.$rsCategory["Category_ID"].'/"'.($url=='/api/'.$_SESSION["Users_ID"].'/shop/category/'.$rsCategory["Category_ID"].'/' ? ' selected' : '').'>&nbsp;&nbsp;├'.$rsCategory["Category_Name"].'</option>';
			}
		}else{
			echo '<option value="/api/'.$_SESSION["Users_ID"].'/shop/category/'.$value["Category_ID"].'/"'.($url=='/api/'.$_SESSION["Users_ID"].'/shop/category/'.$value["Category_ID"].'/' ? ' selected' : '').'>'.$value["Category_Name"].'</option>';
		}
	}
	echo '<optgroup label="------------------文章分类------------------"></optgroup>';
	echo '<option value="/api/'.$_SESSION["Users_ID"].'/shop/articles/"'.($url=='/api/'.$_SESSION["Users_ID"].'/shop/articles/' ? ' selected' : '').'>文章首页</option>';
	$DB->get("shop_articles_category","*","where Users_ID='".$_SESSION["Users_ID"]."'");
	while($r=$DB->fetch_assoc()){
		echo '<option value="/api/'.$_SESSION["Users_ID"].'/shop/articles/category/'.$r["Category_ID"].'/"'.($url=='/api/'.$_SESSION["Users_ID"].'/shop/articles/category/'.$r["Category_ID"].'/' ? ' selected' : '').'>'.$r['Category_Name'].'</option>';
	}
	echo '<optgroup label="------------------文章列表------------------"></optgroup>';
	$DB->get("shop_articles","*","where Users_ID='".$_SESSION["Users_ID"]."' and Article_Status=1");
	while($r=$DB->fetch_assoc()){
		echo '<option value="/api/'.$_SESSION["Users_ID"].'/shop/article/'.$r["Article_ID"].'/"'.($url=='/api/'.$_SESSION["Users_ID"].'/shop/article/'.$r["Article_ID"].'/' ? ' selected' : '').'>'.$r['Article_Title'].'</option>';
	}
	echo '<optgroup label="------------------自定义URL------------------"></optgroup>';
	$DB->get("wechat_url","*","where Users_ID='".$_SESSION["Users_ID"]."'");
	while($rsUrl=$DB->fetch_assoc()){
		echo '<option value="'.$rsUrl['Url_Value'].'"'.($url==$rsUrl['Url_Value'] ? ' selected' : '').'>'.$rsUrl['Url_Name'].'('.$rsUrl['Url_Value'].')</option>';
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
<script type='text/javascript' src='/static/member/js/shop.js'></script>
<script type="text/javascript">
$(document).ready(shop_obj.dis_config_init);
</script>
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
	
	K('#QrcodeBgUpload').click(function(){
		editor.loadPlugin('image', function(){
			editor.plugin.imageDialog({
				imageUrl : K('#QrcodeBg').val(),
				clickFn : function(url, title, width, height, border, align){
					K('#QrcodeBg').val(url);
					K('#QrcodeBgDetail').html('<img src="'+url+'" />');
					editor.hideDialog();
				}
			});
		});
	});
	
	K('#ApplyBannerUpload').click(function(){
		editor.loadPlugin('image', function(){
			editor.plugin.imageDialog({
				imageUrl : K('#ApplyBanner').val(),
				clickFn : function(url, title, width, height, border, align){
					K('#ApplyBanner').val(url);
					K('#ApplyBannerDetail').html('<img src="'+url+'" />');
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
    <link href='/static/member/css/shop.css' rel='stylesheet' type='text/css' />
    <div class="r_nav">
      <ul>
        <li><a href="config.php">基本设置</a></li>
        <li><a href="other_config.php">活动设置</a></li>
        <li class="cur"><a href="distribute_config.php">分销设置</a></li>
        <li><a href="skin.php">风格设置</a></li>
        <li><a href="home.php">首页设置</a></li>
      </ul>
    </div>
    <div id="products" class="r_con_wrap">
      <form id="products_form" class="r_con_form" method="post" action="distribute_config.php">
        <div class="rows">
          <label>成为分销商门槛</label>
          <span class="input">
           <select name="Type" id="type">
           	 <option value="0"<?php echo $rsConfig["Distribute_Type"]==0 ? ' selected' : '';?>>无门槛，自动成为分销商</option>
             <option value="1"<?php echo $rsConfig["Distribute_Type"]==1 ? ' selected' : '';?>>积分限制</option>
             <option value="2"<?php echo $rsConfig["Distribute_Type"]==2 ? ' selected' : '';?>>消费金额限制</option>
             <option value="3"<?php echo $rsConfig["Distribute_Type"]==3 ? ' selected' : '';?>>购买商品</option>
             <option value="4"<?php echo $rsConfig["Distribute_Type"]==4 ? ' selected' : '';?>>手动申请</option>
           </select>
          </span>
          <div class="clear"></div>
        </div>
        
        <div class="rows" id="rows_1"<?php echo $rsConfig["Distribute_Type"] != 1 ? ' style="display:none"' : '';?>>
          <label>最低积分</label>
          <span class="input">
          <input type="text" name="Limit[1]" value="<?php echo $rsConfig["Distribute_Type"]==1 ? $rsConfig["Distribute_Limit"] : 0;?>" class="form_input" size="5" maxlength="10" /> <span class="tips">&nbsp;注:当用户积分达到此额度时自动成为分销商.</span>
          </span>
          <div class="clear"></div>
        </div>
        
        <div class="rows" id="rows_2"<?php echo $rsConfig["Distribute_Type"] != 2 ? ' style="display:none"' : '';?>>
          <label>最低消费额</label>
          <span class="input">
          <input type="text" name="Limit[2]" value="<?php echo $rsConfig["Distribute_Type"]==2 ? $rsConfig["Distribute_Limit"] : 0;?>" class="form_input" size="5" maxlength="10" /> <span class="tips">&nbsp;注:当用户消费总金额达到此额度时自动成为分销商，单位是元.</span>
          </span>
          <div class="clear"></div>
        </div>
        
        <div id="rows_3"<?php echo $rsConfig["Distribute_Type"] != 3 ? ' style="display:none"' : '';?>>
         <div class="rows">
           <label>选择商品</label>
           <span class="input">
             <select id="Category" >
              <option value=''>--请选择--</option>
              <?php foreach($category_list as $key=>$item):?>
              <option value="<?=$key?>"><?=$item['name']?></option>
               <?php if(count($item['children'])>0):?>              
                   <?php foreach($item['children'] as $cate_id=>$child):?>
                    <option value="<?php echo $cate_id;?>">&nbsp;&nbsp;&nbsp;&nbsp;<?=$child?></option>
                   <?php endforeach;?>
               <?php endif;?>
              <?php endforeach;?>
             </select>
             <input type="text"  id="keyword" placeholder="关键字" value="" class="form_input" size="35" maxlength="30" />
             <button type="button" id="search">搜索</button>
           </span>
           <div class="clear"></div>
         </div>
         <div class="rows">
           <label>可选商品</label>
           <span class="input">
            <select size='10' id="select_product" style="width:500px; height:100px">
            </select>
            <input type="hidden" id="limit3" name="Limit[3]" value="<?php echo $rsConfig["Distribute_Type"]==3 ? $rsConfig["Distribute_Limit"] : 0;?>" />
           </span>
           <div class="clear"></div>
         </div>
         <div class="rows">
           <label>商品名称</label>
           <span class="input">
            <input type="text" id="products_name" value="<?php echo $product_name;?>" class="form_input" />
           </span>
           <div class="clear"></div>
         </div>
 		</div>
        
        <div class="rows" id="rows_4"<?php echo $rsConfig["Distribute_Type"] != 4 ? ' style="display:none"' : '';?>>
          <label>是否需要审核</label>
          <span class="input">
           <input type="radio" name="Limit[4]" id="l_0" value="0"<?php echo $rsConfig["Distribute_Limit"]==0 ? ' checked' : '';?>/><label for="l_0"> 关闭</label>&nbsp;&nbsp;
           <input type="radio" name="Limit[4]" id="l_1" value="1"<?php echo $rsConfig["Distribute_Limit"]==1 ? ' checked' : '';?>/><label for="l_1"> 开启</label>
           <span class="tips">&nbsp;&nbsp;(开启审核后，用户提交申请后，要经过后台审核才能成为分销商)</span>
          </span>
          <div class="clear"></div>
        </div>
        
        <div class="rows">
          <label>自定义店名和头像</label>
          <span class="input">
           <input type="radio" name="Customize" id="c_0" value="0"<?php echo $rsConfig["Distribute_Customize"]==0 ? ' checked' : '';?>/><label for="c_0"> 关闭</label>&nbsp;&nbsp;
           <input type="radio" name="Customize" id="c_1" value="1"<?php echo $rsConfig["Distribute_Customize"]==1 ? ' checked' : '';?>/><label for="c_1"> 开启</label>
           <span class="tips">&nbsp;&nbsp;(设置分销商能否自定义店名与头像)</span>
          </span>
          <div class="clear"></div>
        </div>
        
		<div class="rows">
          <label>分销商提现门槛</label>
          <span class="input">
          	<select name="DType" id="dtype">
             <option value="1"<?php echo $rsConfig["Withdraw_Type"]==1 ? ' selected' : '';?>>购买任意商品</option>
             <option value="2"<?php echo $rsConfig["Withdraw_Type"]==2 ? ' selected' : '';?>>购买指定商品</option>
             <option value="3"<?php echo $rsConfig["Withdraw_Type"]==3 ? ' selected' : '';?>>所得佣金限制</option>
           </select>
          </span>
          <div class="clear"></div>
        </div>
        
        <div id="drows_2"<?php echo $rsConfig["Withdraw_Type"] != 2 ? ' style="display:none"' : '';?>>
         <div class="rows">
           <label>选择商品</label>
           <span class="input">
             <select id="DCategory" >
              <option value=''>--请选择--</option>
              <?php foreach($category_list as $key=>$item):?>
              <option value="<?=$key?>"><?=$item['name']?></option>
               <?php if(count($item['children'])>0):?>              
                   <?php foreach($item['children'] as $cate_id=>$child):?>
                    <option value="<?php echo $cate_id;?>">&nbsp;&nbsp;&nbsp;&nbsp;<?=$child?></option>
                   <?php endforeach;?>
               <?php endif;?>
              <?php endforeach;?>
             </select>
             <input type="text"  id="dkeyword" placeholder="关键字" value="" class="form_input" size="35" maxlength="30" />
             <button type="button" id="dsearch">搜索</button>
           </span>
           <div class="clear"></div>
         </div>
         <div class="rows">
           <label>可选商品</label>
           <span class="input">
            <select size='10' id="dselect_product" style="width:500px; height:100px">
            </select>
            <input type="hidden" id="dlimit2" name="DLimit[2]" value="<?php echo $rsConfig["Withdraw_Type"]==2 ? $rsConfig["Withdraw_Limit"] : 0;?>" />
           </span>
           <div class="clear"></div>
         </div>
         <div class="rows">
           <label>商品名称</label>
           <span class="input">
            <input type="text" id="dproducts_name" value="<?php echo $dproduct_name;?>" class="form_input" />
           </span>
           <div class="clear"></div>
         </div>
 		</div>
        
        <div class="rows" id="drows_3"<?php echo $rsConfig["Withdraw_Type"] != 3 ? ' style="display:none"' : '';?>>
          <label>最低佣金</label>
          <span class="input">
          <input type="text" name="DLimit[3]" value="<?php echo $rsConfig["Withdraw_Type"]==3 ? $rsConfig["Withdraw_Limit"] : 0;?>" class="form_input" size="5" maxlength="10" /> <span class="tips">&nbsp;注:当分销商佣金达到此额度时才能有提现功能.</span>
          </span>
          <div class="clear"></div>
        </div>
        
        <div class="rows">
          <label>每次提现最小金额</label>
          <span class="input">
          <input type="text" name="PerLimit" value="<?php echo $rsConfig["Withdraw_PerLimit"];?>" class="form_input" size="5" maxlength="10" /> <span class="tips">&nbsp;注:分销商每次申请提现时，所填写金额不得小于该值</span>
          </span>
          <div class="clear"></div>
        </div>
        
        <div class="rows">
           <label>自定义菜单</label>
           <span class="input">
            <div class="menu_add tips"><img src="/static/member/images/ico/add.gif" align="absmiddle" /> 添加菜单<font style="color:#F00">(该菜单将在分销商门槛、分销商提现门槛设置购买特定商品的购买页种显示)</font></div>
			<div class="blank9"></div>
			<table id="for_menu">
			  <tbody> 
				<tr>
				  <td>
                    <select name='MOrder[]' >
                      <?php $i=0; for($i=0; $i<11; $i++){?>
                      <option value='<?php echo $i?>'><?php echo $i==0 ? '默认' : $i;?></option>
                      <?php }?>
					</select>
                  </td>
				  <td>
                    <input type="text" class="form_input" value="" name="MName[]" />
                  </td>
				  <td>
                    <select name='MLink[]' >
                    <?php UrlList('');?>
                    </select>
                  </td>
				  <td align="center"><a href="javascript:void(0);" class="items_del"><img src="/static/member/images/ico/del.gif" /></a></td>
				</tr>
			  </tbody>
			</table>
			<table border="0" cellpadding="5" cellspacing="0" class="reverve_field_table" id="menubox">
			  <thead>
				<tr>
				  <td width="12%">排序</td>
				  <td width="20%">菜单名称</td>
				  <td width="60%">链接</td>
				  <td width="8%" align="center">操作</td>
				</tr>
			  </thead>
              <?php
               if(!empty($MenuList)){
				   foreach($MenuList as $m){
			  ?>
			  <tbody> 
				<tr>
				  <td>
                    <select name='MOrder[]'>
                      <?php $i=0; for($i=0; $i<11; $i++){?>
                      <option value='<?php echo $i?>'<?php echo $m["order"]==$i ? ' selected' : '';?>><?php echo $i==0 ? '默认' : $i;?></option>
                      <?php }?>
					</select>
                  </td>
				  <td>
                    <input type="text" class="form_input" value="<?php echo $m["name"];?>" name="MName[]" />
                  </td>
				  <td>
                    <select name='MLink[]' >
                    <?php UrlList($m["link"]);?>
                    </select>
                  </td>
				  <td align="center"><a href="javascript:void(0);" class="items_del"><img src="/static/member/images/ico/del.gif" /></a></td>
				</tr>
			  </tbody>
              <?php }}?>
		    </table>
           </span>
           <div class="clear"></div>
         </div>
        
        <div class="rows">
          <label>分销商申请页面banner</label>
          <span class="input">
           <span class="upload_file">
            <div>
             <div class="up_input"><input type="button" id="ApplyBannerUpload" value="上传图片" style="width:80px;" /></div>
             <div class="tips">图片建议尺寸：640*自定义</div>
             <div class="clear"></div>
            </div>
            <div class="img" id="ApplyBannerDetail" style="padding-top:8px;"><img src="<?php echo $rsConfig["ApplyBanner"] ? $rsConfig["ApplyBanner"] : '/static/api/distribute/images/apply_distribute.png';?>" /></div>
           </span>
          </span>
          <div class="clear"></div>
        </div>
        
        <div class="rows">
          <label>我的二维码背景图片</label>
          <span class="input">
           <span class="upload_file">
            <div>
             <div class="up_input"><input type="button" id="QrcodeBgUpload" value="上传图片" style="width:80px;" /></div>
             <div class="tips">图片建议尺寸：640*1010px</div>
             <div class="clear"></div>
            </div>
            <div class="img" id="QrcodeBgDetail" style="padding-top:8px;"><img src="<?php echo $rsConfig["QrcodeBg"] ? $rsConfig["QrcodeBg"] : '/static/api/distribute/images/qrcode_bg.jpg';?>" /></div>
           </span>
          </span>
          <div class="clear"></div>
        </div>
        
        <div class="rows">
        	<label>总部分销商排行榜</label>
            <span class="input">
            	  <input type="radio" name="HIncomelist_Open" 
                  value="1"<?php echo $rsConfig["HIncomelist_Open"]==1 ? ' checked' : '';?>/><label for="c_0">公开</label>&nbsp;&nbsp;
           <input type="radio" name="HIncomelist_Open"  value="0" <?php echo $rsConfig["HIncomelist_Open"]==0 ? ' checked' : '';?>/><label for="c_1">不公开</label>
           <span class="tips">&nbsp;&nbsp;(仅上榜后才有权限查看)</span>
            </span>
        </div>
        
         <div class="rows" >
        	<label>分销商代理类型</label>
            <span class="input">
                <?php
					$dis_type_list = array('关闭','普通代理','地区代理');
				?>
                <?php foreach($dis_type_list as $key=>$agent_name):?>
               <input type="radio" name="Dis_Agent_Type" value="<?=$key?>" <?=$key==$rsConfig["Dis_Agent_Type"]?'checked':''?> /><label for="c_0"><?=$agent_name?></label>&nbsp;&nbsp;                 
                <?php endforeach;?>
            </span>
        </div>
        
         <!-- 代理利润率begin -->
        	<div  class="rows" id="Agent_Rate_Row">
        <label>代理利润率</label>
        <?php if($rsConfig['Dis_Agent_Type'] != 0):?>
        	
        	
        		
                 <span class="input" id="Agent_Rate_Input">
        		
				<?php if($rsConfig['Dis_Agent_Type'] == 1):?>
            	 %<input type="text" name="Agent_Rate" value="<?=$rsConfig['Agent_Rate']?>" class="form_input" size="3" maxlength="10" notnull /> <span class="tips">占产品售价的百分比</span>
                <?php else: ?>
                  <?php 
				    $Agent_Rate_list = json_decode($rsConfig['Agent_Rate'],TRUE);
				  ?>
            	 省%<input type="text" name="Agent_Rate[Province]" value="<?= $Agent_Rate_list['Province']?>" class="form_input" size="3" maxlength="10" notnull />
				 市%<input type="text" name="Agent_Rate[City]" value="<?= $Agent_Rate_list['City']?>" class="form_input" size="3" maxlength="10" notnull />	 
	   			<?php endif; ?>
                
                 </span>
       		
        <?php else: ?>  
         
       	 <span class="input" id="Agent_Rate_Input"></span>
        <?php endif; ?>
         </div>
         <!-- 代理利润率end -->
         
        <div class="rows">
        	<label>入榜最低佣金</label>
            <span class="input">
            	<input type="text" name="H_Incomelist_Limit" value="<?php echo $rsConfig["H_Incomelist_Limit"];?>" class="form_input" size="8" maxlength="10" /> <span class="tips">&nbsp;注:单位是元.</span>
            </span>
        </div>
        <div class="rows">
          <label></label>
          <span class="input">
          <input type="submit" class="btn_green" name="submit_button" value="提交保存" />
          </span>
          <div class="clear"></div>
        </div> 
        <input type="hidden" name="QrcodeBg" id="QrcodeBg" value="<?php echo $rsConfig["QrcodeBg"] ? $rsConfig["QrcodeBg"] : '/static/api/distribute/images/qrcode_bg.jpg';?>" /> 
        <input type="hidden" name="ApplyBanner" id="ApplyBanner" value="<?php echo $rsConfig["ApplyBanner"] ? $rsConfig["ApplyBanner"] : '/static/api/distribute/images/apply_distribute.png';?>" />     
      </form>
    </div>
  </div>
</div>
</body>
</html>