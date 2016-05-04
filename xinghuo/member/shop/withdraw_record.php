<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
$base_url = base_url();
//ini_set("display_errors","On");
$_SERVER['HTTP_REFERER'] =  $base_url.'member/shop/withdraw_record.php';
if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}

$action=empty($_GET['action'])?'':$_GET['action'];

if(isset($action))
{	

	if($action=="del"){
		//1删除分销记录
		$Flag=$DB->Del("shop_distribute_account_record","Users_ID='".$_SESSION["Users_ID"]."' and Record_ID=".$_GET["RecordID"]);
	
		if($Flag)
		{
			echo '<script language="javascript">alert("删除成功");window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';
		}else
		{
			echo '<script language="javascript">alert("删除失败");history.back();</script>';
		}
		exit;
	}elseif($action == "fullfill"){
	
		$data = array("Record_Status"=>1);
		$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Record_ID='".$_GET['RecordID']."'";
		$Flag = $DB->set("shop_distribute_account_record",$data,$condition);
		
		if($Flag)
		{
			echo '<script language="javascript">alert("更新成功");window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';
		}else
		{
			echo '<script language="javascript">alert("更新失败");history.back();</script>';
		}
		exit;
		
	}elseif($action == "weixinpay"){
		$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Record_ID='".$_GET['RecordID']."'";
		$Flag = $DB->GetRs("shop_distribute_account_record","*",$condition);
		if($Flag['Record_Status'] == 1){
			echo '<script language="javascript">alert("付款失败，订单已经付款，请勿重复确认");history.back();</script>';
			exit;
		}
		if($Flag && !empty($Flag['realname']) && !empty($Flag['openid']) && $Flag['Method_Type'] == "red")
		{
			require_once($_SERVER["DOCUMENT_ROOT"] . '/include/library/weixin_pay.class.php');
			$pay = new weixin_pay($Flag);
			$payResult = $pay->startPay();
			if($payResult === true) {
				$data = array("Record_Status"=>1);
				$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Record_ID='".$_GET['RecordID']."'";
				$FlagUpdata = $DB->set("shop_distribute_account_record",$data,$condition);
				echo '<script language="javascript">alert("更新成功");window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';
			}else{
				
				echo '<script language="javascript">alert("付款失败，微信返回的原因：【'.$payResult['return_msg'].'】");window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';
			}
		}else
		{
			echo '<script language="javascript">alert("付款失败，要支付的订单不存在或用户资料不全，微信支付仅限升级以后提交的提现申请");history.back();</script>';
		}
		exit;
	}elseif($action == "reject"){
		
		//获取此次提现记录内容
	
		$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Record_ID='".$_GET['RecordID']."'";
		$rsRecord = $DB->getRs("shop_distribute_account_record","Record_Money,User_ID",$condition);
	
		$data = array("Record_Status"=>2);
		$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Record_ID='".$_GET['RecordID']."'";
		$Flag = $DB->set("shop_distribute_account_record",$data,$condition);
		
		//将钱退回
		$money = $rsRecord['Record_Money'];
		
		$condition = "where User_ID=".$rsRecord['User_ID']." and Users_ID='".$_SESSION["Users_ID"]."'";
	    $DB->set('shop_distribute_account',"balance=balance+$money",$condition);
		
		if($Flag)
		{
			unset($_Get);
			echo '<script language="javascript">window.location="withdraw_record.php";</script>';
		}else
		{
			echo '<script language="javascript">alert("更新失败");history.back();</script>';
		}
		exit;
	}
}


$condition = "where Users_ID='".$_SESSION["Users_ID"]."'";

if(isset($_GET["search"])){

	if($_GET["search"]==1){
	
    if(!empty($_GET["Keyword"])){
      $condition .= " and Record_Sn like '%".$_GET["Keyword"]."%'";
    }
    if(isset($_GET["Status"])){
      if($_GET["Status"]<>''){
        $condition .= " and Record_Status=".$_GET["Status"];
      }
    }
    if(!empty($_GET["AccTime_S"])){
      $condition .= " and Record_CreateTime>=".strtotime($_GET["AccTime_S"]);
    }
    if(!empty($_GET["AccTime_E"])){
      $condition .= " and Record_CreateTime<=".strtotime($_GET["AccTime_E"]);
    }
	
	}
}


$condition .= " and Record_Type = 1";
$condition .= " order by Record_CreateTime desc";

$rsRecords = $DB->getPage("shop_distribute_account_record","*",$condition,$pageSize=10);
$record_list = $DB->toArray($rsRecords);

$user_array = array();
$product_array = array();
$bank_array = array();

//获取用户列表
$condition = "where Users_ID='".$_SESSION["Users_ID"]."'"; 
if(count($user_array)>0){
	$condition .= "and User_ID in (".implode(',',$user_array).")";
}

$rsUsers = $DB->get('user','User_ID,User_NickName',$condition);
$User_list = $DB->toArray($rsUsers );

//获取商品列表
$condition = "where Users_ID='".$_SESSION["Users_ID"]."'";

if(count($user_array)>0){
	$condition .= "and Products_ID in (".implode(',',$product_array).")";
}

$rsProducts = $DB->get('shop_products',"Products_ID,Products_Name",$condition);
$Product_list = $DB->toArray($rsProducts );

$user_dropdown = get_dropdown_list($User_list,'User_ID','User_NickName');
$product_dropdown = get_dropdown_list($Product_list,'Products_ID','Products_Name');


