<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');

$base_url = base_url();
$shop_url = shop_url();

/*分享页面初始化配置*/
$share_flag = 1;
$signature = '';

if(isset($_GET["UsersID"])){
  $UsersID = $_GET["UsersID"];
}else{
  echo '缺少必要的参数';
  exit;
}

$rsConfig = $DB->GetRs("shop_config", "*", "where Users_ID='" . $UsersID . "'");

$is_login = 1;
$owner = getOwner($DB, $UsersID);
require_once $_SERVER["DOCUMENT_ROOT"] . '/include/library/wechatuser.php';
$owner = getOwner($DB, $UsersID);

if ($owner['id'] != '0') {
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	
}

$rsUser = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]);

//获取此用户分销账号信息
$rsAccount  = $DB->GetRs("shop_distribute_account","*","where Users_ID='".$UsersID."' and User_ID= '".$_SESSION[$UsersID.'User_ID']."'");
if(!$rsAccount){
	echo "你还不是分销商";
	exit;
}

//提现方式列表
$rsUserMethods = $DB->Get("shop_user_withdraw_methods","*","where Users_ID='".$UsersID."' and User_ID= '".$_SESSION[$UsersID.'User_ID']."'");
$user_method_list = $DB->toArray($rsUserMethods);

//获取帮助此用户的记录
$condition = "where Users_ID='".$UsersID."' and User_ID= '".$_SESSION[$UsersID.'User_ID']."'";

$rsRecord = $DB->Get('shop_distribute_account_record','*',$condition);
$Records = $DB->toArray($rsRecord);
//统计
$self_distribute_count = 0;
$posterity_distribute_count = 0;
$withdraw_count = 0;

foreach($Records as $key=>$record){
	if($record['Record_Type'] == 0){
		if($record['User_ID'] == $record['Owner_ID'] ){
			$self_distribute_count++;
			$self_record[] = $record;
		}else{
			$posterity_distribute_count++;
			$posterity_record[] = $record;
		}
	}else{
		$withdraw_count++;
		$withdraw_record[] = $record;
	}
}

if(!empty($_GET['filter'])){
	
	if($_GET['filter'] == 'self'){
		$filter_record = $self_record;
	}elseif($_GET['filter'] == 'down'){
		$filter_record =$posterity_record;
	}elseif($_GET['filter'] == 'withdraw'){
		$filter_record =$withdraw_record;
	}
	
}else{
	$filter_record = $Records; 
}


//获取此用户可用的提现方式
$condition = "where Users_ID= '".$UsersID."' and Status = 1 order by Method_ID";
$rsMethods = $DB->Get('shop_withdraw_method','*',$condition);
$enabled_method_list = $DB->toArray($rsMethods);

if($rsAccount["Enable_Tixian"]==0 && $rsConfig["Withdraw_Type"]==3){
	if($rsAccount["balance"]>=$rsConfig["Withdraw_Limit"]){
		$ff = $DB->Set("shop_distribute_account",array("Enable_Tixian"=>1),"where Users_ID='".$UsersID."' and Account_ID=".$rsAccount["Account_ID"]);
		if($ff){
			$rsAccount["Enable_Tixian"] = 1;
		}
	}
}

$withdraw_msg = get_distribute_withdraw($DB,$UsersID,$rsAccount["Enable_Tixian"],$rsConfig["Withdraw_Type"],$rsConfig["Withdraw_Limit"],$shop_url,'#F00',0);
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>用户提现</title>
 <link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
    <link href="/static/api/distribute/css/style.css" rel="stylesheet">
     <link href="/static/api/distribute/css/withdraw.css" rel="stylesheet">
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/static/js/jquery-1.11.1.min.js"></script>
    <script type='text/javascript' src='/static/js/jquery.validate.js'></script>
    <script src="/static/js/bankInput.js"></script>
    <script type='text/javascript' src='/static/api/js/global.js'></script>
    <script src="/static/api/distribute/js/distribute.js"></script>
     <script language="javascript">
	jQuery.extend(jQuery.validator.messages, {  
       	 	required: "必须填写",  
			email: "请输入正确格式的电子邮件",  
			url: "请输入合法的网址",  
			date: "请输入合法的日期",  
			dateISO: "请输入合法的日期 (ISO).",  
			number: "请输入合法的数字",  
			digits: "只能输入整数",  
			creditcard: "请输入合法的信用卡号",  
			equalTo: "请再次输入相同的值",  
			accept: "请输入拥有合法后缀名的字符串",  
			maxlength: jQuery.validator.format("请输入一个长度最多是 {0} 的字符串"),  
			minlength: jQuery.validator.format("请输入一个长度最少是 {0} 的字符串"),  
			rangelength: jQuery.validator.format("请输入一个长度介于 {0} 和 {1} 之间的字符串"),  
			range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),  
			max: jQuery.validator.format("请输入一个最大为 {0} 的值"),  
			min: jQuery.validator.format("请输入一个最小为 {0} 的值")  
	});  

	var base_url = '<?=$base_url?>';
	var UsersID = '<?=$UsersID?>';
	var withdraw_limit = '<?=$rsConfig["Withdraw_PerLimit"]?>';
	$(document).ready(distribute_obj.init);
	$(document).ready(distribute_obj.withdraw_page);

