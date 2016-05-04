<?php
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/General_tree.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/shipping.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/tools.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/flow.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/lib_products.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/order.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/smarty.php');
require_once ($_SERVER ["DOCUMENT_ROOT"] . '/Framework/Ext/virtual.func.php');
require_once ($_SERVER ["DOCUMENT_ROOT"] . '/Framework/Ext/sms.func.php');

//ini_set("display_errors","On");
if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo 'error';
	exit;
}

//设置smarty
$smarty->left_delimiter = "{{";
$smarty->right_delimiter = "}}";
$template_dir = $_SERVER["DOCUMENT_ROOT"].'/api/shop/html';
$smarty->template_dir = $template_dir;

$action=empty($_REQUEST["action"])?"":$_REQUEST["action"];

if(empty($action)){

	/*获取此产品所选属性id*/	
	$rsProducts=$DB->GetRs("shop_products","*","where Users_ID='".$UsersID."' and Products_ID=".$_POST["ProductsID"]);
	$cur_price = $rsProducts["Products_PriceX"];
	
	/*如果此产品包含属性*/
	$Property = array();
	if(strlen($_POST['spec_list'])>0){
		$attr_id = explode(',',$_POST['spec_list']);
		$cur_price = get_final_price($cur_price,$attr_id,$_POST["ProductsID"],$UsersID);		
		$Property = get_posterty_desc($_POST['spec_list'],$UsersID,$_POST["ProductsID"]);
	}
	
	//获取登录用户的用户级别及其是否对应优惠价
	if(!empty($_SESSION[$UsersID.'User_ID'])){ 	
		$rsUser = $DB->GetRs("user","User_Level","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID']);
		if($rsUser['User_Level'] >0 ){
			$rsUserConfig = $DB->GetRs("User_Config","UserLevel","where Users_ID='".$UsersID."'");
			$discount_list = json_decode($rsUserConfig["UserLevel"],TRUE);
			$discount =  empty($discount_list[$rsUser['User_Level']]['Discount']) ? 0 : $discount_list[$rsUser['User_Level']]['Discount'];
			$discount_price = $cur_price*(1-$discount/100);
			$discount_price = round($discount_price,2);
			$cur_price = $discount_price;
		}
		
	}
	
	$needcart = isset($_POST["needcart"]) ? $_POST["needcart"] : 1;
	
	$JSON=json_decode($rsProducts['Products_JSON'],true);
	$OwnerID = !empty($_POST['OwnerID'])?$_POST['OwnerID']:0;
	
	$success = TRUE; //产品成功加入购物车标记
	
	if($rsProducts["Products_IsVirtual"]==0){
		if($needcart==0){
			$cart_key = $UsersID."BuyList";
			$_SESSION[$cart_key] = '';
		}else{
			$cart_key = $UsersID."CartList";
		}
		
	}else{
		$cart_key = $UsersID."Virtual";
		$_SESSION[$cart_key] = '';
	}
	
	$array_temp = array(
		"ProductsName"=>$rsProducts["Products_Name"],
		"ImgPath"=>empty($JSON["ImgPath"])?"":$JSON["ImgPath"][0],
		"ProductsPriceX"=>$cur_price,
		"ProductsPriceY"=>$rsProducts["Products_PriceY"],
		"ProductsWeight"=>$rsProducts["Products_Weight"],
		"Products_Shipping"=>$rsProducts["Products_Shipping"],
		"Products_Business"=>$rsProducts["Products_Business"],
		"Shipping_Free_Company"=>$rsProducts["Shipping_Free_Company"],
		"IsShippingFree"=>$rsProducts["Products_IsShippingFree"],
		"OwnerID"=>$OwnerID,
		"ProductsIsShipping"=>$rsProducts["Products_IsVirtual"]==0 ? $rsProducts["Products_IsShippingFree"] : 1,
		"Qty"=>$_POST["Qty"],
		"spec_list"=>isset($_POST['spec_list'])?$_POST['spec_list']:'',
		"Property"=>$Property
	);
	
	if(empty($_SESSION[$cart_key])){
		$CartList[$_POST["ProductsID"]][]=$array_temp;
	}else{
		$CartList=json_decode($_SESSION[$cart_key],true);
		if(empty($CartList[$_POST["ProductsID"]])){
			$CartList[$_POST["ProductsID"]][]= $array_temp;
		}else{
			foreach($CartList[$_POST["ProductsID"]] as $k=>$v){
				//array_diff_assoc 比较数组键值与键名,返回数组
				$spec_list = isset($_POST['spec_list'])?$_POST['spec_list']:''; 
				$array= array_diff(explode(',',$spec_list),explode(',',$v["spec_list"]));
				if(empty($array)){
					$CartList[$_POST["ProductsID"]][$k]["Qty"]+=$_POST["Qty"];
					$flag=false;
					break;
				}
			}
					
			if($flag){
				$CartList[$_POST["ProductsID"]][]=$array_temp;
			}
		}
	}
	$_SESSION[$cart_key]=json_encode($CartList,JSON_UNESCAPED_UNICODE);
	
	$qty=0;
	$total=0;
	
	foreach($CartList as $key=>$value){
		foreach($value as $k=>$v){
			$qty+=$v["Qty"];
			$total+=$v["Qty"]*$v["ProductsPriceX"];
		}
	}
	
	$Data=array(
		"status"=>1,
		"qty"=>$qty,
		"total"=>$total
	);
	
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);
	
}elseif($action=="del"){
	
	$CartList=json_decode($_SESSION[$UsersID."CartList"],true);
	$k=explode("_",$_POST["CartID"]);
	unset($CartList[$k[0]][$k[1]]);
	
	if(count($CartList[$k[0]] == 0 )){
		unset($CartList[$k[0]]);
	}
	
	$_SESSION[$UsersID."CartList"]=json_encode($CartList,JSON_UNESCAPED_UNICODE);
	$total=0;
	foreach($CartList as $key=>$value){
		foreach($value as $k=>$v){
			$total+=$v["Qty"]*$v["ProductsPriceX"];
		}
	}
	
	if(empty($total)){
		$_SESSION[$UsersID."CartList"]="";
		
	}

	$Data=array(
		"status"=>1,
		"total"=>$total
	);
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);
	
}elseif($action=="update"){
	
	$CartList=json_decode($_SESSION[$UsersID."CartList"],true);
	
	$k=explode("_",$_POST["_CartID"]);
	$CartList[$k[0]][$k[1]]["Qty"]=$_POST["_Qty"];
	$price=$CartList[$k[0]][$k[1]]["ProductsPriceX"];
	$_SESSION[$UsersID."CartList"]=json_encode($CartList,JSON_UNESCAPED_UNICODE);
	$total=0;
	foreach($CartList as $key=>$value){
		foreach($value as $k=>$v){
			$total+=$v["Qty"]*$v["ProductsPriceX"];
		}
	}
	
	$Data=array(
		"status"=>1,
		"price"=>$price,
		"total"=>$total
	);
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);
	
}elseif($action=="favourite"){
	
	//检测用户是否登陆
	if(empty($_SESSION[$UsersID."User_ID"])){
		$_SESSION[$UsersID."HTTP_REFERER"]='http://'.$_SERVER['HTTP_HOST']."/api/".$UsersID."/shop/products/".$_POST['productId'].'/?wxref=mp.weixin.qq.com';
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

	echo json_encode($Data,JSON_UNESCAPED_UNICODE);
	
	
}elseif($action == "cancel_favourite"){
	
	//检测用户是否登陆
	if(empty($_SESSION[$UsersID."User_ID"])){
		$_SESSION[$UsersID."HTTP_REFERER"]='http://'.$_SERVER['HTTP_HOST']."/api/".$UsersID."/shop/products/".$_POST['productId'].'/?wxref=mp.weixin.qq.com';
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

	echo json_encode($Data,JSON_UNESCAPED_UNICODE);

	

}elseif($action=="check"){
	
	$CartList=json_decode($_SESSION[$UsersID."CartList"],true);
	if(count($CartList)>0){
		$Data=array(
			"status"=>1
		);
	}else{
		$Data=array(
			"status"=>0,
			"info"=>"购物车空的，赶快去逛逛吧！"
		);
	}
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);
}elseif($action == "checkout"){
	
	$Data=array(
		"Users_ID"=>$UsersID,
		"User_ID"=>$_SESSION[$UsersID."User_ID"]
	);
	
	$virtual = isset($_POST['virtual']) ? $_POST['virtual'] : 0;
	$needcart = isset($_POST['needcart']) ? $_POST['needcart'] : 1;
	if($virtual){
		$cart_key = $UsersID."Virtual";
	}else{
		if($needcart==1){
			$cart_key = $UsersID."CartList";
		}else{
			$cart_key = $UsersID."BuyList";
		}
	}
	
	if($virtual == 0){
		$AddressID=empty($_POST['AddressID'])?0:$_POST['AddressID'];
		$Need_Shipping = $_POST['Need_Shipping'];
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
			
			//填充商品物流信息
			
			$CartList = json_decode($_SESSION[$cart_key],true);
			
			
			$_SESSION[$cart_key]= json_encode($CartList,JSON_UNESCAPED_UNICODE);
			
		}
	}else{
		$Data["Order_IsVirtual"] = 1;
		$Data["Order_IsRecieve"] = $_POST["recieve"];
		$Data["Address_Mobile"] = $_POST["Mobile"];
	}
	
	
	$Data["Order_Type"] = "shop";
	$Data["Order_Remark"] = $_POST["Remark"];
	
	$Data["Order_Shipping"]=json_encode(empty($_POST["Order_Shipping"]["Express"])?array():$_POST["Order_Shipping"],JSON_UNESCAPED_UNICODE);
	
	$Data["Order_CartList"]= $_SESSION[$cart_key];
	if(empty($_SESSION[$cart_key])){
		echo json_encode(array("status"=>0),JSON_UNESCAPED_UNICODE);
		exit;
	}
	$total_price = $_POST["total_price"];
	$Data["Order_TotalAmount"]=$total_price+(empty($_POST["Shipping"]["Price"])?0:$_POST["Shipping"]["Price"]);
	
	if(isset($_POST["CouponID"])){//优惠券使用判断
		$r = $DB->GetRs("user_coupon_record","*","where Coupon_ID=".$_POST["CouponID"]." and Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID."User_ID"]." and Coupon_UseArea=1 and (Coupon_UsedTimes=-1 or Coupon_UsedTimes>0) and Coupon_StartTime<=".time()." and Coupon_EndTime>=".time()." and Coupon_Condition<=".$total_price);
		if($r){
			$Logs_Price=0;
			$Data["Coupon_ID"]=$_POST["CouponID"];
			if($r["Coupon_UseType"]==0 && $r["Coupon_Discount"]>0 && $r["Coupon_Discount"]<1){
				$total_price = $total_price * $r["Coupon_Discount"];
				$Logs_Price = $total_price * $r["Coupon_Discount"];
				$Data["Coupon_Discount"]=$r["Coupon_Discount"];
			}
			if($r['Coupon_UseType']==1 && $r['Coupon_Cash']>0){
				//$total_price = $total_price - $r['Coupon_Cash'];
				$Logs_Price = $r['Coupon_Cash'];
				$Data["Coupon_Cash"] = $r["Coupon_Cash"];
			}
			
			if($r['Coupon_UsedTimes']>=1){
				$DB->Set("user_coupon_record","Coupon_UsedTimes=Coupon_UsedTimes-1","where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Coupon_ID=".$_POST['CouponID']);
			}
			$item = $DB->GetRs("user","User_Name","where User_ID=".$_SESSION[$UsersID."User_ID"]);
			$Data1=array(
				'Users_ID'=>$UsersID,
				'User_ID'=>$_SESSION[$UsersID."User_ID"],
				'User_Name'=>$item["User_Name"],
				'Coupon_Subject'=>"优惠券编号：".$_POST["CouponID"],
				'Logs_Price'=>$Logs_Price,
				'Coupon_UsedTimes'=>$r['Coupon_UsedTimes']>=1?$r['Coupon_UsedTimes']-1:$r['Coupon_UsedTimes'],
				'Logs_CreateTime'=>time(),
				'Operator_UserName'=>"微商城购买商品"
			);
			$DB->Add("user_coupon_logs",$Data1);
		}
	}
	

	//此订单完成后所获积分
	if (!empty($rsConfig['Integral_Convert'])) {
		$interval = floor($order['Order_TotalPrice'] / abs($rsConfig['Integral_Convert']));
		$Data['Integral_Get'] = $interval;
	}
	
	$Data["Order_TotalPrice"]=$total_price+(empty($_POST["Shipping"]["Price"])?0:$_POST["Shipping"]["Price"]);
	$Data["Order_CreateTime"]=time();
	$rsConfig = shop_config($UsersID);
	
	$Data["Order_Status"]= $rsConfig["CheckOrder"] == 1 ? 1 : 0;
	
	//获取店主ID
	$owner = get_owner($rsConfig,$UsersID);

	$Data['Owner_ID'] = $owner['id'];
	$Flag=$DB->Add("user_order",$Data);
	
	
	$neworderid = $DB->insert_id();
	$CartList=json_decode($Data["Order_CartList"],true);
	
	/*增加产品销量*/
	foreach($CartList as $ProductID=>$product_list){
		$qty = 0;
		foreach($product_list as $key=>$item){
			$qty += $item['Qty'];
		}
		
		$condition ="where Users_ID='".$UsersID."' and Products_ID=".$ProductID;
		$DB->set('shop_products','Products_Sales=Products_Sales+'.$qty,$condition);
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
	
	
	if($Flag){	
		$url = $rsConfig["CheckOrder"]==1 ? "/api/".$UsersID."/shop/cart/payment/".$neworderid."/":"/api/".$UsersID."/shop/member/status/0/";		
		
		require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/weixin_message.class.php');
		$weixin_message = new weixin_message($DB,$UsersID,$_SESSION[$UsersID."User_ID"]);
		$contentStr = '您已成功提交微商城订单，<a href="http://'.$_SERVER["HTTP_HOST"].'/api/'.$UsersID.'/shop/member/detail/'.$neworderid.'/">查看详情</a>';
		$weixin_message->sendscorenotice($contentStr);
		
		$_SESSION[$cart_key]="";
		$Data=array(
			"status"=>1,
			"url"=>$url,
		);
	}else{
		$Data=array(
			"status"=>0
		);
	}
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);
	
}elseif($action=="payment"){
	
	$OrderID=empty($_POST['OrderID'])?0:$_POST['OrderID'];
	$rsOrder=$DB->GetRs("user_order","*","where Users_ID='".$UsersID."' and Order_ID='".$OrderID."'");
	$isvirtual = $rsOrder["Order_IsVirtual"];
	$recieve = $rsOrder["Order_IsRecieve"];
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
		$url=empty($_POST['DefautlPaymentMethod'])?"/api/".$UsersID."/shop/member/status/".$Status."/":"/api/".$UsersID."/shop/member/detail/".$_POST['OrderID']."/";
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
		$url="/api/".$UsersID."/shop/cart/pay/".$OrderID."/".$PaymentMethod[$_POST['PaymentMethod']]."/";
		
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
}elseif($action == 'distribute_product'){
	
	//检测用户是否登陆
	if(empty($_SESSION[$UsersID."User_ID"])){
		$_SESSION[$UsersID."HTTP_REFERER"]='http://'.$_SERVER['HTTP_HOST']."/api/".$UsersID."/shop/products/".$_POST['productid'].'/?wxref=mp.weixin.qq.com';
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
	
	echo json_encode($response,JSON_UNESCAPED_UNICODE);	
}else if($action == 'price'){

	$Products_ID = $_REQUEST["ProductsID"];
	$response    = array('err_msg' => '', 'result' => '', 'qty' => 1);
    $attr_id    = isset($_REQUEST['attr']) ? $_REQUEST['attr']: array();
    $qty     = (isset($_REQUEST['qty'])) ? intval($_REQUEST['qty']) : 1;
	$no_attr_price = $_REQUEST['no_attr_price'];
	$UsersID = $_REQUEST['UsersID'];
	
    if ($Products_ID == 0)
    {
        $response['msg'] = '无产品ID';
        $response['status']  = 0;
    }
    else
    {
        if ($qty == 0)
        {
            $response['qty'] = $qty = 1;
        }
        else
        {
            $response['qty'] = $qty;
        }
		
	
        $shop_price  = get_final_price($no_attr_price,$attr_id,$Products_ID,$UsersID);
        //查看用户是否登陆
		//获取登录用户的用户级别及其是否对应优惠价
		if(!empty($_SESSION[$UsersID.'User_ID'])){ 	
			$rsUser = $DB->GetRs("user","User_Level","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID']);
			
			if($rsUser['User_Level'] >0 ){
				$rsUserConfig = $DB->GetRs("User_Config","UserLevel","where Users_ID='".$UsersID."'");
				$discount_list = json_decode($rsUserConfig["UserLevel"],TRUE);
				$discount =  $discount_list[$rsUser['User_Level']]['Discount'];
				$discount_price = $shop_price*(1-$discount/100);
				$discount_price = round($discount_price,2);
				$shop_price = $discount_price;
			}
		
		}
		$response['result'] = getFloatValue($shop_price,2);
    }
	
	echo json_encode($response,JSON_UNESCAPED_UNICODE);	
}else if($action == 'diyong'){
	
	//记录此订单消耗多少积分
	$Data = array('Integral_Consumption'=>$_POST['Integral_Consumption'],
				   'Integral_Money'=>$_POST['Integral_Money'],
				   'Order_TotalPrice'=>'Order_TotalPrice-'.$_POST['Integral_Money']);
	$condition = "where Users_ID = '".$UsersID."' and Order_ID=".$_POST['Order_ID'];
	
	mysql_query('start transaction');
	$Flag_a = $DB->Set('user_order',$Data,$condition,'Order_TotalPrice');
	//将积分移入不可用积分
	$Flag_b = add_userless_integral($UsersID,$_SESSION[$UsersID.'User_ID'],$_POST['Integral_Consumption']);
			  	
	
	if($Flag_a&&$Flag_b){
		mysql_query('commit');
		$response = array('status'=>1,'msg'=>'抵用消耗记录到订单成功');
	}else{
		mysql_query("ROLLBACK");
	    $response = array('status'=>0,'msg'=>'抵用消耗记录到订单失败');
	}
	
	echo json_encode($response,JSON_UNESCAPED_UNICODE);	
	
}elseif($action == 'checkout_update'){
	
	$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
	
	$virtual = isset($_POST['virtual']) ? $_POST['virtual'] : 0;
	$needcart = isset($_POST['needcart']) ? $_POST['needcart'] : 1;
	$Shipping_ID = isset($_POST['Shipping_ID']) ? $_POST['Shipping_ID'] : 1;
		
	$cart_key = get_cart_key($UsersID,$virtual,$needcart);
	$CartList = json_decode($_SESSION[$cart_key],true);
	$Qty = $_POST["_Qty"];
	$k=explode("_",$_POST["_CartID"]);

	$CartList[$k[0]][$k[1]]["Qty"] = $Qty;
	$price=$CartList[$k[0]][$k[1]]["ProductsPriceX"];
	$_SESSION[$cart_key]=json_encode($CartList,JSON_UNESCAPED_UNICODE);
	
	if($virtual == 1){
		$total_info = get_order_total_info($UsersID,$CartList,$rsConfig,0,0);
	}else{
		$total_info = get_order_total_info($UsersID,$CartList,$rsConfig,$Shipping_ID,$_POST['City_Code']);
	}
	
	//计算所需运费
	$Info = array('Qty'=>$Qty,
	                      'Weight'=>$CartList[$k[0]][$k[1]]["ProductsWeight"],
						  'Price'=>$CartList[$k[0]][$k[1]]["ProductsPriceX"]);
	
	/*免运费商品运费为零*/
	if($_POST['virtual'] == 1){

			$shipping_fee = 0;
		
	}else{
		$shipping_fee = 0;
	}
	
	if(!empty($rsConfig['Integral_Convert'])){
		$integral = ($total_info['total']+$total_info['total_shipping_fee'])/abs($rsConfig['Integral_Convert']);
	}else{
		$integral = 0;
	}
	
	$man_flag = $total_info['man_flag']?1:0;
	$reduce = !empty($total_info['reduce'])?$total_info['reduce']:0;
	
	/*获取可用优惠券信息*/
	//获取优惠券
	
    $coupon_info = get_useful_coupons($_SESSION[$UsersID."User_ID"],$UsersID,$total_info['total']);
	if($coupon_info['num'] >0 ){
		$coupon_html = build_coupon_html($smarty,$coupon_info);
	}else{
		$coupon_html = '';
	}
	

	$Data=array(
		"status"=>1,
		"Sub_Total"=>$Info['Price']*$Info['Qty']+$shipping_fee,
		"Sub_Qty"=>$Info['Qty'],
		"price"=>$price,
		"total_shipping_fee"=>$total_info['total_shipping_fee'],
		"total"=>$total_info['total'],
		"man_flag"=>$man_flag,
		"reduce"=>$reduce,
		"integral"=>$integral,
		"coupon_html"=>$coupon_html
	);
	
	
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);
	
}elseif($action == 'change_shipping_method'){
	
	$rsConfig=$DB->GetRs("shop_config","*","where Users_ID='".$UsersID."'");
	$virtual = isset($_POST['virtual']) ? $_POST['virtual'] : 0;
	$needcart = isset($_POST['needcart']) ? $_POST['needcart'] : 1;
	$Shipping_ID = $_POST['Shipping_ID'];
	$City_Code = $_POST['City_Code'];
	
	$cart_key = get_cart_key($UsersID,$virtual,$needcart);
	$CartList = json_decode($_SESSION[$cart_key],true);
	
	$total_info = get_order_total_info($UsersID,$CartList,$rsConfig,$Shipping_ID,$City_Code);
	
	$Data = array(
	   "status"=>1,
	   "total_shipping_fee"=>$total_info['total_shipping_fee'],
	   "total"=>$total_info['total']
	   );
	
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);
}

function get_final_price($cur_price,$attr_id,$ProductID,$UsersID){
	
	global $DB1;
	
    $_SESSION[$UsersID.'attr_id'] = $attr_id;
	
	$attr_id_str = implode(',',$attr_id);
	
	$condition = "where  Products_ID =".$ProductID;
	$condition .= " And Product_Attr_ID in (".$attr_id_str.")";
   
	$rsProductAttrs = $DB1->Get("shop_products_attr","*",$condition);
	$product_attrs = $DB1->toArray($rsProductAttrs); 
    
	if(!empty($product_attrs)){
		foreach($product_attrs as $key=>$item){
			$cur_price += $item['Attr_Price']; 
		}
	}
	
	return $cur_price;
	
}

/*获取浮点数*/
function getFloatValue($f,$len)
{
  $tmpInt=intval($f);

  $tmpDecimal=$f-$tmpInt;
  $str="$tmpDecimal";
  $subStr=strstr($str,'.');
  if($tmpDecimal != 0){	
	 if(strlen($subStr)<$len+1)
 	{
  		$repeatCount=$len+1-strlen($subStr);
  		$str=$str."".str_repeat("0",$repeatCount);

 	}
	
	$result = $tmpInt."".substr($str,1,1+$len);
  }else{
  	$result =  	$tmpInt.".".str_repeat("0",$len);
  }

  return   $result;

} 

?>