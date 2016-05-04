<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');


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

$rsConfig =$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$rsUser = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]);



//获取此用户分销账号信息
$accountObj =  Dis_Account::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$_SESSION[$UsersID.'User_ID']))
			   ->first();
$rsAccount = $accountObj->toArray();	
$all_distribute_count = $self_distribute_count = $posterity_distribute_count = 0;


//计算分销总次数

$User_ID = $_SESSION[$UsersID.'User_ID'];
										           
//计算全部个数
$builder = Dis_Account_Record::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$_SESSION[$UsersID.'User_ID']));
$all_distribute_count = $builder->count();

//自销次数
$builder = Dis_Account_Record::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$_SESSION[$UsersID.'User_ID']));
$self_distribute_count = $builder->whereHas('DisRecord',function($query) use($User_ID){
									$query->where('Owner_ID','=',$User_ID);
								})->count();
//下级销售次数
$builder = Dis_Account_Record::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$_SESSION[$UsersID.'User_ID']));
$posterity_distribute_count = $builder->whereHas('DisRecord',function($query) use($User_ID){
									$query->where('Owner_ID','!=',$User_ID);
								})->count();

if(!empty($dis_account_record)){
	$dis_account_record_list = $dis_account_record->toArray();								 
	
	foreach($dis_account_record_list as $key=>$account_record){
		$account_record['Owner_ID'] = $account_record['dis_record']['Owner_ID'];
		unset($account_record['dis_record']);
		$dis_account_record_list[$key]	= 	$account_record;
	}
	
}


//获取记录
$builder = Dis_Account_Record::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$_SESSION[$UsersID.'User_ID']));

$url_param = array('UsersID'=>$UsersID);
if($_GET['filter'] != 'all'){
	
	if($_GET['filter'] == 'self'){
		$builder->whereHas('DisRecord',function($query) use($User_ID){
									$query->where('Owner_ID','=',$User_ID);
								});
		$url_param['filter'] = 'self';	
	}elseif($_GET['filter'] == 'down'){
		$builder->whereHas('DisRecord',function($query) use($User_ID){
									$query->where('Owner_ID','!=',$User_ID);
								});		
		$url_param['filter'] = 'down';			
	}
}else{
	$url_param['filter'] = 'all';
}

$builder->orderBy('Record_CreateTime','desc');

$Records_paginate_obj = $builder->simplePaginate(5);
$Records_paginate_obj->setPath(base_url('api/shop/distribute/detaillist.php'));


$Records_paginate_obj->appends($url_param);
$distribute_record  = $Records_paginate_obj->toArray()['data'];

$page_link = $Records_paginate_obj->render();

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
			<?=$item['Record_Description']?>&nbsp;&nbsp;<span class="red">&yen;(<?=round_pad_zero($item['Record_Money'],2)?>)</span>
			
                     
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
      	echo $page_link;
      ?>
        </div>

    </div>
    
  	
  
    
</div>

 
<?php require_once('../distribute_footer.php');?> 
 
 
</body>
</html>
