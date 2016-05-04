<?php
/**
 *订单的观察者
 */
class OrderObserver {
	
	private $shop_config;  //店铺配置
	private $user_config;  //店铺用户相关配置
	private $order;
	private $user;	
	

	/**
	 *订单确认收货之后的事情
	 */
	public function confirmed($order){

		$this->shop_config = shop_config($order['Users_ID']);
		$this->user_config = shop_user_config($order['Users_ID']);
		
		$this->order = $order;	 
		
		$flag_a = $this->handle_user_info();
		
		$flag_b = $flag_c = $flag_d =true;
		
		if($order->disAccountRecord()->count() > 0){
			//更改分销账户得钱记录状态
			$flag_b = $this->handle_dis_record_info ();  
			//处理分销账号信息，增加余额，总收入，以及晋级操作
			$flag_c = $this->handle_dis_account_info ();  
		}
		
		   //获取本店分销配置

		if($this->shop_config['Dis_Agent_Type'] != 0){
			$flag_d = $this->handle_dis_agent_info();
		}
	
	    if(!$flag_a && $flag_b && $flag_c && $flag_d){
		
			$response = array(
				"status"=>0,
				"msg"=>'确认收货失败'
			);
			echo json_encode($response,JSON_UNESCAPED_UNICODE);
		}
	
	
	}
	
	
	/**
	 * 处理用户信息
	 * 更新用户积分，用户销售额等信息
	 * 增加积分记录
	 */
	private function handle_user_info() {
		$order = $this->order;
		
		// 用户级别设置
		$User_Level_Json = $this->user_config['UserLevel'];
		$User_Level_Config = json_decode ( $User_Level_Json, TRUE );
		
		$interval = 0;
		if (! empty ( $this->shop_config ['Integral_Convert'] )) {
			$interval = intval ( $order ['Order_TotalPrice'] / abs ( $this->shop_config ['Integral_Convert'] ) );
		}
		
		$user = $order->User ()->getResults ();
		$this->user = $user;
		
		$user->User_Integral = $user->User_Integral + $interval;
		$user->User_TotalIntegral = $user->User_TotalIntegral + $interval;
		$user->User_Cost = $user->User_Cost + $order->Order_TotalAmount;
		
		if (count ( $User_Level_Config ) > 1) {
			$level = $user->determineUserLevel($User_Level_Config,$order->Order_TotalAmount);
			if ($level > $user->User_Level) {
				$user->User_Level = $level;
			}
		}
		
		$res = $user->save ();
		
		// 增加积分记录
		if ($interval > 0) {
			$this->handle_integral_record ( $interval );
		}
		
		// 发送积分变动信息
		if ($interval > 0) {
				require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/weixin_message.class.php');
				global $DB1;
				$weixin_message = new weixin_message($DB1,$order['Users_ID'],$order['User_ID']);
				$contentStr = '购买商品送 '.$interval.' 个积分';
				$weixin_message->sendscorenotice($contentStr);
		}
		
		return $res;
	}
	
	/**
	 * 增加用户积分记录
	 */
	private function handle_integral_record($interval) {
		$user_integral_record = new User_Integral_Record ();
		
		$user_integral_record->Record_Integral = $interval;
		$user_integral_record->Record_SurplusIntegral = $this->user ['User_Integral'] + $interval;
		$user_integral_record->Operator_UserName = '';
		$user_integral_record->Record_Type = 2;
		$user_integral_record->Record_Description = '购买商品送 ' . $interval . ' 个积分';
		$user_integral_record->Record_CreateTime = time ();
		$user_integral_record->User_ID = $this->user ['User_ID'];
		$user_integral_record->Users_ID = $this->user['Users_ID'];
		$flag = $user_integral_record->save ();
		
		return $flag;
	}
	
	/**
	 * 处理分销记录信息
	 */
	private function handle_dis_record_info() {
		
		// 将分销记录设置为完成
		$flag_a = $this->order->disRecord ()->rawUpdate ( [ 
				'status' => 1 
		] );
		
		// 将分销账号记录置为完成
		$flag_b = $this->order->disAccountRecord ()->rawUpdate ( [ 
				'Record_Status' => 2 
		] );
		
		return $flag_a && $flag_b;
	}
	
