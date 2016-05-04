<?php
//ini_set("display_errors","On");
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');


if (isset($_GET["UsersID"])) {
	$UsersID = $_GET["UsersID"];
} else {
	echo '缺少必要的参数';
	exit;
}

if(!empty($_SESSION[$UsersID."User_ID"])){
  $User_ID = $_SESSION[$UsersID."User_ID"];
  $userexit = User::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$User_ID))
  					->first();
  if(empty($userexit)){
    $_SESSION[$UsersID."User_ID"] = "";
  }
}

$base_url = base_url();
$shop_url = shop_url();


/*分享页面初始化配置*/
$share_flag = 1;
$signature = '';

//获取本店配置
$rsConfig = shop_config($UsersID);
$dis_agent_type = $rsConfig['Dis_Agent_Type'];

//获取登录用户分销账号
$rsAccount =  Dis_Account::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$User_ID))
			   ->first()
			   ->toArray();


$Total_Agent_Money = Dis_Agent_Record::multiWhere(array('Users_ID'=>$UsersID,'Account_ID'=>$rsAccount['Account_ID']))
                                      ->sum('Record_Money');

$records = Dis_AGent_Record::multiWhere(array('Users_ID'=>$UsersID,'Account_ID'=>$rsAccount['Account_ID']))
                                      ->get();

$record_list = array();				
if(!empty($records)){
	$record_list = $records->toArray();
}

$record_type = array(1=>'普通代理',2=>'地区代理');

?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>代理信息</title>
 <link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
    <link href="/static/api/distribute/css/style.css" rel="stylesheet">
     <link href="/static/api/distribute/css/withdraw.css" rel="stylesheet">
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
  <h1 class="title">代理信息</h1>
  
</header>

<div class="wrap">
	<div class="container">
    	<div class="row page-title">
           <h4>&nbsp;&nbsp;&nbsp;&nbsp;代理佣金总和&nbsp;&nbsp;&nbsp;&nbsp;<span class="red">&yen;<?=round_pad_zero($Total_Agent_Money,2)?></span> </h4>  
            
        </div>
        
        <div class="row center-block">
         <?php if($dis_agent_type == 1):?>		
             代理商类型:<span class="red">普通代理</span>
         <?php else:?>
         	 代理商类型:<span class="red">地区代理</span>
         <?php endif;?>
         
        </div>
		
         <div class="row">
         	
            <table class="table">
      <caption>&nbsp;&nbsp;&nbsp;&nbsp;获得代理佣金记录</caption>
      
      <?php if(!empty($record_list)):?>
      	<tbody>
        	<?php foreach($record_list as $key=>$record):?>
            <tr>
        	<th scope="row"><?=$record['Record_ID']?></th>
        	<td><?=$record_type[$record['Record_Type']]?></td>
          	<td>&yen;<sapn class="red"><?=round_pad_zero($record['Record_Money'],2)?></span></td>
          	<td><?=sdate($record['Record_CreateTime'])?></td>
        	</tr>
   			<?php endforeach; ?> 
  		    </tbody>
    	</table>
        <?php else:?>
         暂无代理佣金记录
        <?php endif;?>  
         </div>
        
        

    </div>
    
  	
  
    
</div>

 
<?php require_once('../distribute_footer.php');?> 
 
 
</body>
</html>

	