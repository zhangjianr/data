<?php
/**
 *分销账号观察者
 */
class DisAccountObserver{
	
	private $ds_account;
	private $account_list;

	
	
	/**
	 *分销账号创建以后的后续操作
	 */
	public function created($ds_account){

		$this->ds_account = $ds_account;
		$this->get_account_list($ds_account->Users_ID);		
		
		
		$User_ID = $ds_account->User_ID;
		
		//将用户身份改为分销商
		$flag_a = User::find($User_ID)->update(array('Is_Distribute'=>1));
		$flag_b = $this->handle_group_num();
		
		return $flag_a&&$flag_b;
	}
	
	/*
	 *处理分销团队人数,若符合升级条件，则升级并给予额外奖励
	 *
	 */
	private function handle_group_num(){
		
		$UsersID = $this->ds_account->Users_ID;
		$Owner_ID = $this->ds_account->Owner_ID;
		$User_ID = $this->ds_account->User_ID;
		
		$ancestors = get_ancestor($UsersID,$Owner_ID,$User_ID,$this->account_list);

		$Flag = TRUE;
		if(!empty($ancestors)){
			foreach($ancestors as $key=>$item){
				//增加祖先团队数量，若符合升级条件，则升级并给予额外奖励
				$item_flag = $this->up_professional_title_by_group_num($item['User_ID']);
				
				if(!$item_flag){
					$Flag = FALSE;
					break;
				}
			}
			
		}
		
		return $Flag;
		
	}
	
	
	private function up_professional_title_by_group_num($UserID){
		
		$ds_dropdown = get_dropdown_list($this->account_list, 'User_ID');
		$pro_titles = Dis_Config::get_dis_pro_title($this->ds_account['Users_ID']);
		$ds_account = $ds_dropdown[$UserID];

		$data = array();
		$data['Group_Num'] = $ds_account['Group_Num'] + 1;
		$Flag = TRUE;
		
		if (!empty($pro_titles)) {
		
			$top_level = count($pro_titles);

			$up_group_num = $ds_account['Up_Group_Num'];
			$Ex_Bonus = $ds_account['Ex_Bonus'];
			$last_award_income = $ds_account['last_award_income'];
			$pro_title = $ds_account['Professional_Title'];

		//已经是最高级
		if ($pro_title == $top_level) {
			if (count($pro_titles) > 2) {
				$top_up_stock = $pro_titles[$top_level]['Group_Num'] - $pro_titles[$top_level - 1]['Group_Num'];
			} else {
				$top_up_stock = $pro_titles[$top_level]['Group_Num'];
			}

			//增加人数后需要奖励
			if ($top_up_stock == $up_group_num + 1) {
				$income = $ds_account['Total_Income'] - $last_award_income;
				$Bonus = $pro_titles[$top_level]['Bonus'];
				$rate = $Bonus / 100;
				$Ex_Bonus += $income * $rate;
				$last_award_income = $ds_account['Total_Income'];
				$up_group_num = 0; //奖励成功后升级所需用户数被置零
			} else {
				//未奖励
				$up_group_num = $up_group_num + 1;
			}

		} else {

			$cur_level = determine_dis_protitle_by_num($pro_titles, $data['Group_Num'], $pro_title);

			//升级
			if ($cur_level > $pro_title) {
				
				$income = $ds_account['Total_Income'] - $last_award_income;
				$Bonus = $pro_titles[$cur_level]['Bonus'];
				$rate = $Bonus / 100;
				$Ex_Bonus += $income * $rate;
				$last_award_income = $ds_account['Total_Income'];
				$up_group_num = 0; //奖励成功后升级所需用户数被置零
				$pro_title = $cur_level;
			} else {
				$up_group_num = $up_group_num + 1;
			}

		}

		$data['Ex_Bonus'] = $Ex_Bonus;
		$data['Up_Group_Num'] = !empty($up_group_num) ? $up_group_num : 0;
		$data['Professional_Title'] = !empty($pro_title) ? $pro_title : 0;
		$data['last_award_income'] = $last_award_income;
		
		$account_id = $ds_account['Account_ID'];


		$Flag = Dis_Account::find($account_id)->update($data);
		
		}
	
		return $Flag;
		 
	}
	
	/**
	 * 
	 * @param String $UsersID 店铺唯一标示
	 * @return Array此店所有分销账号列表 
	 */
	private function get_account_list($UsersID){

		$fields = array('Users_ID','User_ID','invite_id','User_Name','Account_ID',
			            'Shop_Name','Group_Num','Up_Group_Num','Ex_Bonus','last_award_income',
			            'Professional_Title');
		$account_list = Dis_Account::where(array('Users_ID'=>$UsersID))
								       ->get($fields)
									   ->toArray();
		$this->account_list = $account_list;
	}
	
	
}   
   