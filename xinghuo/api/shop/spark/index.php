<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/include/helper/distribute.php');

/* 分享页面初始化配置 */
$share_flag = 1;
$signature = '';
$shop_url = shop_url();
if (isset($_GET["UsersID"])) {
	$UsersID = $_GET["UsersID"];
} else {
	exit('缺少必要的参数');
}
if (!empty($_SESSION[$UsersID . "User_ID"])) {
	$userexit = $DB->GetRs("user", "*", "where User_ID=" . $_SESSION[$UsersID . "User_ID"] . " and Users_ID='" . $UsersID . "'");
	if (!$userexit) {
		$_SESSION[$UsersID . "User_ID"] = "";
	}
}
$owner = getOwner($DB,$UsersID);
$is_login=1;
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');

//获取本店配置
$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$rsUser=$DB->GetRs("user","*","where Users_ID='{$UsersID}' AND User_ID=".$_SESSION[$UsersID."User_ID"]);

//业务开始
$userInfo = $DB->GetR("spark_user", "*", " WHERE Users_ID='{$UsersID}' AND User_ID='{$_SESSION[$UsersID . "User_ID"]}'");
//读取产品包
$lists = $DB->GetS("spark_package", "*", " WHERE Users_ID='{$UsersID}'");
//读取轮播图
$slide = $DB->GetS("spark_slide", "*", " WHERE Users_ID='{$UsersID}' ORDER BY displayOrder DESC, id ASC");
//读取配置
$sparkConfig=$DB->GetRs("spark_config","*","where Users_ID='".$UsersID."'");
?>
<!doctype html>
<html class="no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="email=no"/>
	<title><?php echo !empty($sparkConfig['sparkTitle']) ? $sparkConfig['sparkTitle'] : "购买代理";?></title>
	<link rel="stylesheet" type="text/css" href="/static/spark/css/common.css?<?=$cacheVer?>"/>
	<link rel="stylesheet" type="text/css" href="/static/css/font-awesome.css?<?=$cacheVer?>">
	<link rel="stylesheet" type="text/css" href="/static/spark/css/app.css?<?=$cacheVer?>"/>
	<script src="/static/spark/js/zepto.min.js"></script>
	<script src="/static/spark/js/slide_left.js"></script>
	<script src="/static/spark/js/app.js"></script>
