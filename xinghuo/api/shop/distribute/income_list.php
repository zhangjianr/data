<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
					
/*分享页面初始化配置*/
$share_flag = 1;
$signature = '';

$base_url = base_url();
$shop_url = shop_url();

if(isset($_GET["UsersID"])){
  $UsersID=$_GET["UsersID"];
}else{
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

if(empty($_SESSION[$UsersID."User_ID"]))
{
  $_SESSION[$UsersID."HTTP_REFERER"]="/api/".$UsersID."/shop/distribute/";
  header("location:/api/".$UsersID."/user/login/");
}

//获取本店配置
$rsConfig = Shop_Config::where('Users_ID',$UsersID)
							->first()
							->toArray();
				
//获取登录用户分销账号
$dis_ccount =  Dis_Account::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$User_ID))
			   ->first()
			   ->toArray();
			   
$Dis_Account_List  = Dis_Account::where(array('Users_ID'=>$UsersID))
		              ->get(array('Users_ID','User_ID','invite_id','User_Name','Account_ID','Shop_Name','Total_Income'))
					  ->toArray();

$tree = new BlueM\Tree($Dis_Account_List,array('rootid'=>1));
$node = $tree->getNodeByID($User_ID);
$Descendants = $node->getDescendants();

//获取此用户下属排行榜
$p_level= $node->getLevel();
$posterity = get_posterity($User_ID,$Descendants,$p_level);
$posterity_income_list = income_list($posterity,100);

//获取总店排行榜
$HeadDistributeList = Dis_Account::where(array('Users_ID'=>$UsersID))
		              ->orderBy('Total_Income','desc')
					  ->take(10)
					  ->get(array('Users_ID','User_ID','invite_id','User_Name','Account_ID','Shop_Name','Total_Income'));
					 
$H_Incomelist = $HeadDistributeList->toArray();
$in_list = $HeadDistributeList->contains('User_ID',$User_ID);

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
      
        <?php if($in_list  == false):?>	
         	
            <tr><td colspan="4"><span class="alert-danger">无权查看，需入榜后才能查看。</span></td></tr>
        <?php else: ?>
        
      
      <tbody>
       	<?php foreach($H_Incomelist as $key=>$item):?>
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
		  <?=str_limit($item['Shop_Name'],10)?>
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
          
          <td><span class="total_income">&yen;<?=round_pad_zero($item['Total_Income'],2)?></span></td>
        
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
       
       <?php foreach($posterity_income_list as $key=>$item):?>
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
		  <?=str_limit($item['Shop_Name'],10)?>
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
          
          <td><span class="total_income">&yen;<?=round_pad_zero($item['Total_Income'],2)?></span></td>
        
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


