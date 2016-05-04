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

if(!strpos($_SERVER['REQUEST_URI'],"mp.weixin.qq.com")){
  	header("location:?wxref=mp.weixin.qq.com");
}

//会员信息
$front_title = get_dis_pro_title($DB,$UsersID);

$fields = "User_ID,Shop_Name,Group_Sales,Up_Group_Sales,last_award_income,Total_Income";
$fields .= ",Professional_Title,Ex_Bonus,Total_Sales,Group_Num,Up_Group_Num";
$condition = "where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID'];
$rsDistirbuteAccount = $DB->getRs('shop_distribute_account',$fields,$condition);


$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");


?>


<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>渠道晋升</title>
 <link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
    <link href="/static/api/distribute/css/style.css" rel="stylesheet">
     <link href="/static/api/distribute/css/protitle.css" rel="stylesheet">
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/static/js/jquery-1.11.1.min.js"></script>
    <script type='text/javascript' src='/static/api/js/global.js'></script>
    <script src="/static/api/distribute/js/distribute.js"></script>
     <script language="javascript">
	 
	var base_url = '<?=$base_url?>';
	var UsersID = '<?=$UsersID?>';
	
	$(document).ready(distribute_obj.pro_file_init);

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
  <h1 class="title">我的等级</h1>
  
</header>

<div class="wrap">
 <div class="container">
    
  
  	<div class="row">
      
    	
        <div class="panel panel-default">
  <!-- Default panel contents -->
 
  <div class="panel-body">
    
    <p><h4 style="color:#F29611;">
	<?php if(!empty($rsDistirbuteAccount['Professional_Title'])&&!empty($front_title[$rsDistirbuteAccount['Professional_Title']])):?>
		<img src="<?=$front_title[$rsDistirbuteAccount['Professional_Title']]['ImgPath']?>" /> <?=$front_title[$rsDistirbuteAccount['Professional_Title']]['Name']?>
    <?php else:?>
       暂无爵位
	<?php endif;?>
    </h4>
     
	 <p>团队销售额:&nbsp;&nbsp;&yen;<span class="red"><?=$rsDistirbuteAccount['Group_Sales']?></span></p>
     <p>晋级后累计团队销售额:&nbsp;&nbsp;&yen;<span class="red"><?=$rsDistirbuteAccount['Up_Group_Sales']?></span></p>
	 <p>晋级后团队人数:&nbsp;&nbsp;<span class="red"><?=$rsDistirbuteAccount['Group_Num']?></span></p>
     <p>晋级后累计团队人数:&nbsp;&nbsp;<span class="red"><?=$rsDistirbuteAccount['Up_Group_Num']?></span></p>	  
	  
	  <?php if($rsDistirbuteAccount['Ex_Bonus'] >0 ):?>
        可领取奖金:&nbsp;&nbsp;<span class="red">&yen;<?=$rsDistirbuteAccount['Ex_Bonus']?></span>
      <?php else: ?>
	 <p class="red">目前无奖金!!!</p>  
	  <?php endif;?>
	  
    </p>
    
    <?php if($rsDistirbuteAccount['Ex_Bonus'] >0):?>
    <div class="button-panel text-center">
    	<button class="btn btn-default" id="get_ex_btn">获得奖励</button>
    </div>
    <?php endif;?>
  </div>
        
 
		
	
		
		<table class="table">
        <thead>
          <tr>
           	<th>#</th>
				<th>爵位</th>
				<th>销售额</th>
				<th>团队人数</th>
				<th>奖励百分比</th>
          </tr>
        </thead>
        <tbody>
		  <?php foreach($front_title as $key=>$item):?>	
          <tr>
            <td scope="row"><?=$key?></td>
            <td><?=$item['Name']?></td>
            <td><span class="red">&yen;<?=$item['Saleroom']?></span></td>
            <td style="text-align:center"><?=$item['Group_Num']?></td>
			<td style="text-align:center"><span class="label label-info"><?=$item['Bonus']?>%</span></td>
          </tr>
           <?php endforeach;?>
        </tbody>
      </table>
    	
    </div>
  </div>
</div>

 
<?php require_once('../distribute_footer.php');?> 
 
 
</body>
</html>

