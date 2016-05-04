<?php
include_once $_SERVER["DOCUMENT_ROOT"].'/include/observers/spark.class.php';
include_once $_SERVER["DOCUMENT_ROOT"] . '/include/library/traffic.class.php';
class pay_order{
	var $db;
	var $orderid;
	public $transaction_id;
	function __construct($DB,$orderid,$transaction_id = ""){
		$this->db = $DB;
		$this->orderid = $orderid;
		$this->transaction_id = $transaction_id;
	}
	
	private function get_order($orderid){
		$r = $this->db->GetRs("user_order","*","where Order_ID=".$orderid);
		return $r;
	}
	
	private function get_user($userid){
		$r = $this->db->GetRs("user","*","where User_ID=".$userid);
		return $r;
	}
	
	private function get_products($pid){
		$r = $this->db->GetRs("shop_products","*","where Products_ID=".$pid);
		return $r;
	}
	
	private function get_shopconfig($usersid){
		$r = $this->db->GetRs("shop_config","*","where Users_ID='".$usersid."'");
		return $r;
	}
	
	private function update_order($orderid,$data){
		$this->db->Set("user_order",$data,"where Order_ID=".$orderid);
	}
	
	public function pay_orders(){
		$orderid = $this->orderid;
		if(strpos($this->orderid,'gift')>-1){
			$data = $this->pay_gift_order();
			return $data;
		}
		if(strpos($this->orderid,'SPARK')>-1){
			$data = $this->pay_spark_order();
			return $data;
		}
		if(strpos($this->orderid,'TRA')>-1){
			$data = $this->pay_tra_order();
			return $data;
		}
		$rsOrder = $this->get_order($orderid);
		$rsUser = $this->get_user($rsOrder["User_ID"]);
		
		if(!$rsOrder){
			return array("status"=>0,"msg"=>"订单不存在");
		}
		
		$url = '/api/'.$rsOrder["Users_ID"].'/'.$rsOrder["Order_Type"].'/member/status/'.$rsOrder["Order_Status"].'/';
		
		if($rsOrder["Order_Status"]<>1){
			return array("status"=>1,"url"=>$url);
		}
		//更新订单状态
		$Data = array(
			"Order_Status" => 2
		);
		$this->update_order($orderid, $Data);
		if(strpos($rsOrder["Order_Type"],'zhongchou')>-1){
			$url = '/api/'.$rsOrder["Users_ID"].'/zhongchou/orders/';
			return array("status"=>1,"url"=>$url);
		}
		
		if($rsOrder["Order_Type"]=='kanjia'){
			$url = "/api/".$rsOrder["Users_ID"]."/user/kanjia_order/status/1/";
			return array("status"=>1,"url"=>$url);
		}
		
		//积分抵用
		$Flag_b = TRUE;
		if($rsOrder["Integral_Consumption"] > 0 ){
			$Flag_b = change_user_integral($rsOrder["Users_ID"],$rsOrder["User_ID"],$rsOrder["Integral_Consumption"],'reduce','积分抵用消耗积分');
		}
		
		//更改分销账号记录状态,置为已付款
		$Flag_c = change_dsaccount_record_status($orderid,1);
		
		handle_products_count($rsOrder["Users_ID"],$rsOrder);
		
		if($Flag_b&&$Flag_c){
			$isvirtual = $rsOrder["Order_IsVirtual"];
			$url = '/api/'.$rsOrder["Users_ID"].'/'.$rsOrder["Order_Type"].'/member/status/2/';
			$rsConfig=$this->get_shopconfig($rsOrder["Users_ID"]);				
			$CartList = json_decode($rsOrder["Order_CartList"], true);
			$distribute_enabled = 0;
			$tixian = 0;
			if($rsConfig["Withdraw_Type"]==1){
				$tixian = 1;
			}
			foreach ($CartList as $ProductID => $product_list){
				if($rsConfig["Distribute_Type"]==3 && $rsConfig["Distribute_Limit"]==$ProductID){
					$distribute_enabled = 1;
				}
				
				if($rsConfig["Withdraw_Type"]==2 && $rsConfig["Withdraw_Limit"]==$ProductID){
					$tixian = 1;
				}
			}
					
			if($rsUser["Is_Distribute"]==0 && $distribute_enabled==1){
				$truename = $rsUser["User_Name"] ? $rsUser["User_Name"] : ($rsUser["User_NickName"] ? $rsUser["User_NickName"] : '真实姓名');
				$owner["id"] = $rsUser["Owner_Id"];
				
				create_distribute_acccount($rsConfig, $rsOrder['User_ID'], $truename, $owner, '',1);
				
			}
						
			$confirm_code = '';
			if($rsOrder["Order_IsVirtual"]==1){
				if($rsOrder["Order_IsRecieve"]==1){
			
					Order::observe(new OrderObserver());
					$order = Order::find($orderid);
					$Flag = $order->confirmReceive();
					
					$url="/api/".$rsOrder["Users_ID"]."/".$rsOrder["Order_Type"]."/member/status/4/";
				
				}else{
					$confirm_code = get_virtual_confirm_code($rsOrder["Users_ID"]);
					$Data = array('Order_Code'=>$confirm_code);
					$this->update_order($orderid,$Data);
				}
			}
			
			$setting = $this->db->GetRs("setting","sms_enabled","where id=1");
			if($rsConfig["SendSms"]==1 && $setting["sms_enabled"]==1){
				if($rsConfig["MobilePhone"]){
					$sms_mess = '您的商品有订单付款，订单号'.$orderid.'请及时查看！';
					send_sms($rsConfig["MobilePhone"], $sms_mess, $rsOrder["Users_ID"]);
				}					
				if($rsOrder["Order_IsVirtual"]==1 && $rsOrder["Order_IsRecieve"]==0){
					$sms_mess = '您已成功购买商品，订单号'.$orderid.'，消费券码为 '.$confirm_code;
					send_sms($rsOrder["Address_Mobile"], $sms_mess, $rsOrder["Users_ID"]);
				}
			}
			
			if($tixian==1 && $rsUser["Is_Distribute"]==1){
				$rsAccount = $this->db->GetRs("shop_distribute_account","Enable_Tixian,Account_ID","where Users_ID='".$rsOrder["Users_ID"]."' and User_ID=".$rsOrder["User_ID"]);
				if($rsAccount){
					if($rsAccount["Enable_Tixian"]==0){
						$this->db->Set("shop_distribute_account",array("Enable_Tixian"=>1),"where Users_ID='".$rsOrder["Users_ID"]."' and Account_ID=".$rsAccount["Account_ID"]);
					}
				}
			}
			require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/weixin_message.class.php');
			$weixin_message = new weixin_message($this->db,$rsOrder["Users_ID"],$rsOrder["User_ID"]);
			$weixin_message->sendorder($rsOrder["Order_TotalPrice"],$orderid);
			return array("status"=>1,"url"=>$url);
		}else{
			return array("status"=>0,"msg"=>"订单支付失败");
		}
	}
	
