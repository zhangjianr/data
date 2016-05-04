<?php
//ini_set("display_errors","On");
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/General_tree.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/shipping.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/flow.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/lib_products.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/order.php');
require_once ($_SERVER ["DOCUMENT_ROOT"] . '/Framework/Ext/virtual.func.php');
require_once ($_SERVER ["DOCUMENT_ROOT"] . '/Framework/Ext/sms.func.php');
if(isset($_REQUEST["UsersID"])){	
	$UsersID=$_REQUEST["UsersID"];
}else{
	echo 'error';
	exit;
}
$action=empty($_REQUEST["action"])?"":$_REQUEST["action"];
if($action=="qty"){//商品加减操作
    if(isset($_REQUEST["ProductID"]))
	{
		$ProductID=$_REQUEST["ProductID"];
	}else{
		echo 'error';
		exit;
	}
	$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
	//商城配置一股脑转换批发配置
	$Config = $DB->GetRs("pifa_config","*","where Users_ID='".$UsersID."'");
	$rsConfig['ShopName'] = $Config['PifaName'];
	$rsConfig['NeedShipping'] = $Config['p_NeedShipping'];
	$rsConfig['SendSms'] = $Config['p_SendSms'];
	$rsConfig['MobilePhone'] = $Config['p_MobilePhone'];
	$rsConfig['Commit_Check'] = $Config['p_Commit_Check'];
	
	$qty = $_POST['Qty'];
	$product_info = get_product($ProductID,$qty);
	$error = $product_info['error'];
	$rsProducts = $product_info['rsProducts'];
	$cur_price = $product_info['cur_price'];
	$Shiping_ID = $_POST['Shiping_ID'];
	$City_Code = $_POST['City_Code'];
	$ownerid = $_POST['OwnerID'];
	//运费计算
	$total_info = _get_shipping_fee($ProductID,$qty,$rsConfig,$Shiping_ID,$City_Code,$ownerid);
	if(empty($error)){
		$Data=array(
			"status"=>1,
			"qty"=>$qty,
			"price"=>$cur_price,
			"shipping_fee"=>$total_info['total_shipping_fee'],
			"total"=>$total_info['total'],
		);
	}else{
		$Data=array(
			"status"=>0,
			"msg"=>$error,
		);
	}
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);exit;
	
}elseif($action=="favourite"){
	
	//检测用户是否登陆
	if(empty($_SESSION[$UsersID."User_ID"])){
		$_SESSION[$UsersID."HTTP_REFERER"]='http://'.$_SERVER['HTTP_HOST']."/api/".$UsersID."/pifa/product/".$_POST['productId'].'/?wxref=mp.weixin.qq.com';
		$url = 'http://'.$_SERVER['HTTP_HOST']."/api/".$UsersID."/user/login/";
		/*返回值*/
		$Data=array(
			"status"=>0,
			"info"=>"您还为登陆，请登陆！",
			"url"=>$url
		);
		
	}else{

		$insertInfo = array('User_ID'=>$_SESSION[$UsersID.'User_ID'],
							'Products_ID'=>$_POST['productId'],
							'IS_Attention'=>1);
		
		$Result=$DB->Add("user_favourite_products",$insertInfo);
		
			$Data=array(
			"status"=>1,
			"info"=>"收藏成功！",
			
			);
	}

	echo json_encode($Data,JSON_UNESCAPED_UNICODE);exit;
	
	
}elseif($action == "cancel_favourite"){
	
	//检测用户是否登陆
	if(empty($_SESSION[$UsersID."User_ID"])){
		$_SESSION[$UsersID."HTTP_REFERER"]='http://'.$_SERVER['HTTP_HOST']."/api/".$UsersID."/pifa/product/".$_POST['productId'].'/?wxref=mp.weixin.qq.com';
		$url = 'http://'.$_SERVER['HTTP_HOST']."/api/".$UsersID."/user/login/";
		/*返回值*/
		$Data=array(
			"status"=>0,
			"info"=>"您还为登陆，请登陆！",
			"url"=>$url
		);
		
	}else{

		$insertInfo = array('User_ID'=>$_SESSION[$UsersID.'User_ID'],
							'Products_ID'=>$_POST['productId'],
							'IS_Attention'=>1);
		
		$DB->Del("user_favourite_products","User_ID='".$_SESSION[$UsersID."User_ID"]."' and Products_ID=".$_POST['productId']);
		
		$Data=array(
			"status"=>1,
			"info"=>"取消收藏成功！",	
			);
	}
	
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);exit;
	
}elseif($action == "select_payment"){//选择支付方式时的动作
    $ProductID = $_POST['ProductID'];
    $qty = empty($_POST['Qty']) ? 0 : $_POST['Qty'];
	$product_info = get_product($ProductID,$qty);
	$rsProducts = $product_info['rsProducts'];
	$total_price = $qty * $product_info['cur_price'];
	
	$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
	//商城配置一股脑转换批发配置
	$Config = $DB->GetRs("pifa_config","*","where Users_ID='".$UsersID."'");
	$rsConfig['ShopName'] = $Config['PifaName'];
	$rsConfig['NeedShipping'] = $Config['p_NeedShipping'];
	$rsConfig['SendSms'] = $Config['p_SendSms'];
	$rsConfig['MobilePhone'] = $Config['p_MobilePhone'];
	$rsConfig['Commit_Check'] = $Config['p_Commit_Check'];
	
	$Shiping_ID = $_POST['Shiping_ID'];
	$City_Code = $_POST['City_Code'];
	$ownerid = $_POST['OwnerID'];
	
	//运费计算
	$total_info = _get_shipping_fee($ProductID,$qty,$rsConfig,$Shiping_ID,$City_Code,$ownerid);
	$total_shipping_fee = $total_info['total_shipping_fee'];
	if($total_price){
		$Data=array(
			"status"=>1,
			"total"=>$total_price+$total_shipping_fee,	
		);
	}else{
		$Data=array(
			"status"=>0,	
		);
	}
	
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);exit;
	
}elseif($action == "payment"){//前台产品提交订单 && 支付
	//订单提交
	$Order_Shipping = json_encode(empty($_POST["Order_Shipping"]["Express"])?array():$_POST["Order_Shipping"],JSON_UNESCAPED_UNICODE);
	if(empty($_POST['OrderID'])){
		$OrderID = _add_order($_POST['ProductID'], $_POST['Qty'], $_POST['AddressID'], $_POST['Need_Shipping'], $Order_Shipping, $_POST['OwnerID'], $_POST["Order_Shipping"]["Price"]);
	}else{
		$OrderID = $_POST['OrderID']; //兼容线下支付杀回马枪
	}
	$rsOrder = $DB->GetRs("user_order","*","where Users_ID='".$UsersID."' and Order_ID='".$OrderID."'");
	$rsUser = $DB->GetRs("user","User_Money,User_PayPassword,Is_Distribute,User_Name,User_NickName,Owner_Id,User_Integral","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID']);
	
	$PaymentMethod = array(
		"微支付"=>"1",
		"支付宝"=>"2",
		"线下支付"=>"3",
		"易宝支付"=>"4",
	);

	if($_POST['PaymentMethod']=="线下支付" || $rsOrder["Order_TotalPrice"]<=0){//货到付款
		if(!empty($_POST['offline']) && $_POST['offline'] == 1){
		    $Data = array(
				"Order_PaymentMethod"=>$_POST["PaymentMethod"],
				"Order_PaymentInfo"=>$_POST['PaymentInfo'],
				"Order_DefautlPaymentMethod"=>$_POST["DefautlPaymentMethod"],
			);
			
			$Status = 1;
			$Flag = $DB->Set("user_order",$Data,"where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Order_ID=".$OrderID);
			$url = empty($_POST['DefautlPaymentMethod']) ? "/api/".$UsersID."/pifa/member/status/".$Status."/" : "/api/".$UsersID."/pifa/member/detail/".$_POST['OrderID']."/";
			if($Flag){
				$Data=array(
					"status"=>1,
					"url"=>$url
				);
			}else{
				$Data=array(
					"status"=>0,
					"msg"=>'线下支付提交失败'
				);
			}	
		}else{
			$Data = array(
			    "status"=>1,
				"url"=>"/api/".$UsersID."/pifa/cart/complete_pay/huodao/".$OrderID."/",
			);
		}	
	}elseif($_POST['PaymentMethod']=="余额支付"){//余额支付
		if($rsUser["User_Money"] >= $rsOrder["Order_TotalPrice"] && !empty($_POST["PayPassword"])){
			//增加资金流水
			if($rsOrder["Order_Status"] != 1){
				$Data=array(
					"status"=>0,
					"msg"=>'该订单状态不是待付款状态，不能付款'
				);
			}elseif(md5($_POST["PayPassword"])!=$rsUser["User_PayPassword"]){
				$Data=array(
					"status"=>0,
					"msg"=>'支付密码输入错误'
				);
			}else{
				$Data=array(
					'Users_ID'=>$UsersID,
					'User_ID'=>$_SESSION[$UsersID.'User_ID'],				
					'Type'=>0,
					'Amount'=>$rsOrder["Order_TotalPrice"],
					'Total'=>$rsUser['User_Money']-$rsOrder["Order_TotalPrice"],
					'Note'=>"批发支出 -".$rsOrder["Order_TotalPrice"]." (订单号:".$OrderID.")",
					'CreateTime'=>time()		
				);
				$Flag=$DB->Add('user_money_record',$Data);
				//更新用户余额
				$Data=array(				
					'User_Money'=>$rsUser['User_Money']-$rsOrder["Order_TotalPrice"]				
				);
				$Flag=$DB->Set('user',$Data,"where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID']);
				
				$Data=array(
					"Order_PaymentMethod"=>$_POST['PaymentMethod'],
					"Order_PaymentInfo"=>"",
					"Order_DefautlPaymentMethod"=>$_POST["DefautlPaymentMethod"]	
				);
				
				$Flag_a =$DB->Set('user_order',$Data,"where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Order_ID=".$OrderID);
				
				require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/pay_order.class.php');
				
				$pay_order = new pay_order($DB,$OrderID);
				$Data = $pay_order->pay_orders();
			}
		}else{
			$Data = array(
			    "status"=>1,
				"url"=>"/api/".$UsersID."/pifa/cart/complete_pay/money/".$OrderID."/",
			);
		}			
	}else{//在线支付
		$Data=array(
			"Order_PaymentMethod"=>$_POST['PaymentMethod'],
			"Order_PaymentInfo"=>"",
			"Order_DefautlPaymentMethod"=>$_POST["DefautlPaymentMethod"]
		);
		$Flag=$DB->Set("user_order",$Data,"where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Order_ID=".$OrderID);
		$url="/api/".$UsersID."/pifa/cart/pay/".$OrderID."/".$PaymentMethod[$_POST['PaymentMethod']]."/";
		
		if($Flag){
			$Data=array(
				"status"=>1,
				"url"=>$url
			);
		}else{
			$Data=array(
				"status"=>0,
				"msg"=>'在线支付出现错误'
			);
		}		
	}
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);exit;
	
}elseif($action == 'distribute_product'){//分销商品
	//检测用户是否登陆
	if(empty($_SESSION[$UsersID."User_ID"])){
		$_SESSION[$UsersID."HTTP_REFERER"]='http://'.$_SERVER['HTTP_HOST']."/api/".$UsersID."/pifa/product/".$_POST['productid'].'/?wxref=mp.weixin.qq.com';
		$url = 'http://'.$_SERVER['HTTP_HOST']."/api/".$UsersID."/user/login/";
		/*返回值*/
		$response=array(
			"status"=>0,
			"info"=>"您还为登陆，请登陆！",
			"url"=>$url
		);
		
	}else{
		$condition = "where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."'";
		$rsUser = $DB->getRs("user","Is_Distribute",$condition);
		
		$response = array(
				"status"=>1,
				"Is_Distribute"=>$rsUser['Is_Distribute'],
			);	

	}
	echo json_encode($response,JSON_UNESCAPED_UNICODE);	exit;
	
}elseif($action == 'change_shipping_method'){
	$rsConfig = $DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
	//商城配置一股脑转换批发配置
	$Config = $DB->GetRs("pifa_config","*","where Users_ID='".$UsersID."'");
	$rsConfig['ShopName'] = $Config['PifaName'];
	$rsConfig['NeedShipping'] = $Config['p_NeedShipping'];
	$rsConfig['SendSms'] = $Config['p_SendSms'];
	$rsConfig['MobilePhone'] = $Config['p_MobilePhone'];
	$rsConfig['Commit_Check'] = $Config['p_Commit_Check'];
	
	$Shiping_ID = $_POST['Shiping_ID'];
	$City_Code = $_POST['City_Code'];
	$qty = $_POST['qty'];
	$ProductID = $_POST['ProductID'];
	$ownerid = $_POST['ownerid'];
	
	$total_info = _get_shipping_fee($ProductID,$qty,$rsConfig,$Shiping_ID,$City_Code,$ownerid);
	$Data = array(
	   "status"=>1,
	   "total_shipping_fee"=>$total_info['total_shipping_fee'],
	   "total"=>$total_info['total']
	);
	
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);exit;
	
}elseif($action == 'paymentorder'){//用户中心订单支付
	$OrderID=empty($_POST['OrderID'])?0:$_POST['OrderID'];
	$rsOrder=$DB->GetRs("user_order","*","where Users_ID='".$UsersID."' and Order_ID='".$OrderID."'");
	$rsUser = $DB->GetRs("user","User_Money,User_PayPassword,Is_Distribute,User_Name,User_NickName,Owner_Id,User_Integral","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID']);
	$PaymentMethod = array(
		"微支付"=>"1",
		"支付宝"=>"2",
		"线下支付"=>"3",
		"易宝支付"=>"4",
	);
	if($_POST['PaymentMethod']=="线下支付" || $rsOrder["Order_TotalPrice"]<=0){
		$Data=array(
			"Order_PaymentMethod"=>$_POST['PaymentMethod'],
			"Order_PaymentInfo"=>$_POST["PaymentInfo"],
			"Order_DefautlPaymentMethod"=>$_POST["DefautlPaymentMethod"],
			"Order_Status"=>1
		);
		$Status=1;
		$Flag=$DB->Set("user_order",$Data,"where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Order_ID=".$OrderID);
		$url=empty($_POST['DefautlPaymentMethod'])?"/api/".$UsersID."/pifa/member/status/".$Status."/":"/api/".$UsersID."/pifa/member/detail/".$_POST['OrderID']."/";
		if($Flag){
			$Data=array(
				"status"=>1,
				"url"=>$url
			);
		}else{
			$Data=array(
				"status"=>0,
				"msg"=>'线下支付提交失败'
			);
		}
	}elseif($_POST['PaymentMethod']=="余额支付" && $rsUser["User_Money"]>=$rsOrder["Order_TotalPrice"]){//余额支付
		//增加资金流水
		if($rsOrder["Order_Status"] != 1){
			$Data=array(
				"status"=>0,
				"msg"=>'该订单状态不是待付款状态，不能付款'
			);
		}elseif(!$_POST["PayPassword"]){
			$Data=array(
				"status"=>0,
				"msg"=>'请输入支付密码'
			);
			
		}elseif(md5($_POST["PayPassword"])!=$rsUser["User_PayPassword"]){
			$Data=array(
				"status"=>0,
				"msg"=>'支付密码输入错误'
			);
		}else{
			$Data=array(
				'Users_ID'=>$UsersID,
				'User_ID'=>$_SESSION[$UsersID.'User_ID'],				
				'Type'=>0,
				'Amount'=>$rsOrder["Order_TotalPrice"],
				'Total'=>$rsUser['User_Money']-$rsOrder["Order_TotalPrice"],
				'Note'=>"商城购买支出 -".$rsOrder["Order_TotalPrice"]." (订单号:".$OrderID.")",
				'CreateTime'=>time()		
			);
			$Flag=$DB->Add('user_money_record',$Data);
			//更新用户余额
			$Data=array(				
				'User_Money'=>$rsUser['User_Money']-$rsOrder["Order_TotalPrice"]				
			);
			$Flag=$DB->Set('user',$Data,"where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID']);
			
			$Data=array(
				"Order_PaymentMethod"=>$_POST['PaymentMethod'],
				"Order_PaymentInfo"=>"",
				"Order_DefautlPaymentMethod"=>$_POST["DefautlPaymentMethod"]	
			);
			
			$Flag_a =$DB->Set('user_order',$Data,"where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Order_ID=".$OrderID);
			
			require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/pay_order.class.php');
			
			$pay_order = new pay_order($DB,$OrderID);
			$Data = $pay_order->pay_orders();
		}			
	}else{//在线支付
		$Data=array(
			"Order_PaymentMethod"=>$_POST['PaymentMethod'],
			"Order_PaymentInfo"=>"",
			"Order_DefautlPaymentMethod"=>$_POST["DefautlPaymentMethod"]
		);
		$Flag=$DB->Set("user_order",$Data,"where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Order_ID=".$OrderID);
		$url="/api/".$UsersID."/pifa/cart/pay/".$OrderID."/".$PaymentMethod[$_POST['PaymentMethod']]."/";
		
		if($Flag){
			$Data=array(
				"status"=>1,
				"url"=>$url
			);
		}else{
			$Data=array(
				"status"=>0,
				"msg"=>'在线支付出现错误'
			);
		}		
	}
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);
}
//插入订单
function _add_order($ProductID,$qty=0,$AddressID=0,$Need_Shipping=0,$Order_Shipping='',$ownerid=0,$Shipping_Price = 0){
	global $DB,$UsersID;
	$product_info = get_product($ProductID,$qty);//呵呵  没法
	$rsProducts = $product_info['rsProducts'];
	$JSON = json_decode($rsProducts['Products_JSON'],TRUE);//图片信息
	$price_rule = $price_rule = json_decode($rsProducts['Products_price_rule'],true);//价格区间信息
	$total_price = $qty * $product_info['cur_price'];
	$cur_price = $product_info['cur_price'];
	$CartList[$ProductID][]=array(
		"ProductsName"=>$rsProducts["Products_Name"],
		"ImgPath"=>empty($JSON["ImgPath"])?"":$JSON["ImgPath"][0],
		"ProductsPriceX"=>$cur_price,
		"ProductsPriceY"=>$price_rule[0][2],
		"ProductsWeight"=>$rsProducts["Products_Weight"],
		"OwnerID"=>$ownerid,
		"ProductsIsShipping"=>$rsProducts["Products_IsShippingFree"],
		"Qty"=>$qty,
		"spec_list"=>'',
		"Property"=>array(),
	);
	$Data=array(
		"Users_ID"=>$UsersID,
		"User_ID"=>$_SESSION[$UsersID."User_ID"]
	);
	//物流信息
	if($Need_Shipping == 1){
		if(!empty($_POST['AddressID'])){
			$rsAddress=$DB->GetRs("user_address","*","where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Address_ID='".$AddressID."'");
			$Data["Address_Name"] = $rsAddress['Address_Name'];
			$Data["Address_Mobile"] = $rsAddress["Address_Mobile"];
			$Data["Address_Province"] = $rsAddress["Address_Province"];
			$Data["Address_City"] = $rsAddress["Address_City"];
			$Data["Address_Area"] = $rsAddress["Address_Area"];
			$Data["Address_Detailed"] = $rsAddress["Address_Detailed"];	
		}	
	}
	$Data["Order_Type"] = "pifa";
	$Data["Order_Shipping"] = $Order_Shipping;
	
	$Data["Order_CartList"]= json_encode($CartList,JSON_UNESCAPED_UNICODE);
	if(empty($CartList)){
		echo json_encode(array("status"=>0),JSON_UNESCAPED_UNICODE);
		exit;
	}
	$Data["Order_TotalAmount"]=$total_price+(empty($Shipping_Price)?0:$Shipping_Price);

	
	$Data["Order_TotalPrice"]=$total_price+(empty($Shipping_Price)?0:$Shipping_Price);
	$Data["Order_CreateTime"]=time();
	$rsConfig = shop_config($UsersID);
	//商城配置一股脑转换批发配置
	$Config = $DB->GetRs("pifa_config","*","where Users_ID='".$UsersID."'");
	$rsConfig['ShopName'] = $Config['PifaName'];
	$rsConfig['NeedShipping'] = $Config['p_NeedShipping'];
	$rsConfig['SendSms'] = $Config['p_SendSms'];
	$rsConfig['MobilePhone'] = $Config['p_MobilePhone'];
	$rsConfig['Commit_Check'] = $Config['p_Commit_Check'];
	
	$Data["Order_Status"]= 1;
	
	//获取店主ID
	$owner = get_owner($rsConfig,$UsersID);

	$Data['Owner_ID'] = $owner['id'];
	$Flag=$DB->Add("user_order",$Data);
	
	$neworderid = $DB->insert_id();
	
	/*增加产品销量*/
	foreach($CartList as $ProductID=>$product_list){
		$qty = 0;
		foreach($product_list as $key=>$item){
			$qty += $item['Qty'];
		}
		
		$condition ="where Users_ID='".$UsersID."' and Products_ID=".$ProductID;
		$DB->set('pifa_products','Products_Sales=Products_Sales+'.$qty,$condition);
	}
	
	/*加入分销记录之中*/	
	//若购买者为本店店主，且本店主为顶级分销商，则不计入分销记录
		
	$is_distribute = TRUE;  //此次下单是分销行为	
	
	$user_invite_id = User::find($_SESSION[$UsersID.'User_ID'])->Owner_Id;
	
	if($_SESSION[$UsersID.'User_ID'] == $owner['id']&&$user_invite_id == 0){
		$is_distribute = FALSE;
	}
	
	if($is_distribute){
		Dis_Record::observe(new DisRecordObserver());
		foreach($CartList as $ProductID=>$product_list){
			foreach($product_list as $key=>$item){
				if($item['OwnerID']>0){
					add_distribute_record($UsersID,$item['OwnerID'],$item['ProductsPriceX'],$ProductID,$item['Qty'],$neworderid);
				}
			}
		}
	}
	
	//if($Flag){
		//require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/weixin_message.class.php');
		//$weixin_message = new weixin_message($DB,$UsersID,$_SESSION[$UsersID."User_ID"]);
		//$contentStr = '您已成功提交批发订单，<a href="http://'.$_SERVER["HTTP_HOST"].'/api/'.$UsersID.'/pifa/member/detail/'.$neworderid.'/">查看详情</a>';
		//$weixin_message->sendscorenotice($contentStr);
	//}
	return $neworderid;
}
function get_product($ProductID,$qty){
	global $DB, $UsersID;
	//获取此产品
	$rsProducts = $DB->GetRs("pifa_products","*","where Users_ID='".$UsersID."' and Products_SoldOut=0 and Products_ID=".$ProductID);
	$price_rule = json_decode($rsProducts['Products_price_rule'],true);//价格区间信息
	foreach($price_rule as $key => $val){
		if(!empty($val[1])){
			if($qty>=$val[0] && $qty<=$val[1]){
				$cur_price = $val[2];
			}
		}elseif(empty($val[0])){
			if($qty<=$val[1]){
				$cur_price = $val[2];
			}
		}else{
			if($qty>=$val[0]){
				$cur_price = $val[2];
			}
		}
	}
	//不能低于最低批发数
	$error = '';
	if(empty($cur_price)){
		$error = '数量不能低于'.$price_rule[0][0];
		$cur_price = $price_rule[0][2];
	}
	return array('rsProducts'=>$rsProducts,'cur_price'=>$cur_price,'error'=>$error);
}
//获取运费
function _get_shipping_fee($ProductID,$qty,$rsConfig,$Shiping_ID,$City_Code,$ownerid){
	global $UsersID;
	$product_info = get_product($ProductID,$qty);
	$rsProducts = $product_info['rsProducts'];
	$JSON = json_decode($rsProducts['Products_JSON'],TRUE);//图片信息
	$price_rule = $price_rule = json_decode($rsProducts['Products_price_rule'],true);//价格区间信息
	$total_price = $qty * $product_info['cur_price'];
	$cur_price = $product_info['cur_price'];
	$CartList[$ProductID][]=array(
		"ProductsName"=>$rsProducts["Products_Name"],
		"ImgPath"=>empty($JSON["ImgPath"])?"":$JSON["ImgPath"][0],
		"ProductsPriceX"=>$cur_price,
		"ProductsPriceY"=>$price_rule[0][2],
		"ProductsWeight"=>$rsProducts["Products_Weight"],
		"OwnerID"=>$ownerid,
		"ProductsIsShipping"=>$rsProducts["Products_IsShippingFree"],
		"IsShippingFree"=>$rsProducts["Products_IsShippingFree"],
		"Shipping_Free_Company"=>$rsProducts["Shipping_Free_Company"],
		"Qty"=>$qty,
		"spec_list"=>'',
		"Property"=>array(),
	);
	$total_info = get_order_total_info($UsersID,$CartList,$rsConfig,$Shiping_ID,$City_Code);
	return $total_info;
}
?>