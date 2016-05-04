<?php 
if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}
$rsUsers=$DB->GetRs("users","*","where Users_ID='".$_SESSION["Users_ID"]."'");
$starttime = strtotime(date("Y-m-d")." 00:00:00");
$endtime = strtotime(date("Y-m-d")." 23:59:59");

$date = $count_web = $count_shop = array();
for($i=20;$i>0;$i--){
	$fromtime = $starttime-($i-1)*86400;
	$totime = $endtime-($i-1)*86400;
	$date[] = date("m-d", $fromtime);
	$r_web = $DB->GetRs("statistics","count(*) as num","where S_Module='web' and S_CreateTime>=".$fromtime." and S_CreateTime<=".$totime);
	$r_shop = $DB->GetRs("statistics","count(*) as num","where S_Module='shop' and S_CreateTime>=".$fromtime." and S_CreateTime<=".$totime);
	$count_shop[] = intval($r_shop["num"]);
	$count_web[] = intval($r_web["num"]);
}
$Data1[] = array(
	"name" => "微官网访问量",
	"data" => $count_web
);
$Data1[] = array(
	"name" => "微商城访问量",
	"data" => $count_shop
);
$Data = array(
	"count" => $Data1,
	"date" => $date
);

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
    <div class="r_nav">
        <ul>
            <li class=""><a href="fans.php">粉丝数据统计</a></li>
            <li class="cur"><a href="visit.php">页面访问统计</a></li>
            <li class=""><a href="sales.php">微促销参与次数</a></li>
            <li class=""><a href="user.php">会员注册统计</a></li>
            <li class=""><a href="user_area.php">会员来源地统计</a></li>
        </ul>
	</div>
    <script type='text/javascript' src='/static/js/plugin/highcharts/highcharts.js'></script>
    <script type='text/javascript' src='/static/member/js/statistics.js' ></script>
    <link href='/static/member/css/statistics.css' rel='stylesheet' type='text/css' />
    <script language="javascript">
    var chart_data=<?php echo json_encode($Data,JSON_UNESCAPED_UNICODE);?>;
    $(document).ready(statistics_obj.stat_init);
    </script>
    <div class="r_con_wrap">
    	<div class="chart_btn"><a href="javascript:void(0);" class="tab_bar">切换<span>曲线图</span></a></div>
        <div class="chart"></div>
    </div>
  </div>
</div>
</body>
</html>