</head>
<body style="background:#F6F6F6;">
	<div class="commonHead" style="background: #0085d0;color:#fff">
		<i class="fa fa-angle-left commonBack"></i>
		<h3 class="text-overflow_1"><?php echo !empty($sparkConfig['sparkTitle']) ? $sparkConfig['sparkTitle'] : "购买代理";?></h3>
	</div>
	<div id="spark_slide">
		<div class="hd">
			<ul></ul>
		</div>
		<div class="bd">
			<ul>
				<?php foreach ($slide as $key=>$val): ?>
				<li><a href="<?=$val['url']?>"><img src="<?=$val['thumb']?>" /></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<?php if(empty($userInfo)): ?>
	<div class="spark_content">
		<ul class="clr">
			<li class="flex">
				<h4>产&nbsp&nbsp&nbsp&nbsp品</h4>
				<div class="flex_1">
					<?php foreach($lists as $key=>$val): ?>
					<span class="<?php echo ($key == "0") ? "active" : "";?>" item="{'id':'<?=$val['id']?>','price':'<?=$val['price']?>','levelName':'<?=$val['levelName']?>'}"><?=$val['levelName']?></span>
					<?php endforeach; ?>
				</div>
			</li>
			<li class="flex">
				<h4>姓&nbsp&nbsp&nbsp&nbsp名</h4>
				<div class="flex_1">
					<input type="text" name="realName" placeholder="提现需要微支付绑定的姓名" />
				</div>
			</li>
			<li class="flex">
				<h4>微信号</h4>
				<div class="flex_1">
					<input type="text" name="nickName" placeholder="请输入微信号" />
				</div>
			</li>
			<li class="flex">
				<h4>手机号</h4>
				<div class="flex_1">
					<input type="text" name="mobile" placeholder="请输入11位手机号" />
				</div>
			</li>
			<li class="flex">
				<h4>住&nbsp&nbsp&nbsp&nbsp址</h4>
				<div class="flex_1">
					<input type="text" name="address" placeholder="请输入精确的收货地址" />
				</div>
			</li>
			<?php if ($sparkConfig["isShop"] == "1" && $rsUser['Owner_Id'] == "0" && $sparkConfig["nickNameId"] != $rsUser["User_No"]): ?>
			<li class="flex">
				<h4>邀请码</h4>
				<div class="flex_1">
					<input type="text" name="intCode" placeholder="请输入推荐人的邀请码" />
				</div>
			</li>
			<?php endif; ?>
		</ul>
		<div class="spark_agree"><i class="fa fa-check-square-o"></i><a href="<?=$shop_url?>spark/agree/">我已阅读并接受此协议</a></div>
	</div>
	<div style="height:50px"></div>
	<div class="spark_buy flex">
		<div class="flex_1 spark_price">价格 ￥<span><?=$lists["0"]['price']?></span></div>
		<button class="spark_submit" onclick="submitOrder()">立即购买</button>
	</div>
	
	<?php else: ?>
	
	<div class="spark_content">
		<ul class="clr">
			<li class="flex">
				<h4>产&nbsp&nbsp&nbsp&nbsp品</h4>
				<div class="flex_1">
					<?php
						foreach($lists as $key=>$val):
						if($userInfo['price'] >= $val['price']){
							unset($lists[$key]);
							continue;
						}
					?>
					<span class="" item="{'id':'<?=$val['id']?>','price':'<?=$val['price']-$userInfo['price']?>','levelName':'<?=$val['levelName']?>'}"><?=$val['levelName']?></span>
					<?php endforeach; ?>
				</div>
			</li>
			<li class="flex">
				<h4>姓&nbsp&nbsp&nbsp&nbsp名</h4>
				<div class="flex_1">
					<input type="text" name="realName" value="<?=$userInfo['realName']?>" placeholder="提现需要微支付绑定的姓名" />
				</div>
			</li>
			<li class="flex">
				<h4>微信号</h4>
				<div class="flex_1">
					<input type="text" name="nickName" value="<?=$userInfo['nickName']?>" placeholder="请输入微信号" />
				</div>
			</li>
			<li class="flex">
				<h4>手机号</h4>
				<div class="flex_1">
					<input type="text" name="mobile" value="<?=$userInfo['mobile']?>" placeholder="请输入11位手机号" />
				</div>
			</li>
			<li class="flex">
				<h4>住&nbsp&nbsp&nbsp&nbsp址</h4>
				<div class="flex_1">
					<input type="text" name="address" value="<?=$userInfo['address']?>" placeholder="请输入精确的收货地址" />
				</div>
			</li>
		</ul>
		<div class="spark_agree"><i class="fa fa-check-square-o"></i><a href="<?=$shop_url?>spark/agree/">我已阅读并接受此协议</a></div>
	</div>
	<div style="height:50px"></div>
	<div class="spark_buy flex" style="display: none;">
		<div class="flex_1 spark_price">差价金额 ￥<span></span></div>
		<button class="spark_submit" onclick="submitOrder()">立即升级</button>
	</div>
	<div class="spark_up" style="<?=empty($slide)?"50px":"250px"?>">
		<p>您当前购买的套餐是：<?php echo $userInfo['packageLevelName'];?></p>
		<ul>
			<?php if(count($lists) == "0"):?><li>当前最高等级</li><?php else: ?><li onclick="upPackage()">升级套餐</li><?php endif;?>
			<a href="<?=$shop_url?>spark/my/"><li><?php echo !empty($sparkConfig['myTitle']) ? $sparkConfig['myTitle'] : "家族中心";?></li>
		</ul>
	</div>
	<?php endif; ?>
	
	<script type="text/javascript">
		TouchSlide({
			slideCell:"#spark_slide",
			titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
			mainCell:".bd ul",
			effect:"left",
			autoPlay:true,//自动播放
			autoPage:true //自动分页
		});
		$(function(){
			$(".spark_content span").click(function(){
				var item = eval("("+$(this).attr('item')+")");
				$(".spark_content span").removeClass("active");
				$(this).addClass("active");
				$(".spark_buy span").text(item.price);
			});
			
			$(".spark_agree i").click(function(){
				if($(this).hasClass("fa-square-o")){
					$(this).removeClass("fa-square-o").addClass("fa-check-square-o");
				}else{
					$(this).removeClass("fa-check-square-o").addClass("fa-square-o");
				}
			});
		});

		function submitOrder(){
			if($(".spark_agree i").hasClass("fa-square-o")){
				alert("请先同意用户协议");
				return;
			}
			var item = eval("("+$(".spark_content ul li span.active").attr("item")+")");
			var packageId = item.id;
			var realName = $("input[name=realName]").val();
			var nickName = $("input[name=nickName]").val();
			var mobile = $("input[name=mobile]").val();
			var address = $("input[name=address]").val();
                       
			if(packageId == ""){
				alert("请选择开通权限");
				return;
			}
			if(realName == ""){
				alert("姓名不能为空");
				$("input[name=realName]").focus();
				return;
			}
			if(nickName == ""){
				alert("微信号不能为空");
				$("input[name=nickName]").focus();
				return;
			}
			var isMobile = /^1\d{10}$/;
			if(!isMobile.test(mobile)){
				alert("请输入正确的11位手机号");
				$("input[name=mobile]").focus();
				return;
			}
			if(address == ""){
				alert("地址不能为空");
				$("input[name=address]").focus();
				return;
			}
			var isShop = "<?php echo $sparkConfig["isShop"]; ?>";
			if(isShop == "1"){
				var intCode = $("input[name=intCode]").val();
				if(intCode == ""){
					alert("邀请码不能为空");
					$("input[name=intCode]").focus();
					return;
				}
			}
			$(".spark_submit").attr('disabled',"true");
			$(".spark_submit").addClass("disabled");
			$(".spark_submit").text("正在提交");
                        if(intCode == "" || intCode == undefined ){
                            $.post(
                                            "<?=shop_url()?>/spark/ajax/",
                                            {action : "checkout", packageId : packageId, realName : realName, nickName : nickName, mobile : mobile, address : address},
                                            function(data){
                                                    if(data.type == "success"){
                                                            window.location.href=data.redirectUrl;
                                                    }else{
                                                            alert(data.msg);
                                                            location.reload();
                                                            return;
                                                    }
                                            },
                                            "json"
                            );
                        }else{
                                    $.post(
                                            "<?=shop_url()?>/spark/ajax/",
                                            {action : "checkout", packageId : packageId, realName : realName, nickName : nickName, mobile : mobile, address : address,intCode:intCode},
                                            function(data){
                                                    if(data.type == "success"){
                                                            window.location.href=data.redirectUrl;
                                                    }else{
                                                            alert(data.msg);
                                                            location.reload();
                                                            return;
                                                    }
                                            },
                                            "json"
                            );
                        }
		}
	</script>
</body>
</html>