</script>


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>

<body>
<header class="bar bar-nav">
  <a href="javascript:history.back()" class="fa fa-2x fa-chevron-left grey pull-left"></a>
  <a href="/api/<?=$UsersID?>/shop/distribute/" class="fa fa-2x fa-sitemap grey pull-right"></a>
  <h1 class="title">申请提现</h1>
  
</header>

<div class="wrap">
	<div class="container">
    	<div class="row page-title">
           <h4>&nbsp;&nbsp;&nbsp;&nbsp;可提现余额&nbsp;&nbsp;&nbsp;&nbsp;<span class="red">&yen;<?=$rsAccount['balance']?></span> </h4>  
         	
        </div>
        
		
        <div class="row">
    <form action="/api/<?=$UsersID?>/shop/distribute/ajax/" id="withdraw-form"/>     
        	<ul class="list-group withdraw-panel">
     
     <input type="hidden" id="balance" value="<?=$rsAccount['balance']?>"/>
  	 <input type="hidden" name="action" value="withdraw_appy"/>
  	 
  <li class="list-group-item">
     申请提现后店主会手动将钱打入您的账号
  </li>         
  <li class="list-group-item">
  	 <label>提现额</label>&nbsp;&nbsp;<input name="money" id="withdraw-money" notnull type="text" placeholder="提现的金额" />
  </li>
  
  <li class="list-group-item">
  
  	  <label>账&nbsp;&nbsp;号</label> &nbsp;&nbsp;
     <?php if(count($user_method_list) >0 ):?>
     <select id="user_method_id"  name="User_Method_ID">
      	<?php foreach($user_method_list as $key=>$item):?>
        	<option value="<?=$item['User_Method_ID']?>"><?=$item['Method_Name']?>&nbsp;&nbsp;<?=$item['Account_Val']?></option>        
		<?php endforeach;?>
      </select>
      
     <?php else:?>
     	没有提现方法
     <?php endif;?>
     
    </li>
	<?php if($rsAccount["status"]==0){?>
    <li class="list-group-item text-center">
    	<span class="red">您的分销账号已被禁用，不可提现</span>
    </li>
    <?php }elseif($withdraw_msg){?>
    <li class="list-group-item text-center">
    	<strong><?php echo $withdraw_msg;?>，即可提现</strong>
    </li>
    <?php }elseif(count($user_method_list) >0){ ?>
    <li class="list-group-item text-center">
    	<a href="javascript:void(0)" id="btn-withdraw" class="submit-btn btn btn-default btn-disable">申请提现</a>
    </li>
    <?php }?>
