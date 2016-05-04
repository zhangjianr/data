<?php
define("APPID" , trim($rsUsers["Users_WechatAppId"]));
define("APPSECRET", trim($rsUsers["Users_WechatAppSecret"]));
define("MCHID",trim($rsPay["PaymentWxpayPartnerId"]));
define("KEY",trim($rsPay["PaymentWxpayPartnerKey"]));
define("JS_API_CALL_URL","http://".$_SERVER['HTTP_HOST']."/pay/wxpay2/sendto.php?UsersID=".$UsersID."_".$OrderID);
define("JS_API_CALL_URL_PACKAGE","http://".$_SERVER['HTTP_HOST']."/pay/wxpay2/pay.php?UsersID=".$UsersID."&orderId=".$orderId);
define("NOTIFY_URL","http://".$_SERVER['HTTP_HOST']."/pay/wxpay2/notify_url.php");
define("NOTIFY_URL_PACKAGE","http://".$_SERVER['HTTP_HOST']."/pay/wxpay2/notify.php");
define("CURL_TIMEOUT",30);
define("SSLCERT_PATH","http://".$_SERVER['HTTP_HOST']."/pay/wxpay2/apiclient_cert.pem");
define("SSLKEY_PATH","http://".$_SERVER['HTTP_HOST']."/pay/wxpay2/apiclient_key.pem");
?>