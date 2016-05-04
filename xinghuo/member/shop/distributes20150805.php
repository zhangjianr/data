<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
$base_url = base_url();

if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}

$rsConfig=$DB->GetRs("user_config","UserLevel","where Users_ID='".$_SESSION["Users_ID"]."'");

$dis_title_level = get_dis_pro_title($DB,$_SESSION['Users_ID']);


if(isset($_GET["action"])){
	if($_GET["action"]=="del"){
		
		$rs = $DB->GetRs("shop_distribute_account","*","where Users_ID='".$_SESSION["Users_ID"]."' and Account_ID=".$_GET["AccountID"]);
		
		if($rs){
		  //针对于此平台的两次删除bug
		  $Flag = $DB->Set("user",array("Is_Distribute"=>0),"where Users_ID='".$_SESSION["Users_ID"]."' and User_ID=".$rs["User_ID"]);
		}
		$Flag=$DB->Del("shop_distribute_account","Users_ID='".$_SESSION["Users_ID"]."' and Account_ID=".$_GET["AccountID"]);
		if($Flag){
			echo '<script language="javascript">alert("删除成功");window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';
		}else{
			echo '<script language="javascript">alert("删除失败");history.back();</script>';
		}
		exit;
	}
	
	if($_GET["action"]=="pass"){
	
	
		$Flag=$DB->Set("shop_distribute_account",array("Is_Audit"=>1),"where Users_ID='".$_SESSION["Users_ID"]."' and Account_ID=".$_GET["AccountID"]);
		if($Flag){
			echo '<script language="javascript">alert("审核通过");window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';
		}else{
			echo '<script language="javascript">alert("操作失败");history.back();</script>';
		}
		exit;
	}
	
	if($_GET["action"] == "disable"){
		$Flag=$DB->Set("shop_distribute_account",array("status"=>0),"where Users_ID='".$_SESSION["Users_ID"]."' and Account_ID=".$_GET["AccountID"]);
		if($Flag){
			echo '<script language="javascript">alert("禁用成功");window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';
		}else{
			echo '<script language="javascript">alert("操作失败");history.back();</script>';
		}
		
		exit;
	}
	
	if($_GET["action"] == "enable"){
		$Flag=$DB->Set("shop_distribute_account",array("status"=>1),"where Users_ID='".$_SESSION["Users_ID"]."' and Account_ID=".$_GET["AccountID"]);
		if($Flag){
			echo '<script language="javascript">alert("启用成功");window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';
		}else{
			echo '<script language="javascript">alert("操作失败");history.back();</script>';
		}
		exit;
	}
	
	
}

//获取营销会员的用户名
$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Is_Distribute = 1";
$rsUsers = $DB->get('user','User_ID,User_NickName',$condition);
$user_list = $DB->toArray($rsUsers);

//生成drop_down数组
$user_dropdown = array();
foreach($user_list as $key=>$user){
 	$user_dropdown[$user['User_ID']] = $user['User_NickName'];	
}

$condition = "where Users_ID='".$_SESSION["Users_ID"]."'";


if(isset($_GET["search"])){
	if($_GET["search"]==1){
	
		if(!empty($_GET["Keyword"])){
			$condition .= " and  Shop_Name like '%".$_GET["Keyword"]."%'";
		}
		
	
	}
}
$condition .= " order by Account_CreateTime desc";


?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/member/js/global.js'></script>
</head>

<body>
<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->
<style type="text/css">
body, html{background:url(/static/member/images/main/main-bg.jpg) left top fixed no-repeat;}
</style>
<div id="iframe_page">
  <div class="iframe_content">
    <link href='/static/member/css/user.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/member/js/shop.js'></script>
    <div class="r_nav">
      <ul>

        <li class="cur"> <a href="distributes.php">分销账号管理</a> </li>
        <li class=""><a href="distribute_record.php">分销记录</a></li>
        <li class=""><a href="withdraw_record.php">提现记录</a></li>
        <li class=""><a href="distribute_title.php">爵位设置</a></li>
        <li class=""><a href="withdraw_method.php">提现方法管理</a></li>
        
        
      </ul>
    </div>
    <link href='/static/js/plugin/lean-modal/style.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/js/plugin/lean-modal/lean-modal.min.js'></script> 
     <script type='text/javascript' src='/static/js/inputFormat.js'></script>
    <script language="javascript">

	$(document).ready(function(){shop_obj.distribute_init();});