	public function get_pay_info(){
		if(strpos($this->orderid,'gift')>-1){
			$data = $this->get_gift_order();
			return $data;
		}
		
		$orderinfo = $this->get_order($this->orderid);
		if(!strpos($orderinfo["Order_Type"],'zhongchou')){
			if($orderinfo["Order_Type"]=='weicbd'){
				$pay_subject = "微商圈在线付款，订单编号:".$this->orderid;
			}elseif($orderinfo["Order_Type"]=='kanjia'){
				$pay_subject = "微砍价在线付款，订单编号:".$this->orderid;
			}else{
				$pay_subject = "微商城在线付款，订单编号:".$this->orderid;
			}
		}else{
			$pay_subject = "微众筹在线付款，订单编号:".$this->orderid;
		}
		$data = array(
			"out_trade_no"=>$orderinfo["Order_CreateTime"].$this->orderid,
			"subject"=>$pay_subject,
			"total_fee"=>$orderinfo["Order_TotalPrice"]
		);
		return $data;
	}
	
	public function get_pay_package(){
		$orderId = $this->orderid;
		$orderinfo = $this->db->GetRs("spark_order","*","WHERE orderId='{$orderId}'");
		$pay_subject = $orderinfo['packageLevelName']."，订单编号:".$orderId;
		$data = array(
			"out_trade_no"=>$orderId,
			"subject"=>$pay_subject,
			"total_fee"=>$orderinfo["price"]
		);
		return $data;
	}
	
	public function get_pay_traffic(){
		$orderId = $this->orderid;
		$orderinfo = $this->db->GetRs("spark_traffic_order","*","WHERE orderId='{$orderId}'");
		$pay_subject = $orderinfo['name']."，订单编号:".$orderId;
		$data = array(
			"out_trade_no"=>$orderId,
			"subject"=>$pay_subject,
			"total_fee"=>$orderinfo["priceX"]
		);
		return $data;
	}	
	
	private function get_gift_order(){
		$ordersid = substr($this->orderid,4);
		$orderinfo = $this->db->GetRs("user_gift_orders","*","where Orders_ID=".$ordersid);
		$pay_subject = "积分换礼支付，订单编号:".$ordersid;
		$data = array(
			"out_trade_no"=>'gift'.$ordersid,
			"subject"=>$pay_subject,
			"total_fee"=>$orderinfo["Orders_TotalPrice"]
		);
		return $data;
	}
	
