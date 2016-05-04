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

$Status=empty($_GET["Status"])?0:$_GET["Status"];
if(!strpos($_SERVER['REQUEST_URI'],"mp.weixin.qq.com")){
  header("location:?wxref=mp.weixin.qq.com");
}
$rsConfig =$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$rsUser = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]);


//获取此用户分销账号信息
$rsAccount  = $DB->getRs("shop_distribute_account","*","where Users_ID='".$UsersID."' and User_ID= '".$_SESSION[$UsersID.'User_ID']."'");


$all_distribute_count = $self_distribute_count = $posterity_distribute_count = 0;

//计算分销总次数
$condition = "where Users_ID='".$UsersID."' and Record_Type = 0 and User_ID=".$_SESSION[$UsersID.'User_ID'];
$count = $DB->getRs("shop_distribute_account_record","count(Record_ID) as count",$condition);
$all_distribute_count = $count['count'];

//计算自销次数
$condition = "where Users_ID='".$UsersID."' and Record_Type = 0 and User_ID=".$_SESSION[$UsersID.'User_ID'];
$condition .= " and  User_ID = Owner_ID";


$count = $DB->getRs("shop_distribute_account_record","count(Record_ID) as count",$condition);
$self_distribute_count = $count['count'];

//计算下级分销次数
$condition = "where Users_ID='".$UsersID."' and Record_Type = 0 and User_ID=".$_SESSION[$UsersID.'User_ID'];
$condition .= " and  User_ID != Owner_ID";
$count = $DB->getRs("shop_distribute_account_record","count(Record_ID) as count",$condition);
$posterity_distribute_count =  $count['count'];

//获取记录
$condition = "where Users_ID='".$UsersID."' and Record_Type = 0 and User_ID=".$_SESSION[$UsersID.'User_ID'];


if($_GET['filter'] != 'all'){
	
	if($_GET['filter'] == 'self'){
		$condition .= " and  User_ID = Owner_ID";
	}elseif($_GET['filter'] == 'down'){
		$condition .= " and  User_ID != Owner_ID";
	}
}



$rsRecords = $DB->getPage("shop_distribute_account_record","*",$condition,5);

$distribute_record = $DB->toArray($rsRecords);


?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>分销账户明细</title>
 <link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
    <link href="/static/api/distribute/css/style.css" rel="stylesheet">
     <link href="/static/api/distribute/css/detaillist.css" rel="stylesheet">
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/static/js/jquery-1.11.1.min.js"></script>
    <script type='text/javascript' src='/static/api/js/global.js'></script>
    <script src="/static/api/distribute/js/distribute.js"></script>
    
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
  <h1 class="title">分销账户明细</h1>
  
</header>

<div class="wrap">
	<div class="container">
    	
	
        <div class="row">
        	<ul id="distribute-brief-info">
         <li class="item"><a href="<?=$shop_url?>distribute/detaillist/all/"><span class="red bold">&nbsp;<?=$all_distribute_count?></span><br/>全部</a></li>
         <li class="item"><a href="<?=$shop_url?>distribute/detaillist/self/"><span class="red bold">&nbsp;<?=$self_distribute_count?></span><br/>自销</a></li>
         <li class="item"><a href="<?=$shop_url?>distribute/detaillist/down/"><span class="red bold">&nbsp;<?=$posterity_distribute_count?></span><br/>下级分销</a></li>
         <li class="clearfix"></li>
      </ul>
      
      <ul class="list-group" id="record-panel">
    
      	<?php foreach($distribute_record as $key=>$item):?>
        <li class="list-group-item">
        	<p class="record-description">
			<?=$item['Record_Description']?>&nbsp;&nbsp;<span class="red">&yen;(<?=$item['Record_Money']?>)</span>
			
                     
            </p>
            <p class="record-description" ><?=ldate($item['Record_CreateTime'])?>&nbsp;&nbsp;&nbsp;&nbsp;</p>
            <p >
             <?php 
			 	if($item['Record_Status'] == 0){
					echo '进行中';
				}else{
					echo '已完成';
				}
			 ?>
             </p>
             
              
        </li>
        <?php endforeach;?>
      </ul>   
      <?php
      	echo $DB->showWechatPage1();
      ?>
        </div>

    </div>
    
  	
  
    
</div>

 
<?php require_once('../distribute_footer.php');?> 
 
 
</body>
</html>
