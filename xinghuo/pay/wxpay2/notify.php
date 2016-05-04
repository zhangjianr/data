<?php
//ini_set("display_errors", "On"); //调试信息
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Ext/virtual.func.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/order.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Ext/sms.func.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/pay_order.class.php');
if(isset($GLOBALS['HTTP_RAW_POST_DATA'])){
	include_once("WxPayPubHelper.php");
	$notify = new Notify_pub();
	$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
	write_file("log.txt",$xml);//测试数据
	$notify->saveData($xml);
	$orderId = $notify->data["out_trade_no"];
	if(strpos($orderId,'SPARK')>-1){
		$rsOrder=$DB->GetRs("spark_order","Users_ID,User_ID,payCode","where orderId='{$orderId}'");
		$UsersID = $rsOrder["Users_ID"];
		$UserID = $rsOrder["User_ID"];
		$Status = $rsOrder["payCode"]==0 ? 1 : 2;
	}else if(strpos($orderId,'TRA')>-1){
		$rsOrder=$DB->GetRs("spark_traffic_order","Users_ID,User_ID,payCode","where orderId='{$orderId}'");
		$UsersID = $rsOrder["Users_ID"];
		$UserID = $rsOrder["User_ID"];
		$Status = $rsOrder["payCode"]==0 ? 1 : 2;
	}
	
	$rsUsers=$DB->GetRs("users","*","where Users_ID='{$UsersID}'");
	$rsPay=$DB->GetRs("users_payconfig","*","where Users_ID='{$UsersID}'");
	include_once("WxPay.pub.config.php");
	
	if($notify->checkSign() == FALSE){
		$notify->setReturnParameter("return_code","FAIL");//返回状态码
		$notify->setReturnParameter("return_msg","签名失败");//返回信息
	}else{
		$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
	}
	$returnXml = $notify->returnXml();
	if($notify->checkSign() == TRUE){
		if ($notify->data["return_code"] == "FAIL") {
			echo "【通信出错】";
		}elseif($notify->data["result_code"] == "FAIL"){
			echo "【业务出错】";
		}else{
			$pay_order = new pay_order($DB,$notify->data["out_trade_no"],$notify->data["transaction_id"]);
			$rsUser=$DB->GetRs("user","*","where Users_ID='{$UsersID}' and User_ID='{$UserID}'");
			if($Status==1){
				$data = $pay_order->pay_orders();
				if($data["status"]==1){
					echo $data["msg"];
					exit;
				}else{
					echo $data["msg"];
					exit;
				}				
			}else{
				echo "SUCCESS";
				exit;
			}
		}
	}
}else{
	$orderId = isset($_GET["orderId"]) ? $_GET["orderId"] : 0;
	if(strpos($orderId,'TRA')>-1){
		$rsOrder=$DB->GetRs("spark_traffic_order","Users_ID,User_ID,payCode","where orderId='{$orderId}'");
		if(!$rsOrder){
			exit("订单不存在");
		}
		$UsersID = $rsOrder["Users_ID"];
		$url = '/api/'.$UsersID.'/shop/spark/my/';
		if($rsOrder['payCode'] == "1"){
			exit("<script type='text/javascript'>alert('支付成功！');window.location.href='".$url."';</script>");
		}else{
			exit("<script type='text/javascript'>alert('支付信息正在确认中！');window.location.href='".$url."';</script>");
		}
	}else if(strpos($orderId,'SPARK')>-1){
		$rsOrder=$DB->GetRs("spark_order","Users_ID,User_ID,payCode","where orderId='{$orderId}'");
		if(!$rsOrder){
			exit("订单不存在");
		}
		$UsersID = $rsOrder["Users_ID"];
		$url = '/api/'.$UsersID.'/shop/spark/my/';
		if($rsOrder['payCode'] == "1"){
			exit("<script type='text/javascript'>alert('支付成功！');window.location.href='".$url."';</script>");
		}else{
			exit("<script type='text/javascript'>alert('支付信息正在确认中！');window.location.href='".$url."';</script>");
		}
	}
}

function write_file($path, $data, $mode = 'ab') {
	if (!$fp = @fopen($path, $mode)) {
		return FALSE;
	}
	if(is_array($data)){
		fwrite($fp, print_r($data, true));
	}else{
		fwrite($fp, $data);
	}
	$result = fclose($fp);
}
?>