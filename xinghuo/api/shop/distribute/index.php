<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');

if (isset($_GET["UsersID"])) {
	$UsersID = $_GET["UsersID"];
} else {
	echo '缺少必要的参数';
	exit;
}

if(!empty($_SESSION[$UsersID."User_ID"])){
  
  $userexit = User::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$_SESSION[$UsersID."User_ID"]))
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

$is_login = 1;
$owner = get_owner($rsConfig,$UsersID);
require_once $_SERVER["DOCUMENT_ROOT"] . '/include/library/wechatuser.php';
$owner = get_owner($rsConfig,$UsersID);

//获取登录用户账号
$User_ID = $_SESSION[$UsersID."User_ID"];
$rsUser =  User::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$User_ID))
			   ->first()
			   ->toArray();
			   
if(empty($rsUser) || $rsUser['Is_Distribute'] == 0) {
	header("location:".$shop_url."distribute/join/");
}
if(empty($rsUser['User_Name']) || empty($rsUser['User_Mobile'])) {
	header("location:".$shop_url."distribute/profile/");
}
//获取登录用户分销账号
$accountObj =  Dis_Account::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$User_ID))
			   ->first();
$rsAccount = $accountObj->toArray();			 
	   
$tree = create_distribute_tree($UsersID);
$node = $tree->getNodeByID($User_ID);

//获取自身树层
$p_level = $node->getLevel();
$Descendants = $node->getDescendants();

//获取此用户所有三级下属
$posterity = get_posterity($User_ID,$Descendants,$p_level);
$posterity_count = count($posterity);

//获取此用户整理过的下属
$posterity_list = organize_level($User_ID,$Descendants,$p_level);
//获取此分销账户佣金情况


$record_list = Dis_Account_Record::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$User_ID,'Record_Type'=>0))
                                   ->get(array('Record_Money','Record_Status','Record_CreateTime'))
								   ->toArray();

		
							   
$bonus_list = dsaccount_bonus_statistic($record_list);

if ($rsConfig['Distribute_Customize'] == 0) {
	$show_name = $rsUser['User_NickName'];
	$show_logo = !empty($rsUser['User_HeadImg']) ? $rsUser['User_HeadImg'] : '/static/api/images/user/face.jpg';
} else {
	$show_name = !empty($rsAccount['Shop_Name']) ? $rsAccount['Shop_Name'] : '暂无';
	$show_logo = !empty($rsAccount['Shop_Logo']) ? $rsAccount['Shop_Logo'] : '/static/api/images/user/face.jpg';
}	
$level_name_list = array(1 => '一级分销商', 2 => '二级分销商', 3 => '三级分销商');

					                												
$withdraw_msg = get_distribute_withdraw($UsersID,$rsAccount["Enable_Tixian"],$rsConfig["Withdraw_Type"],$rsConfig["Withdraw_Limit"],$shop_url,'#FFF',1);
if($rsAccount["Enable_Tixian"] == 0&&$rsConfig["Withdraw_Type"]==3&&$rsConfig["Withdraw_Limit"]==0){
	$accountObj->Enable_Tixian = 1;
	$accountObj->save();
}

$total_sales = round_pad_zero(get_my_leiji_sales($UsersID,$User_ID,$posterity),2);
$total_income = round_pad_zero(get_my_leiji_income($UsersID,$User_ID),2);
$resAgent =  $DB->query("SELECT * FROM `shop_dis_agent_rec` WHERE Account_ID = '{$rsAccount['Account_ID']}'");
$resDis =  $DB->query("SELECT * FROM `shop_distribute_account_record` WHERE User_ID = '{$User_ID}'");
$myOrderNum = $DB->num_rows($resAgent)+$DB->num_rows($resDis);

$record = Dis_Record::with('DisAccountRecord')
                      ->Multiwhere(array('Users_ID'=>$UsersID,'Record_ID'=>78))
                      ->first();

$dis_agent_type = $rsConfig['Dis_Agent_Type'];
$is_agent = is_agent($rsConfig,$rsAccount);

