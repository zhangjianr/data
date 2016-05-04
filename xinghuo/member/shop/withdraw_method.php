<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');

$base_url = base_url();

$_SERVER['HTTP_REFERER'] =  $base_url.'member/shop/distribute_record.php';
if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}

$action=empty($_REQUEST['action'])?'':$_REQUEST['action'];
if(!empty($action))
{	
   
  
	if($action =='add_method'){
		if($_POST["Method_Type"]  == 'bank_card'){
			 $Method_Name = trim($_POST['Method_Name']);
		}else if($_POST["Method_Type"]  == 'red'){
			 $Method_Name = '微信企业付款';
		}else{
			$Method_Name = '支付宝';
		}
		
		//如果已经存在这种提现方式，不可再添加
		$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Method_Name= '".$Method_Name."'";
		$rsMethod = $DB->getRs('shop_withdraw_method','*',$condition);
		
		if($rsMethod){
			echo '<script language="javascript">alert("添加失败，提现方式不能重名");history.back();</script>';
			exit();
		}
		
		$data = array(
			  "Users_ID"=>$_SESSION["Users_ID"],
			  "Method_Type"=>$_POST["Method_Type"],
			  "Method_Name"=>$Method_Name,
			  "Status"=>$_POST['Status'],
			  "Method_CreateTime"=>time(),
			  );
			  
		$DB->Add('shop_withdraw_method',$data);	  
		
	}
	
	
	
	if($action == 'edit_method'){
		
		if($_POST["Method_Type"]  == 'bank_card'){
			 $Method_Name = trim($_POST['Method_Name']);
		}elseif($_POST["Method_Type"]  == 'red'){
			$Method_Name = "微信企业付款";
		}else{
			$Method_Name = '支付宝';
		}
		
		
		$data = array(
			  "Users_ID"=>$_SESSION["Users_ID"],
			  "Method_Type"=>$_POST["Method_Type"],
			  "Method_Name"=>$Method_Name,
			  "Status"=>$_POST['Status'],
			  "Method_CreateTime"=>time(),
			  );
		if(isset($_POST['isAuto']) && $_POST["Method_Type"]  == 'red'){
			$data['isAuto'] = intval($_POST['isAuto']);
		}else{
			$data['isAuto'] = 0;
		}
		
		$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Method_ID=".$_POST["Method_ID"];				  
		$Flag = $DB->Set('shop_withdraw_method',$data,$condition);
		
		if($Flag)
		{
			echo '<script language="javascript">alert("编辑成功");window.location="withdraw_method.php";</script>';
		}else
		{
			echo '<script language="javascript">alert("编辑成功");history.back();</script>';
		}
		exit;	  
	}
	
	if($action=="del"){
		//删除分销记录
		$Flag=$DB->Del("shop_withdraw_method","Users_ID='".$_SESSION["Users_ID"]."' and Method_ID=".$_GET["Method_ID"]);
	
		if($Flag)
		{
			echo '<script language="javascript">alert("删除成功");window.location="withdraw_method.php";</script>';
		}else
		{
			echo '<script language="javascript">alert("删除失败");history.back();</script>';
		}
		exit;
	}
}

$condition = "where Users_ID='".$_SESSION["Users_ID"]."'";
if(isset($_GET["search"])){
	if($_GET["search"]==1){
		if(!empty($_GET["Keyword"])){
			$condition .= " and (Method_Name like '%".$_GET["Keyword"]."%' )";
		}
	
	}
}

$condition .= " order by Method_CreateTime";

$rsMethods = $DB->getPage("shop_withdraw_method","*",$condition,$pageSize=10);
$method_list = $DB->toArray($rsMethods);


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
<script type="text/javascript">
	var base_url = '<?=$base_url?>';	
</script>

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
        <li class=""><a href="withdraw_record.php">提现记录</a></li>
        <li class=""><a href="distribute_title.php">爵位设置</a></li>
		<li class=""><a href="channel_config.php">渠道设置</a></li>
        <li class="cur"><a href="withdraw_method.php">提现方法管理</a></li>
      </ul>
    </div>
    <link href='/static/js/plugin/lean-modal/style.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/js/plugin/lean-modal/lean-modal.min.js'></script> 
     <script type='text/javascript' src='/static/js/inputFormat.js'></script>
    <script language="javascript">
	$(document).ready(function(){shop_obj.withdraw_method_init();});
	
