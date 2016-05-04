<?php require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');

$base_url = base_url();
$shop_url = shop_url();

/*分享页面初始化配置*/
$share_flag = 1;
$signature = '';

if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo '缺少必要的参数';
	exit;
}
if(empty($_SESSION[$UsersID."User_ID"]))
{
	header("location:/api/".$UsersID."/user/login/");
}
if(!strpos($_SERVER['REQUEST_URI'],"mp.weixin.qq.com")){
	header("location:?wxref=mp.weixin.qq.com");
}
$BackID = $_GET['BackID'];

$rsConfig = $DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$rsBackup = $DB->GetRs("user_back_order","*","where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Back_ID='".$BackID."'");
$total = 0;

$JSON = json_decode($rsBackup['Back_Json'],TRUE);

foreach($JSON as $key=>$product){
	$total += $product['Products_Price']*$product['back_num'];
}

$Status = $rsBackup["Back_Status"];
$Back_Status=array("申请中","已批准","退货中","已退货");

$Shipping = json_decode($rsBackup["Back_Shipping"],true);
$ProductList = json_decode($rsBackup["Back_Json"],true);
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<meta content="telephone=no" name="format-detection" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $rsConfig["ShopName"] ?></title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/api/shop/skin/default/css/style.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/api/js/global.js'></script>
<script type='text/javascript' src='/static/api/shop/js/shop.js'></script>
<script language="javascript">$(document).ready(shop_obj.backup_init);</script>
</head>

<body>
<div id="shop_page_contents">
  <div id="cover_layer"></div>
  <link href='/static/api/shop/skin/default/css/member.css?t=<?php echo time();?>' rel='stylesheet' type='text/css' />
  <ul id="member_nav">
    <li class="<?php echo $Status==0?"cur":"" ?>"><a href="/api/<?php echo $UsersID ?>/shop/member/backup/status/0/">申请中</a></li>
    <li class="<?php echo $Status==1?"cur":"" ?>"><a href="/api/<?php echo $UsersID ?>/shop/member/backup/status/1/">已批准</a></li>
    <li class="<?php echo $Status==2?"cur":"" ?>"><a href="/api/<?php echo $UsersID ?>/shop/member/backup/status/2/">退货中</a></li>
    <li class="<?php echo $Status==3?"cur":"" ?>"><a href="/api/<?php echo $UsersID ?>/shop/member/backup/status/3/">已退货</a></li>
    <?php if(!empty($rsConfig["NeedShipping"])){?>
    <li class=""><a href="/api/<?php echo $UsersID ?>/shop/member/address/">地址簿</a></li>
    <?php }?>
  </ul>
  
  <div id="order_detail">
    <div class="item">
      <ul>
        <li>退货单编号<?=$rsBackup['Back_Sn']?></li>
        <li>申请时间: <?php echo date("Y-m-d H:i:s",$rsBackup["Back_CreateTime"]) ?></li>
        <li>退货状态: <?php echo $Back_Status[$rsBackup["Back_Status"]] ?></li>
        <li>总价: <strong class="fc_red">￥<?php echo $total ?></strong></li>
      </ul>
    </div>
    <div class="item">
      <ul>
        <li>退货地址:郑州市金水区经三路广电南路交叉口格林融熙国际2110室</li>

      </ul>
    </div>
	<div class="item">
    	<table class="bordered">
    <thead>

    <tr>
        <th>#</th>        
        <th>商品名</th>
        <th>小计</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($ProductList as $key=>$product):?>
    <tr>
        <td><?=$key?></td>        
        <td><?=$product['Products_Name']?></td>
        <td>&yen;<?=$product['Products_Price']?>*<?=$product['back_num']?>=&yen;<?=$product['Products_Price']*$product['back_num']?></td>
    </tr>   
    <?php endforeach;?>     
    
</tbody></table>
    </div>
      <?php if($rsBackup["Back_Status"] >= 2):?>
    <div class="item">
    	 <h1>&nbsp;&nbsp;快递信息</h1>
    	<dl>
        	<dd>快递公司:<?=$rsBackup["Back_Shipping"]?></dd>
          	<dd>快递单号:<?=$rsBackup["Back_ShippingID"]?></dd>	

        </dl>	
    </div>
     <?php endif;?>
    <?php if($rsBackup["Back_Status"] == 1):?>
    <form action="<?=$base_url?>api/<?=$UsersID?>/shop/member/" method="post" name="backup_shipping_form" id="backup_shipping_form">
    <input type="hidden" name="action" value="submit_shipping" />
    <input type="hidden" name="Back_ID" value="<?=$BackID?>"/>
   
    <div class="item">
        <h1>&nbsp;&nbsp;快递信息</h1>
    	<dl>
        	<dd>快递公司<font class="fc_red">*</font>
          		<input name="Back_Shipping" notnull type="text">
        	</dd>
            
            <dd>快递单号<font class="fc_red">*</font>
          		<input name="Back_ShippingID"  notnull type="text">
        	</dd>
        	
        </dl>
    </div>
    </form>
     <?php endif;?>
     
     
    <?php if($rsBackup["Back_Status"] == 1):?>
    <div class="backup"><a id="submit_shipping" href="javascript:void(0)">提交</a></div>
    <?php endif;?>
    
  </div>
</div>
<?php
 	require_once('../distribute_footer.php');
 ?>
</body>
</html>