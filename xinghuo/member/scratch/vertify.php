<?php
$userright=$DB->GetRs("users","Users_Right","where Users_ID='".$_SESSION["Users_ID"]."'");
$RIGHT=json_decode($userright["Users_Right"],true);
if(empty($RIGHT["weicuxiao"])){
	echo "您暂未开通刮刮卡权限,请联系管理员";
	exit;
}else{
	if(!in_array('sctrach',$RIGHT["weicuxiao"])){
		echo "您暂未开通刮刮卡权限,请联系管理员";
		exit;
	}
}

?>