$disArea = Dis_Agent_Area::Multiwhere(array('Users_ID'=>$UsersID,"Account_ID"=>$User_ID))->get()->toArray();
if(!empty($disArea) && $dis_agent_type == 4){
	$is_agent = true;
}
//爵位设置
$pro_titles = Dis_Config::get_dis_pro_title($UsersID);
$show_support = true;

?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>分销中心</title>
 <link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
    <link href="/static/api/distribute/css/style.css" rel="stylesheet">
     <link href="/static/api/distribute/css/distribute_center.css?1.1" rel="stylesheet">
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/static/js/jquery-1.11.1.min.js"></script>
	<script src="/static/api/distribute/js/distribute.js"></script>
    <script type="text/javascript">
		$(document).ready(function(){
			distribute_obj.init();
		});
    </script>


<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
<?php if ($rsAccount['Is_Audit'] == 1): ?>
<div class="wrap">
	<?php if ($rsAccount['status']): ?>
    	<div class="container">

      	<div class="row">
         <div class="distribute_header">
      <div id="header_cushion">
     
      		<div id="account_info" style="width:100%">
            	<div class="pull-left" style="width:30%">
                	  	 <img id="hd_image" src="<?=$show_logo?>"/>
                </div>
            
                <div class="pull-right" style="width:70%;">
                	<ul id="txt" style="padding-left:0px;<?php echo $withdraw_msg=="" ? '' : ' margin-top:20px;';?>">
                      <li><?=$show_name?></li>
                      <li style="padding-right:3px;">
						<?php echo $withdraw_msg=="" ? '您已成为老板' : $withdraw_msg;?>
					  </li>
                    </ul>
                </div>
           		<div class="clearfix">
                </div>
            </div>
      </div>

	 
      <div id="account_sum">	
      	    <a href="javascript:void(0)"><span><?=$total_sales?></span><br>累计销售额</a>
   			<a href="javascript:void(0)"><span><?=$total_income?></span><br>累计佣金</a>
            <div class="clearfix"></div>
      </div>
      
      </div>
    </div>
   	    <div class="clearfix"></div>

  		</div>
		
<!--box start-->
<div class="dis_box">
	<h3><i class="fa fa-1x fa-area-chart" style="background:#d1531e"></i>推广信息</h3>
	<div class="zl_flex">
		<p class="zl_flex_1" onclick="window.location.href='/api/<?=$UsersID?>/shop/distribute/mypopular/1/'"><span><?=$posterity_count?></span><br />我的人气</p>
		<p class="zl_flex_1" onclick="window.location.href='/api/shop/distribute/myorder.php?UsersID=<?=$UsersID?>&filter=all'"><span><?=$myOrderNum?></span><br /><?=date('m')?>月订单</p>
		<p class="zl_flex_1" onclick="window.location.href='/api/shop/distribute/myassets.php?UsersID=<?=$UsersID?>&filter=all'"><span>￥<?=round_pad_zero($rsAccount['balance'],2)?></span><br /><?=date('m')?>月资产</p>
	</div>
</div>
  		<!-- 我的收入统计begin -->
  		<div class="list_item">
   <div class="dline"></div>
     <a href="javascript:void(0)" class="item_group_title"><img src="/static/api/distribute/images/coin_stack.png"/>&nbsp;&nbsp;我的佣金&nbsp;&nbsp;<span class="pink font17">(<?=$bonus_list['total']?>)</span></a>
    <div class="dline"></div>

    <a href="javascript:void(0)" class="item item_0"><span class="ico"></span>本周收入<button class="btn btn-default btn-sm bonus_sum"><?=$bonus_list['week_income']?></button></a>
	<a href="javascript:void(0)" class="item item_1"><span class="ico"></span>本月收入<button class="btn btn-default btn-sm bonus_sum"><?=$bonus_list['month_income']?></button></a>
    <a href="javascript:void(0)" class="item item_2"><span class="ico"></span>未付款佣金<button class="btn btn-default btn-sm bonus_sum"><?=$bonus_list['un_pay'];?></button></a>
      <a href="javascript:void(0)" class="item item_2"><span class="ico"></span>已付款佣金<button class="btn btn-default btn-sm bonus_sum"><?=$bonus_list['payed'];?></button></a>
    <a href="javascript:void(0)" class="item item_2"><span class="ico"></span>可提现佣金
    <button class="btn-sm btn btn-default" id="withdraw_btn" link="/api/<?=$UsersID?>/shop/distribute/withdraw/">提&nbsp;&nbsp;现</button><span class="divider">&nbsp;</span>
    <button class="btn btn-default btn-sm" id="balance_sum"><?=round_pad_zero($rsAccount['balance'],2)?></button></a>
	<div class="clearfix"></div>
