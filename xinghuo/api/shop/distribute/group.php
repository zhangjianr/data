<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/General_tree.php');

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

//获取所有分销账号列表
$rsAccount = $DB->get('shop_distribute_account','User_ID,invite_id,level,Real_Name',"where Users_ID='".$UsersID."'");
$ds_list = $DB->toArray($rsAccount);
//实例化通用树类
$param = array('result'=>$ds_list,'fields'=>array('User_ID','invite_id'));
$generalTree = new General_tree($param);
//获取此分销商所有下属ID,包括自己的ID
$account_list = $generalTree->leafid($_SESSION[$UsersID.'User_ID']);

foreach($ds_list  as $kye=>$item){
	$ds_dropdown[$item['User_ID']] = $item; 
}

$level_list = array(1=>array(),2=>array(),3=>array());
foreach($account_list as $key=>$item){
	$level_list[$ds_dropdown[$item]['level']][$item] =$ds_dropdown[$item] ;
}

$level_name_list = array(1=>'一级分销商',2=>'二级分销商',3=>'三级分销商');

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
     <link href="/static/api/distribute/css/group.css" rel="stylesheet">
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
     <script src="/static/js/jquery-1.11.1.min.js"></script>
	
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
    <h4 class="row page-title">我的团队</h4>
    </div>
    
  
    <ul id="distribute_group">
   		<li class="item cur"><a href="/api/<?=$UsersID?>/shop/distribute/group/?wxref=mp.weixin.qq.com">我的团队</a></li>
   		<li class="item "><a href="/api/<?=$UsersID?>/shop/distribute/my_distribute/?wxref=mp.weixin.qq.com">我的推广</a></li>
   		<li class="item"><a href="/api/<?=$UsersID?>/shop/distribute/income/?wxref=mp.weixin.qq.com">分销佣金</a></li>
  		<li class="clearfix"></li>
  	</ul>

  
  	<div class="list_item">
	<div class="dline"></div>
    <?php foreach($level_list as $key=>$account_list):?>
    <a href="javascript:void(0)" class="item item_0"><?=$level_name_list[$key]?><span class="jt"></span></a>
		<ul class="distribute_list">
        	<?php if(count($account_list) >0 ):?>
            	<?php foreach($account_list as $k=>$v):?>
                <li><?php if(strlen($v['Real_Name']>0)){
						echo $v['Real_Name'];
					}else{
						echo '暂无';
					}?>
                 &nbsp;&nbsp;&nbsp;&nbsp;<?=$shop_url.$v['User_ID']?>/</li>
           		<?php endforeach;?>
			<?php endif;?>
        </ul>
   <?php endforeach;?>
   
    </div>
  
</div>


</body>
</html>
