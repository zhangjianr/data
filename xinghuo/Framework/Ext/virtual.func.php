<?php
function get_virtual_confirm_code($UsersID) {
	global $DB1;
	for($i=0;$i<=1;$i++){
		$temchars = virtual_randcode(10);
		$r = $DB1->GetRs("user_order","*","where Users_ID='".$UsersID."' and Order_Code='".$temchars."'");
		$i=$r?0:1;
	}
	return $temchars;
}
function virtual_randcode($length=10){
	$chars = '0123456789';
	$temchars = '';
	for($i=0;$i<$length;$i++){
		$temchars .= $chars[ mt_rand(0, strlen($chars) - 1) ];
	}
	return $temchars;
}
?>