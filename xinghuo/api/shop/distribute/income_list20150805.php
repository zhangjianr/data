<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');
$base_url = base_url();
$shop_url = shop_url();
/*分享页面初始化配置*/
$share_flag = 1;
$signature = '';

if(isset($_GET["UsersID"])){
  $UsersID=$_GET["UsersID"];
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


//获取此用户分销账号信息
$rsAccount  = $DB->GetRs("shop_distribute_account","*","where Users_ID='".$UsersID."' and User_ID= '".$_SESSION[$UsersID.'User_ID']."'");
$rsConfig = $DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");

//获取总店分销商排行榜
$condition = "where Users_ID = '".$UsersID."' Order By Total_Sales desc Limit 10";
$rsHeadDistributes  = $DB->get("shop_distribute_account","User_ID,Shop_Name,Total_Sales,Professional_Title",$condition);
$HeadDistributeList = $DB->toArray($rsHeadDistributes);

//获取下属分销商排行榜
$condition = "where Users_ID = '".$UsersID."' and invite_id =".$_SESSION[$UsersID."User_ID"]." Order By Total_Income desc Limit 100";
$rsPosterityDistributes  = $DB->get("shop_distribute_account","User_ID,Shop_Name,Shop_Logo,Total_Income,Professional_Title",$condition);
$PosterityDistributeList = $DB->toArray($rsPosterityDistributes);

$dis_title_level = get_dis_pro_title($DB,$UsersID);


//获取自己下属分销商排行榜

$h_result = get_h_incomelist_rank($UsersID,$_SESSION[$UsersID.'User_ID'],$rsConfig['H_Incomelist_Limit'],$rsConfig['HIncomelist_Open']);

?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>财富排行榜</title>
 <link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
    <link href="/static/api/distribute/css/style.css" rel="stylesheet">
     <link href="/static/api/distribute/css/income_list.css" rel="stylesheet">
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/static/js/jquery-1.11.1.min.js"></script>
<script src="/static/js/jquery.idTabs.min.js"></script>
    <script type="text/javascript">
		$(document).ready(function(){
			$("#income-filter").idTabs("table1");
		});
    	
    </script>
 
    
</head>

<body>
<div class="wrap">
	<div class="container">
    
      <div class="row">
      		<div class="income-list-image">
            	 <img  width="100%" src="/static/api/distribute/images/income_list_banner.jpg"/>	
            </div>
      </div>
   	 
      <div class="row" id="filter-panel">
    		 <div id="income-filter" class="btn-group"><a href="#table1" class="item btn btn-default">总部分销商</a><a href="#table2" class="item btn btn-default">我的好友</a>
             <a href="#" class="clearfix"></a></div>
      </div>
      
     
		<div class="row" >
           <div id="table-panel" >
           <table class="table income_list" id="table1">
           <thead>
        <tr>
          <th colspan="2">排名</th>
          <th>爵位</th>
          <th>佣金</th>
        </tr>
      </thead>
      
        <?php if($h_result  == false):?>	
         	
            <tr><td colspan="4"><span class="alert-danger">无权查看，需入榜后才能查看。</span></td></tr>
        <?php else: ?>
        
      
      <tbody>
       	<?php foreach($h_result['H_Incomelist'] as $key=>$item):?>
        	<tr id="rank_<?=$key+1?>">
          <th>
            
             <span class="rank"><?=($key>2)?($key+1):''?></span> 
          </th>
          <td>
          <?php if(!empty($item['Shop_Logo'])):?>
		  	  <img class="hd_img" src="<?=$item['Shop_Logo']?>"/>
		  <?php else: ?>
		  	  <img class="hd_img" src="/static/api/images/user/face.jpg"/>	
		  <?php endif; ?>
		  <?=sub_str($item['Shop_Name'],10,TRUE)?>
          </td>
          <td>
          
            <?php if(!empty($dis_title_level)): ?>
          		<?php if(!empty($dis_title_level[$item['Professional_Title']])):?>	
               
                <span class="juewei"><?=$dis_title_level[$item['Professional_Title']]['Name'];?></span>
                <?php else:?>
                 无
                <?php endif;?>
			<?php else: ?>
                无
			<?php endif;?>
          </td>
          
          <td><span class="total_income">&yen;<?=$item['Total_Income']?></span></td>
        
        </tr>
         <?php endforeach; ?>
      </tbody>
    
        <?php endif; ?>
        </table>
   		  <table class="table income_list" id="table2">
      <thead>
        <tr>
      
          <tr>
          <th colspan="2">排名</th>
          <th>爵位</th>
          <th>佣金</th>
        </tr>
        
        </tr>
      </thead>
      <tbody>
       
       <?php foreach($PosterityDistributeList as $key=>$item):?>
        	<tr id="rank_<?=$key+1?>">
          <th>
            
             <span class="rank"><?=($key>2)?($key+1):''?></span> 
          </th>
          <td>
          <?php if(!empty($item['Shop_Logo'])):?>
		  	  <img class="hd_img" src="<?=$item['Shop_Logo']?>"/>
		  <?php else: ?>
		  	  <img class="hd_img" src="/static/api/images/user/face.jpg"/>	
		  <?php endif; ?>
		  <?=sub_str($item['Shop_Name'],10,TRUE)?>
          </td>
          <td>
          
            <?php if(!empty($dis_title_level)): ?>
          		<?php if(!empty($dis_title_level[$item['Professional_Title']])):?>	
               
                <span class="juewei"><?=$dis_title_level[$item['Professional_Title']]['Name'];?></span>
                <?php else:?>
                 无
                <?php endif;?>
			<?php else: ?>
                无
			<?php endif;?>
          </td>
          
          <td><span class="total_income">&yen;<?=$item['Total_Income']?></span></td>
        
        </tr>
       <?php endforeach;?>
        
     
     
      </tbody>
    </table>
           </div>
   	  </div>
  </div>

</div>
    
  	
  
    
</div>

<?php require_once('../distribute_footer.php');?> 
 
 
</body>
</html>