</script>
    <div id="update_post_tips"></div>
    <div id="user" class="r_con_wrap">
      <form class="search" id="search_form" method="get" action="?">
        <div class="l"> 关键字：
          <input type="text" name="Keyword" value="" class="form_input" size="15" />
   
          <input type="hidden" name="search" value="1" />
          <input type="submit" class="search_btn" value=" 搜索 " />
           <p>删除分销账号会导致数据不完整，所以只能禁用分销账号</p>
        </div>
        <div class="r"><strong></strong><span class="fc_red"></span></div>
      </form>
      <table width="100%" align="center" border="0" cellpadding="5" cellspacing="0" class="r_con_table">
        <thead>
          <tr>
            <td width="5%" nowrap="nowrap">序号</td>
            <td width="5%" nowrap="nowrap">推荐人</td>
            <td width="8%" nowrap="nowrap">店名</td>
            <td width="5%" nowrap="nowrap">佣金余额</td>    
            <td  width="5%" nowrap="nowrap">审核状态</td>
            <td width="6%" nowrap="nowrap">总收入</td>
            <td width="6%" nowrap="nowrap">销售额</td>
            <td width="5%" nowrap="nowrap">爵位</td>
            <td width="5%" nowrap="nowrap">加入时间</td>
            <td width="5%" nowrap="nowrap">状态</td>
            <td width="8%" nowrap="nowrap" class="last"><strong>操作</strong></td>
          </tr>
        </thead>
        <tbody>
          <?php 
		   $rsAccounts = $DB->getPage("shop_distribute_account","*",$condition,$pageSize=10);
		   $account_list = $DB->toArray($rsAccounts);	
		
		foreach($account_list as $key=>$account){?>
      	
            <tr UserID="<?=$account['User_ID']?>">
          	<td nowarp="nowrap"><?=$account['Account_ID']?></td>  
            <td nowrap="nowraqp">
		
            <?php 
				if($account['invite_id'] == 0){
					$inviter_name = '来自总店';
				}else{
					$inviter_name =  !empty($user_dropdown[$account['invite_id']])?$user_dropdown[$account['invite_id']]:'信息缺失';
				}

			?>
            
            <span><?=$inviter_name?></span>
            </td>
            <td nowarp="nowrap" field=1><?=$account['Shop_Name']?></td>
            <td nowarp="nowrap">&yen;<?=$account['balance']?></td>
            <td nowarp="nowrap"><?=$account['Is_Audit']?'已通过':'未通过'?></td>
            <td nowarp="nowrap">&yen;<?=$account['Total_Income']?></td>
            <td nowrap="nowrap">&yen;<?=$account['Total_Sales']?>元</td>
            <td nowrap="nowrap"><?=!empty($account['Professional_Title'])?$dis_title_level[$account['Professional_Title']]['Name']:'无';?></td>
            <td nowrap="nowrap"><?php echo date("Y-m-d H:i:s",$account['Account_CreateTime']) ?></td>
          	<td nowrap="nowrap">
            	<?php if($account['status'] ==1 ):?>
                	<img src="/static/member/images/ico/yes.gif"/>
				<?php else: ?>
                    <img src="/static/member/images/ico/no.gif"/>
                <?php endif; ?>
            	
            </td>
            <td nowrap="nowrap" class="last">
            <?php if($account['Is_Audit'] == 0): ?>
            <a href="?action=pass&AccountID=<?=$account['Account_ID']?>">通过</a>
            <?php endif; ?>
            <?php if($account['status'] == 1):?>
            <a href="?action=disable&AccountID=<?=$account['Account_ID']?>" onClick="if(!confirm('禁用后此分销商不可分销,你确定要禁用么？')){return false};">禁用</a>|
            <a href="ds_account_posterity.php?User_ID=<?=$account['User_ID']?>" >下属</a>
            </td>
          	<?php else: ?>
            
            <a href="?action=enable&AccountID=<?=$account['Account_ID']?>" title="启用" >启用</a></td>
            
            <?php endif; ?>
          </tr>
          <?php  }?>
		 
        </tbody>
      </table>
      <div class="blank20"></div>
      <?php $DB->showPage(); ?>
    </div>
  </div>
  
  
  
</div>
</div>
</body>
</html>