<?php

require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/shipping.php');

//ini_set("display_errors","On");

if(empty($_SESSION["Users_Account"])){
	header("location:/member/login.php");
}
$OrderID=empty($_REQUEST['OrderID'])?0:$_REQUEST['OrderID'];

/* //判断是否地区管理
$condition = "";
if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'employee'){
	$employee_info = $DB->GetRs("users_employee","","where id = '{$_SESSION["employee_id"]}'");
	if(!empty($employee_info) && $employee_info['isAbleArea'] == "1"){
		if(!empty($employee_info['loc_province'])){
			$condition .= " AND Address_Province = '{$employee_info["loc_province"]}'";
		}
		if(!empty($employee_info['loc_city'])){
			$condition .= " AND Address_City = '{$employee_info["loc_city"]}'";
		}
		if(!empty($employee_info['loc_town'])){
			$condition .= " AND Address_Area = '{$employee_info["loc_town"]}'";
		}
		
	}
} */

$rsOrder=$DB->GetRs("spark_order","*","where Users_ID='".$_SESSION["Users_ID"]."' and id='".$OrderID."'");
if(empty($rsOrder)){
	echo '<script language="javascript">alert("你要查看的订单不存在！");history.back();</script>';
	exit;
}
//$Shipping=json_decode($rsOrder["Order_Shipping"],true);
if($_POST){
	//var_dump($_POST);exit();
	$Data=array(
		
		"realName"=>$_POST['Name'],
		"mobile"=>$_POST["Mobile"],
		"payCode"=>$_POST["Status"]
	);
/* 	if(!empty($_POST["Shipping"]["Express"])){
		$ShippingN = array(
			"Express"=>$_POST["Shipping"]["Express"],
			"Price"=>empty($Shipping["Price"]) ? 0 : $Shipping["Price"]
		);
		$Data["Order_Shipping"] = json_encode($ShippingN,JSON_UNESCAPED_UNICODE);
	} */
	$Flag=$DB->Set("spark_order",$Data," where Users_ID='".$_SESSION["Users_ID"]."' and id=".$OrderID);
	if($Flag){		
		
		echo '<script language="javascript">alert("修改成功");window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';
	}else{
		echo '<script language="javascript">alert("保存失败");history.back();</script>';
	}
}else{
	$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$_SESSION["Users_ID"]."'");
	$rsPay=$DB->GetRs("users_payconfig","Shipping","where Users_ID='".$_SESSION["Users_ID"]."'");

	$Status=$rsOrder["payCode"];
	$Order_Status=array("未支付","已支付");
	
	$PayShipping = get_front_shiping_company_dropdown($_SESSION["Users_ID"],$rsConfig);
	$CartList=json_decode($rsOrder["Order_CartList"],true);
	$amount = $fee = 0;
	if(is_numeric($rsOrder['Address_Province'])){
		$area_json = read_file($_SERVER["DOCUMENT_ROOT"].'/data/area.js');
		$area_array = json_decode($area_json,TRUE);
		$province_list = $area_array[0];
		$Province = '';
		if(!empty($rsOrder['Address_Province'])){
			$Province = $province_list[$rsOrder['Address_Province']].',';
		}
		$City = '';
		if(!empty($rsOrder['Address_City'])){
			$City = $area_array['0,'.$rsOrder['Address_Province']][$rsOrder['Address_City']].',';
		}

		$Area = '';
		if(!empty($rsOrder['Address_Area'])){
			$Area = $area_array['0,'.$rsOrder['Address_Province'].','.$rsOrder['Address_City']][$rsOrder['Address_Area']];
		}
	}else{
		$Province = $rsOrder['Address_Province'];
		$City = $rsOrder['Address_City'];
		$Area = $rsOrder['Address_Area'];
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
        <li class="cur"><a href="order.php">订单管理</a></li>
      </ul>
    </div>
    <link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script> 
    <script language="javascript">
var NeedShipping=1;
var orders_status=["未支付","已支付"];
$(document).ready(shop_obj.orders_init);
</script>
    <div id="orders" class="r_con_wrap">
      <div class="control_btn">
      <a href="javascript:void(0);" class="btn_gray" onClick="history.go(-1);">返 回</a>
      <a href="order_print.php?OrderID=<?=$rsOrder["id"]?>" target="blank" class="btn_gray" id="order_print">打印订单</a>
      </div>
      
      <div class="cp_title">
        <div id="cp_view" class="cur">订单详情</div>
        <div id="cp_mod">修改订单</div>
      </div>
      <div class="detail_card">
        <form id="orders_mod_form" method="post" action="">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="order_info">
            <tr>
              <td width="8%" nowrap>订单编号：</td>
              <td width="92%"><?php echo date("Ymd",$rsOrder["createtime"]).$rsOrder["id"] ?></td>
            </tr>
			
            <tr>
              <td nowrap>订单总价：</td>
              <td><span class="cp_item_view">￥<?php echo $rsOrder["price"] ?></span> <span class="cp_item_mod">￥<input name="TotalPrice" value="<?php echo $rsOrder["price"] ?>" size="5" /></span></td>
            </tr>
			<?php if($rsOrder["Coupon_ID"]>0){?>
			<tr>
			  <td nowrap>优惠详情</td>
			  <td><font style="color:blue;">已使用优惠券</font>(
				  <?php if($rsOrder["Coupon_Discount"]>0){?>
				  享受<?php echo $rsOrder["Coupon_Discount"]*10;?>折
				  <?php }?>
				  <?php if($rsOrder["Coupon_Cash"]>0){?>
				  抵现金<?php echo $rsOrder["Coupon_Cash"];?>元
				  <?php }?>)
			  </td>
			</tr>
			<?php }?>
            <tr>
              <td nowrap>订单时间：</td>
              <td><?php echo date("Y-m-d H:i:s",$rsOrder["createtime"]) ?></td>
            </tr>
            <tr>
              <td nowrap>订单状态：</td>
              <td><span class="cp_item_view"><?php echo $Order_Status[$Status];?></span> <span class="cp_item_mod">
                <select name="Status">
                  <option value='0'<?php echo $rsOrder["payCode"]==0?" selected":"" ?>>未支付</option>
                  <option value='1'<?php echo $rsOrder["payCode"]==1?" selected":"" ?>>已支付</option>
                 </select>
                </span></td>
            </tr>
            
           <!--  <tr>
              <td nowrap>付款信息：</td>
              <td><?php echo $rsOrder["Order_PaymentInfo"] ?></td>
            </tr> -->
            <tr>
              <td nowrap>联系人：</td>
              <td><span class="cp_item_view"><?php echo $rsOrder["realName"] ?></span> <span class="cp_item_mod">
                <input name="Name" value="<?php echo $rsOrder["realName"] ?>" size="10" />
                </span></td>
            </tr>
            <tr>
              <td nowrap>手机号码：</td>
              <td><span class="cp_item_view"><?php echo $rsOrder["mobile"] ?></span> <span class="cp_item_mod">
                <input name="Mobile" value="<?php echo $rsOrder["mobile"] ?>" size="15" />
                </span></td>
            </tr>
            
            <tr>
              <td nowrap>地址信息：</td>
              <td><span class="cp_item_view"><?php echo $rsOrder["address"] ?></span> <span class="cp_item_mod">
                <input name="address" value="<?php echo $rsOrder["address"] ?>" size="15" />
                </span></td>
            </tr>
           
            <tr class="cp_item_mod">
              <td></td>
              <td><input type="submit" class="btn_green" name="submit_button" value="提交保存" />
                <input type="button" class="back btn_gray" name="back" value="取消" /></td>
            </tr>
          </table>
          <input type="hidden" name="OrderID" value="<?php echo $rsOrder["id"] ?>" />
        </form>
        <div class="blank12"></div>
        
    </div>
  </div>

</body>
</html>