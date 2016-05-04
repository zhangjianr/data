<?php
/**
 *分销记录观察者
 */
class DisRecordObserver{

	
	private $DisRecord;
	static $Product;
	static  $Qty;
		
		
	//创建此分销记录所产生的佣金记录
	public function created($DisRecord){
		
		$this->DisRecord = $DisRecord;
		if($DisRecord->type == 0){
			
			$account_records = $this->getDistributeAccountRecord();
			
			$flag = $DisRecord->DisAccountRecord()->saveMany($account_records);   
		}
	
	}
	



	/**
	 * 获取本分销记录对应分销佣金记录
	 * @return Array $dis_account_records 分销佣金记录
	 */
	private function getDistributeAccountRecord(){
		
		//获取祖先id
		$UsersID = $this->DisRecord->Users_ID;
		$Owner_ID = $this->DisRecord->Owner_ID;
		$User_ID = $this->DisRecord->Buyer_ID;
		
		$Ds_Record_ID = $this->DisRecord->Record_ID;
		$Product = self::$Product;
		
		$Qty = self::$Qty;
		
		$level=count(json_decode($Product['Products_Distributes']));
		$ancestors = get_ancestor($UsersID,$Owner_ID,$User_ID,array(),$level);
		//print_r($ancestors);exit;
		

		$dis_account_records = array();
		
		
	
		$Distribute_List = $Product['Distribute_List'];
		
	
		foreach ($ancestors as $key => $item) {
			$dis_account_record = new Dis_Account_Record();
			
			
			if ($Owner_ID == $item['User_ID']) {
				//自己获取佣金                                
				$Record_Description = '自己销售' . $Product['Products_Name'] . '&yen;' . $Product['Products_Price']. '成功，获取奖金';
			} else {
				//上级分销商获取佣金
				$Record_Description = '下属分销商分销' . $Product['Products_Name'] . '&yen;' .$Product['Products_Price']. '成功，获取奖金';
			}

			$Record_Money = !empty($Distribute_List[$key]) ? $Product['Products_Price']*$Product['Distribute_List'][$key]*$Qty : 0;
			
			$dis_account_record->Users_ID = $UsersID;
			$dis_account_record->Ds_Record_ID = $Ds_Record_ID;
			$dis_account_record->User_ID = $item['User_ID'];
			$dis_account_record->Record_Sn = build_withdraw_sn();
			$dis_account_record->level = $key+1;
			$dis_account_record->Record_Money = $Record_Money;
			$dis_account_record->Record_CreateTime = time();
			$dis_account_record->Record_Description = $Record_Description;
			$dis_account_record->Record_Type = 0;
			$dis_account_record->Record_Status = 0;
			$dis_account_records[] = $dis_account_record;
			
		}
		
		return $dis_account_records;
		
	}
	
	
	

}   
   