<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');

if (isset($_GET["UsersID"])) {
	$UsersID = $_GET["UsersID"];
} else {
	echo '缺少必要的参数';
	exit;
}
if (isset($_GET["level"])) {
	$level = intval($_GET["level"]);
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
if(empty($_SESSION[$UsersID."User_ID"]))
{
  $_SESSION[$UsersID."HTTP_REFERER"]="/api/".$UsersID."/shop/distribute/";
  header("location:/api/".$UsersID."/user/login/");
}

$base_url = base_url();
$shop_url = shop_url();


/*分享页面初始化配置*/
$share_flag = 1;
$signature = '';

//获取本店配置
$rsConfig = shop_config($UsersID);
$dis_agent_type = $rsConfig['Dis_Agent_Type'];

//获取自身树层
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
foreach($posterity_list as $key=>$val){
	foreach($val as $k=>$v){
		$tmp_user = User::Multiwhere(array('Users_ID'=>$UsersID,'User_ID'=>$v['User_ID']))
						->first();
		$posterity_list[$key][$k]['User_Name'] = $tmp_user['User_Name'];
		$posterity_list[$key][$k]['User_Mobile'] = $tmp_user['User_Mobile'];
		$posterity_list[$key][$k]['User_NickName'] = $tmp_user['User_NickName'];
		unset($tmp_user);
	}
}
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>我的人气</title>
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
<style>
.zl_select {
	width: 100%;
	padding: 10px 30px;
	background: #56c39a;
	height: 50px;
	box-sizing: border-box;
}
.zl_select_span{
	border-radius: 5px;
	background: #80d1b3;
	height: 30px;
	overflow: hidden;
}
.zl_select span {
	width: 30%;
	display: inline-block;
	text-align: center;
	color: #fff;
	line-height: 30px;
}
.zl_select .active{
	background: #2a8865;
}
</style>
</head>

<body>
<header class="bar bar-nav">
  <a href="javascript:history.back()" class="fa fa-2x fa-chevron-left grey pull-left"></a>
  <a href="/api/<?=$UsersID?>/shop/distribute/" class="fa fa-2x fa-sitemap grey pull-right"></a>
  <h1 class="title">我的人气</h1>
</header>
<div class="zl_select">
	<div class="zl_select_span">
		<a href="/api/<?=$UsersID?>/shop/distribute/mypopular/1/"><span class="<?php if($level == 1){echo 'active';}?>">一级</span></a>
		<a href="/api/<?=$UsersID?>/shop/distribute/mypopular/2/"><span class="<?php if($level == 2){echo 'active';}?>">二级</span></a>
		<a href="/api/<?=$UsersID?>/shop/distribute/mypopular/3/"><span class="<?php if($level == 3){echo 'active';}?>">三级</span></a>
	</div>
</div>
<table class="table table-bordered" style="background: #fff;margin-top: 10px;">
	<thead>
        <tr>
			<th>级别</th>
			<th style="width:100px;">微信昵称</th>
			<th>姓名</th>
			<th>手机号</th>
        </tr>
	</thead>
		<?php foreach($posterity_list[$level] as $k=>$v): ?>
        <tr>
			<th scope="row"><?=$level?></th>
			<td><?=$v['User_NickName']?></td>
			<td><?=$v['User_Name']?></td>
			<td><?=$v['User_Mobile']?></td>
        </tr>
		<?php endforeach;?>
	</tbody>
</table>

<?php require_once('../distribute_footer.php');?> 
 
</body>
</html>