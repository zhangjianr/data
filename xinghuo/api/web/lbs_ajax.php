<?php require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
$UsersID=$_GET["UsersID"];
$rsConfig=$DB->GetRs("web_config","*","where Users_ID='".$UsersID."'");
?>
<style type="text/css">
body, html {
	background:#eee;
}
</style>
<div id="lbs" class="wrap">
  <div class="shop_img"><img src="<?php echo $rsConfig["Stores_ImgPath"] ?>"></div>
  <a class="gps" href="http://api.map.baidu.com/marker?location=<?php echo $rsConfig["Stores_PrimaryLat"].','.$rsConfig["Stores_PrimaryLng"] ?>&title=<?php echo $rsConfig["Stores_Name"] ?>&name=<?php echo $rsConfig["Stores_Name"] ?>&content=<?php echo $rsConfig["Stores_Address"] ?>&output=html&wxref=mp.weixin.qq.com" target="_self"><img src="/static/api/web/skin/default/images/gps.png"></a>
  <div class="item">
    <div class="name"><?php echo $rsConfig["Stores_Name"] ?></div>
  </div>
  <div class="item">
    <div class="tel_ico"></div>
    <div class="item_name">电话:</div>
    <div class="tel_number"><a href="tel:<?php echo $rsConfig["CallPhoneNumber"] ?>" target="_self"><?php echo $rsConfig["CallPhoneNumber"] ?></a></div>
  </div>
  <div class="item">
    <div class="address_ico"></div>
    <div class="item_name">地址:</div>
    <div class="address"><?php echo $rsConfig["Stores_Address"] ?></div>
  </div>
  <div class="item">
    <div class="description"><?php echo $rsConfig["Stores_Description"] ?></div>
  </div>
</div>
