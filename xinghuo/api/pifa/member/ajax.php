<?php 
//ini_set("display_errors","On");
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/order.php');
$base_url = base_url();
if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo 'error';
	exit;
}

$action=empty($_REQUEST["action"])?"":$_REQUEST["action"];
if($action=="address"){
	if(empty($_POST['AddressID'])){
		//增加
		$Data=array(
			"Address_Name"=>$_POST['Name'],
			"Address_Mobile"=>$_POST["Mobile"],
			"Address_Province"=>$_POST["Province"],
			"Address_City"=>$_POST["City"],
			"Address_Area"=>$_POST["Area"],
			"Address_Detailed"=>$_POST["Detailed"],
			"Users_ID"=>$UsersID,
			"User_ID"=>$_SESSION[$UsersID."User_ID"]
		);
		$Flag=$DB->Add("user_address",$Data);
	
	}else{
		//修改
		$Data=array(
			"Address_Name"=>$_POST['Name'],
			"Address_Mobile"=>$_POST["Mobile"],
			"Address_Province"=>$_POST["Province"],
			"Address_City"=>$_POST["City"],
			"Address_Area"=>$_POST["Area"],
			"Address_Detailed"=>$_POST["Detailed"]
		);
		$Flag=$DB->Set("user_address",$Data,"where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Address_ID=".$_POST['AddressID']);
	}
	
	if($Flag){
		$Data=array(
			"status"=>1
		);
	}else{
		$Data=array(
			"status"=>0
		);
	}
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);
	
}elseif($action == "apply_backup"){

	$back_list = array();
	foreach($_POST['Products_ID'] as $Products_ID){
		$back_list[$Products_ID] = array(
									'Products_Name'=>$_POST['Products_Name'][$Products_ID],
									'Products_Image'=>$_POST['Products_Image'][$Products_ID],
									'Products_Price'=>$_POST['Products_Price'][$Products_ID],
									'back_num'=>$_POST['backup_num'][$Products_ID],
									'reason'=>$_POST['reason'][$Products_ID]	
									);
	}
	
	$data = array('Users_ID'=>$UsersID,
				  'User_ID'=>$_SESSION[$UsersID.'User_ID'],
				  'Back_SN' => build_order_no(),
				  'Back_Type'=>'pifa',
				  'Back_Json'=>json_encode($back_list,JSON_UNESCAPED_UNICODE),
				  'Back_Status'=>0,
				  'Back_CreateTime'=>time(),
				  'Order_ID'=>$_POST['Order_ID'],);
				  
	//获取店主ID
	$owner = getOwner($DB,$UsersID);
	$data['Owner_ID'] = $owner['id'];
				  
	$Flag = $DB->add('user_back_order',$data);
	$Order_ID = $_POST['Order_ID'];
	$condition = "where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Order_ID=".$Order_ID;
	$data = array('Is_Backup'=>1);
	
	$DB->set("user_order",$data,$condition);
	
	if($Flag){
		$Data=array(
			"status"=>1,
			"url"=>$base_url.'api/'.$UsersID.'/pifa/member/backup/status/0/'
		);
	}else{
		$Data=array(
			"status"=>0
		);
	}
	
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);
	
}elseif($action=="commit"){
	$OrderID = $_POST["OrderID"];
	$rsConfig = $DB->GetRs("shop_config","Commit_Check","where Users_ID='".$UsersID."'");
	//商城配置一股脑转换批发配置
	$Config = $DB->GetRs("pifa_config","*","where Users_ID='".$UsersID."'");
	$rsConfig['ShopName'] = $Config['PifaName'];
	$rsConfig['NeedShipping'] = $Config['p_NeedShipping'];
	$rsConfig['SendSms'] = $Config['p_SendSms'];
	$rsConfig['MobilePhone'] = $Config['p_MobilePhone'];
	$rsConfig['Commit_Check'] = $Config['p_Commit_Check'];
	
	$rsOrder=$DB->GetRs("user_order","*","where Order_ID=".$OrderID." and User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."' and Order_Status=4");
	if(!$rsOrder){
		$Data=array(
			"status"=>2,
			"msg"=>"无此订单"
		);
		
	}else{
		if($rsOrder["Is_Commit"]==1){
			$Data=array(
				"status"=>4,
				"msg"=>"此订单已评论过，不可重复评论"
			);
		}else{
			$Data1=array(
				"Is_Commit"=>1
			);
			
			$DB->Set("user_order",$Data1,"where Order_ID=".$OrderID);
			$CartList=json_decode($rsOrder["Order_CartList"],true);
			foreach($CartList as $key=>$v){
				$Data=array(
					"MID"=>$rsOrder["Order_Type"],
					"Order_ID"=>$OrderID,
					"Product_ID"=>$key,
					"Score"=>$_POST["Score"],
					"Note"=>$_POST["Note"],
					"Status"=>$rsConfig["Commit_Check"]==1 ? 1 : 0,
					"Users_ID"=>$UsersID,
					"User_ID"=>$_SESSION[$UsersID."User_ID"],
					"CreateTime"=>time()
				);
				$DB->Add("user_order_commit",$Data);
			}
			
			$Data=array(
				"status"=>1
			);
		}
	}
	echo json_encode($Data,JSON_UNESCAPED_UNICODE);
	
}elseif($action == 'submit_shipping'){
	
	$Back_ID = $_POST['Back_ID'];
	$data = array("Back_Shipping"=>$_POST['Back_Shipping'],
				  "Back_ShippingID"=>$_POST['Back_ShippingID'],
				  "Back_Status"=>2);
	
	$condition = "where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID."User_ID"]."' and Back_ID=".$Back_ID;
	
	$DB->set('user_back_order',$data,$condition);			  
	$response = array(
		"status"=>1,
		"url"=>$base_url.'api/'.$UsersID.'/pifa/member/backup/status/2/'
	);
	
	echo json_encode($response,JSON_UNESCAPED_UNICODE);

}elseif($action == 'confirm_receive'){
	
		Order::observe(new OrderObserver());
		$Order_ID = $_POST['Order_ID'];
		$order = Order::find($Order_ID);
		$Flag = $order->confirmReceive();
	
		if($Flag)
		{
			$response = array(
				"status"=>1,
				"url"=>$base_url.'api/'.$UsersID.'/pifa/member/backup/status/3/'
			);

			echo json_encode($response,JSON_UNESCAPED_UNICODE);
		}else
		{
			$response = array(
				"status"=>0,
				"msg"=>'确认收货失败'
			);
			echo json_encode($response,JSON_UNESCAPED_UNICODE);
		}
		
		exit();
		
}

/**
 * 得到新订单号
 * @return  string
 */
function build_order_no()
{
    /* 选择一个随机的方案 */
    mt_srand((double) microtime() * 1000000);
    return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}
?>