$status = array("申请中","已执行","已驳回");

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
<style type="text/css">
body, html{background:url(/static/member/images/main/main-bg.jpg) left top fixed no-repeat;}
</style>
<div id="iframe_page">
  <div class="iframe_content">
    <link href='/static/member/css/user.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/member/js/shop.js'></script>
    <div class="r_nav">
      <ul>
        <li > <a href="distributes.php">分销账号管理</a> </li>
        <li ><a href="distribute_record.php">分销记录</a></li>
        <li class="cur"><a href="withdraw_record.php">提现记录</a></li>
        <li class=""><a href="distribute_title.php">爵位设置</a></li>
        <li class=""><a href="channel_config.php">渠道设置</a></li>
        <li class=""><a href="withdraw_method.php">提现方法管理</a></li>
      </ul>
    </div>
    <link href='/static/js/plugin/lean-modal/style.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/js/plugin/lean-modal/lean-modal.min.js'></script> 
     <script type='text/javascript' src='/static/js/inputFormat.js'></script>
    <link href='/static/js/plugin/operamasks/operamasks-ui.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/js/plugin/operamasks/operamasks-ui.min.js'></script> 
    <script language="javascript">
	$(document).ready(function(){shop_obj.distribute_init();});
	
</script>
    <div id="update_post_tips"></div>
    <div id="user" class="r_con_wrap">
      
      <form class="search" id="search_form" method="get" action="?">
        关键词：
        <input name="Keyword" value="" class="form_input" size="15" type="text">
        记录状态：
        <select name="Status">
          <option value="">--请选择--</option>
          <option value="0">申请中</option>
          <option value="1">已执行</option>
          <option value="2">已驳回</option>
        </select>
        时间：
         <input type="text" class="input" name="AccTime_S" value="" maxlength="20" />
        -
        <input type="text" class="input" name="AccTime_E" value="" maxlength="20" />
		<input value="1" name="search" type="hidden">
        <input class="search_btn" value="搜索" type="submit">
      </form>
      <table width="100%" align="center" border="0" cellpadding="5" cellspacing="0" class="r_con_table">
        <thead>
          <tr>
            <td width="5%" nowrap="nowrap">序号</td>
            <td width="8%" nowrap="nowrap">用户</td>
            <td width="5%" nowrap="nowrap">流水号</td>
            <td width="15%" nowrap="nowrap">提现账户</td>
            <td width="8%" nowrap="nowrap">金额</td>
            
      	   <td width="8%" nowrap="nowrap">状态</td>
            <td width="10%" nowrap="nowrap">时间</td>
            <td width="8%" nowrap="nowrap" class="last"><strong>操作</strong></td>
          </tr>
        </thead>
        <tbody>
      
		  
	<?php foreach($record_list as $key=>$rsRecord):?>
           <tr Record_ID="<?php echo $rsRecord['Record_ID'] ?>">
          	<td nowarp="nowrap"><?=$rsRecord['Record_ID']?></td>
            <td nowarp="nowrap" field=1>
            <?=!empty($user_dropdown[$rsRecord['User_ID']])?$user_dropdown[$rsRecord['User_ID']]:'用户已被删除';?>
            </td>
            <td nowarp="nowrap"><?=$rsRecord['Record_Sn']?></td>
             <td nowarp="nowrap"><?=$rsRecord['Account_Info']?></td>
            <td nowarp="nowrap">&yen;<?=$rsRecord['Record_Money']?></td>           
            <td nowrap="nowrap"><?=$status[$rsRecord['Record_Status']]?></td>
            <td nowrap="nowrap"><?php echo date("Y-m-d H:i:s",$rsRecord['Record_CreateTime']) ?></td>
            <td nowrap="nowrap" class="last">
            
            <?php if($rsRecord['Record_Status'] == 0):?>
			
				<?php if($rsRecord['Method_Type'] == "red"):?>
				<a href="<?=$base_url?>member/shop/withdraw_record.php?action=weixinpay&RecordID=<?=$rsRecord['Record_ID']?>" onClick="if(!confirm('确认完成之后无法撤回，继续吗？')){return false};">微信企业付款</a> |
				<?php else:?>
				<a href="<?=$base_url?>member/shop/withdraw_record.php?action=fullfill&RecordID=<?=$rsRecord['Record_ID']?>" onClick="if(!confirm('确认完成吗？')){return false};">线下已付款</a> |
				<?php endif;?>
			
            <a id="reject_btn" href="javascript:void(0)">驳回</a>
			<?php endif; ?>
			<a href="withdraw_record.php?action=del&RecordID=<?php echo $rsRecord['Record_ID'] ?>" onClick="if(!confirm('删除后不可恢复，继续吗？')){return false};"><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a>
            
            
            
            
            </td>
          </tr>
      <?php endforeach; ?>
        </tbody>
      </table>
      <div class="blank20"></div>
      <?php $DB->showPage(); ?>
    </div>
  </div>
  
  <div id="reject_withdraw" class="lean-modal lean-modal-form">
    <div class="h">驳回用户提现理由<a class="modal_close" href="#"></a></div>
    <form class="form">
      <div class="rows">
        <label>驳回理由：</label>
        <span class="input">
        <textarea name="Reject_Reason" id="Reject_Reason" notnull ></textarea>
        <font class="fc_red">*</font></span>
        <div class="clear"></div>
      </div>
      <div class="rows">
        <label></label>
        <span class="submit">
        <input type="submit" value="确定提交" name="submit_btn">
        </span>
        <div class="clear"></div>
      </div>
      <input type="hidden" name="Record_ID" value="" >
      <input type="hidden" name="action" value="reject_withdraw">
    </form>
    <div class="tips"></div>
  </div>
  
</div>
</div>
</body>
</html>