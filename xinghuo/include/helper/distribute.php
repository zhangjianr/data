<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/include/library/General_tree.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/include/library/Distribute.php';

/*获取此店主的信息*/
function getOwner($DB, $UsersID) {

	$rsConfig = $DB->GetRs("shop_config", "Distribute_Customize,ShopName,ShopLogo", "where Users_ID='" . $UsersID . "'");

	//生命全局distribute_class
	
	global $DS_OBJ;
	$DS_OBJ = new Distribute($DB, $UsersID);

	if (!isset($_SESSION[$UsersID.'User_ID']) || empty($_SESSION[$UsersID.'User_ID'])) {
		$owner = getOwnerByUrl($DB, $UsersID); //用户不登录
	} else {
		$owner = getOwnerBySql($DB, $UsersID); //用户登录

	}

	//如果不允许会员自定义店名
	if ($rsConfig['Distribute_Customize'] == 0) {
		$owner['shop_name'] = $rsConfig['ShopName'];
		$owner['shop_logo'] = !empty($rsConfig['ShopLogo']) ? $rsConfig['ShopLogo'] : '/static/api/images/user/face.jpg';
	}

	return $owner;

}

/*通过数据库获取此店主的信息*/
function getOwnerBySql($DB, $UsersID) {

	$user = $DB->GetRs("user", "Owner_ID,Is_Distribute", "where User_ID=" . $_SESSION[$UsersID."User_ID"] . " and Users_ID='" . $UsersID . "'");

	if ($user['Is_Distribute'] == 1) {
		$owner_id = $_SESSION[$UsersID.'User_ID'];
		$ownerAccount = get_dsaccount_by_id($DB, $UsersID, $_SESSION[$UsersID.'User_ID']);

		//若登录用户的分销身份已审核通过,则店主就是他自己
		//若登录用户的分销身份未审核通过,则店主仍是他的推荐人
		if ($ownerAccount['Is_Audit'] != 1) {
			$owner_id = $user['Owner_ID'];

			if($owner_id>0){
				$ownerAccount = get_dsaccount_by_id($DB, $UsersID, $user['Owner_ID']);
	
				if (empty($ownerAccount)) {
					echo '不存在这个店主,您的推荐人已被删除!!!';
					exit();
				}
			}else{
				$ownerAccount = array(
					'Shop_Name'=>'',
					'Shop_Logo'=>'',
					'Shop_Announce'=>''
				);
			}
		}

	} else {

		$owner_id = $user['Owner_ID'];
		$ownerAccount = get_dsaccount_by_id($DB, $UsersID, $user['Owner_ID']);
	}

	$shop_name = $ownerAccount['Shop_Name'];
	$shop_logo = !empty($ownerAccount['Shop_Logo']) ? $ownerAccount['Shop_Logo'] : '/static/api/images/user/face.jpg';
	$shop_announce = $ownerAccount['Shop_Announce'];

	$owner = array('id' => $owner_id, 'shop_name' => $shop_name, 'shop_logo' => $shop_logo, 'shop_announce' => $shop_announce);
	return $owner;

}

/*通过url获取此店主的信息*/
function getOwnerByUrl($DB, $UsersID) {

	$owner_id = !empty($_GET['OwnerID']) ? $_GET['OwnerID'] : 0;
	$UsersID = $_GET['UsersID'];

	if ($owner_id != 0) {
		$ownerAccount = get_dsaccount_by_id($DB, $UsersID, $owner_id);
		$shop_name = $ownerAccount['Shop_Name'];
		$shop_logo = !empty($ownerAccount['Shop_Logo']) ? $ownerAccount['Shop_Logo'] : '/static/api/images/user/face.jpg';
		$shop_announce = $ownerAccount['Shop_Announce'];
		$owner = array('id' => $owner_id, 'shop_name' => $shop_name, 'shop_logo' => $shop_logo, 'shop_announce' => $shop_announce);
	} else {
		$owner = array('id' => 0);

	}

	return $owner;

}

/**
 * 得到体现流水号
 * @return  string
 */