	private function pay_gift_order(){
		$ordersid = substr($this->orderid,4);
		$orderinfo = $this->db->GetRs("user_gift_orders","*","where Orders_ID=".$ordersid);
		if(!$orderinfo){
			return array("status"=>0,"msg"=>"订单不存在");
		}
		
		$Data = array(
			'Orders_Status'=>1
		);
		$Flag = $this->db->Set("user_gift_orders",$Data,"where Orders_ID=".$ordersid);
		if($Flag){
			$data = array(
				'status'=>1,
				"url"=>'/api/'.$orderinfo["Users_ID"].'/user/gift/my/'
			);
		}else{
			$data = array(
				'status'=>0,
				"msg"=>'订单支付失败'
			);
		}
		return $data;
	}
	
	private function pay_spark_order(){
		$orderid = $this->orderid;
		$orderinfo = $this->db->GetRs("spark_order","*","where orderId='{$orderid}'");
		if(!$orderinfo){
			return array("status"=>0,"msg"=>"订单不存在");
		}
		$package = $this->db->GetRs("spark_package","*"," WHERE Users_ID='{$orderinfo['Users_ID']}' AND id='{$orderinfo['packageId']}'");
		$orderData = array(
			"isUp"=>0,
			"transaction_id"=>$this->transaction_id,
			'payCode'=>1
		);
		$FlagA = $this->db->Set("spark_order",$orderData,"where orderId='{$orderid}'");
		if($FlagA === FALSE){
			return array("status"=>0,"msg"=>"订单支付状态更新失败");
		}
		$userInfo = $this->db->GetRs("spark_user","*","WHERE Users_ID='{$orderinfo['Users_ID']}' AND User_ID='{$orderinfo['User_ID']}'");
		if($orderinfo['isUp'] || !empty($userInfo)){
			$userData = array(
				"realName"			=> $orderinfo['realName'],
				"nickName"			=> $orderinfo['nickName'],
				"mobile"			=> $orderinfo['mobile'],
				"address"			=> $orderinfo['address'],
				"packageId"			=> $orderinfo['packageId'],
				"price"				=> $package['price'],
				"packageLevelName"	=> $orderinfo['packageLevelName'],
				"createtime"		=> time()
			);
			$FlagB = $this->db->Set("spark_user",$userData, "WHERE Users_ID='{$orderinfo['Users_ID']}' AND User_ID='{$orderinfo['User_ID']}'");
			if($FlagB === FALSE){
				return array("status"=>0,"msg"=>"订单升级失败");
			}else{
				$spark = new spark($this->db, $orderid);
				$resSpark = $spark->start();
			}
		}else{
			$userData = array(
				"Users_ID"			=> $orderinfo['Users_ID'],
				"User_ID"			=> $orderinfo['User_ID'],
				"realName"			=> $orderinfo['realName'],
				"nickName"			=> $orderinfo['nickName'],
				"mobile"			=> $orderinfo['mobile'],
				"address"			=> $orderinfo['address'],
				"packageId"			=> $orderinfo['packageId'],
				"price"				=> $orderinfo['price'],
				"packageLevelName"	=> $orderinfo['packageLevelName'],
				"createtime"		=> time()
			);
			$FlagB = $this->db->Add("spark_user",$userData);
			if($FlagB === FALSE){
				return array("status"=>0,"msg"=>"订单添加用户失败");
			}
			if($FlagB){
				$spark = new spark($this->db, $orderid);
				$resSpark = $spark->start();
			}
		}
		if($FlagA && $FlagB){
			$data = array(
				'status'=>1,
				"url"=>'/api/'.$orderinfo["Users_ID"].'/shop/spark/my/',
				"msg"=>"SUCCESS"
			);
		}else{
			$data = array(
				'status'=>0,
				"msg"=>'订单支付失败'
			);
		}
		return $data;
	}
	
	private function pay_tra_order(){
		$orderid = $this->orderid;
		$orderinfo = $this->db->GetRs("spark_traffic_order","*","where orderId='{$orderid}'");
		if(!$orderinfo){
			return array("status"=>0,"msg"=>"订单不存在");
		}
		$orderData = array(
			"transaction_id"=>$this->transaction_id,
			'payCode'=>1
		);
		$FlagA = $this->db->Set("spark_traffic_order",$orderData,"where orderId='{$orderid}'");
		if($FlagA){
			$resDis = new traffic($orderinfo['Users_ID']);
			$resDis->sendTraffic($orderid);
			$resDis->getOrderList($orderid);
			$data = array(
				'status'=>1,
				"url"=>'/api/'.$orderinfo["Users_ID"].'/shop/spark/order/'
			);
		}else{
			$data = array(
				'status'=>0,
				"msg"=>'订单支付失败'
			);
		}
		return $data;
	}
}
?>