	/**
	 * 增加分销账号余额,总销售额
	 */
	private function handle_dis_account_info() {
	
		
		// 得到获得佣金的UserID
		$disAccountRecord = $this->order->disAccountRecord ()->getResults ();
		
		$userID_List = $disAccountRecord->map ( function ($disAccountRecord) {
			return $disAccountRecord->User_ID;
		} )->all ();
		
		$userIDS = array_unique ( $userID_List );
	  
	    $pro_titles = Dis_Config::get_dis_pro_title($this->order->Users_ID);
		if (! empty ( $userIDS )) {
			
			foreach ( $userIDS as $key => $item ) {
				$interest_list [$item] = 0;
				$sales_list [$item] = 0;
			}
			
		
		
			foreach ( $disAccountRecord as $key => $accountRecord ) {
				$interest_list [$accountRecord->User_ID] += $accountRecord->Record_Money;
				$DisRecord = $accountRecord->DisRecord ()->getResults ();
				$sales_list [$accountRecord->User_ID] += $DisRecord->Product_Price * $DisRecord->Qty;
			}
		
		
			$disAccoutn_list = Dis_Account::where('Users_ID',$this->order->Users_ID)
										   ->whereIn('User_ID',$userIDS )
										   ->get();
			
			// 取出所有获得佣金者的分销账号
			$flag = FALSE;
			foreach ( $disAccoutn_list as $disAccount ) {
	
				$disAccount->balance = $disAccount->balance + $interest_list[$disAccount->User_ID];
				$disAccount->Total_Income = $disAccount->Total_Income + $interest_list [$disAccount->User_ID];
				//更新用户销量，用进行晋级操作
				$disAccount = $this->up_professional_title_by_group_sales($disAccount);
				$flag = $disAccount->save();
			
				if (! $flag) {
					break;
				}

			}
			
		}
		
	}


	/**
	 *
	 * @return DisAccount $disAccount 需要晋级的分销账号
	 */
	private function up_professional_title_by_group_sales($dsAccount) {


	//获取本站分销账号级别信息
	$pro_titles = Dis_Config::get_dis_pro_title($dsAccount->Users_ID);
	//获取此订单店主分销账号信息
  	
	$dsAccount->Total_Sales = $dsAccount->Total_Sales + $this->order->Order_TotalPrice;
	$dsAccount->Group_Sales = $dsAccount->Group_Sales + $this->order->Order_TotalPrice;

	if (!empty($pro_titles)) {

		$top_level = count($pro_titles);
		
		$total_sales = $dsAccount->Total_Sales;
		$group_sales = $dsAccount->Group_Sales;

		$up_group_sales = $dsAccount->Up_Group_Sales + $this->order->Order_TotalPrice;
		
		$Ex_Bonus = $dsAccount->Ex_Bonus;
		$last_award_income = $dsAccount->last_award_income;
		$pro_title = $dsAccount->Professional_Title;


		//已经是最高级
		if ($pro_title == $top_level) {

			//最高级利润率
			if (count($pro_titles) > 2) {
				$top_up_stock = $pro_titles[$top_level]['Saleroom'] - $pro_titles[$top_level - 1]['Saleroom'];
			} else {
				$top_up_stock = $pro_titles[$top_level];
			}

			if ($up_group_sales >= $top_up_stock) {
				$income = $dsAccount->Total_Income - $last_award_income;
				$Bonus = $pro_titles[$top_level]['Bonus'];
				$rate = $Bonus / 100;
				$Ex_Bonus += $income * $rate;
				$last_award_income = $dsAccount->Total_Income;
				$up_group_sales = $up_group_sales - $top_up_stock;
			}

			$cur_title = $pro_title;

		} else {
			//不是最高级

			$cur_title = determine_dis_protitle_by_group_sales($pro_titles, $group_sales, $pro_title);

			if ($cur_title > $pro_title) {
				
				$income = $dsAccount->Total_Income - $last_award_income;
				$Bonus = $pro_titles[$cur_title]['Bonus'];
				$rate = $Bonus / 100;
				$Ex_Bonus += $income * $rate;

				//计算极差
				if ($cur_title == 1) {
					$level_range = $pro_titles[$cur_title]['Saleroom'];
				} else {
					$level_range = $pro_titles[$cur_title]['Saleroom'] - $pro_titles[$cur_title - 1]['Saleroom'];
				}

				$last_award_income = $dsAccount->Total_Income;
				$up_group_sales = $up_group_sales - $level_range;
			}

		}

		$dsAccount->Up_Group_Sales = $up_group_sales;
		$dsAccount->Professional_Title = $cur_title;
		$dsAccount->Ex_Bonus = $Ex_Bonus;
		$dsAccount->last_award_income = $last_award_income;
		}

		return $dsAccount;

	}

	
    /**
     *处理分销代理信息 
     */
	private function handle_dis_agent_info(){
		
		if($this->shop_config['Dis_Agent_Type'] == 1){
			$res = $this->handle_common_dis_agent();
		}else{
			$res = $this->handle_area_dis_agent();
		}
		
		return $res;
	   
	}
	
