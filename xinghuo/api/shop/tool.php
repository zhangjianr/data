<?php
//ini_set("display_errors","On");
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
	$uid = 20615;
	$orderid = 6371;
	
	$disAccountRecord = Order::find($orderid)->disAccountRecord()->getResults ();
		
		$userID_List = $disAccountRecord->map ( function ($disAccountRecord) {
			return $disAccountRecord->User_ID;
		} )->all ();
		
		$userIDS = array_unique ( $userID_List );
		
		foreach ( $userIDS as $key => $item ) {
				$interest_list [$item] = 0;
				$sales_list [$item] = 0;
			}
			
		foreach ( $disAccountRecord as $key =>$accountRecord) {
			
				$interest_list [$accountRecord->User_ID] += $accountRecord->Record_Money;
				$DisRecord = $accountRecord->DisRecord ()->getResults ();
				$sales_list [$accountRecord->User_ID] += $DisRecord->Product_Price * $DisRecord->Qty;
			}
			
			
	   var_dump($interest_list);		

	