</div>
  		<!-- 我的收入统计end -->


    	<div class="list_item">
     <div class="dline"></div>
     <a href="/api/<?=$UsersID?>/shop/distribute/qrcodehb/" class="item_group_title"><img src="/static/api/distribute/images/qrcode.png"/>&nbsp;&nbsp;我的推广二维码
      <span class="fa fa-2x fa-chevron-right grey pull-right"></span>
     </a>



</div>

    <div class="list_item">
     <div class="dline"></div>
     <a href="/api/<?=$UsersID?>/shop/distribute/income_list/" class="item_group_title"><img src="/static/api/distribute/images/income_list.jpg"/>&nbsp;&nbsp;财富排行榜
      <span class="fa fa-2x fa-chevron-right grey pull-right"></span>
     </a>



	</div>
    
   <?php if($dis_agent_type >0&$is_agent):?> 
   <div class="list_item">
     <div class="dline"></div>
     <a href="/api/<?=$UsersID?>/shop/distribute/agent_info/" class="item_group_title">&nbsp;<img src="/static/api/distribute/images/agent.png"/>&nbsp;&nbsp;代理信息 
      <span class="fa fa-2x fa-chevron-right grey pull-right"></span>
     </a>
	</div>
    
    <?php endif; ?>

    <div class="list_item">
	 <div class="dline"></div>
     <a href="/api/<?=$UsersID?>/shop/distribute/edit_shop/" class="item_group_title"><img src="/static/api/distribute/images/config.png"/>&nbsp;&nbsp;我的店铺配置
     <span class="fa fa-2x fa-chevron-right grey pull-right"></span>
     </a>

	</div>

	<?php if ($rsConfig['Distribute_Customize'] == 1): ?>

    	<div class="list_item">
	 <div class="dline"></div>
     <a href="/api/<?=$UsersID?>/shop/distribute/edit_headimg/" class="item_group_title"><img src="/static/api/distribute/images/face_pre.png"/>&nbsp;&nbsp;自定义头像
 	 <span class="fa fa-2x fa-chevron-right grey pull-right"></span>
     </a>
     </div>

   <?php endif;?>
   	<?php if (!empty($pro_titles)): ?>
    	<div class="list_item">
	 		<div class="dline"></div>
     		<a href="/api/<?=$UsersID?>/shop/distribute/pro_title/" class="item_group_title"><img src="/static/api/distribute/images/title.jpg"/>&nbsp;&nbsp;爵位晋升     <span class="fa fa-2x fa-chevron-right grey pull-right"></span>
     		</a>
     	</div>
   <?php endif;?>
	<?php else: ?>
        <p>您的分销账号已被禁用</p>
        <a href="<?=$shop_url?>">返回</a>
    <?php endif;?>
<?php else: ?>
	<div>
            	<div id="desc" class="col-xs-10">
    				<p style="font-size:18px;color:red;">
       				 <br/>
        			<br/>
           				您的分销申请正在审核中,<br/>
                        请耐心等待...
        			</p>
  				</div>
            </div>
<?php endif;?>

<?php require_once '../distribute_footer.php';?>

</body>
</html>