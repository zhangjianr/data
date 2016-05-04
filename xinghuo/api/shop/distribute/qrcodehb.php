<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/Curl.php');
$need_gegerate = 0;
$base_url = base_url();
$shop_url = shop_url();
if(isset($_GET["UsersID"])){
  $UsersID=$_GET["UsersID"];
}else{
  echo '缺少必要的参数';
  exit;
}

$rsConfig = $DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
$Shop_Name = $rsConfig["ShopName"];


$is_login=1;


$owner = getOwner($DB,$UsersID);
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/wechatuser.php');
$owner = getOwner($DB,$UsersID);

if($owner['id'] != '0'){
	$rsConfig["ShopName"] = $owner['shop_name'];
	$rsConfig["ShopLogo"] = $owner['shop_logo'];
	$shop_url = $shop_url.$owner['id'].'/';
};

//获取此用户的用户信息
$rsUser=$DB->GetRs("user","User_NickName,Is_Distribute","where User_ID=".$_SESSION[$UsersID."User_ID"]);

	//获取此用户分销账号信息

if($rsUser['Is_Distribute'] == 1){
	
	$poster_path = $_SERVER["DOCUMENT_ROOT"].'/data/poster/'.$UsersID.$owner['id'].'.png';	
	$poster_web_path = '/data/poster/'.$UsersID.$owner['id'].'.png';	

	$rsAccount  = $DB->GetRs("shop_distribute_account","*","where Users_ID='".$UsersID."' and User_ID= '".$_SESSION[$UsersID.'User_ID']."'");
	
	//判断是否需要生成海报
	$need_gegerate = !is_file($poster_path)||$rsAccount['Is_Regeposter'];
	
	if($need_gegerate){
		if($rsAccount["Shop_Logo"]){
	   		if(strpos($rsAccount["Shop_Logo"],'http://') !== false){
				$curl = new Curl();
				define('UPLOAD_DIR', $_SERVER["DOCUMENT_ROOT"].'/data/avatar/');
				$file_name = $UsersID.$owner['id'].'.jpg';
				$file_path = UPLOAD_DIR.$file_name;
				
				if(!is_file($file_path)){	
		    		put_file_from_url_content($rsAccount["Shop_Logo"],$UsersID.$owner['id'].'.jpg',UPLOAD_DIR);
				}
				
				$rsAccount["Shop_Logo"] = '/data/avatar/'.$file_name;
			}
		}else{
			$rsAccount["Shop_Logo"] =  '/static/api/images/user/face.jpg';
		}
		
		require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/weixin_qrcode.class.php');
		$weixin_qrcode = new weixin_qrcode($DB,$UsersID);
		$qrcode_path = $weixin_qrcode->get_qrcode("user_".$owner['id']);
		
	}
}

//自定义分享信息
$share_link = $shop_url;
require_once('../../share.php');
if($owner['id'] != '0' && $rsConfig["Distribute_Customize"]==1){
	$share_desc = $owner['shop_announce'] ? $owner['shop_announce'] : $rsConfig["ShareIntro"];
	$share_img = strpos($owner['shop_logo'],"http://")>-1 ? $owner['shop_logo'] : 'http://'.$_SERVER["HTTP_HOST"].$owner['shop_logo'];
}else{
	$share_desc = $rsConfig["ShareIntro"];
	$share_img = strpos($rsConfig['ShareLogo'],"http://")>-1 ? $rsConfig['ShareLogo'] : 'http://'.$_SERVER["HTTP_HOST"].$rsConfig['ShareLogo'];
}



?>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<meta content="telephone=no" name="format-detection" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
 <title>推广二维码</title>
 
<script type="text/javascript" src="/static/js/jquery-1.11.1.min.js"></script>       
<script type='text/javascript' src='/static/api/js/global.js?t=<?php echo time();?>'></script> 
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
		   link:"<?php echo empty($share_link) ? '' : $share_link;?>"
		};
		
		$(document).ready(global_obj.share_init_config);
	</script>
<?php }?>
    
</head>


<script language="javascript">



</script>

<style type="text/css">
body{position:relative; padding:0px; margin:0px}
.bg{position:absolute; top:0px; left:0px; width:100%; z-index:-1}
.owner_info{width:90%; margin:0px auto; height:60px;padding-top:10px;}
.owner_info .content{position:relative; height:60px;}
.owner_info img{display:block; width:50px; height:50px; margin:0px auto; position:absolute; left:5px; top:5px}
.owner_info p{padding:0px; margin:0px 0px 0px 60px; height:44px; overflow:hidden; padding-top:7px; line-height:22px; color:#000; font-size:14px; font-family:"微软雅黑";}
.owner_info p span{color:#700000}
.qrcode{width:100%; position:absolute; left:0px}
.qrcode img{display:block; margin:0px auto; width:50%;margin-top:230px;}
</style>
 
        
<body>

<?php if($rsUser['Is_Distribute']): ?>
	
    <?php if($rsAccount["status"]):?>
    	<?php if($need_gegerate):?>  
    		<img src="<?php echo $rsConfig["QrcodeBg"] ? $rsConfig["QrcodeBg"] : '/static/api/distribute/images/qrcode_bg.jpg';?>" class="bg">
			<div class="owner_info">
              <div class="content">
				<img src="<?php echo $rsAccount["Shop_Logo"];?>" width="100%"/>
				<p>我是<span><?=$rsAccount['Shop_Name']?></span><br />我为“<span><?=$Shop_Name?></span>”代言</p>
              </div>
			</div>
			<div class="qrcode">
			<img src="<?=$qrcode_path?>" />
			</div>
		<?php else: ?>
    		<img src="<?=$poster_web_path?>" width="100%"/>
		<?php endif;?>
   <?php else: ?>
        
        <p>您的分销账号已被禁用</p>
        <a href="<?=$shop_url?>">返回</a>  
     
   <?php endif; ?> 
   
 <?php else:?>
 	<?php header("location:".$shop_url."distribute/join/");?>
 <?php endif; ?> 
<?php if($need_gegerate):?>  
<script type="text/javascript" src="/static/js/plugin/cupture/cupture.js"></script>  
       	  
        <script  type="text/javascript" > 
		var base_url = '<?=$base_url?>'; 
		var Users_ID = '<?=$UsersID?>';
		var owner_id = '<?=$owner['id']?>';
		var ajax_url = base_url+'api/'+Users_ID+'/shop/distribute/ajax/';
		
	
		

        $(document).ready( function(){
			
            html2canvas(document.body, {  
						allowTaint: true,  
                        taintTest: false,  
                        onrendered: function(canvas) {  
                            canvas.id = "mycanvas";
                            var dataUrl = canvas.toDataURL();
                                                      
						    var param = {action:'store_poster',dataUrl:dataUrl,owner_id:owner_id};
							
							
							$.post(ajax_url,param,function(data){
								if(data.status == 1){

									var  newImg = '<img src="'+data.poster_path+'?='+Date.parse(new Date())+'" width="100%" />';
									
                            		$('body').html(newImg);
								}
							},'json');	
							
                        } 
            });
			   
        });  
	
	
           
        </script> 
        
 	<?php endif; ?> 
</body>
</html>