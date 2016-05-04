
<img src='/static/api/images/cover_img/shop.jpg' class='shareimg'/>
<script language="javascript">$(document).ready(function(){$('#global_support').css('bottom', 0);});</script>
<div id="global_support_point"></div>
<div id="global_support">
<div class="bg"></div>
<?php echo $Copyright;?>
</div>
<?php
$KfIco = '';
$kfConfig=$DB->GetRs("kf_config","*","where Users_ID='".$UsersID."' and KF_IsShop=1 and KF_Code<>''");
$kfConfig["KF_Code"] = htmlspecialchars_decode($kfConfig["KF_Code"],ENT_QUOTES);
?>

<?php if(!empty($kfConfig)){?>
<?php echo $kfConfig["KF_Code"];?>
<?php }?>
<?php if($share_flag==1 && $signature<>""){?>
	<script language="javascript">
		var share_config = {
		   appId:"<?php echo $share_user["Users_WechatAppId"];?>",		   
		   timestamp:<?php echo $timestamp;?>,
		   nonceStr:"<?php echo $noncestr?>",
		   url:"<?php echo $url?>",
		   signature:"<?php echo $signature;?>",
		   title:"<?php echo $share_title;?>",
		   desc:"<?php echo $share_desc;?>",
		   img_url:"<?php echo $share_img;?>",
		   link:"<?php echo $share_link;?>"
		};
		$(document).ready(global_obj.share_init_config);
	</script>
<?php }?>