function build_withdraw_sn() {
	/* 选择一个随机的方案 */
	mt_srand((double) microtime() * 1000000);

	return 'WD' . date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

/**
 *增加分销记录
 */
function add_distribute_record($UsersID, $DB, $OwnerID, $Product_Price, $ProductID, $Qty, $OrderID) {

	
	$Product = get_product_distribute_info($DB, $UsersID, $ProductID);
	//此产品利润为零,不处理
	if($Product['Products_Profit'] == 0){
		return false;
	}
	
	//此产品未设置返回佣金，不处理
	if(count($Product['Distribute_List']) == 0 ){
		return false;
	}
	
	//增加分销成功记录
	$User = $DB->getRs('user', 'User_Name', "where Users_ID='" . $UsersID . "' and User_ID='" . $_SESSION[$UsersID.'User_ID'] . "'");
	$User_Name = $User["User_Name"];

	$data = array("Users_ID" => $UsersID,
		"Buyer_ID" => $_SESSION[$UsersID.'User_ID'],
		"Owner_ID" => $OwnerID,
		"Order_ID" => $OrderID,
		"Product_ID" => $ProductID,
		'Product_Price' =>$Product_Price,
		'Qty'=>$Qty,
		"Record_CreateTime" => time(),
		"status" => 0);

	$DB->add('shop_distribute_record', $data);

	$Ds_Record_ID = $DB->insert_id();
	add_distribute_account_record($DB, $UsersID,$Product,$OrderID,$OwnerID,$Qty,$Product_Price,$Ds_Record_ID);


}

/**
 *增加分销账户记录
 */
function add_distribute_account_record($DB, $UsersID,$Product, $OrderID, $OwnerID,$Qty,$Product_Price,$Ds_Record_ID){
	//增加分销账户记录

	global $DS_OBJ;
	
	$ancestor_list = $DS_OBJ->get_ancestor($OwnerID, $_SESSION[$UsersID.'User_ID']);	
	foreach ($ancestor_list as $key => $item) {

		if ($OwnerID == $item['User_ID']) {
			//自己获取佣金
			$Record_Description = '自己销售' . $Product['Products_Name'] . '&yen;' . $Product_Price. '成功，获取奖金';

		} else {
			//上级分销商获取佣金
			$Record_Description = '下属分销商分销' . $Product['Products_Name'] . '&yen;' . $Product_Price. '成功，获取奖金';
		}

		$data = array(
			"Users_ID" => $UsersID,
			"Buyer_ID" => $_SESSION[$UsersID.'User_ID'],
			"Ds_Record_ID"=>$Ds_Record_ID,
			"Order_ID" => $OrderID,
			"Product_ID"=> $Product['Products_ID'],
			"User_ID" => $item['User_ID'],
			"Owner_ID" => $OwnerID,
			"Record_Sn" => build_withdraw_sn(),
			"level" => $key + 1,
			"Record_Money" => !empty($Product['Distribute_List'][$key]) ? $Product_Price*$Product['Distribute_List'][$key]*$Qty : 0,
			"Record_CreateTime" => time(),
			"Record_Description" => $Record_Description,
			"Record_Type" => 0,
			"Record_Status" => 0,
		);

		$DB->add('shop_distribute_account_record', $data);
	}

}

/**
 *获取此分销商的祖先
 */
function get_distribute_ancestor($DB, $UsersID, $OwnerID) {
	$rsAccounts = $DB->Get('shop_distribute_account', '*', "where Users_ID='" . $UsersID . "'");
	//实例化通用树类
	$account_list = $DB->toArray($rsAccounts);
	$param = array('result' => $account_list, 'fields' => array('User_ID', 'invite_id'));
	$generalTree = new General_tree($param);
	$ancestor_list = $generalTree->navi($OwnerID);
	//返回数组中前三个元素
	ksort($ancestor_list);
	//如果是自己在自己的店购买，自己不得佣金
	if ($_SESSION[$UsersID."User_ID"] == $OwnerID) {
		array_shift($ancestor_list);
	}

	while (count($ancestor_list) > 3) {
		array_pop($ancestor_list);
	}

	return $ancestor_list;

}


/*分销成功后续操作*/
function handle_distribute_success($DB, $Order_ID,$ProductID,$UsersID) {

	mysql_query('start transaction');

	//更改分销记录状态
	$data = array('status' => 1);
	$condition = "where Users_ID='" . $UsersID . "' and Order_ID=" .$Order_ID." and Product_ID =".$ProductID;
	$DB->set('shop_distribute_record', $data, $condition);

	

	//更改分销账户记录
	$data = array('Record_Status' => 2);
	$condition = "where Users_ID='" . $UsersID . "' and Order_ID=" . $Order_ID." and Product_ID =".$ProductID;
	$DB->set('shop_distribute_account_record', $data, $condition);

	//分销账户增加余额
	$condition = "where Users_ID='" . $UsersID . "' and Order_ID=" . $Order_ID." and Product_ID =".$ProductID;
	$rsAccounts = $DB->Get('shop_distribute_account_record', "User_ID,Record_Money", $condition);
	$account_list = $DB->toArray($rsAccounts);

	foreach ($account_list as $key => $item) {
		$condition = "where Users_ID='" . $UsersID . "' and User_ID=" . $item['User_ID'];
		$interest = $item['Record_Money'];
		$DB->set('shop_distribute_account', 'balance=balance+' . $interest . ',Total_Income=Total_Income+' . $interest, $condition);
	}
	
	mysql_query('commit');
	
	//增加卖出者销售额
	global $DS_OBJ;
	$DS_OBJ = new Distribute($DB, $UsersID);
	$DS_OBJ->refresh();
	
	$DS_OBJ->update_group_sales($Order_ID,$ProductID);
}

/**
 *获取产品各级分销奖金
 *
 */
function get_product_distribute_info($DB, $UsersID, $ProductID) {

	//获取产品各级分销奖金
	$Product = $DB->getRs('shop_products', 'Products_ID,Products_Distributes,Products_Profit,Products_Name,Products_PriceX', "where Users_ID='" . $UsersID . "' and Products_ID='" . $ProductID . "'");
	$Products_Distributes = json_decode($Product['Products_Distributes'], TRUE);
	
	$distribute_bonus_list = array();
	if(count($Products_Distributes) > 0){
		foreach($Products_Distributes as $Key=>$item){
			$distribute_bonus_list[$Key] = $Product['Products_Profit']*$item/10000;
		}
	}
	
	$Product['Distribute_List'] = $distribute_bonus_list;

	return $Product;
}

/**
 *创建分销账户
 */
function create_distribute_acccount($DB, $UsersID, $UserID, $Real_Name, $owner, $Account_Mobile, $status=0) {

	/*获取此店铺的配置信息*/
	$rsConfig = $DB->GetRs("shop_config", "*", "where Users_ID='" . $UsersID . "'");

	$user = $DB->GetRs("user", "*", "where User_ID=" . $UserID . " and Users_ID='" . $UsersID . "'");
	if(!$user){
		return false;
	}
	
	$item = $DB->GetRs("shop_distribute_account", "*", "where User_ID=" . $UserID . " and Users_ID='" . $UsersID . "'");
	
	if($item){
		if($status==1 && $item["Is_Audit"]==0){
			$DB->Set("shop_distribute_account",array('Is_Audit' => 1),"where Account_ID=".$item["Account_ID"]);
		}
		return false;
	}
	$data = array(
		"Users_ID" => $UsersID,
		"User_ID" => $UserID,
		"Real_Name" => $Real_Name,
		"Shop_Name" => $user['User_NickName'],
		"Shop_Logo" => $user['User_HeadImg'],
		"balance" => 0,
		"status" => 1,
		'invite_id' => !empty($owner['id']) ? $owner['id'] : 0,
		'Is_Audit' => $status,
		'Account_Mobile' => $Account_Mobile,
		"Account_CreateTime" => time(),
		"Group_Num" => 1,
	);

	mysql_query("BEGIN"); //开始事务定义

	$flag_a = $DB->add('shop_distribute_account', $data);
	$flag_b = $DB->set('user', array('Is_Distribute' => 1), "where User_ID=" . $UserID . " and Users_ID='" . $UsersID . "'");
	
	global $DS_OBJ;
	$DS_OBJ = new Distribute($DB, $UsersID);
	$flag_c = $DS_OBJ->update_ancestor_group_num($owner['id'], $UserID);
	
	if ($flag_a && $flag_b && $flag_c) {
		mysql_query("COMMIT"); //执行事务
		return true;
	} else {
		return false;
	}

}

/**
 * 根据用户id获得分销账号
 * @param  int $userid 用户id
 * @return array     $account 返回的分销账号
 */
function get_dsaccount_by_id($DB, $UsersID, $userid) {

	$condition = "where Users_ID='" . $UsersID . "' and User_ID='" . $userid . "'";
	$fields = 'Shop_Name,Shop_Logo,Is_Audit,Shop_Announce,invite_id';
	$account = $DB->getRs('shop_distribute_account', $fields, $condition);
	return $account;
}

/*
 *整理级别列表
 */
function orange_level($ds_dropdown, $user_dropdown, $UserID) {

	$level1 = $level2 = $level3 = array();

	foreach ($ds_dropdown as $key => $item) {
		if ($item['invite_id'] == $UserID) {
			if (!empty($user_dropdown[$key])) {
				$item['User_Name'] = $user_dropdown[$key];
				$level1[$item['User_ID']] = $item;
			}
		}

	}

	$leve1_ids = array_keys($level1);

	foreach ($ds_dropdown as $key => $item) {
		if (in_array($item['invite_id'], $leve1_ids)) {
			if (!empty($user_dropdown[$key])) {
				$item['User_Name'] = $user_dropdown[$key];
				$level2[$item['User_ID']] = $item;
			}
		}
	}

	$level2_ids = array_keys($level2);

	foreach ($ds_dropdown as $key => $item) {
		if (in_array($item['invite_id'], $level2_ids)) {
			if (!empty($user_dropdown[$key])) {
				$item['User_Name'] = $user_dropdown[$key];
				$level3[$item['User_ID']] = $item;
			}
		}
	}

	$level_list = array(1 => $level1, 2 => $level2, 3 => $level3);
	return $level_list;
}

/**
 *删除分销记录
 */
function delete_distribute_record($DB, $UsersID, $OrderID) {
	//删除分销记录
	$condition = "Users_ID='" . $UsersID . "' and Order_ID=" . $OrderID;
	$DB->Del("shop_distribute_record", $condition);
	//删除分销账户记录
	$DB->Del("shop_distribute_account_record", $condition);

}

/**
 *判断此订单是否为分销订单
 */
function is_distribute_order($DB, $UsersID, $OrderID) {
	//检测此订单是否为分销订单
	$condition = "where Users_ID='" . $UsersID . "' and Order_ID=" . $OrderID;

	$order = $DB->getRs('user_order', 'Order_CartList', $condition);

	$Flag = false;

	$CartList = json_decode($order['Order_CartList'], TRUE);

	if (count($CartList) > 0) {
		foreach ($CartList as $ProductID => $product_list) {
			foreach ($product_list as $key => $item) {
				if ($item['OwnerID'] > 0) {
					$Flag = true;
					break 2;
				}
			}
		}
	}

	return $Flag;
}

/**
 * 生成分销推广海报
 * @param  string $data 海报内容的base64数据
 * @return  bool  $Flag 是否成功生成海报
 *
 * */
function generate_postere($img, $UsersID, $owner_id) {

	define('UPLOAD_DIR', $_SERVER["DOCUMENT_ROOT"] . '/data/poster/');
	$img = str_replace('data:image/png;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	$file_name = '';
	$file_path = UPLOAD_DIR . $UsersID . $owner_id . '.png';
	$web_path = '/data/poster/' . $UsersID . $owner_id . '.png';
	$Flag = file_put_contents($file_path, $data);

	return $Flag;
}

/**
 * 增加分销称号
 * @param   $DB 数据库连接
 * @param   $UsersID
 * @return  $Flag 是否设置成功
 *
 */
function add_dis_pro_title($DB, $UsersID, $pro_titles) {
	$data = array('Users_ID' => $UsersID,
		'Pro_Title_Level' => json_encode($pro_titles, JSON_UNESCAPED_UNICODE));

	$Flag = $DB->Add('shop_distribute_config', $data, "where Users_ID='" . $UsersID . "'");
	return $Flag;

}
function set_dis_pro_title($DB, $UsersID, $pro_titles) {
	$data = array('Users_ID' => $UsersID,
		'Pro_Title_Level' => json_encode($pro_titles, JSON_UNESCAPED_UNICODE));

	$Flag = $DB->Set('shop_distribute_config', $data, "where Users_ID='" . $UsersID . "'");
	return $Flag;

}


function add_channel_type($DB, $UsersID, $channel_types) {
	$data = array('Users_ID' => $UsersID,
		'Channel_Type' => json_encode($channel_types, JSON_UNESCAPED_UNICODE));

	$Flag = $DB->Add('shop_channel_config', $data, "where Users_ID='" . $UsersID . "'");
	return $Flag;

}
function set_channel_type($DB, $UsersID, $channel_types,$Depth) {
	$data = array('Users_ID' => $UsersID,
		'Channel_Type' => json_encode($channel_types, JSON_UNESCAPED_UNICODE),
		'Depth'=>$Depth
		);
//print_r($data);exit;
	$Flag = $DB->Set('shop_channel_config', $data, "where Users_ID='" . $UsersID . "'");
	return $Flag;

}

/**
 * 获得分销商称号
 * @param   $DB 数据库连接
 * @param   $UsersID
 * @param   $type  front前台调用，back 后台调用
 * @return Array $rsDsConfig 分销商配置
 *  */
function get_dis_pro_title($DB, $UsersID,$type= 'front') {
	$rsDsConfig = $DB->GetRs('shop_distribute_config', 'Pro_Title_Level', "where Users_ID='" . $UsersID . "'");

	$pro_titles = false;

	if ($rsDsConfig) {
		$pro_titles = json_decode($rsDsConfig['Pro_Title_Level'], TRUE);
		
		if(!empty($pro_titles)){
		if($type == 'front'){
			
			foreach($pro_titles as $key=>$item){
				if(strlen($item['Name']) == 0){
					unset($pro_titles[$key]);
				}
			}
			ksort($pro_titles);
			
		}
		}
		
	}

	return $pro_titles;
}

/**
 * 获得渠道商称号
 * @param   $DB 数据库连接
 * @param   $UsersID
 * @param   $type  front前台调用，back 后台调用
 * @return Array $rsDsConfig 分销商配置
 *  */
function get_channel_type($DB, $UsersID,$type= 'front') {
	$rsDsConfig = $DB->GetRs('shop_channel_config', 'Channel_Type', "where Users_ID='" . $UsersID . "'");

	$channel_types = false;

	if ($rsDsConfig) {
		$channel_types = json_decode($rsDsConfig['Channel_Type'], TRUE);
		
		if(!empty($channel_types)){
		if($type == 'front'){
			
			foreach($channel_types as $key=>$item){
				if(strlen($item['Name']) == 0){
					unset($channel_types[$key]);
				}
			}
			ksort($channel_types);
			
		}
		}
		
	}

	return $channel_types;
}


function up_professional_title_by_group_sales($DB, $UsersID, $User_ID, $account_list, $OrderSales,$Owner_ID) {

	$Flag = true;
	if (!empty($Owner_ID)) {
	
		//获取此订单店主分销账号信息
		$ds_account = $account_list[$User_ID];
		
		//获取分销称号信息
		$data['Total_Sales'] = $ds_account['Total_Sales'] + $OrderSales;
		$pro_titles = get_dis_pro_title($DB, $UsersID);
		
		if (!empty($pro_titles)) {
			
			$top_level = count($pro_titles);
			$total_sales = $ds_account['Total_Sales'];
			$up_group_sales = $ds_account['Up_Group_Sales'];
			$group_sales = $ds_account['Group_Sales'];
			$Ex_Bonus = $ds_account['Ex_Bonus'];
			$last_award_income = $ds_account['last_award_income'];
			$pro_title = $ds_account['Professional_Title'];

			$total_sales = $total_sales + $OrderSales;
			$up_group_sales = $up_group_sales + $OrderSales;

			$group_sales = $group_sales + $OrderSales;
		
		
			//已经是最高级
			if ($pro_title == $top_level) {
				//最高级利润率

				if (count($pro_titles) > 2) {
					$top_up_stock = $pro_titles[$top_level]['Saleroom'] - $pro_titles[$top_level - 1]['Saleroom'];
				} else {
					$top_up_stock = $pro_titles[$top_level];
				}

				if ($up_group_sales >= $top_up_stock) {
					$income = $ds_account['Total_Income'] - $last_award_income;
					$Bonus = $pro_titles[$top_level]['Bonus'];
					$rate = $Bonus / 100;
					$Ex_Bonus += $income * $rate;
					$last_award_income = $ds_account['Total_Income'];
					$up_group_sales = $up_group_sales - $top_up_stock;
				}
				
				$cur_title = $pro_title;

			}else{
				//不是最高级

				$cur_title = determine_dis_protitle_by_group_sales($pro_titles, $group_sales, $pro_title);

				if ($cur_title > $pro_title) {
					$income = $ds_account['Total_Income'] - $last_award_income;
				
					
					$Bonus = $pro_titles[$cur_title]['Bonus'];
					$rate = $Bonus/100;
					$Ex_Bonus += $income*$rate;
					
					//计算极差
					if($cur_title == 1){
						$level_range = $pro_titles[$cur_title]['Saleroom'];
					}else{
						$level_range = $pro_titles[$cur_title]['Saleroom']-$pro_titles[$cur_title-1]['Saleroom'];
					}
					
					$last_award_income = $ds_account['Total_Income'];
					$up_group_sales = $up_group_sales - $level_range;
				}

			}

			$data['Up_Group_Sales'] = $up_group_sales;
			$data['Group_Sales'] = $group_sales;
			$data['Professional_Title'] = $cur_title;
			$data['Ex_Bonus'] = $Ex_Bonus;
			$data['last_award_income'] = $last_award_income;
		}

		$condition = "where Users_ID='" . $UsersID . "' and User_ID=" . $User_ID;
		$Flag = $DB->set('shop_distribute_account', $data, $condition);
	}
	
	return $Flag;

}

/**
 * 确定用户称号
 * @param  int $Pro_Title_Level 用户称号列表
 * @param  float $user_Sales  团队销售额
 * @param  int $cur_title      用户当前title级别
 * @return int                计算后的title级别
 */
function determine_dis_protitle_by_group_sales($Pro_Title_Level, $Group_Sales, $cur_title) {


	$level_dropdown = array();
	$level_range_list = array();
	$level_count = count($Pro_Title_Level);
	$level_begin_sales = $Pro_Title_Level[1]['Saleroom'];
	$level_end_sales = $Pro_Title_Level[$level_count]['Saleroom'];

	//如果消费额小于等级起始消费额，1级
	if ($Group_Sales < $level_begin_sales) {
		return $cur_title;
	}

	//如果消费额大于等级结束消费额,最高级

	if ($Group_Sales >= $level_end_sales) {
		return $level_count;
	}

	//除此之外，循环确定
	foreach ($Pro_Title_Level as $key => $item) {

		if ($key != $level_count) {
			$end_cost = $Pro_Title_Level[$key + 1]['Saleroom'];
		} else {
			$end_cost = 99999999999; //用一个很大的数表示等级的终点
		}

		$level_range_list[$key] = array('begin_cost' => $item['Saleroom'], 'end_cost' => $end_cost);
	}

	foreach ($level_range_list as $key => $item) {

		if ($Group_Sales >= $item['begin_cost'] && $Group_Sales < $item['end_cost']) {

			if ($key > $cur_title) {
				return $key;
			} else {
				return $cur_title;
			}

		}
	}

}

//生成dropdown数组
function get_dropdown_list($data, $id_field, $value_field = '') {
	$drop_down = array();

	foreach ($data as $key => $item) {
		
		if (strlen($value_field) > 0) {
			$drop_down[$item[$id_field]] = $item[$value_field];
		} else {
			$drop_down[$item[$id_field]] = $item;
		}
	}

	return $drop_down;
}

/**
 * 通过团队人数确定用户级别
 * @param  array $Pro_Title_Level 用户称号列表
 * @param  float $user_Sales   用户晋级团队人数
 * @param  int  $cur_title      用户当前title
 * @return int   $cur_title              计算后的级别
 */
function determine_dis_protitle_by_num($Pro_Title_Level, $Group_Num, $cur_title) {

	$level_dropdown = array();
	$level_range_list = array();
	$level_count = count($Pro_Title_Level);
	$level_begin_group_num = $Pro_Title_Level[1]['Group_Num'];
	$level_end_group_num = $Pro_Title_Level[$level_count]['Group_Num'];

	//如果消费额小于等级起始消费额，1级
	if ($Group_Num < $level_begin_group_num) {
		return $cur_title;
	}

	//如果消费额大于等级结束消费额,最高级
	if ($Group_Num >= $level_end_group_num) {
		return $level_count;
	}

	//除此之外，循环确定
	foreach ($Pro_Title_Level as $key => $item) {

		if ($key != $level_count) {
			$end_group_num = $Pro_Title_Level[$key + 1]['Group_Num'];
		} else {
			$end_group_num = 99999999999; //用一个很大的数表示等级的终点
		}
		$level_range_list[$key] = array('begin_group_num' => $item['Group_Num'], 'end_group_num' => $end_group_num);
	}

	foreach ($level_range_list as $key => $item) {
		if ($Group_Num >= $item['begin_group_num'] && $Group_Num < $item['end_group_num']) {

			if ($key > $cur_title) {
				return $key;
			} else {
				return $cur_title;
			}

		}
	}

}

/**
 *通过团队人数对分销账号授予爵位并发送奖励
 */
function up_professional_title_by_group_num($DB, $UsersID, $UserID, $account_list) {
	$ds_dropdown = get_dropdown_list($account_list, 'User_ID');
	$pro_titles = get_dis_pro_title($DB, $UsersID);
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

		$condition = "where Users_ID = '" . $UsersID . "' and User_ID=" . $UserID;
		$Flag = $DB->Set('shop_distribute_account', $data, $condition);
		
	}
	
	return $Flag;

}

function pre_add_distribute_account($DB,$UsersID){
	$error_msg = '';
	if(!empty($_SESSION[$UsersID.'User_ID'])){
		$item = $DB->GetRs("shop_config","Distribute_Type,Distribute_Limit","where Users_ID='".$UsersID."'");
		$user = $DB->GetRs("user","*","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID']);
		$owner["id"] = $user["Owner_Id"];
		if($user){
			$truename = $user["User_Name"] ? $user["User_Name"] : ($user["User_NickName"] ? $user["User_NickName"] : '真实姓名');
			if($user["Is_Distribute"]==0){
				switch($item["Distribute_Type"]){
					case '0'://自动成为分销商
						$flag = create_distribute_acccount($DB, $UsersID, $_SESSION[$UsersID.'User_ID'], $truename, $owner, '',1);
						$error_msg = $flag ? 'OK' : '会员自动成为分销商失败';
					break;
					case '1'://积分限制
						if($user["User_TotalIntegral"]>=$item["Distribute_Limit"]){
							$flag = create_distribute_acccount($DB, $UsersID, $_SESSION[$UsersID.'User_ID'], $truename, $owner, '',1);
							$error_msg = $flag ? 'OK' : '会员自动成为分销商失败';
						}else{
							$error_msg = '1';
						}
					break;
					case '2'://消费金额
						if($user["User_Cost"]>=$item["Distribute_Limit"]){
							$flag = create_distribute_acccount($DB, $UsersID, $_SESSION[$UsersID.'User_ID'], $truename, $owner, '',1);
							$error_msg = $flag ? 'OK' : '会员自动成为分销商失败';
						}else{
							$error_msg = '2';
						}
					break;
					case '3':
						$error_msg = '3';
					break;
					case '4':
						$error_msg = '4';
					break;
					case '5':
						$orderInfo = $DB->GetRs("spark_user", "*", " WHERE Users_ID='{$UsersID}' AND User_ID='{$_SESSION[$UsersID.'User_ID']}'");
                                                if(empty($orderInfo)){
							$error_msg = "5";
						}else{
							$flag = create_distribute_acccount($DB, $UsersID, $_SESSION[$UsersID.'User_ID'], $orderInfo['realName'], $owner, $orderInfo['mobile'],1);
							$DB->Set("user",array('User_Name' => $orderInfo['realName'], 'User_Mobile'=>$orderInfo['mobile']),"where Users_ID='{$UsersID}' AND User_ID='{$_SESSION[$UsersID.'User_ID']}'");
							$error_msg = $flag ? 'OK' : '会员自动成为分销商失败';
						}
					break;
				}	
			}else{
				$error_msg = 'OK';
			}
		}else{
			$error_msg = "会员不存在，请先清除缓存";
		}
	}
	return $error_msg;
}


/**
 *分销账号佣金统计
 */
function dsaccount_bonus_statistic($UsersID,$User_ID){
	
	global $DB1;
	$condition = "where User_ID=" . $User_ID. " and Users_ID='" . $UsersID . "' and Record_Type=0";
	$rsRecords = $DB1->Get('shop_distribute_account_record', 'Record_Money,Record_Status,Record_CreateTime', $condition);
	$record_list = $DB1->toArray($rsRecords);
	
	$un_pay = $payed = $completed = $total  = 0;
	$day_income = $month_income = $week_income = 0;
	
	//计算时间点
	$today = strtotime('today');
	$now = strtotime('now');

	//计算本周时间始末
	$date = date('Y-m-d');  //当前日期
	$first = 1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
	$w = date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6 
	$week_start =strtotime("$date -".($w ? $w - $first : 6).' days'); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
	$week_start_day = date('Y-m-d',$week_start); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
	$week_end = strtotime("$week_start_day +6 days");  //本周结束日期


	//计算本月时间始末
	$month_start = mktime(0,0,0,date('m'),1,date('Y'));
	$month_end = mktime(23,59,59,date('m'),date('t'),date('Y'));
	 
	foreach($record_list as $key=>$item){
		
		//未付款
		if($item['Record_Status'] == 0){
			$un_pay += $item['Record_Money'];
		}
		
		//已付款
		if($item['Record_Status'] == 1){
			$payed += $item['Record_Money'];
		}
		
		//已完成
		if($item['Record_Status'] == 2){
			$completed += $item['Record_Money'];
		}
		
		//今日收入
		if($today<$item['Record_CreateTime']&&$item['Record_CreateTime']<$now){
			$day_income += $item['Record_Money'];
		}
		
		//本周收入
		if($week_start<$item['Record_CreateTime']&&$item['Record_CreateTime']<$week_end){
			$week_income += $item['Record_Money'];
		}
		
		//本月收入
		if($month_start<$item['Record_CreateTime']&&$item['Record_CreateTime']<$month_end){
			$month_income += $item['Record_Money'];
		}
	
	    $total += $item['Record_Money'];
	}
	
	
	$result = array('un_pay'=>$un_pay,
					'payed'=>$payed,
					'completed'=>$completed,
					'day_income'=>$day_income,
					'week_income'=>$week_income,
					'month_income'=>$month_income,
					'total'=>$total);
	
	return $result;
	
	
}

/**
 *获取此用户的不可用余额(即已申请提现，但未执行提现的现金金额)
 */
function get_useless_sum($UsersID,$User_ID){
	global $DB1;
	
	$condition = "where User_ID=".$User_ID." and Users_ID='" . $UsersID . "' and Record_Type=1 and Record_Status = 0";
	$withdraw_records = $DB1->getRs("shop_distribute_account_record", "sum(Record_Money) as useless_sum", $condition);
	$useless_sum = !empty($withdraw_records['useless_sum']) ? $withdraw_records['useless_sum'] : 0;
	return $useless_sum;
}

/**
 *更改分销账号明细状态
 *
 */
function change_dsaccount_record_status($UsersID,$OrderID,$Status){
   global $DB1;
   
   $condition =  "where Users_ID = '".$UsersID."' and Order_ID=".$OrderID;
   
   $Data['Record_Status'] = $Status;
   
   $Flag = $DB1->set('shop_distribute_account_record',$Data,$condition);
	
   return $Flag;	
}


/**
 *获取总部分销商排行榜名次
 */
function get_h_incomelist_rank($UsersID,$User_ID,$H_Incomelist_Limit,$Open){
   
   global $DB1;
   $condition = "where Users_ID= '".$UsersID."' and Total_Income >= ".$H_Incomelist_Limit;
   $condition .= ' order by Total_Income desc limit 0,100';

   $fields = 'User_ID,Shop_Name,Shop_Logo,Professional_Title,Total_Income';
   
   $rsAccounts = $DB1->get('shop_distribute_account',$fields,$condition);
   $account_list = $DB1->toArray($rsAccounts);
 
   //判断指定$User_ID 是否在列表之中
   $Flag = FALSE;
   $Rank = NULL;
   
   if($Open == 1){
	
	$Flag = TRUE;
	$Rank = TRUE;
	
   }else{
	   
	foreach($account_list as $key=>$item){
			if($User_ID == $item['User_ID']){
				$Rank = $key+1;
				$Flag = TRUE;
				break;
			}
	}   
   
   }
   
   
   if($Flag){
	   $result = array('rank'=>$Rank,
					  'H_Incomelist'=>$account_list);
	   
   }else{
	  $result = false;
   }
	
   return $result;
}

function get_distribute_withdraw($DB,$UsersID,$enable,$type,$limit,$shop_url,$color='#FFF',$status=0){
	$msg = '';
	if($enable==0){
		$msg .= '您还不是老板，';
		if($type==1){
			$msg .= '请 <a href="'.$shop_url.'" style="color:'.$color.'; text-decoration:underline">购买</a> 成为老板';
			if($status==1){
				$msg .= '<a href="'.$shop_url.'" style="display:block; width:120px; margin-top:4px; line-height:28px; height:28px; border:1px #FFF solid; border-radius:10px; color:#FFF; text-align:center">立即成为老板</a>';
			}
		}elseif($type==2){
			$products = $DB->GetRs("shop_products","Products_ID,Products_Name","where Users_ID='".$UsersID."' and Products_ID=".$limit);
			if($products){
				$msg .= '请 <a href="'.$shop_url.'products_virtual/'.$products["Products_ID"].'/" style="color:'.$color.'; text-decoration:underline">点击购买</a> 成为老板';
				if($status==1){
					$msg .= '<a href="'.$shop_url.'products_virtual/'.$products["Products_ID"].'/" style="display:block; width:120px; margin-top:4px; line-height:28px; height:28px; border:1px #FFF solid; border-radius:10px; color:#FFF; text-align:center">立即成为老板</a>';
				}
			}else{
				$msg .= '请 <a href="'.$shop_url.'" style="color:'.$color.'; text-decoration:underline">点击购买</a> 成为老板';
				if($status==1){
					$msg .= '<a href="'.$shop_url.'" style="display:block; width:120px; margin-top:4px; line-height:28px; height:28px; border:1px #FFF solid; border-radius:10px; color:#FFF; text-align:center">立即成为老板</a>';
				}
			}
		}elseif($type==3){
			$msg .= '您的分销佣金达到 '.$limit.' 元才可成为老板';
			if($status==1){
				$msg .= '<a href="'.$shop_url.'" style="display:block; width:120px; margin-top:4px; line-height:28px; height:28px; border:1px #FFF solid; border-radius:10px; color:#FFF; text-align:center">立即成为老板</a>';
			}
		}
	}
	return $msg;
}

/**
 *获取我的累计销售额 
 */
function get_my_leiji_sales($UsersID,$UserID,$posterity){
	
	//计算本店下用户买商品销售额	
	global $DB1;
	$condition = "where Users_ID='".$UsersID."' and Owner_ID=".$UserID;
	
	$rsRecords = $DB1->get('shop_distribute_record','*',$condition);
	$record_list = $DB1->toArray($rsRecords);
	
	$user_total_sales = 0;
	foreach($record_list as $key=>$item){
		$user_total_sales += $item['Product_Price']*$item['Qty'];
	}
	$total_sales = $user_total_sales;
	//计算本店下属分销商作为用户所购买商品销售额
	$posterityids = get_posterity_ids($posterity);
	
	if(count($posterityids) > 0 ){
		$posterity_id_string = implode(',',$posterityids);
		$condition = "where Users_ID = '".$UsersID."'";
		$condition .= "and Buyer_ID = Owner_ID ";
		$condition .= "and Owner_ID in (".$posterity_id_string.")";
		$rsRecords = $DB1->get('shop_distribute_record','*',$condition);
		$record_list = $DB1->toArray($rsRecords);
		
		$posterity_total_sales = 0;
		foreach($record_list as $key=>$item){
			$posterity_total_sales += $item['Product_Price']*$item['Qty'];
		}
		$total_sales += $posterity_total_sales;
	}
	
	
	
	return $total_sales;
	
}

/*
 *获取我的累计佣金收入
 */
function get_my_leiji_income($UsersID,$UserID){
	global $DB1;
	$condition = "where Users_ID='".$UsersID."' and User_ID=".$UserID;
	
	$rsRecords = $DB1->get('shop_distribute_account_record','*',$condition);
	$record_list = $DB1->toArray($rsRecords);
	
	$total_income = 0;
	
	foreach($record_list as $key=>$item){
		$total_income += $item['Record_Money'];
	}
	
	return $total_income;
		
}

/**
 *获取下属分销商id数组
 */
function get_posterity_ids($posterity){
	
	$id_array = array();
	foreach($posterity as $key=>$sub_list){
		foreach($sub_list as $key=>$item){
			$id_array[] = $item['User_ID'];
		}
	}
	
	return $id_array;
	
}

//获取下级
function getTree($DB, $UsersID, $UserID, $level = 3) {
	$tree = array();
	$tree[0][] = array("User_ID"=>$UserID);
	for($i = 1; $i <= $level; $i++){
		foreach($tree[$i-1] as $key=>$val){
                            $tree[$i] = $DB->GetS("user", "User_ID", "WHERE Users_ID='{$UsersID}' AND Owner_Id='{$val['User_ID']}'");                        
		}
	}
	return $tree;
}
