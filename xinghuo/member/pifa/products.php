<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');

if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}
if(isset($_GET["action"]))
{
	if($_GET["action"]=="del")
	{
		$Flag=$DB->Del("pifa_products","Users_ID='".$_SESSION["Users_ID"]."' and Products_ID=".$_GET["ProductsID"]);
		if($Flag){
			echo '<script language="javascript">alert("删除成功");window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';
		}else{
			echo '<script language="javascript">alert("删除失败");history.back();</script>';
		}
		exit;
	}
}

$condition = "where Users_ID='".$_SESSION["Users_ID"]."'";
if(isset($_GET['search'])){
	if($_GET['Keyword']){
		$condition .= " and Products_Name like '%".$_GET['Keyword']."%'";
	}
	if($_GET['SearchCateId']>0){
		$condition .= " and Products_Category=".$_GET['SearchCateId'];
	}
	if($_GET["Attr"]){
		$condition .= " and Products_".$_GET["Attr"]."=1";
	}
}
$condition .= " order by Products_ID desc";
function get_category($catid){
	global $DB;
	$r = $DB->GetRs("pifa_category","*","where Category_ID='".$catid."'");
	return $r['Category_Name'];
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
</head>

<body>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->

<div id="iframe_page">
  <div class="iframe_content">
    <link href='/static/member/css/pifa.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/member/js/pifa.js'></script>
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
      <script language="javascript">$(document).ready(pifa_obj.products_list_init);</script>
      <div class="control_btn">
      <a href="products_add.php" class="btn_green btn_w_120">添加产品</a> <a href="#search" class="btn_green btn_w_120">产品搜索</a> 
      <a href="output.php?type=product_gross_info" class="btn_green btn_w_120" style="display:none;">导出产品</a>
      </div>
      <form class="search" method="get" action="products.php">
        关键词：
        <input type="text" name="Keyword" value="" class="form_input" size="15" />
        产品分类：
        <select name='SearchCateId'>
          <option value=''>--请选择--</option>
          <?php $DB->get("pifa_category","*","where Users_ID='".$_SESSION["Users_ID"]."' and Category_ParentID=0 order by Category_Index asc");
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
					  echo '<option value="'.$rsCategory["Category_ID"].'">'.$rsCategory["Category_Name"].'</option>';
				  }
			  echo '</optgroup>';
			  }else{
				  echo '<option value="'.$value["Category_ID"].'">'.$value["Category_Name"].'</option>';
			  }
		  }?>
        </select>
        其他属性：
        <select name="Attr">
          <option value="0">--请选择--</option>
          <option value="SoldOut">下架</option>
          <option value="IsNew">新品</option>
          <option value="IsHot">热卖</option>
        </select>
		<input type="hidden" name="search" value="1" />
        <input type="submit" class="search_btn" value="搜索" />
      </form>
      <table width="100%" align="center" border="0" cellpadding="5" cellspacing="0" class="r_con_table">
        <thead>
          <tr>
            <td width="8%" nowrap="nowrap">序号</td>
            <td width="8%" nowrap="nowrap">名称</td>
            <td width="8%" nowrap="nowrap">分销佣金</td>
            <td width="8%" nowrap="nowrap">属性分类</td>
            <td width="15%" nowrap="nowrap">价格区间</td>
            <td width="10%" nowrap="nowrap">图片</td>
            <td width="10%" nowrap="nowrap">二维码</td>
            <td width="6%" nowrap="nowrap">其他属性</td>
            <td width="10%" nowrap="nowrap">时间</td>
            <td width="22%" nowrap="nowrap" class="last">操作</td>
          </tr>
        </thead>
        <tbody>
          <?php 
		  $lists = array();
		  $DB->getPage("pifa_products","*",$condition,10);
		  
		  while($r=$DB->fetch_assoc()){
			  $lists[] = $r;
		  }
		  foreach($lists as $k=>$rsProducts){
			  $JSON = json_decode($rsProducts['Products_JSON'],true);
			  $price_rule = json_decode($rsProducts['Products_price_rule'],true);
		  ?>
              
          <tr>
            <td nowrap="nowrap"><?php echo $rsProducts["Products_ID"] ?></td>
            <td><?php echo $rsProducts["Products_Name"] ?></td>
            <td>
            	<?php 
					$distribute_list = json_decode($rsProducts["Products_Distributes"],true);
				?>
                一级&nbsp;&nbsp;%<?=!empty($distribute_list[0])?$distribute_list[0]:0?><br/>
                二级&nbsp;&nbsp;%<?=!empty($distribute_list[1])?$distribute_list[1]:0?><br/>
                三级&nbsp;&nbsp;%<?=!empty($distribute_list[2])?$distribute_list[2]:0?><br/>
            </td>
            <td>
            <?php echo get_category($rsProducts["Products_Category"]);?>
			</td>
            <td nowrap="nowrap">
			    <?php foreach($price_rule as $k => $v){
					$str0 = $v[0];
					$str1 = $v[1];
					if($v[0] === 0) $str0 = '不限';
					if($v[1] === 0) $str1 = '不限';
					echo '数量'.$str0.'&nbsp;-&nbsp;'.$str1.'&nbsp;<b class="red">￥'.$v[2].'</b><br />';
				}?>
			</td>
            <td nowrap="nowrap"><?php echo empty($JSON["ImgPath"])?'':'<img src="'.$JSON["ImgPath"][0].'" class="proimg" />'; ?></td>
            <td nowraqp="nowrap">
            <img width="80" height="80" src="<?=$rsProducts['Products_Qrcode']?>" /></td>
            <td nowrap="nowrap"><?php echo empty($rsProducts["Products_SoldOut"])?"":"下架<br>";
			echo empty($rsProducts["Products_IsShippingFree"])?"":"免运费<br>";
			echo empty($rsProducts["Products_IsNew"])?"":"新品<br>";
			echo empty($rsProducts["Products_IsRecommend"])?"":"推荐<br>";
			echo empty($rsProducts["Products_IsHot"])?"":"热卖"; ?></td>
         
            <td nowrap="nowrap"><?php echo date("Y-m-d",$rsProducts["Products_CreateTime"]) ?></td>
            <td class="last" nowrap="nowrap"><a href="products_edit.php?ProductsID=<?php echo $rsProducts["Products_ID"] ?>"><img src="/static/member/images/ico/mod.gif" align="absmiddle" alt="修改" /></a>
			<a href="products.php?action=del&ProductsID=<?php echo $rsProducts["Products_ID"] ?>" onClick="if(!confirm('删除后不可恢复，继续吗？')){return false};"><img src="/static/member/images/ico/del.gif" align="absmiddle" alt="删除" /></a>
			</td>
          </tr>
          <?php }?>
        </tbody>
      </table>
      <div class="blank20"></div>
      <?php $DB->showPage(); ?>
    </div>
  </div>
</div>
</body>
</html>