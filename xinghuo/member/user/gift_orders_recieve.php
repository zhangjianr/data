<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/shipping.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');

if(empty($_SESSION["Users_Account"])){
	header("location:/member/login.php");
}

	$DB->Get("user_gift_orders","Orders_SendTime,Orders_Status,Orders_Integral,User_ID,Orders_IsShipping,Orders_ID","where Users_ID='".$_SESSION["Users_ID"]."' and Orders_Status=2 and Orders_SendTime<=".(time()-86400*7));
	$lists = array();
	while($r = $DB->fetch_assoc()){
		$lists[] = $r;
	}
	foreach($lists as $v){
		$DB->Set("user_gift_orders","Orders_Status=3,Orders_FinishTime=".time(),"where Users_ID='".$_SESSION["Users_ID"]."' and Orders_ID=".$v["Orders_ID"]);
		$DB->Set("user","User_UseLessIntegral=User_UseLessIntegral-".$v['Orders_Integral'],"where Users_ID='".$_SESSION["Users_ID"]."' and User_ID=".$v["User_ID"]);
		$rsUser = $DB->GetRs("user","User_Integral","where Users_ID='".$_SESSION["Users_ID"]."' and User_ID=".$v["User_ID"]);
		$Data=array(
			'Record_Integral'=>-$v["Orders_Integral"],
			'Record_SurplusIntegral'=>$rsUser['User_Integral'],
			'Operator_UserName'=>'',
			'Record_Type'=>5,
			'Record_Description'=>'使用积分兑换礼品',
			'Record_CreateTime'=>time(),
			'Users_ID'=>$_SESSION["Users_ID"],
			'User_ID'=>$v["User_ID"]
		);
		$DB->Add('user_Integral_record',$Data);
	}
	echo '<script language="javascript">alert("操作成功");window.location.href="gift_orders.php";</script>';
	exit;
?>