<?php 
if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}
$BackID=empty($_REQUEST['BackID'])?0:$_REQUEST['BackID'];
$rsBack = $DB->GetRs("user_back_order","*","where Users_ID='".$_SESSION["Users_ID"]."' and Back_ID='".$BackID."'");
//获取相关订单信息


$rsUser = $DB->GetRs("user","*","where User_ID=".$rsBack['User_ID']);

if($_POST){
	
	$Back_ID = $_POST['BackID'];
	$Data=array(
		"Back_Status"=>$_POST["Status"]
	);
	
	$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Back_ID=".$BackID;
	//如果退货完成,则减去销量,恢复库存	
	if($_POST["Status"] == 3){
		$rsBackOrder = $DB->GetRs('user_back_order','*',$condition);
		$Product_List = json_decode($rsBackOrder['Back_Json'],TRUE);
		
		if(!empty($Product_List)){
			foreach($Product_List as $product_id=>$product){	
			$qty = $product['back_num'];
			$condition ="where Users_ID='".$_SESSION["Users_ID"]."' and Products_ID=".$product_id;
			$content = 'Products_Sales=Products_Sales+'.$qty.',Products_Count=Products_Count-'.$qty.',Products_SoldOut = 0';
			$DB->set('shop_products',$content,$condition);
			}
		}
		
	}

	$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Back_ID=".$BackID;
	$Flag=$DB->Set("user_back_order",$Data,$condition);
	
	if($Flag){
		echo '<script language="javascript">alert("修改成功");window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';
	}else{
		echo '<script language="javascript">alert("保存失败");history.back();</script>';
	}
	exit;
}

$rsConfig=$DB->GetRs("shop_config","ShopName,NeedShipping","where Users_ID='".$_SESSION["Users_ID"]."'");
$rsPay=$DB->GetRs("users_payconfig","Shipping","where Users_ID='".$_SESSION["Users_ID"]."'");

$Status=$rsBack["Back_Status"];
$Back_Status=array("申请中","已批准","退货中","已退货");
$Shipping=json_decode($rsBack["Back_Shipping"],true);
$PayShipping=empty($rsPay["Shipping"])?array():json_decode($rsPay["Shipping"],true);
$rsBack["Back_Json"] = str_replace('\n','',$rsBack["Back_Json"]);
$ProductList = json_decode($rsBack["Back_Json"],true);

$total = 0;
$total_num = 0;
foreach($ProductList as $key=>$product){
	$total += $product['Products_Price']*$product['back_num'];
	$total_num += $product['back_num'];
}

$amount = $fee = 0;
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
</head>

<body>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->

<div id="iframe_page">
  <div class="iframe_content">
    <link href='/static/member/css/shop.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/member/js/shop.js'></script>
    <div class="r_nav">
      <ul>
        <li class=""><a href="config.php">基本设置</a></li>
        <li class=""><a href="skin.php">风格设置</a></li>
        <li class=""><a href="home.php">首页设置</a></li>
        <li class=""><a href="products.php">产品管理</a></li>
        
        <li class="cur"><a href="orders.php">退货单管理</a></li>
      </ul>
    </div>
    <link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script> 
    <script language="javascript">
