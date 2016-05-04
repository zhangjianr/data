<?php
/**
 *订单的观察者
 */
defined('BASEPATH') OR exit('No direct script access allowed');
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
		$order = $this->order->toArray();
		$flag_b = $this->order->disAccountRecord ()->rawUpdate ( [ 
				'Record_Status' => 2,
				'orderId' => $order['Order_ID']
		] );
		
		return $flag_a && $flag_b;
	}
	
	/**
	 * 增加分销账号余额,总销售额
	 */
	private function handle_dis_account_info() {
	
		
		// 得到获得佣金的UserID
		$disAccountRecord = $this->order->disAccountRecord()->getResults ();
		
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
			
		
		
			foreach ( $disAccountRecord as $key =>$accountRecord) {
			
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
		}else if($this->shop_config['Dis_Agent_Type'] == 2){
			$res = $this->handle_area_dis_agent();
		}else if($this->shop_config['Dis_Agent_Type'] == 3){
			$res = $this->handle_channel_dis_agent();
			//echo "====";exit;
		}else if($this->shop_config['Dis_Agent_Type'] == 4){
			$res1 = $this->handle_area_dis_agent();
			$res2 = $this->handle_channel_dis_agent();
			$res=$res1&&$res2;
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
	 * 处理渠道分销代理信息
	 * @return boolean
	 */
	private function handle_channel_dis_agent(){
		$this->handle_up_to_channel();//计算深度内所有上线是否需要升为渠道
		$Users_ID = $this->order->Users_ID;
		$User_ID = $this->order->User_ID;
		
		$user = $this->user = User::find($User_ID);
		$owner_id = $user->Owner_Id;
		$order = $this->order;
		
		$channel_types = Channel_Config::get_dis_channel_type($Users_ID);
		$flag = false;
		//获取所有上线的渠道商
		$channel_ids = get_channel_id($Users_ID,$owner_id);
		//print_r($channel_ids);
		$last_type=0;
			foreach($channel_ids as $k=>$v)
				{
				//计算出用户应得钱数
				$channel_account=Dis_Account::find($v);	
				$type=$channel_account->Channel_Type;
				//echo $last_type;
					if($last_type==0||$type<$last_type)
					{
						if($last_type==0)
						{
							$Channel_Rate=$channel_types[$type]['Bonus'];
						}else{
							$Channel_Rate=$channel_types[$type]['Bonus']-$channel_types[$last_type]['Bonus'];
						}
						//$total_price = $order->Order_TotalPrice;
						//获取商品现价*利润百分率
						$orderGoods = json_decode($order->Order_CartList, true);
						$figures=0;
						foreach($orderGoods AS $key=>$val){
							$tmp_product = Product::Multiwhere(array('Users_ID' => $order->Users_ID, 'Products_ID' => $key))
							->first()
							->toArray();
							$figures += $val[0]['ProductsPriceX']*$val[0]['Qty']*$tmp_product['Products_Profit']*$tmp_product['Channel_Profit']/10000;
						}
						$record_money=$figures*$Channel_Rate/100;
						$flag_a = $this->do_agent_award($v,$record_money);
						$flag_b = $this->add_agent_award_record($v,$record_money);
						$this->handle_up_channel_type($v);//所有渠道上线是否需要升级
						$flag = $flag_a&&$flag_b;
						$last_type=$type;
						
					}

				}
		//echo "222";exit;
		
		return $flag;
		
	}
	
	
	private function handle_up_to_channel(){

		
		//$flag = false;
		//获取所有上线的渠道商
		$Users_ID = $this->order->Users_ID;
		$User_ID = $this->order->User_ID;
		$user = $this->user = User::find($User_ID);
		$Owner_ID = $user->Owner_Id;
		$order = $this->order;
		$channel_depth = Channel_Config::get_dis_channel_Depth($Users_ID);
		$parent_ids = get_ancestor($Users_ID, $Owner_ID, $User_ID, array(), $channel_depth);
		//print_r($channel_ids);
				foreach($parent_ids as $v)
				{
					$this->handle_up_channel_type($v['User_ID']);
				}
		
	}

	/**
	 *
     */
	private function handle_up_channel_type($User_ID){
		$Users_ID = $this->order->Users_ID;
		//自消费
		//echo $User_ID;
		$self=Order::Where(array('Users_ID'=>$Users_ID,'User_ID'=>$User_ID,'Order_Status'=>4))->sum('Order_TotalPrice');

		//直接发展分销商

		$direct=Dis_Account::Where(array('invite_id'=>$User_ID))->count();

		//团队费额
		$Level=Channel_Config::get_dis_channel_Depth($Users_ID);
		$groups=get_child($Users_ID,$User_ID,$Level);
		$group_sum=Order::Where(array('Users_ID'=>$Users_ID,'Order_Status'=>4))->WhereIn('User_ID',$groups)->sum('Order_TotalPrice');
		
		//团队人数
		$group_num=count($groups);
		
		
		$channel_types=Channel_Config::get_dis_channel_type($Users_ID);
		$user = $this->user = User::find($User_ID);
		$channel_type=$user->Channel_Type;
		$channel_type_tmp='';
		foreach($channel_types as $k=>$v){
			// echo $self.'|'.$v['Self'].'</br>';
			// echo $direct.'|'.$v['Direct_Num'].'</br>';
			// echo $group_num.'|'.$v['Group_Num'].'</br>';
			// echo $group_sum.'|'.$v['Group_Sum'].'</br>';
			
			
			if($self>=$v['Self']&&$direct>=$v['Direct_Num']&&$group_num>=$v['Group_Num']&&$group_sum>=$v['Group_Sum'])
			{
				$channel_type_tmp=$k;
				if($channel_type_tmp<$channel_type||$channel_type==0)
				{
					Dis_Account::where(array('Users_Id'=>$Users_ID,'User_id'=>$User_ID))->update(array('Channel_Type'=>$channel_type_tmp,'Enable_Agent'=>3));
				}
			}

		}
//exit;
	}
	
	
	
	/**
	 * 处理地区分销代理信息
	 */
	private function handle_area_dis_agent(){
		
	    $order = $this->order;

	    $area_rate = json_decode($this->shop_config['Agent_Rate'],TRUE);
	    $province_rate = $area_rate['Province'];
	    $city_rate = $area_rate['City'];
	    $county_rate = $area_rate['County'];
	  	
		$user = $order->User()->getResults();
		$User_Province = trim($order->Address_Province);
		$User_City = trim($order->Address_City);
		$User_County = trim($order->Address_Area);
		
		$area_agents = Dis_Agent_Area::where('Users_ID',$order->Users_ID)
					   ->whereIn('area_id',array($User_Province,$User_City,$User_County))
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
					$record_money = $order->Order_TotalPrice*$city_rate/100;
					$this->do_agent_award($city_agent_id,$record_money);
					$this->add_agent_award_record($city_agent_id,$record_money,3);
				}
			}
			
			//县代account_id
			if($agent_area->type == 3){
				$county_agent_id =  $agent_area->Account_ID;
				if($county_agent_id > 0 ){
					$record_money = $order->Order_TotalPrice*$county_rate/100;
					$this->do_agent_award($county_agent_id,$record_money);
					$this->add_agent_award_record($county_agent_id,$record_money,3);
				}
			}
		}
	
		
	}
	/**
	 * 处理独立合伙人分销代理信息
	 */
	private function handle_one_dis_agent(){
		$Users_ID = $this->order->Users_ID;
		$User_ID = $this->order->User_ID;
		
		$user = $this->user = User::find($User_ID);		
		$owner_id = $user->Owner_Id;
		$order = $this->order;
		$flag = false;
		$Agent_Rate = 0;
		//获取一级合伙人
		$one_id = get_one_id($Users_ID,$owner_id);
		foreach($one_id AS $kay=>$val){
			if($val != 0){
				$Agent_Rate = Dis_Agent_One::where('Account_ID',$val)->pluck('Agent_Rate');
				//计算出用户应得钱数
				if($Agent_Rate > 0 ){
					$total_price = $order->Order_TotalPrice;
					$record_money = $total_price*$Agent_Rate/100;
					$flag_a = $this->do_agent_award($val,$record_money);
					$flag_b = $this->add_agent_award_record($val,$record_money);
					$flag = $flag_a&&$flag_b;
				}
			}
		}		
		
		
		return $flag;
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
		$dis_agent_record->Order_ID = $order->Order_ID;
		$dis_agent_record->Users_ID = $order->Users_ID;
		$dis_agent_record->Account_ID = $root_id;
		$dis_agent_record->Record_Money = $record_money;
		$dis_agent_record->Record_CreateTime = time();
		$dis_agent_record->Record_Type = $type;
		//print_r($dis_agent_record);
		$flag = $dis_agent_record->save();
		return $flag;
	}
	
	
	
	
}


