</ul>
	
    </form>
    
    <?php if(count($user_method_list) >0 ):?>
  
    <ul class="list-group withdraw-panel" id="add_card_panel">
   
   		<form action="/api/<?=$UsersID?>/shop/distribute/ajax/" id="bank_card_form" >
            <li class="list-group-item">添加提现方法后才可提现</li>    
            
             <input type="hidden" name="action" value="add_user_withdraw_method"/>
  			 <input type="hidden" name="Method_Type" id="Method_Type" value="<?=$enabled_method_list[0]['Method_Type']?>"/>
 <li class="list-group-item">
  
  	<label>提现方式</label>&nbsp;&nbsp;<select name="Method_Name" id="User_Method_Name">
    	<?php foreach($enabled_method_list as $key=>$item): ?>
        	<option vlaue="<?=$item['Method_Name']?>" method_type="<?=$item['Method_Type']?>"><?=$item['Method_Name']?></option> 
        <?php endforeach; ?>
    </select>
   
  </li>
  
  <li class="list-group-item">
  	 <label id="Account_Name_Txt">开户&nbsp;&nbsp;名</label>
    &nbsp;&nbsp;<input type="text" name="Account_Name" placeholder="请输入您的真实开户名！" /> 
    
    
 </li>
 
   
 
  <?php if($enabled_method_list[0]['Method_Type'] == 'bank_card'):?>
    <li class="list-group-item Bank_Card_Txt">
  	  <label id="Bank_Card_Txt">卡&nbsp;&nbsp;号</label>&nbsp;&nbsp;<input type="text" name="Account_Val" placeholder="请输入您的卡号" />	
  	</li>
    
  	<li class="list-group-item  bank_card_info">
  	  <label>开户行</label>&nbsp;&nbsp;<input type="text" name="Bank_Position" placeholder="请输入您的开户行" />	
  	</li>  
	<?php elseif($enabled_method_list[0]['Method_Type'] == 'red'):?>
	
    <?php else: ?>
    
    <li class="list-group-item Bank_Card_Txt">
  	  <label id="Bank_Card_Txt">账&nbsp;&nbsp;户</label>&nbsp;&nbsp;<input type="text" name="Account_Val" placeholder="请输入您的账户" />	
  	</li>

    <li class="list-group-item bank_card_info" style="display:none;"  disabled="true">
  	  <label>开户行</label>&nbsp;&nbsp;<input type="text" name="Bank_Position" placeholder="请输入您的开户行" />	
  	</li>
  
  	
    
 <?php endif; ?> 
  <li class="list-group-item text-center">
  		 <a href="javascript:void(0)" id="btn-addcard" class="btn btn-default submit-btn">添加</a>
  </li>
   </form>
   
	</ul>
    
         <ul class="list-group withdraw-panel">
			<li class="list-group-item"> <a class="btn btn-default" id="add-card"><span class="fa fa-plus"></span>&nbsp;&nbsp;添加新的提现方法</a>	</li>
            <li class="list-group-item"> <a class="btn btn-default" href="/api/<?=$UsersID?>/shop/distribute/bankcards/" id="manage-card"><span class="fa fa-credit-card"></span>&nbsp;&nbsp;管理我的提现方法</a>	</li>
	     </ul>  
         
	<?php else: ?>
		  <ul class="list-group withdraw-panel">
   		<?php if(!empty($enabled_method_list)):?>
      
   			<form action="/api/<?=$UsersID?>/shop/distribute/ajax/" id="bank_card_form" >
            <li class="list-group-item">添加提现方法后才可提现</li>    
            
             <input type="hidden" name="action" value="add_user_withdraw_method"/>
  			 <input type="hidden" name="Method_Type" id="Method_Type" value="<?=$enabled_method_list[0]['Method_Type']?>"/>
 <li class="list-group-item">
  
  	<label>提现方式</label>&nbsp;&nbsp;<select name="Method_Name" id="User_Method_Name" automatically="off">
    	<?php foreach($enabled_method_list as $key=>$item): ?>
        	<option vlaue="<?=$item['Method_Name']?>" method_type="<?=$item['Method_Type']?>"><?=$item['Method_Name']?></option> 
        <?php endforeach; ?>
    </select>
   
  </li>
  
	  <li class="list-group-item">
		 <label id="Account_Name_Txt">真实姓名</label>
		&nbsp;&nbsp;<input type="text" name="Account_Name" placeholder="请输入您的真实姓名！" />
	 </li>
 
   
     <li class="list-group-item Bank_Card_Txt bank_card_infos" style="display:none;"  disabled="true">
  	  <label id="Bank_Card_Txt">账&nbsp;&nbsp;户</label>&nbsp;&nbsp;<input type="text" name="Account_Val" placeholder="请输入您的账户" />	
  	</li>
  
  <?php if($enabled_method_list[0]['Method_Type'] == 'bank_card'):?>
    <li class="list-group-item Bank_Card_Txt">
  	  <label id="Bank_Card_Txt">卡&nbsp;&nbsp;号</label>&nbsp;&nbsp;<input type="text" name="Account_Val" placeholder="请输入您的卡号" />	
  	</li>
    
  	<li class="list-group-item  bank_card_info">
  	  <label>开户行</label>&nbsp;&nbsp;<input type="text" name="Bank_Position" placeholder="请输入您的开户行" />	
  	</li>  
   
    
	<?php else: ?>
    
    

    <li class="list-group-item bank_card_info" style="display:none;"  disabled="true">
  	  <label>开户行</label>&nbsp;&nbsp;<input type="text" name="Bank_Position" placeholder="请输入您的开户行" />	
  	</li>
  
  	
    
 <?php endif; ?> 
  <li class="list-group-item text-center">
  		 <a href="javascript:void(0)" id="btn-addcard" class="btn btn-default submit-btn">添加</a>
  </li>
   </form>
		
		<?php else: ?>
         
          <li class="list-group-item red">管理员尚未设置可用提现方式</li>
        <?php endif; ?>	
        </ul>

   <?php endif;?>   
        
    
        
        </div>
        
        <div class="row">
        	<ul id="distribute-brief-info">
         <li class="item"><a href="<?=$shop_url?>distribute/detaillist/self/"><span class="red bold">&nbsp;<?=$self_distribute_count?></span><br/>自销</a></li>
         <li class="item"><a href="<?=$shop_url?>distribute/detaillist/down/"><span class="red bold">&nbsp;<?=$posterity_distribute_count?></span><br/>下级分销</a></li>
         <li class="item"><a href="<?=$shop_url?>distribute/withdraw_record/"><span class="red bold">&nbsp;<?=$withdraw_count?></span><br/>提现次数</a></li>
         <li class="clearfix"></li>
      </ul>
        
      	
    
        </div>

    </div>
    
  	
  
    
</div>

 
<?php require_once('../distribute_footer.php');?> 
 <script>
 $("#User_Method_Name").change(function(){
			 
		   var method_type = $(this).find("option:selected").attr("method_type");
		  
		   $("#Method_Type").attr('value',method_type);
		   if(method_type == 'alipay'){
			  
			   
			  $(".bank_card_infos").css("display",'block').removeAttr('disabled');
			  
		   }
		   else if(method_type == 'bank_card'){
			  $("#Bank_Card_Txt").html('卡&nbsp;&nbsp;号');
			  $(".bank_card_info").css("display",'block').removeAttr('disabled');
		   }else if(method_type == 'red'){
			   $(".bank_card_info").css("display",'none').attr('disabled','true');
		   }
		});
 
 </script>
 
</body>
</html>
