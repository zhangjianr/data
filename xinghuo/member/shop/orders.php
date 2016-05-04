<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');

require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/lib_products.php');

if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}

//获取所有分销商列表
$ds_list = Dis_Account::with('User')
			->where(array('Users_ID' => $_SESSION["Users_ID"]))
			->get(array('Users_ID', 'User_ID', 'invite_id', 'User_Name', 'Account_ID', 'Shop_Name','Account_CreateTime'))
			->toArray();
			
$ds_list_dropdown = array();
foreach($ds_list as $key=>$item){
	if(!empty($item['user'])){
		$ds_list_dropdown[$item['User_ID']] = $item['user']['User_NickName'];
	}
}

//获取可用的支付方式列表
$Pay_List = get_enabled_pays($DB,$_SESSION["Users_ID"]);

//取出商城配置信息
$rsConfig=$DB->GetRs("shop_config","ShopName,NeedShipping","where Users_ID='".$_SESSION["Users_ID"]."'");

$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Order_Type='shop'";
//判断是否地区管理
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
}

if(isset($_GET["search"])){
	if($_GET["search"]==1){
		if(!empty($_GET["Keyword"])){
			$condition .= " and `".$_GET["Fields"]."` like '%".$_GET["Keyword"]."%'";
		}
		if(isset($_GET["Status"])){
			if($_GET["Status"]<>''){
				$condition .= " and Order_Status=".$_GET["Status"];
			}
		}
		if(!empty($_GET["AccTime_S"])){
			$condition .= " and Order_CreateTime>=".strtotime($_GET["AccTime_S"]);
		}
		if(!empty($_GET["AccTime_E"])){
			$condition .= " and Order_CreateTime<=".strtotime($_GET["AccTime_E"]);
		}
	}
}

$condition .= " order by Order_CreateTime desc";