</script>
    <div id="update_post_tips"></div>
    <div id="user" class="r_con_wrap">
      <div class="control_btn">
      <a href="javascript:void(0)" id="create_method_btn" class="btn_green btn_w_120">添加</a> 
    
      </div>
      
      <table width="100%" align="center" border="0" cellpadding="5" cellspacing="0" class="r_con_table">
        <thead>
          <tr>
            <td width="5%" nowrap="nowrap">序号</td>
            <td width="8%" nowrap="nowrap">类型</td>
            <td width="10%" nowrap="nowrap">银行名</td>
            <td width="10%" nowrap="nowrap">方式</td>
             <td width="10%" nowrap="nowrap">状态</td>
            <td width="10%" nowrap="nowrap">添加时间</td>
            <td width="8%" nowrap="nowrap" class="last"><strong>操作</strong></td>
          </tr>
        </thead>
        <tbody>
      
		  
	<?php foreach($method_list as $key=>$rsMethod):?>
           <tr UserID="<?php echo $rsMethod['Method_ID'] ?>">
            <td><?=$rsMethod['Method_ID']?></td>
            <td>
            	<?php if($rsMethod['Method_Type'] =='bank_card'): ?>
                	银行卡
				<?php elseif($rsMethod['Method_Type'] =='red'): ?>
					微信企业付款
				<?php else: ?>
                   支付宝
                <?php endif;?>
            
            </td>
            <td><?=$rsMethod['Method_Name']?></td>
            <td>
				<?php if($rsMethod['isAuto'] =='0'): ?>
                	人工审核
				<?php else: ?>
                   自动付款
                <?php endif;?>
			</td>
            <td><?php if($rsMethod['Status'] ==1 ){ echo '启用';}else{echo '禁用';}?></td>
            <td nowrap="nowrap"><?php echo date("Y-m-d H:i:s",$rsMethod['Method_CreateTime']) ?></td>
            
            <td nowrap="nowrap" class="last">
            <a href="javascript:void(0)" method-id="<?=$rsMethod['Method_ID']?>" class="method_edit_btn"><img src="/static/member/images/ico/mod.gif" alt="修改" align="absmiddle"></a>
            <a href="withdraw_method.php?action=del&Method_ID=<?php echo $rsMethod['Method_ID'] ?>" onClick="if(!confirm('删除后不可恢复，继续吗？')){return false};"><img src="/static/member/images/ico/del.gif" align="absmiddle" /></a></td>
          </tr>
      <?php endforeach; ?>
        </tbody>
      </table>
      <div class="blank20"></div>
      <?php $DB->showPage(); ?>
    </div>
  </div>
  
  <div id="mod_create_method" class="lean-modal lean-modal-form">
  		 <div class="h">添加提现方式<span></span><a class="modal_close" href="#"></a></div>
         <form class="form" action="withdraw_method.php" method="post" id="create_method_form" name="mod_create_method">
         
         <div class="rows">
          <label>类型</label>
          <span class="input">  <input value="bank_card" name="Method_Type" type="radio" checked>&nbsp;&nbsp;银行卡
           <input value="red" name="Method_Type" type="radio">&nbsp;&nbsp; 微信企业付款
           <input value="alipay" name="Method_Type" type="radio">&nbsp;&nbsp; 支付宝
</span>
          <div class="clear"></div>
        </div>
          
        <div class="rows method_name_rows" >
        	<label>名称</label>
            <span class="input"><input type="text" value="" class="form_input" name="Method_Name"  notnull /></span>
            <div class="clear"></div>
        </div>
		<div class="rows method_type_rows" style="display:none">
			<label>方式</label>
			<span class="input">
				<input value="0" name="isAuto" type="radio" {{if $Method.isAuto eq '0'}}checked{{/if}} checked>&nbsp;&nbsp;审核
				<input value="1" name="isAuto" type="radio" {{if $Method.isAuto eq '1'}}checked{{/if}}>&nbsp;&nbsp; 自动
			</span>
			<div class="clear"></div>
		</div>
		
         <div class="rows">
          <label>状态</label>
          <span class="input">  <input value="1" name="Status" type="radio" checked>&nbsp;&nbsp;可用
           <input value="0" name="Status" type="radio">&nbsp;&nbsp; 不可用
</span>
          <div class="clear"></div>
        </div>
           
       <div class="rows">
        <label></label>
        <span class="submit">
        <input type="submit" value="确定提交" name="submit_btn">
        </span>
        <div class="clear"></div>
      </div>
      <input type="hidden" name="action" value="add_method">  
         </form>
  </div>
  
  <div id="mod_edit_method" class="lean-modal lean-modal-form">
    <div class="h">修改提现方式<span></span><a class="modal_close" href="#"></a></div>
    <div id="method_edit_content">
    	<form class="form">
      <div class="rows">
        <label>银行卡号：</label>
        <span class="input">
        <input name="Bank_Card" id="Bank_Card" value="" type="text" class="form_input" size="26" maxlength="25" notnull>
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
      <input type="hidden" name="UserID" value="">
      <input type="hidden" name="action" value="mod_bankcard">
    </form>
    </div>
    <div class="tips"></div>
  </div>
  
  
</div>
</div>
</body>
</html>