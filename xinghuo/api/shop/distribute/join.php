<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
$base_url = base_url();
$shop_url = shop_url();

/*分享页面初始化配置*/
$share_flag = 1;
$signature = '';

if(isset($_GET["UsersID"])){
  $UsersID = $_GET["UsersID"];
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

if(!strpos($_SERVER['REQUEST_URI'],"mp.weixin.qq.com")){
  header("location:?wxref=mp.weixin.qq.com");
}

$rsConfig =$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$rsUser = $DB->GetRs("user","*","where User_ID=".$_SESSION[$UsersID."User_ID"]);


if($rsUser['Is_Distribute'] == 1){
	echo '您已经是分销商了，请不要重复申请';
	exit();
}
/*edit by xpc*/
$error_msg = pre_add_distribute_account($DB,$UsersID);
if($error_msg=='OK'){
	header("location:/api/" . $UsersID . "/shop/distribute/");
	exit;
}
if($error_msg =="5"){
    header("location:" . $shop_url . "spark/index/");
    exit;
}

/*edit by xpc end*/
$owner = getOwner($DB,$UsersID);

//调取最近文章列表
$condition = "where Users_ID='".$UsersID."' order by Article_CreateTime desc";
$rsArticles = $DB->getPage('shop_articles','*',$condition,4);
$article_list = $DB->toArray($rsArticles);

?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>申请成为分销商</title>
 <link href="/static/css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="/static/css/font-awesome.css">
    <link href="/static/api/distribute/css/style.css" rel="stylesheet">
     <link href="/static/api/distribute/css/apply_distribute.css" rel="stylesheet">
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/static/js/jquery-1.11.1.min.js"></script>
	<script type='text/javascript' src='/static/js/jquery.validate.js'></script>
    <script type='text/javascript' src='/static/api/js/global.js'></script>
    <script src="/static/api/distribute/js/distribute.js"></script>
  
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script language="javascript">
	jQuery.extend(jQuery.validator.messages, {  
       	 	required: "必须填写",  
			email: "请输入正确格式的电子邮件",  
			url: "请输入合法的网址",  
			date: "请输入合法的日期",  
			dateISO: "请输入合法的日期 (ISO).",  
			number: "请输入合法的数字",  
			digits: "只能输入整数",  
			creditcard: "请输入合法的信用卡号",  
			equalTo: "请再次输入相同的值",  
			accept: "请输入拥有合法后缀名的字符串",  
			maxlength: jQuery.validator.format("请输入一个长度最多是 {0} 的字符串"),  
			minlength: jQuery.validator.format("请输入一个长度最少是 {0} 的字符串"),  
			rangelength: jQuery.validator.format("请输入一个长度介于 {0} 和 {1} 之间的字符串"),  
			range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),  
			max: jQuery.validator.format("请输入一个最大为 {0} 的值"),  
			min: jQuery.validator.format("请输入一个最小为 {0} 的值")  
	});  

	var base_url = '<?=$base_url?>';
	var UsersID = '<?=$UsersID?>';
	$(document).ready(distribute_obj.apply_distribute);

</script>

</head>

<body>
<div class="wrap">
	<div class="container">
     <div class="row">
      <div class="apply-image">
      	<img  width="100%" src="<?php echo $rsConfig["ApplyBanner"] ? $rsConfig["ApplyBanner"] : '/static/api/distribute/images/apply_distribute.png';?>" />
      </div>
     </div>
     <?php if($error_msg=='1'){?>
     <div style="padding:8px; font-size:14px; line-height:24px;">本店累计积满<span style="padding:0px 2px; font-size:16px; font-family:'Times New Roman'; color:#F60"><?php echo $rsConfig["Distribute_Limit"];?></span>积分，才可申请成为分销商，您已积满<span style="padding:0px 2px; font-size:16px; font-family:'Times New Roman'; color:#F60"><?php echo $rsUser["User_TotalIntegral"];?></span>积分，马上赚积分</div><a href="<?php echo $shop_url;?>" style="display:block; margin:8px auto; text-align:center; width:100%; height:34px; line-height:32px; border-radius:5px; background:#3396fe; color:#FFF; font-size:14px;">继续购物</a></div>
     <?php }elseif($error_msg=='2'){?>
     <div style="padding:8px; font-size:14px; line-height:24px;">本店累计消费满<span style="padding:0px 2px; font-size:16px; font-family:'Times New Roman'; color:#F60"><?php echo $rsConfig["Distribute_Limit"];?></span>元，才可申请成为分销商，您已累计消费<span style="padding:0px 2px; font-size:16px; font-family:'Times New Roman'; color:#F60"><?php echo $rsUser["User_Cost"];?></span>元，马上消费</div><a href="<?php echo $shop_url;?>" style="display:block; margin:8px auto; text-align:center; width:100%; height:34px; line-height:32px; border-radius:5px; background:#3396fe; color:#FFF; font-size:14px;">继续购物</a></div>
     <?php }elseif($error_msg=='3'){
		 $products = $DB->GetRs("shop_products","Products_Name","where Users_ID='".$UsersID."' and Products_ID=".$rsConfig["Distribute_Limit"]);
	?>
    <div style="padding:8px; font-size:14px; line-height:24px;">本店需购买<span style="padding:0px 2px; font-size:16px; font-family:'Times New Roman'; color:#F60"><?php echo $products["Products_Name"];?></span>，才可申请成为分销商，马上购买</div><a href="<?php echo $shop_url;?>products_virtual/<?php echo $rsConfig["Distribute_Limit"];?>/" style="display:block; margin:8px auto; text-align:center; width:100%; height:34px; line-height:32px; border-radius:5px; background:#3396fe; color:#FFF; font-size:14px;">马上购买</a></div>
    <?php }elseif($error_msg=='5'){ ?>
     <div style="padding:8px; font-size:14px; line-height:24px;">本店需购买<span style="padding:0px 2px; font-size:16px; font-family:'Times New Roman'; color:#F60">星火代理</span>，才可申请成为分销商，马上购买</div><a href="<?php echo $shop_url;?>spark/index/" style="display:block; margin:8px auto; text-align:center; width:100%; height:34px; line-height:32px; border-radius:5px; background:#3396fe; color:#FFF; font-size:14px;">马上购买</a></div>
     <?php }else{?>
        <div class="row apply_title">
         	 <div style="padding:8px 8px 10px 8px; font-size:14px; color:#323232; line-height:24px;">填写申请信息<br />推荐人：<?php echo $owner["shop_name"] ? '<font style="color:#F60">'.$owner["shop_name"].'</font>' : '<font style="color:#F60">总店</font>';?></div>
           	 <div>
            	<ul class="list-group" id="apply_form_panel">
 <form action="/api/<?=$UsersID?>/shop/distribute/ajax/" id="join-distribute-form"/>
 
  <input type="hidden" name="action"  value="join"/>
  <li class="list-group-item">
     <label>姓名</label>&nbsp;&nbsp;<input type="text" name="Real_Name"  value="" placeholder="请输入你您的姓名" />
  </li>
  
  <li class="list-group-item">
     <label>手机</label>&nbsp;&nbsp;<input type="text" name="User_Mobile" value="<?=$rsUser['User_Mobile']?>" placeholder="请输入您的手机号码" />
  </li>

  <li class="list-group-item  text-center" style="margin:0px; padding:10px 0px">
  	 <a href="javascript:void(0)" id="submit-btn" class="btn btn-default">提交申请</a>
  </li>

</form>
</ul>
            </div>
        </div>
        <?php }?>
    </div>
</div>

 
<?php require_once('../distribute_footer.php');?> 
 
</body>
</html>