if(isset($_GET["action"]))
{
	if($_GET["action"]=="del")
	{
		$Flag=$DB->Del("user_order","Users_ID='".$_SESSION["Users_ID"]."' and Order_ID=".$_GET["OrderID"]);
		if($Flag)
		{
			echo '<script language="javascript">alert("删除成功");window.location="orders.php'.(isset($_GET["page"]) ? '?page='.$_GET["page"] : '').'";</script>';
		}else
		{
			echo '<script language="javascript">alert("删除失败");history.back();</script>';
		}
		exit;
	}elseif($_GET["action"]=="set_read")
	{
		$Flag=$DB->Set("user_order","Order_IsRead=1","where Users_ID='".$_SESSION["Users_ID"]."' and Order_ID=".$_GET["OrderID"]);
		$Data=array("ret"=>1);
		echo json_encode($Data,JSON_UNESCAPED_UNICODE);
		exit;
	}elseif($_GET["action"]=="is_not_read")
	{
		$Flag=$DB->Set("user_order","Order_IsRead=1","where Users_ID='".$_SESSION["Users_ID"]."' and Order_ID=".$_GET["OrderID"]);
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
<script type='text/javascript' src='/static/js/plugin/laydate/laydate.js'></script>
<link href='/static/css/bootstrap.min.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/bootstrap.min.js'></script>
<style>
.page .pre, .page .next, .page .nopre, .page .nonext {
	width:60px!important;
}
</style>
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
        <li class="cur"><a href="orders.php">订单管理</a></li>
        <li><a href="virtual_orders.php">消费认证</a></li>
      </ul>
    </div>
    <script language="javascript">
var NeedShipping=1;
var orders_status=["待付款","待确认","已付款","已发货","已完成"];
$(document).ready(shop_obj.orders_init);
</script>
<script>
function CheckAll(form1){
	for(var i=0;i<form1.elements.length;i++){
		var e = form1.elements[i];
		if(e.name != 'chkall'){
			e.checked = form1.chkall.checked;
		}
	}
}
function SelectThis(index){
	if(typeof(form1.ID[index-1])=='undefined'){
		form1.ID.checked=!form1.ID.checked;
	}else{
		form1.ID[index-1].checked=!form1.ID[index-1].checked;
	}
}
</script>
    <div id="orders" class="r_con_wrap">
      <form class="search" id="search_form" method="get" action="?">
        <select name="Fields">
			<option value='Order_CartList'>商品</option>
			<option value='Address_Name'>购买人</option>
			<option value='Address_Mobile'>购买手机</option>
			<option value='Address_Detailed'>收货地址</option>
		</select>
        <input type="text" name="Keyword" value="" class="form_input" size="15" />
        订单状态：
        <select name="Status">
          <option value="">--请选择--</option>
          <option value='0'>待确认</option>
          <option value='1'>待付款</option>
          <option value='2'>已付款</option>
          <option value='3'>已发货</option>
          <option value='4'>已完成</option>
        </select>
        时间：
        <input type="text" class="input" name="AccTime_S" value="<?php echo !empty($_GET['AccTime_S']) ? $_GET['AccTime_S'] : "" ?>" id="AccTime_S" maxlength="20" />
        -
        <input type="text" class="input" name="AccTime_E" value="<?php echo !empty($_GET['AccTime_E']) ? $_GET['AccTime_E'] : "" ?>" id="AccTime_E" maxlength="20" />
		<input type="hidden" value="1" name="search" />
        <input type="submit" class="search_btn" value="搜索" />
        <input type="button" class="output_btn" value="导出" />
        <span id="import_btn" style="background:red;display:inline-block;text-align: center;border-radius: 4px;cursor: pointer;color: #fff;line-height: 22px;padding: 0 20px;"/>批量发货</span>
      </form>
	  <form name="form1" method="post" action="?">
      <table border="0" cellpadding="5" cellspacing="0" class="r_con_table" id="order_list">
        <thead>
          <tr>
			<td width="5%" nowrap="nowrap"><input name="chkall" type="checkbox" id="chkall" value="select" onClick="CheckAll(this.form)" style="border:0"></td>
            <td width="5%" nowrap="nowrap">序号</td>
            <td width="10%" nowrap="nowrap">订单号</td>
            <td width="5%" nowrap="nowrap">分销商</td>
            <td width="13%" nowrap="nowrap">姓名</td>
            <td width="12%" nowrap="nowrap">金额</td>
            <td width="9%" nowrap="nowrap">配送方式</td>
            <td width="9%" nowrap="nowrap">订单状态</td>
            <td width="12%" nowrap="nowrap">时间</td>
            <td width="10%" nowrap="nowrap" class="last">操作</td>
          </tr>
        </thead>
        <tbody>
          <?php
		  $i=0;
		  $DB->getPage("user_order","*",$condition,10);
$Order_Status=array("待确认","待付款","已付款","已发货","已完成");
/*获取订单列表牵扯到的分销商*/
while($rsOrder=$DB->fetch_assoc()){
$Shipping=json_decode($rsOrder["Order_Shipping"],true);
?>


          <tr class="<?php echo empty($rsOrder["Order_IsRead"])?"is_not_read":"" ?>" IsRead="<?php echo $rsOrder["Order_IsRead"] ?>" OrderId="<?php echo $rsOrder["Order_ID"] ?>">
			<td nowrap="nowrap"><input type="checkbox" name="ID" value="<?php echo $rsOrder["Order_ID"]; ?>" style="border:0" onClick="SelectThis(<?php echo $i+1;?>)"></td>
            <td nowrap="nowrap"><?php echo $i+1 ?></td>
         
            <td nowrap="nowrap"><?php echo date("Ymd",$rsOrder["Order_CreateTime"]).$rsOrder["Order_ID"] ?></td>
            <td nowrap="nowrap">
			<?php
          
			if($rsOrder["Owner_ID"] == 0 ){
				echo '无';
			}else{
				
				if(!empty($ds_list_dropdown[$rsOrder["Owner_ID"]])){
					echo $ds_list_dropdown[$rsOrder["Owner_ID"]];
				}else{
					echo '无昵称';
				}
				
				
			}	
			
			?></td>
            
            <td><?php echo $rsOrder["Address_Name"] ?></td>
            <td nowrap="nowrap">￥<?php echo $rsOrder["Order_TotalPrice"] ?></td> 
            <td nowrap="nowrap"><?php		
				if(empty($Shipping)){
					echo "免运费";
				}else{
					if(isset($Shipping["Express"])){
						echo $Shipping["Express"];
					}else{
						echo '无配送信息';
					}
				}
			?></td>
			
	
            <td nowrap="nowrap"><?php echo $Order_Status[$rsOrder["Order_Status"]] ?></td>
            <td nowrap="nowrap"><?php echo date("Y-m-d H:i:s",$rsOrder["Order_CreateTime"]) ?></td>
            <td class="last" nowrap="nowrap"><a href="<?php echo $rsOrder["Order_IsVirtual"]==1 ? 'virtual_' : '';?>orders_view.php?OrderID=<?php echo $rsOrder["Order_ID"] ?>"><img src="/static/member/images/ico/view.gif" align="absmiddle" alt="修改" /></a>
			<!--<a href="orders.php?action=del&OrderID=<?php echo $rsOrder["Order_ID"] ?>&page=<?php echo isset($_GET["page"]) ? $_GET["page"] : 1;?>" title="删除" onClick="if(!confirm('删除后不可恢复，并且会影响分销佣金计算,继续吗？')){return false};"><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a></td>-->
          </tr>
          <?php $i++;}?>
        </tbody>
      </table>
	  </form>
      <div class="blank20"></div>
      <?php $DB->showPage(); ?>
    </div>
  </div>
</div>

<div class="modal fade"  id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">导入EXCL数据</h4>
      </div>
      <div class="modal-body">
		<form method="post" action="import.php" enctype="multipart/form-data">
		  <div class="form-group">
			<label for="exampleInputFile">文件上传</label>
			<input type="file" id="exampleInputFile" name="importExcel">
			<p class="help-block">只接受Office Excel文件</p>
		  </div>
		  <button type="submit" class="btn btn-default">提交</button>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
<script>
var start = {
    elem: '#AccTime_S',
    format: 'YYYY-MM-DD hh:mm:ss',
    max: laydate.now(), //最大日期
    istime: true,
    istoday: false,
    choose: function(datas){
         end.min = datas; //开始日选好后，重置结束日的最小日期
         end.start = datas //将结束日的初始值设定为开始日
    }
};
var end = {
    elem: '#AccTime_E',
    format: 'YYYY-MM-DD hh:mm:ss',
    max: laydate.now(),
    istime: true,
    istoday: false,
    choose: function(datas){
        start.max = datas; //结束日选好后，重置开始日的最大日期
    }
};
laydate(start);
laydate(end);
$("#import_btn").click(function(){
	$("#myModal").modal('show');
});
</script>
</body>
</html>