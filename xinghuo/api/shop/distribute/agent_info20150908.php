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
$rsAccountObj =  Dis_Account::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$User_ID))
			   ->first();
			   
$rsAccount = $rsAccountObj->toArray();

$dsAgentArea = $rsAccountObj->disAreaAgent()->with('area')->getResults()->toArray();
$dsAgentProvince = $dsAgentCity = array();

foreach($dsAgentArea as $key=>$AgentArea){
	 if($AgentArea['area']['area_deep'] == 1){
		$dsAgentProvince[] = $AgentArea['area_name'];
	 }else{
		$dsAgentCity[] = $AgentArea['area_name'];
	 }
	 
	 
}

 
$Total_Agent_Money = Dis_Agent_Record::multiWhere(array('Users_ID'=>$UsersID,'Account_ID'=>$rsAccount['Account_ID']))
                                      ->sum('Record_Money');

$records = Dis_AGent_Record::multiWhere(array('Users_ID'=>$UsersID,'Account_ID'=>$rsAccount['Account_ID']))
                                      ->get();

									  


$record_list = array();				
if(!empty($records)){
	$record_list = $records->toArray();
}

$record_type = array(1=>'合伙人',2=>'地区代理');

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
    
    	<div class="row">
        	 
             <div class="panel panel-default">
             	 <div class="panel-body">	
             
                 <?php if($dis_agent_type == 1):?>		
            		<h4 style="color:#F29611;">代理商类型:合伙人</h4>
      		  	 <?php else:?>
         			<h4 style="color:#F29611;">代理商类型:地区代理</h4>
        	 	 <?php endif;?>
                 
             <?php if($dis_agent_type == 2):?>		
				<?php if(!empty($dsAgentProvince)):?>
			   	省代地区:<span><?=implode(',',$dsAgentProvince)?></span>
				<?php endif;?>
			
				<?php if(!empty($dsAgentCity)):?>
					市代地区:<span><?=implode(',',$dsAgentCity)?></span>	
				<?php endif;?>
			
			
		 	 <?php endif;?>
         
                
                 
                 </div>
      			<?php if(!empty($record_list)):?>
	       			<table class="table">
      <caption>&nbsp;&nbsp;&nbsp;&nbsp;获得代理佣金记录</caption>
      	<tbody>
			<?php $i= 1;?>
        	<?php foreach($record_list as $key=>$record):?>
            <tr>
        	<th scope="row"><?=$i?></th>
        	<td><?=$record_type[$record['Record_Type']]?></td>
          	<td>&yen;<sapn class="red"><?=round_pad_zero($record['Record_Money'],2)?></span></td>
          	<td><?=sdate($record['Record_CreateTime'])?></td>
        	</tr>
			<?php $i++; ?>
   			<?php endforeach; ?> 
  		    </tbody>
    	</table>
        		<?php else:?>
					<ul style="margin-top:10px;" class="list-group">
				<li class="list-group-item">
				<span class="red">暂无代理佣金记录</span>
				</li>
			</ul>
     		   <?php endif;?>  
             
             </div>
        
        </div>
    

    </div>

</div>

 
<?php require_once('../distribute_footer.php');?> 
 
 
</body>
</html>

	