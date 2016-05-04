<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');

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

if(!empty($_SESSION[$UsersID."User_ID"])){
  $userexit = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."'");
  if(!$userexit){
    $_SESSION[$UsersID."User_ID"] = "";
  } 
}

if(empty($_SESSION[$UsersID."User_ID"]))
{
  $_SESSION[$UsersID."HTTP_REFERER"]="/api/".$UsersID."/shop/distribute/";
  header("location:/api/".$UsersID."/user/login/");
}

$today = strtotime('today');
$now = strtotime('now');
$before_oneweek = strtotime('-1 week');
$before_onemonth = strtotime('-1 month');

//计算此用户今天的分销收入


$condition = "where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."' and Record_Type=0 and Record_Status = 1";
$condition .= " and Record_CreateTime >".$today." and Record_CreateTime <".$now;

$day = $DB->getRs('shop_distribute_account_record','sum(Record_Money) as sum',$condition);

$day_sum = ($day['sum'] != null)?$day['sum']:0;

//计算此用户的累计收入
$condition = "where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."' and Record_Type=0 and Record_Status = 1";
$all = $DB->GetRs('shop_distribute_account_record','sum(Record_Money) as sum',$condition);
$all_sum = ($all['sum'] != null)?$all['sum']:0;

//计算此用户一周内的分销收入
$condition = "where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."' and Record_Type=0 and Record_Status = 1";
$condition .= " and Record_CreateTime >".$before_oneweek." and Record_CreateTime <".$now;

$week = $DB->GetRs('shop_distribute_account_record','sum(Record_Money) as sum',$condition);
$week_sum = ($week['sum'] != null)?$week['sum']:0;

//计算此用户一月内的分销收入
$condition = "where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."' and Record_Type=0 and Record_Status = 1";
$condition .= " and Record_CreateTime >".$before_onemonth." and Record_CreateTime <".$now;

$month = $DB->GetRs('shop_distribute_account_record','sum(Record_Money) as sum',$condition);
$month_sum = ($month['sum'] != null)?$month['sum']:0;

//获取此用户分销账号信息
$rsAccount  = $DB->GetRs("shop_distribute_account","*","where Users_ID='".$UsersID."' and User_ID= '".$_SESSION[$UsersID.'User_ID']."'");

//获取此用户的不可用余额(即已申请提现，单未执行提现的现金金额)
$condition ="where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."' and Record_Type=1 and Record_Status = 0";

$withdraw_records = $DB->getRs("shop_distribute_account_record","sum(Record_Money) as useless_sum",$condition);
$useless_sum =  !empty($withdraw_records['useless_sum'])?$withdraw_records['useless_sum']:0;

?>

<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>我的团队</title>
 <link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
    <link href="/static/api/distribute/css/style.css" rel="stylesheet">
     <link href="/static/api/distribute/css/income.css" rel="stylesheet">
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

	
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>

<body>
<div class="wrap">
	<div class="container">
    <h4 class="row page-title">我的收入</h4>
    </div>
    
  
    <ul id="distribute_group">
   		 <li class="item"><a href="/api/<?=$UsersID?>/shop/distribute/group/?wxref=mp.weixin.qq.com">我的团队</a></li>
   		<li class="item"><a href="/api/<?=$UsersID?>/shop/distribute/my_distribute/?wxref=mp.weixin.qq.com">我的推广</a></li>
   		<li class="item cur"><a href="/api/<?=$UsersID?>/shop/distribute/income/?wxref=mp.weixin.qq.com">分销佣金</a></li>
  		<li class="clearfix"></li>
  	</ul>

  
  	<div id="income_list">
    	 <div class="income_item">
         	 <p>本周收入</p>
             <h5>&yen;&nbsp;&nbsp;<?=$week_sum?></h5>
         </div>
         
         <div class="income_item">
         	 <p>本月收入</p>
             <h5>&yen;&nbsp;&nbsp;<?=$month_sum?></h5>
         </div>
         
         <div class="income_item">
         	 <p>累计收入</p>
             <h5>&yen;&nbsp;&nbsp;<?=$all_sum ?></h5>
         </div>
         
         <div class="income_item">
         	 <p>实际总收入</p>
             <h5>&yen;&nbsp;&nbsp;<?=$rsAccount['Total_Income']?></h5>
         </div>
         
         <div class="clearfix">
         </div>
    </div>
  
  
  	<div class="container">
    	<div class="row" id="withdraw_panel">
        	<div class="col-xs-5">
            	<p>
                	可提现金额:<br/>
                    <font style="color:red;font-weight:bold;">&yen;&nbsp;&nbsp;<?=$rsAccount['balance']?></font>
                </p>
            </div>
            
            <div class="col-xs-5" style="text-align:center;">
            	<button class="btn btn-info btn-sm">提现</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>

