<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
if (isset($_GET["UsersID"])) {
	$UsersID = $_GET["UsersID"];
} else {
	message("缺少必要参数", "", "error");
}
$action = empty($_REQUEST["action"]) ? "" : $_REQUEST["action"];

if ($action == "checkout") {
	$packageId = intval($_POST['packageId']);
	$packageItem = $DB->GetRs("spark_package", "*", " WHERE Users_ID='{$UsersID}' AND id = '{$packageId}'");
	if(empty($packageItem)){
		message("产品包不存在", "", "error");
	}
	$Data=array(
		"Users_ID"			=> $UsersID,
		"User_ID"			=> $_SESSION[$UsersID . "User_ID"],
		"realName"			=> htmlspecialchars(trim($_POST["realName"])),
		"nickName"			=> htmlspecialchars(trim($_POST["nickName"])),
		"mobile"			=> htmlspecialchars(trim($_POST["mobile"])),
		"address"			=> htmlspecialchars(trim($_POST["address"])),
		"packageId"			=> $packageItem['id'],
		"price"				=> $packageItem['price'],
		"packageLevelName"	=> $packageItem['levelName'],
		"createtime"		=> time()
	);
	$userInfo = $DB->GetR("spark_user", "*", " WHERE Users_ID='{$UsersID}' AND User_ID='{$_SESSION[$UsersID . "User_ID"]}'");//校验升级
	if(!empty($userInfo)){
		if($userInfo['price'] >= $packageItem['price']){
			message("升级的套餐价格不能小于等于已购买的套餐价格，请联系商家修改", "", "error");
		}
		$Data['price'] = $packageItem['price']-$userInfo['price'];
		$Data['isUp'] = 1;
	}
        if(trim($_POST["intCode"])){
            // 通过自己id 找儿子 换上级
            $userOwner = $DB->GetS("user","*"," where Users_ID='{$UsersID}' AND Owner_Id ='{$_SESSION[$UsersID . "User_ID"]}'");
            if(!empty($userOwner)){
                //
                $user_one = $DB->GetR("user","*","where Users_ID='{$UsersID}' AND User_ID ='{$_SESSION[$UsersID . "User_ID"]}'");
                foreach($userOwner as $k=>$v){
                    $DB->set("user",array("Owner_Id"=>$user_one['Owner_Id'])," where Users_ID='{$UsersID}' AND User_ID='{$v['User_ID']}'");
                    $DB->Set("shop_distribute_account",array("invite_id"=>$user_one['Owner_Id'])," WHERE  Users_ID='{$UsersID}' AND User_ID='{$v['User_ID']}'");
                }
            }
            //处理邀请码
            $arr = $DB->GetR("spark_user","*"," where Users_ID='{$UsersID}' AND intCode='{$_POST['intCode']}'");
            if(!empty($arr)){
                $DB->Set("user",array("Owner_Id"=>$arr['User_ID'])," WHERE  Users_ID='{$UsersID}' AND User_ID='{$_SESSION[$UsersID . "User_ID"]}'");
                $DB->Set("shop_distribute_account",array("invite_id"=>$arr['User_ID'])," WHERE  Users_ID='{$UsersID}' AND User_ID='{$_SESSION[$UsersID . "User_ID"]}'");
            }else{
                message("您填写的邀请码无效，请重试", "", "error");
            }
        }
	$FlagA=$DB->Add("spark_order",$Data);
	if($FlagA === FALSE){
		message("订单提交失败，请重试", "", "error");
	}
	$newId = $DB->insert_id();
	$orderId = "SPARK".date("YmdHi",$Data['createtime']).$newId;
	$FlagB=$DB->Set("spark_order", array("orderId"=>$orderId)," WHERE id='{$newId}'");
	if($FlagB === FALSE){
		message("订单提交失败，请重试", "", "error");
	}
	
	$rsPay = $DB->GetRs("users_payconfig", "*", "where Users_ID='{$UsersID}'");
	if ($rsPay["PaymentWxpayType"] == 1) {
		$payUrl = "/pay/wxpay2/pay.php?UsersID=" . $UsersID . "&orderId=" . $orderId;
	} else {
		$payUrl = "/pay/wxpay/pay.php?UsersID=" . $UsersID . "&OrderID=" . $orderId;
	}
	message("提交成功", $payUrl, "success");
	
}
function message($msg = "", $redirectUrl = "", $type = "error") {
	echo json_encode(array("msg" => $msg, "redirectUrl" => $redirectUrl, "type" => $type));
	exit;
}
