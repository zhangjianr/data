<style>
.cart{position:relative;}
.cart b{background:red; border-radius: 50%;display: block;height: 15px;position: absolute;right: 20px;top: 5px;width: 15px;font-size:12px;text-align:center;line-height:15px;color:#ffffff;}
</style>
<?php require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/substribe.php');?>
<?php
 	$flag = false;
	if($rsConfig['Distribute_Type'] > 0){
		if(!empty($_SESSION[$UsersID.'User_ID'])){ 
			
			$User_ID = empty($_SESSION[$UsersID.'User_ID']) ? 0 : $_SESSION[$UsersID.'User_ID'];
			
			$rsUser=$DB->GetRs("user","Is_Distribute","where Users_ID='".$UsersID."' and User_ID=".$User_ID);
			if($rsUser['Is_Distribute'] == 1){
				$flag = true;
			}
		
		}
	}else{
		$flag = true;
	}
	/*
 	if(!empty($owner) && empty($share_desc)){
		if(!empty($owner['shop_announce'])){
			$share_desc = $owner['shop_announce'];
		}
	}
	*/
 ?>
<?php if(isset($show_support)):?>
<div class="support">汇美令平台 版权所有<?php //echo $Copyright;?></div>
<?php endif;?>
<div id="footer_points"></div>
<footer id="footer">  
  <ul class="list-group" id="footer-nav">
    	<li class="home"><a href="<?=$shop_url?>">首页</a></li>
		<?php if($flag):?>
		<li class="sitemap"><a href="<?=$shop_url.'distribute/'?>">分销中心</a></li>
		<?php else:?>
		<li class="sitemap"><a href="<?=$shop_url.'distribute/join/'?>">我要分销</a></li>
		<?php endif;?>
        
		<li class="cart"><a href="/api/<?=$UsersID?>/shop/cart/">购物车</a>
		<?php 
			$car_num = '';
			if(!empty($_COOKIE[$UsersID.'car_num']) && !empty($_SESSION[$UsersID.'CartList'])){
			    $car_num = $_COOKIE[$UsersID.'car_num'];
		    }
		?>
		<b <?php if(empty($car_num)){?>style="display:none"<?php }?>><?php echo $car_num;?></b>
		</li>
		<li class="user"><a href="<?=$shop_url.'member/'?>">个人中心</a></li>
  </ul>
</footer>
 
<?php
$KfIco = '';
$kfConfig=$DB->GetRs("kf_config","*","where Users_ID='".$UsersID."' and KF_IsShop=1 and KF_Code<>''");
$kfConfig["KF_Code"] = htmlspecialchars_decode($kfConfig["KF_Code"],ENT_QUOTES);
?>

<?php if(!empty($kfConfig)){?>
<?php echo $kfConfig["KF_Code"];?>
<?php }?>
<?php if($rsConfig["CallEnable"] && $rsConfig["CallPhoneNumber"]){?>
<script language='javascript'>var shop_tel='<?php echo $rsConfig["CallPhoneNumber"];?>';</script>
<script type='text/javascript' src='/static/api/shop/js/tel.js?t=<?php echo time();?>'></script>
<?php }?>
<?php if($share_flag==1 && $signature<>""){?>
	<script language="javascript">
		var share_config = {
		   appId:"<?php echo $share_user["Users_WechatAppId"];?>",   
		   timestamp:<?php echo $timestamp;?>,
		   nonceStr:"<?php echo $noncestr?>",
		   url:"<?php echo $url?>",
		   signature:"<?php echo $signature;?>",
		   title:"<?php echo empty($share_title) ? $rsConfig["ShopName"] : $share_title;?>",
		   desc:"<?php echo empty($share_desc) ? $rsConfig["ShopName"] : str_replace(array("\r\n", "\r", "\n"), "", $share_desc);?>",
		   img_url:"<?php echo empty($share_img) ? $rsConfig["ShopLogo"] : $share_img;?>",
		   link:"<?php echo empty($share_id) ? (empty($share_link) ? '' : $share_link) : 'http://'.$_SERVER['HTTP_HOST'].'/api/'.$UsersID.'/'.(empty($owner["id"]) ? '' : $owner["id"].'/').'share_recieve/'.$share_id.'/'; ?>"
		};
		
		$(document).ready(global_obj.share_init_config);
	</script>
<?php }?>
<div class='conver_favourite'><img src="/static/api/images/global/share/favourite.png" /></div>
