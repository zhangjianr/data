<?php 
if(empty($_SESSION["Users_Account"]))
{
	header("location:/member/login.php");
}
$Data = array();
$DB->Get("user","count(*) as num,User_Province","where Users_ID='".$_SESSION["Users_ID"]."' group by User_Province");
while($rsUser=$DB->fetch_assoc()){
	$provice = array();
	$provice[] = empty($rsUser["User_Province"]) ? "其他" : $rsUser["User_Province"];
	$provice[] = intval($rsUser["num"]);
	$Data[] = $provice;
}

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
            <li class=""><a href="visit.php">页面访问统计</a></li>
            <li class=""><a href="sales.php">微促销参与次数</a></li>
            <li class=""><a href="user.php">会员注册统计</a></li>
            <li class="cur"><a href="user_area.php">会员来源地统计</a></li>
        </ul>
	</div>
    <script type='text/javascript' src='/static/js/plugin/highcharts/highcharts.js'></script>
    <script type='text/javascript' src='/static/member/js/statistics.js' ></script>
    <link href='/static/member/css/statistics.css' rel='stylesheet' type='text/css' />
    <script language="javascript">
    var pie_data=<?php echo json_encode($Data,JSON_UNESCAPED_UNICODE);?>;
    $(document).ready(global_obj.chart_pie);
    </script>
    <div class="r_con_wrap">
        <div class="chart"></div>
    </div>
  </div>
</div>
</body>
</html>