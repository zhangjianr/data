<?php require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');

$base_url = base_url();

if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}

$AgentRecPaginate = Dis_Agent_Record::where('Users_ID',$_SESSION["Users_ID"])->paginate(10);

$AgentRecPaginate->setPath(base_url('member/shop/dis_agent_rec.php'));

$page_links = $AgentRecPaginate->render();
$record_list = collect($AgentRecPaginate->items());

//生成用户drop_down数组			   
$accound_ids = $record_list->map(function($account){
				  return $account->Account_ID;
			   })->toArray();
	   
$Account_IDS = array_unique($accound_ids );
$acounts = Dis_Account::whereIn('Account_ID',$Account_IDS);

$account_list = $acounts->with('User')->get(array('Account_ID','User_ID','Shop_Name'));
$acounts = $account_list->toArray();

$account_dropdown = array();
foreach($acounts as $key=>$account){
	
	$account['User_NickName'] = $account['user']['User_NickName'];
	unset($account['user']);
	$account_dropdown[$account['Account_ID']] = $account;
	
}


?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>

<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/css/bootstrap.min.css' rel='stylesheet' type='text/css' />
<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />

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

        <li > <a href="distributes.php">分销账号管理</a> </li>
        <li ><a href="distribute_record.php">分销记录</a></li>
        <li class=""><a href="withdraw_record.php">提现记录</a></li>
        <li class=""><a href="distribute_title.php">爵位设置</a></li>
        <li class=""><a href="withdraw_method.php">提现方法管理</a></li>
 
      </ul>
    </div>
   
    <script language="javascript">
	$(document).ready(function(){shop_obj.withdraw_method_init();});
	
</script>
    <div id="update_post_tips"></div>
    <div id="user" class="r_con_wrap">
     <br/>
      <table width="100%" align="center" border="0" cellpadding="5" cellspacing="0" class="r_con_table">
        <thead>
          <tr>
            <td width="5%" nowrap="nowrap">序号</td>
            <td width="8%" nowrap="nowrap">微信昵称</td>
            <td width="10%" nowrap="nowrap">店名</td>
             <td width="5%" nowrap="nowrap">金额</td>
            <td width="5%" nowrap="nowrap">类型</td>
            <td width="10%">时间</td>
    
          </tr>
        </thead>
        <tbody>
      
		  
	<?php foreach($record_list->toArray() as $key=>$record):?>
           <tr UserID="<?php echo $record['Record_ID'] ?>">
           	<td><?=$record['Record_ID']?></td>
            <td><?=$account_dropdown[$record['Account_ID']]['User_NickName']?></td>
            <td><?=$account_dropdown[$record['Account_ID']]['Shop_Name']?></td>
            <td><span class="red">&yen;<?=round_pad_zero($record['Record_Money'],2)?></span></td>
            <td><?=$record['Record_Type']==1?'普通代理':'地区代理'?></td>
            <td><?=ldate($record['Record_CreateTime'])?></td>
            
          </tr>
      <?php endforeach; ?>
        </tbody>
      </table>

      <div class="page center-block"><?=$page_links?></div>
    </div>
  </div>
  
  
  
  
  
  
</div>
</div>
</body>
</html>

