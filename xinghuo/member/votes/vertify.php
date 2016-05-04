<?php
$userright=$DB->GetRs("users","Users_Right","where Users_ID='".$_SESSION["Users_ID"]."'");
$RIGHT=json_decode($userright["Users_Right"],true);
if(empty($RIGHT["votes"])){
	echo "您暂未开通微投票权限,请联系管理员";
	exit;
}

?>