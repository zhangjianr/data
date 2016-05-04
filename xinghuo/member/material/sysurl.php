<?php 
if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}
$type=empty($_REQUEST['type'])?'shop_category':$_REQUEST['type'];
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $SiteName;?></title>
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
    <link href='/static/member/css/material.css' rel='stylesheet' type='text/css' />
    <script type='text/javascript' src='/static/member/js/material.js'></script>
    <div class="r_nav">
      <ul>
        <li class=""><a href="index.php">图文消息管理</a></li>
        <li class=""><a href="url.php">自定义URL</a></li>
        <li class="cur"><a href="sysurl.php">系统URL查询</a></li>
      </ul>
    </div>
    <div id="url" class="r_con_wrap" style="min-height:500px;">
      <div class="type">
       <a href="#"<?php echo in_array($type,array("shop_category","shop_products")) ? ' class="cur"' : '';?>>微商城
        <span onClick="window.location.href='sysurl.php?type=shop_category';">产品分类</span>
        <span onClick="window.location.href='sysurl.php?type=shop_products';">全部产品</span>
       </a>
	   <a href="sysurl.php?type=spark"<?php echo $type=="spark" ? ' class="cur"' : '';?>>星火草原</a>
	   <a href="sysurl.php?type=distribute"<?php echo $type=="distribute" ? ' class="cur"' : '';?>>分销中心</a>
	   <a href="#"<?php echo in_array($type,array("question_category","question_articles")) ? ' class="cur"' : '';?>>文章
	    <span onClick="window.location.href='sysurl.php?type=question_category';">文章分类</span>
        <span onClick="window.location.href='sysurl.php?type=question_articles';">文章列表</span>
	   </a>
       <a href="sysurl.php?type=wcx"<?php echo $type=="wcx" ? ' class="cur"' : '';?>>微促销</a>
       <a href="sysurl.php?type=user"<?php echo $type=="user" ? ' class="cur"' : '';?>>会员中心</a>
	   <a href="sysurl.php?type=kanjia"<?php echo $type=="kanjia" ? ' class="cur"' : '';?>>微砍价</a>
	   <a href="sysurl.php?type=web"<?php echo $type=="web" ? ' class="cur"' : '';?>>微官网</a>
	   <a href="sysurl.php?type=votes"<?php echo $type=="votes" ? ' class="cur"' : '';?>>微投票</a>
	   <a href="sysurl.php?type=zhongchou"<?php echo $type=="zhongchou" ? ' class="cur"' : '';?>>微众筹</a>
	   
      </div>
      <?php
	   require_once('sysurl/'.$type.'.php');
	  ?>
    </div>
  </div>
</div>
</body>
</html>