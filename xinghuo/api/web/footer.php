<div id="global_support_point"></div><div id="global_support"><div class="bg"></div><?php echo $Copyright;?></div>
<?php
if(!empty($kfConfig["KF_Code"])){
$kfConfig["KF_Code"] = htmlspecialchars_decode($kfConfig["KF_Code"],ENT_QUOTES);
?>
<?php echo $kfConfig["KF_Code"];?>
<?php }?>

<img src='/static/api/images/cover_img/web.jpg' class='shareimg'/>
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
		   img_url:"http://<?php echo $_SERVER["HTTP_HOST"];?>/static/api/images/cover_img/web.jpg"
		};
		$(document).ready(global_obj.share_init_config);
	</script>
<?php }?>
</body>
</html>