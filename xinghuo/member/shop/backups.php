<?php 
if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}
$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Back_Type='shop'";
if(isset($_GET["search"])){
	if($_GET["search"]==1){
		if(!empty($_GET["Keyword"])){
			$condition .= " and Back_Json like '%".$_GET["Keyword"]."%'";
			$condition .= " or Back_Sn like '%".$_GET["Keyword"]."%'";
		}
		if(isset($_GET["Status"])){
			if($_GET["Status"]<>''){
				$condition .= " and Back_Status=".$_GET["Status"];
			}
		}
		if(!empty($_GET["AccTime_S"])){
			$condition .= " and Back_CreateTime>=".strtotime($_GET["AccTime_S"]);
		}
		if(!empty($_GET["AccTime_E"])){
			$condition .= " and Back_CreateTime<=".strtotime($_GET["AccTime_E"]);
		}
	}
}
$condition .= " order by Back_CreateTime desc";

if(isset($_GET["action"]))
{
	if($_GET["action"]=="del")
	{
		$Flag=$DB->Del("user_back_order","Users_ID='".$_SESSION["Users_ID"]."' and Back_ID=".$_GET["OrderID"]);
		
		if($Flag)
		{
			echo '<script language="javascript">alert("删除成功");window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';
		}else
		{
			echo '<script language="javascript">alert("删除失败");history.back();</script>';
		}
		exit;
	}elseif($_GET["action"]=="set_read")
	{
		$Flag=$DB->Set("user_order","Order_IsRead=1","where Users_ID='".$_SESSION["Users_ID"]."' and Back_ID=".$_GET["OrderID"]);
		$Data=array("ret"=>1);
		echo json_encode($Data,JSON_UNESCAPED_UNICODE);
		exit;
	}elseif($_GET["action"]=="is_not_read")
	{
		$Flag=$DB->Set("user_order","Order_IsRead=1","where Users_ID='".$_SESSION["Users_ID"]."' and Back_ID=".$_GET["OrderID"]);
		$Data=array(
			"ret"=>1,
			"msg"=>""
		);
		echo json_encode($Data,JSON_UNESCAPED_UNICODE);
		exit;
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
        <li class=""><a href="config.php">基本设置</a></li>
        <li class=""><a href="skin.php">风格设置</a></li>
        <li class=""><a href="home.php">首页设置</a></li>
        <li class=""><a href="products.php">产品管理</a></li>
        <li class><a href="orders.php">订单管理</a></li>
        <li class="cur"><a href="backups.php">退货单管理</a></li>
      </ul>
    </div>
    <link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script> 
    <script language="javascript">
var NeedShipping=1;
var back_status=["申请中","已批准","退货中","已退货"];
$(document).ready(shop_obj.orders_init);
</script>
    <div id="orders" class="r_con_wrap">
      <form class="search" id="search_form" method="get" action="">
        关键词：
        <input type="text" name="Keyword" value="" class="form_input" size="15" />
        退货单状态：
        <select name="Status">
          <option value="">--请选择--</option>
          <option value='0'>申请中</option>
          <option value='1'>已批准</option>
          <option value='2'>退货中</option>
          <option value='3'>已完成</option>
        </select>
        时间：
        <input type="text" class="input" name="AccTime_S" value="" maxlength="20" />
        -
        <input type="text" class="input" name="AccTime_E" value="" maxlength="20" />
		<input type="hidden" value="1" name="search" />
        <input type="submit" class="search_btn" value="搜索" />

      </form>
      <table border="0" cellpadding="5" cellspacing="0" class="r_con_table" id="order_list">
        <thead>
          <tr>
            <td width="8%" nowrap="nowrap">序号</td>
            <td width="17%" nowrap="nowrap">退货单号</td>
          
            <td width="15%" nowrap="nowrap">配送方式</td>
            <td width="12%" nowrap="nowrap">退货单状态</td>
            <td width="12%" nowrap="nowrap">时间</td>
            <td width="10%" nowrap="nowrap" class="last">操作</td>
          </tr>
        </thead>
        <tbody>
          <?php $DB->getPage("user_back_order","*",$condition,10);

$Back_Status=array("申请中","已批准","退货中","已退货");

while($rsBack=$DB->fetch_assoc()){
$Shipping=json_decode($rsBack["Back_Shipping"],true);?>
          <tr class="<?php echo empty($rsBack["Back_IsRead"])?"is_not_read":"" ?>" IsRead="<?php echo $rsBack["Back_IsRead"] ?>" OrderId="<?php echo $rsBack["Back_ID"] ?>">
            <td nowrap="nowrap"><?php echo $rsBack["Back_ID"] ?></td>
            <td nowrap="nowrap"><?php echo $rsBack["Back_Sn"]  ?>
			
			 &nbsp;&nbsp;&nbsp;&nbsp;<a href="/member/shop/orders_view.php?OrderID=<?=$rsBack['Order_ID']?>">相关订单&nbsp;&nbsp;<img src="/static/member/images/ico/jt.gif"/></a>
			</td>
            <td nowrap="nowrap"><?php echo $rsBack["Back_Shipping"] ?></td>
            <td nowrap="nowrap"><?php echo $Back_Status[$rsBack["Back_Status"]] ?></td>
            <td nowrap="nowrap"><?php echo date("Y-m-d H:i:s",$rsBack["Back_CreateTime"]) ?></td>
            <td class="last" nowrap="nowrap"><a href="back_view.php?BackID=<?php echo $rsBack["Back_ID"] ?>"><img src="/static/member/images/ico/view.gif" align="absmiddle" alt="修改" /></a> <a href="backups.php?action=del&OrderID=<?php echo $rsBack["Back_ID"] ?>" title="删除" onClick="if(!confirm('删除后不可恢复，继续吗？')){return false};"><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a></td>
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