var NeedShipping=1;
var orders_status=["申请中","已批准","退货中","已退货"];
$(document).ready(shop_obj.orders_init);
</script>
    <div id="orders" class="r_con_wrap">
      <div class="control_btn">
      <a href="javascript:void(0);" class="btn_gray" onClick="history.go(-1);">返 回</a>
      </div>
      <script type='text/javascript' src='/static/js/plugin/pcas/pcas.js'></script> 
      <div class="cp_title">
        <div id="cp_view" class="cur">退货单详情</div>
        <div id="cp_mod">修改退货单</div>
      </div>
      <div class="detail_card">
        <form id="orders_mod_form" method="post" action="back_view.php">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="order_info">
            <tr>
              <td width="8%" nowrap>退货单编号：</td>
              <td width="92%"><?php echo date("Ymd",$rsBack["Back_CreateTime"]).$rsBack["Back_ID"] ?></td>
            </tr>
			
            <tr>
              <td nowrap>退货单总价：</td>
              <td><span class="cp_item_view">￥<?=$total;?></span> <span class="cp_item_mod">￥<input name="TotalPrice" value="<?=$total;?>" size="5" /></span></td>
            </tr>
		
            <tr>
              <td nowrap>退货单时间：</td>
              <td><?php echo date("Y-m-d H:i:s",$rsBack["Back_CreateTime"]) ?></td>
            </tr>
            
            <tr>
               <td nowrap>相关订单：</td>
               <td><a href="/member/shop/orders_view.php?OrderID=<?=$rsBack['Order_ID']?>" class="red">查看</a></td>
            </tr>
             <tr>
              <td nowrap>退货人：</td>
              <td><?=!empty($rsUser["User_NickName"])?$rsUser["User_NickName"]:'信息不全'?></td>
            </tr>
            
            <tr>
              <td nowrap>物流公司</td>
			   <td><?=!empty($rsBack["Back_Shipping"])?$rsBack["Back_Shipping"]:"暂无"?></td>
            </tr>
             
             
             <tr>
               <td nowrap>物流单号</td>
               <td><?=!empty($rsBack["Back_ShippingID"])?$rsBack["Back_ShippingID"]:"暂无"?></td>
            </tr>
             
            
            <tr>
              <td nowrap>退货单状态：</td>
              <td><span class="cp_item_view"><?php echo $Back_Status[$Status];?></span> <span class="cp_item_mod">
                <select name="Status">
                  <option value='0'<?php echo $rsBack["Back_Status"]==0?" selected":"" ?>>申请中</option>
                  <option value='1'<?php echo $rsBack["Back_Status"]==1?" selected":"" ?>>已批准</option>
                  <option value='2'<?php echo $rsBack["Back_Status"]==2?" selected":"" ?>>退货中</option>
                  <option value='3'<?php echo $rsBack["Back_Status"]==3?" selected":"" ?>>已退货</option>
                </select>
                </span></td>
            </tr>
          
            
           <?php if($rsBack['Back_Status'] > 2):?>
            <tr>
              <td nowrap>配送方式：</td>
              <td><span class="cp_item_view"><?php echo isset($Shipping) && isset($Shipping["Express"])?$Shipping["Express"]:"" ?></span> <span class="cp_item_mod">
                <select name="Shipping[Express]">
                  <?php foreach($PayShipping as $key=>$value){?>
                  <option value="<?php echo $value["Express"] ?>"<?php echo isset($PayShipping) && isset($PayShipping["Express"]) && $PayShipping["Express"]==$value["Express"]?" selected":"" ?>><?php echo $value["Express"] ?></option>
                  <?php }?>
                </select>
                &nbsp;&nbsp;快递单号：
                <input name="ShippingID" value="<?php echo $rsBack["Back_ShippingID"] ?>" />
                </span></td>
            </tr>
            <?php endif; ?>
          
            <tr class="cp_item_mod">
              <td></td>
              <td><input type="submit" class="btn_green" name="submit_button" value="提交保存" />
                <input type="button" class="back btn_gray" name="back" value="取消" /></td>
            </tr>
          </table>
          <input type="hidden" name="BackID" value="<?php echo $rsBack["Back_ID"] ?>" />
        </form>
        <div class="blank12"></div>
        <div class="item_info">物品清单</div>
        <table class="order_item_list" border="0" cellpadding="0" cellspacing="0" width="100%">
          <tbody><tr class="tb_title">
            <td width="20%">图片</td>
            <td width="35%">产品信息</td>
            <td width="15%">价格</td>
            <td width="15%">数量</td>
            <td class="last" width="15%">小计</td>
          </tr>
          <?php foreach($ProductList as $product_id=>$product):?>

          <tr class="item_list" align="center">
            <td valign="top"><img src="<?=$product['Products_Image']?>" height="100" width="100"></td>
            <td class="flh_180" align="left"><?=$product['Products_Name']?><br/>
            退货原因:<?=$product['reason']?></td>
            <td><?=$product['Products_Price']?>
           
            </td>
            <td><?=$product['back_num']?></td>
            <td><?=$product['Products_Price']*$product['back_num']?></td>
          </tr>
          <?php endforeach; ?>
          <tr class="total">
            <td colspan="3">&nbsp;</td>
            <td><?=$total_num?></td>
            <td><?=$total?></td>
          </tr>
        </tbody></table>
      </div>
    </div>
  </div>
</div>
</body>
</html>