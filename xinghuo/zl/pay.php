<?php

include $_SERVER["DOCUMENT_ROOT"] . '/Framework/Conn.php';
include $_SERVER["DOCUMENT_ROOT"] . '/include/library/weixin_pay.class.php';
include $_SERVER["DOCUMENT_ROOT"] . '/include/library/weixin_pay_red.class.php';
$data = array(
	"Users_ID" => "yfz8rjmlwn",
	"Record_Sn" => "334455",
	"openid" => "oeisaxO3ZvaIbnwK4rCBzZ9soa58",
	"realname" => "张磊",
	"Record_Money" => "1",
	"num" => 1
);
$pay = new weixin_pay_red($data);
$payResult = $pay->startPay();
print_r($payResult);