	/**
	 * 处理普通分销代理信息
	 * @return boolean
	 */
	private function handle_common_dis_agent(){
		
		$Users_ID = $this->order->Users_ID;
		$User_ID = $this->order->User_ID;
		
		$user = $this->user = User::find($User_ID);		
		$owner_id = $user->Owner_Id;
		$Agent_Rate = $this->shop_config['Agent_Rate'];
		$order = $this->order;
		
		$flag = false;
		//如果此用户不是根用户
		if($owner_id != 0){
			$root_id = get_root_id($Users_ID,$owner_id);
			
			if($root_id != 0){
				
				//计算出用户应得钱数
				if($Agent_Rate > 0 ){
					$total_price = $order->Order_TotalPrice;
					$record_money = $total_price*$Agent_Rate/100;
					$flag_a = $this->do_agent_award($root_id,$record_money);
					$flag_b = $this->add_agent_award_record($root_id,$record_money);
					$flag = $flag_a&&$flag_b;
				}
			}
		}
		
		return $flag;
		
	}
	
	
	/**
	 * 处理地区分销代理信息
	 */
	private function handle_area_dis_agent(){
		
	    $order = $this->order;

	    $area_rate = json_decode($this->shop_config['Agent_Rate'],TRUE);
	    $province_rate = $area_rate['Province'];
	    $city_rate = $area_rate['City'];
	  	
		$user = $order->User()->getResults();
		$User_Province = trim($user->User_Province);
		$User_City = trim($user->User_City);
		
		$area_agents = Dis_Agent_Area::where('Users_ID',$order->Users_ID)
					   ->whereIn('area_name',array($User_Province,$User_City))
					   ->get();
		
		foreach($area_agents as $key=>$agent_area){
			
			//省代account_id
			if($agent_area->type == 1){
				$province_agent_id = $agent_area->Account_ID;
				if($province_rate > 0 ){
					$record_money = $order->Order_TotalPrice*$province_rate/100;
					$this->do_agent_award($province_agent_id,$record_money);
					$this->add_agent_award_record($province_agent_id,$record_money,2);
				}
			}
			
			//市代account_id
			if($agent_area->type == 2){
				$city_agent_id =  $agent_area->Account_ID;
				if($city_agent_id > 0 ){
					$record_money = $order->Total_Income*$city_rate;
					$this->do_agent_award($city_agent_id,$record_money);
					$this->add_agent_award_record($city_agent_id,$record_money,3);
				}
			}
		}
	
		
	}
	
	/*
	*给代理人添加佣金
	*添加代理记录
	*/
	private function do_agent_award($root_id,$record_money){		
	
		$UsersID = $this->order['Users_ID'];
		$atributes = array('balance'=>'`balance+100`');
	
		$dis_account = Dis_Account::find($root_id);
								
        $balance = $dis_account->balance +$record_money;
		$Total_Income = $dis_account->Total_Income +$record_money;
		$dis_account->balance = $balance;
		$dis_account->Total_Income = $Total_Income;

		$flag = $dis_account->save();						 
		return $flag;
	}
	
	/**
	 *添加代理奖励记录
	 */
	private function add_agent_award_record($root_id,$record_money,$type = 1){
		$dis_agent_record = new Dis_Agent_Record();
		
		$order = $this->order;
		$dis_agent_record->Users_ID = $order->Users_ID; 
		$dis_agent_record->Account_ID = $root_id;
		$dis_agent_record->Record_Money = $record_money;
		$dis_agent_record->Record_CreateTime = time();
		$dis_agent_record->Record_Type = $type;
		
		$flag = $dis_agent_record->save();
		return $flag;
	}
	
	
	
	
}


















