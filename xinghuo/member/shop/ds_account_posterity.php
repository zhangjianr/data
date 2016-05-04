<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/Zebra_Pagination.php');

require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');


$base_url = base_url();

if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}

$rsConfig=$DB->GetRs("user_config","UserLevel","where Users_ID='".$_SESSION["Users_ID"]."'");

$dis_title_level = get_dis_pro_title($DB,$_SESSION['Users_ID']);

if(!empty($_GET['User_ID'])){
	$User_ID = $_GET['User_ID'];
}else{
	echo '缺少分销账户ID';
	exit();
}

$condition = "where Users_ID = '".$_SESSION["Users_ID"]."' AND User_ID=".$User_ID;
$rsAccount = $DB->getRs('shop_distribute_account',"*",$condition);

$ds_obj  = new Distribute($DB, $_SESSION['Users_ID']);

//获取下属分销商列表并统计数量
$posterity_list = $ds_obj->get_posterity($User_ID);
$posterity_count = 0;

foreach ($posterity_list as $key => $sub_list) {
	$posterity_count += count($sub_list);
}

//获取营销会员的昵称
$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Is_Distribute = 1";
$rsUsers = $DB->get('user','User_ID,User_NickName',$condition);
$user_list = $DB->toArray($rsUsers);

//生成drop_down数组
$user_dropdown = array();
foreach($user_list as $key=>$user){
 	$user_dropdown[$user['User_ID']] = $user['User_NickName'];	
}



if(!empty($_GET['level'])){
	$level = $_GET['level'];
}else{
	$level = 1;
}

$account_list = $posterity_list[$level];
//初始化分页类
$records_per_page = 15;
$total_num = count($account_list);
$pagination = new Zebra_Pagination();
$pagination->records($total_num);
$pagination->records_per_page($records_per_page);

$account_list = array_slice(
            $account_list,                                             //  from the original array we extract
            (($pagination->get_page() - 1) * $records_per_page),    //  starting with these records
            $records_per_page                                       //  this many records
);

$total_pages = $pagination->_properties['total_pages'];
$cur_page = $pagination->_properties['page'];  
$href_template = '?User_ID='.$User_ID.'&level='.$level.'&page={{number}}';

?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link href='/static/css/bootstrap.css' rel='stylesheet' type='text/css' />
<link href='/static/style.css' rel='stylesheet' type='text/css' />
<link href='/static/css/global.css' rel='stylesheet' type='text/css' />
<link href='/static/member/css/main.css' rel='stylesheet' type='text/css' />



<script type='text/javascript' src='/static/js/jquery-1.7.2.min.js'></script>
<script type='text/javascript' src='/static/js/jquery.twbsPagination.min.js'></script>
<script type='text/javascript' src='/static/member/js/global.js'></script>
<script type='text/javascript'>
$(document).ready(function(){
		$('#pagination').twbsPagination({
       totalPages:<?=$total_pages?>,
       visiblePages: 7,
        href: '<?=$href_template?>',
       onPageClick: function (event, page) {
           $('#page-content').text('Page ' + page);
       }
   });
});

	
</script>

<!--[if lte IE 9]><script type='text/javascript' src='/static/js/plugin/jquery/jquery.watermark-1.3.js'></script>
<![endif]-->

<div id="iframe_page">
  <div class="iframe_content">
	
    <div class="r_nav">
      <ul>

        <li class="cur"> <a href="distributes.php">分销账号管理</a> </li>
        <li class=""><a href="distribute_record.php">分销记录</a></li>
        <li class=""><a href="withdraw_record.php">提现记录</a></li>
        <li class=""><a href="distribute_title.php">爵位设置</a></li>
        <li class=""><a href="withdraw_method.php">提现方法管理</a></li>
        
        
      </ul>
    </div>
    
   
    <div class="r_con_wrap">
	 	<h3>下属分销商列表</h3>
     
    <!-- level filter begin -->
    	<div class="btn-group" id="level_filter">
          
  			<a  class="btn btn-default <?=($level == 1)?'cur':''?>" href="?User_ID=<?=$User_ID?>&level=1">一级分销商</a>
  			<a  class="btn btn-default <?=($level == 2)?'cur':''?>" href="?User_ID=<?=$User_ID?>&level=2">二级分销商</a>
  			<a  class="btn btn-default <?=($level == 3)?'cur':''?>" href="?User_ID=<?=$User_ID?>&level=3">三级分销商</a>
         
		</div>
        <p>共<?=$total_pages?>页,<?=$total_num?>个,当前第<?=$cur_page?>页</p>
    <!-- level filter end -->
   
    	<div id="level_filter_panel">
        	<table class="mytable" border="0" cellpadding="0" cellspacing="0" width="100%">
          <tbody><tr bgcolor="#f5f5f5">
            <td width="50" align="center">#序号</td>
            <td width="50" align="center"><strong>微信昵称</strong></td>
            <td width="100" align="center"><strong>店名</strong></td>
            <td width="80" align="center"><strong>佣金余额</strong></td>
            <td width="100" align="center"><strong>审核状态</strong></td>
            <td width="100" align="center"><strong>总收入</strong></td>
            <td width="100" align="center"><strong>加入时间</strong></td>
            
          </tr>
          		  <?php $i=1; ?>
                  <?php foreach($account_list as $key=>$item):?>
                  	<tr onmouseover="this.bgColor='#D8EDF4';" onmouseout="this.bgColor='';" bgcolor="">
            			<td align="center" userid=<?=$item['User_ID']?>><?=$i?></td>
            			<td align="center"><?=!empty($user_dropdown[$item['User_ID']])?$user_dropdown[$item['User_ID']]:'<span class="red">信息缺失</span>'?></td>
             			<td align="center"><?=$item['Shop_Name']?></td>
            			<td align="center">&yen;<?=$item['balance']?></td>
            			<td align="center"><?=$item['Is_Audit']?'已通过':'未通过'?></td>
            			<td align="center">&yen;<?=$item['Total_Income']?></td>
            			<td align="center"><?=ldate($item['Account_CreateTime'])?></td> 
                    	
          			</tr>
                    <?php $i++; ?>
                <?php endforeach ;?>   	
                  
                    
                  </tbody></table>

        </div>
 		
        <div id="pagination_container">
        	<ul id="pagination" class="pagination-sm"></ul>
		</div>
        
    </div>
    
  </div>
</div>
</body>
</html>
