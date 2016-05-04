<?php
//网中网分销处理helper
if (!function_exists('get_owner')) {
	/*获取此店主的信息*/
	function get_owner($rsConfig, $UsersID) {

		
		if (!isset($_SESSION[$UsersID . 'User_ID']) || empty($_SESSION[$UsersID . 'User_ID'])) {
			$owner = get_owner_by_url($UsersID); //用户不登录
		} else {
			$owner = get_owner_by_sql($UsersID); //用户登录

		}
	
		//如果不允许会员自定义店名
		if ($rsConfig['Distribute_Customize'] == 0) {
			$owner['shop_name'] = $rsConfig['ShopName'];
			$owner['shop_logo'] = !empty($rsConfig['ShopLogo']) ? $rsConfig['ShopLogo'] : '/static/api/images/user/face.jpg';
		}

		return $owner;

	}
}

if (!function_exists('get_owner_by_url')) {
	//通过url获得ownerid
	function get_owner_by_url($UsersID) {
		$owner_id = !empty($_GET['OwnerID']) ? $_GET['OwnerID'] : 0;

		if ($owner_id != 0) {
			$ownerAccount = get_dsaccount_by_id($UsersID, $owner_id);
			$shop_name = $ownerAccount['Shop_Name'];
			$shop_logo = !empty($ownerAccount['Shop_Logo']) ? $ownerAccount['Shop_Logo'] : '/static/api/images/user/face.jpg';
			$shop_announce = $ownerAccount['Shop_Announce'];
			$owner = array('id' => $owner_id, 'shop_name' => $shop_name, 'shop_logo' => $shop_logo, 'shop_announce' => $shop_announce);
		} else {
			$owner = array('id' => 0);
		}

		return $owner;
	}
}

if (!function_exists('get_owner_by_sql')) {
	//通过url获得ownerid
	function get_owner_by_sql($UsersID) {
		$User_ID = $_SESSION[$UsersID . "User_ID"];
		$user_obj = User::Multiwhere(array('Users_ID' => $UsersID, 'User_ID' => $User_ID))
			->first();
		
		if(!empty($user_obj)){
			$user = $user_obj->toArray();
		}else{
			echo '发生不可预知的错误，请联系管理员';
			exit();
		}
			

				
		if ($user['Is_Distribute'] == 1) {
			$owner_id = $_SESSION[$UsersID . 'User_ID'];
			$ownerAccount = get_dsaccount_by_id($UsersID, $owner_id);
			
			//若登录用户的分销身份已审核通过,则店主就是他自己
			//若登录用户的分销身份未审核通过,则店主仍是他的推荐人
			if ($ownerAccount['Is_Audit'] != 1) {
				$owner_id = $user['Owner_Id'];
				if ($owner_id > 0) {
					$ownerAccount = get_dsaccount_by_id($UsersID, $user['Owner_Id']);

					if (empty($ownerAccount)) {
						echo '不存在这个店主,您的推荐人已被删除!!!';
						exit();
					}
				} else {
					$ownerAccount = array(
						'Shop_Name' => '',
						'Shop_Logo' => '',
						'Shop_Announce' => '',
					);
				}
			}

		} else {
			$owner_id = $user['Owner_Id'];
			$ownerAccount = get_dsaccount_by_id($UsersID, $user['Owner_Id']);
		}

		$shop_name = $ownerAccount['Shop_Name'];
		$shop_logo = !empty($ownerAccount['Shop_Logo']) ? $ownerAccount['Shop_Logo'] : '/static/api/images/user/face.jpg';
		$shop_announce = $ownerAccount['Shop_Announce'];

		$owner = array('id' => $owner_id, 'shop_name' => $shop_name, 'shop_logo' => $shop_logo, 'shop_announce' => $shop_announce);
		return $owner;
	}
}

if (!function_exists('get_dsaccount_by_id')) {
	function get_dsaccount_by_id($UsersID, $User_ID) {
		//echo $UsersID.$User_ID;
		if($User_ID==0){			
			$account = array('id' => 0, 'Shop_Name' => '', 'Shop_Logo' => '', 'Shop_Announce' => '');
		}else{
			$account = Dis_Account::Multiwhere(array('Users_ID' => $UsersID, 'User_ID' => $User_ID))->first()->toArray();
			//var_dump($account);exit;
		}

		return $account;

	}

}

if (!function_exists('get_posterity')) {
	/**
	 * 获取某用户下属分销账号
	 * @param  Int $User_ID        用户ID
	 * @param  Array $Descendants 下属分销账号
	 * @return Array $posterity    下属三级分销账号列表
	 */
	function get_posterity($User_ID, $Descendants,$p_level) {

		$collecton = collect($Descendants);
		
		$filtered = $collecton->filter(function ($node) use ($p_level){
			$level = $node->getLevel();
			$level = $level-$p_level; 
			
			return $level <= 3;
		});

		$posterity = $filtered->all();

		return $posterity;
	}

}

if (!function_exists('organize_level')) {
	/**
	 * 整理下属分销账号
	 * @param  Int $User_ID        用户ID
	 * @param  Array $Descendants 下属分销账号
	 * @return Array $posterity    含有级别下属分销账号
	 *         结构  array(1=>array(),2=>array(),3=>array());
	 *
	 */
	function organize_level($User_ID, $Descendants,$p_level) {
		
		$posterity = array(1=>array(),2=>array(),3=>array());
		foreach ($Descendants as $key => $node) {
			
			$level = $node->getLevel();
			$level = $level-$p_level; 
		
			if ($level <= 3) {
				$posterity[$level][] = $node->toArray();
			}
		}

		return $posterity;
	}

}

if (!function_exists('income_list')) {
	function income_list($list, $num = 0) {

		$collection = collect($list);
		if ($num == 0) {
			$num = $collection->count();
		}

		$income_list = $collection->sortByDesc('Total_Income')
		                          ->take($num)
		                          ->map(function ($node) {
			                          return $node->toArray();
		                          })->toArray();
		return $income_list;

	}
}

if (!function_exists('dsaccount_bonus_statistic')) {
	/**
	 * 整理下属分销账号
	 * @param  Int $User_ID        用户ID
	 * @param  Array $Descendants 下属分销账号
	 * @return Array $posterity    含有级别下属分销账号
	 *         结构  array(1=>array(),2=>array(),3=>array());
	 *
	 */
	function dsaccount_bonus_statistic($record_list) {

		$un_pay = $payed = $completed = $total = 0;
		$day_income = $month_income = $week_income = 0;

		//计算时间点
		$today = strtotime('today');
		$now = strtotime('now');

		//计算本周时间始末
		$date = date('Y-m-d'); //当前日期
		$first = 1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
		$w = date('w', strtotime($date)); //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
		$week_start = strtotime("$date -" . ($w ? $w - $first : 6) . ' days'); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
		$week_start_day = date('Y-m-d', $week_start); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
		$week_end = strtotime("$week_start_day +6 days"); //本周结束日期

		//计算本月时间始末
		$month_start = mktime(0, 0, 0, date('m'), 1, date('Y'));
		$month_end = mktime(23, 59, 59, date('m'), date('t'), date('Y'));

		foreach ($record_list as $key => $item) {

			//未付款
			if ($item['Record_Status'] == 0) {
				$un_pay += $item['Record_Money'];
			}

			//已付款
			if ($item['Record_Status'] == 1) {
				$payed += $item['Record_Money'];
			}

			//已完成
			if ($item['Record_Status'] == 2) {
				$completed += $item['Record_Money'];
			}

			//今日收入
			if ($today < $item['Record_CreateTime'] && $item['Record_CreateTime'] < $now) {
				$day_income += $item['Record_Money'];
			}

			//本周收入
			if ($week_start < $item['Record_CreateTime'] && $item['Record_CreateTime'] < $week_end) {
				$week_income += $item['Record_Money'];
			}

			//本月收入
			if ($month_start < $item['Record_CreateTime'] && $item['Record_CreateTime'] < $month_end) {
				$month_income += $item['Record_Money'];
			}

			$total += $item['Record_Money'];
		}

		$result = array('un_pay' => round_pad_zero($un_pay, 2),
			'payed' => round_pad_zero($payed, 2),
			'completed' => round_pad_zero($completed, 2),
			'day_income' => round_pad_zero($day_income, 2),
			'week_income' => round_pad_zero($week_income, 2),
			'month_income' => round_pad_zero($month_income, 2),
			'total' => round_pad_zero($total, 2));

		return $result;

	}

}

if (!function_exists('get_distribute_withdraw')) {
	function get_distribute_withdraw($UsersID, $enable, $type, $limit, $shop_url, $color = '#FFF', $status = 0) {
		$msg = '';
		if ($enable == 0) {
			$msg .= '您还不是老板，';
			if ($type == 1) {
				$msg .= '请 <a href="' . $shop_url . '" style="color:' . $color . '; text-decoration:underline">购买</a> 成为老板';
				if ($status == 1) {
					$msg .= '<a href="' . $shop_url . '" style="display:block; width:120px; margin-top:4px; line-height:28px; height:28px; border:1px #FFF solid; border-radius:10px; color:#FFF; text-align:center">立即成为老板</a>';
				}
			} elseif ($type == 2) {
				
				
				$products = Product::Multiwhere(array('Users_ID' => $UsersID, 'Products_ID'=> $limit))
					->first(array('Products_ID', 'Products_Name'));

				if (!empty($products)) {
					$products = $products->toArray();
					$msg .= '请 <a href="' . $shop_url . 'products_virtual/' . $products["Products_ID"] . '/" style="color:' . $color . '; text-decoration:underline">点击购买</a> 成为老板';
					if ($status == 1) {
						$msg .= '<a href="' . $shop_url . 'products_virtual/' . $products["Products_ID"] . '/" style="display:block; width:120px; margin-top:4px; line-height:28px; height:28px; border:1px #FFF solid; border-radius:10px; color:#FFF; text-align:center">立即成为老板</a>';
					}
				} else {
					$msg .= '请 <a href="' . $shop_url . '" style="color:' . $color . '; text-decoration:underline">点击购买</a> 成为老板';
					if ($status == 1) {
						$msg .= '<a href="' . $shop_url . '" style="display:block; width:120px; margin-top:4px; line-height:28px; height:28px; border:1px #FFF solid; border-radius:10px; color:#FFF; text-align:center">立即成为老板</a>';
					}
				}
			} elseif ($type == 3) {
				
				$msg .= '您的分销佣金达到 ' . $limit . ' 元才可成为老板';
				if ($status == 1) {
					$msg .= '<a href="' . $shop_url . '" style="display:block; width:120px; margin-top:4px; line-height:28px; height:28px; border:1px #FFF solid; border-radius:10px; color:#FFF; text-align:center">立即成为老板</a>';
				}
				
				if($limit == 0){
					$msg = '';
				}
				
			}
		}

		return $msg;
	}

}

if (!function_exists('get_my_leiji_sales')) {
	/**
	 *我的团队累计销售额
	 */
	function get_my_leiji_sales($UsersID, $UserID, $posterity) {

		$total_sales = 0;
		
		//计算本店下普通用户所购买商品销售额
		$record_list_obj = Dis_Record::Multiwhere(array('Users_ID' => $UsersID))
				->where('Owner_ID',$UserID)
				->get(array('Product_Price', 'Qty'));
		
		if(!empty($record_list_obj)){
			$record_list = $record_list_obj->toArray();
			foreach ($record_list as $key => $item) {
				$total_sales += $item['Product_Price'] * $item['Qty'];
			}
		}
				
		
		
		//计算本店下属分销商作为用户所购买商品销售额
		
		$posterityids = array();
		if (count($posterity) > 0) {
			$posterityids = collect($posterity)->map(function ($node) {
				return $node->User_ID;
			})->toArray();
		}

		
		
		if (count($posterityids) > 0) {
			$recode_list = array();
			$record_list = Dis_Record::Multiwhere(array('Users_ID' => $UsersID))
				->whereRaw('Buyer_ID = Owner_ID')
				->whereIn('Owner_ID', $posterityids)
				->get(array('Product_Price', 'Qty'))
				->toArray();

			$posterity_total_sales = 0;
			foreach ($record_list as $key => $item) {
				$posterity_total_sales += $item['Product_Price'] * $item['Qty'];
			}
			$total_sales += $posterity_total_sales;

		}

		return $total_sales;

	}
}

if (!function_exists('get_posterity_ids')) {
	/**
	 *获取下属分销商id数组
	 */
	function get_posterity_ids($posterity) {

		$id_array = array();
		foreach ($posterity as $key => $sub_list) {
			foreach ($sub_list as $key => $item) {
				$id_array[] = $item['User_ID'];
			}
		}

		return $id_array;

	}
}

if (!function_exists('get_my_leiji_income')) {
	/*
	 *获取我的累计佣金收入
	 */
	function get_my_leiji_income($UsersID, $UserID) {

		$record_list = Dis_Account_Record::Multiwhere(array('Users_ID' => $UsersID, 'User_ID' => $UserID,'Record_Type'=>0))
			->get(array('Record_Money'))
			->toArray();

		$total_income = 0;

		foreach ($record_list as $key => $item) {
			$total_income += $item['Record_Money'];
		}

		return $total_income;
	}
}

if (!function_exists('add_distribute_record')) {
	//增加分销记录
	function add_distribute_record($UsersID, $OwnerID, $Product_Price, $ProductID, $Qty, $OrderID) {

		$Product = get_product_distribute_info($UsersID, $ProductID);
		
		//此产品利润为零,不处理
		if ($Product['Products_Profit'] == 0) {
			return false;
		}

		//此产品未设置返回佣金，不处理
		if (count($Product['Distribute_List']) == 0) {
			return false;
		}
		$Product['Products_Price'] = $Product_Price;

		//增加分销记录

		$dis_record = new Dis_Record();
		$dis_record->Users_ID = $UsersID;
		$dis_record->Buyer_ID = $_SESSION[$UsersID . 'User_ID'];
		$dis_record->Owner_ID = $OwnerID;
		$dis_record->Order_ID = $OrderID;
		$dis_record->Product_ID = $ProductID;
		$dis_record->Product_Price = $Product_Price;
		$dis_record->Qty = $Qty;
		$dis_record->Record_CreateTime = time();
		$dis_record->status = 0;

		//$dis_record->Product = $Product;
		//为分销记录model设置观察者，
		//以便增加相应分销佣金获得记录

		DisRecordObserver::$Product = $Product;
		DisRecordObserver::$Qty = $Qty;
		
		$dis_record->save();

	}
}

if (!function_exists('get_product_distribute_info')) {
	/**
	 *获取指定产品的分析信息
	 */
	function get_product_distribute_info($UsersID, $ProductID) {


		$product = Product::Multiwhere(array('Users_ID' => $UsersID, 'Products_ID' => $ProductID))
			->first(['Products_ID', 'Products_Distributes', 'Products_Profit', 'Distribute_Profit', 'Products_Name', 'Products_PriceX'])
			->toArray();

		$Products_Distributes = json_decode($product['Products_Distributes'], true);
		$distribute_bonus_list = array();
		if (count($Products_Distributes) > 0) {
			foreach ($Products_Distributes as $Key => $item) {
				$distribute_bonus_list[$Key] = $product['Products_Profit'] * $product['Distribute_Profit'] * $item / 1000000;
			}
		}

		$product['Distribute_List'] = $distribute_bonus_list;//分成层和比例列表

		return $product;
	}
}

if (!function_exists('create_distribute_tree')) {
	/**
	 *创建分销树
	 *@param String $UsersID 店铺唯一标示
	 *@param int $rootid 树根
	 *@return BlueM\Tree $tree 分销树
	 */
	function create_distribute_tree($UsersID, $rootid = 0) {

		$Dis_Account_List = Dis_Account::where(array('Users_ID' => $UsersID))
			->get(array('Users_ID', 'User_ID', 'invite_id', 'User_Name', 'Account_ID', 'Shop_Name','Account_CreateTime','Enable_Agent'))
			->toArray();
		
			
		$tree = new BlueM\Tree($Dis_Account_List, array('rootid' => $rootid));
		return $tree;
	}
}

if (!function_exists('get_root_id')) {
	/**
	 * 获取根店ID
	 * @param string $UsersID  此店ID
	 * @param int $Owner_ID   此店拥有者ID
	 * @return int $root_id  此店代理商id,若无则返回0
	 */
	function get_root_id($UsersID, $Owner_ID) {
		$tree = create_distribute_tree($UsersID);
		$node = $tree->getNodeByID($Owner_ID);

		$root_id = 0;
		if ($node != false) {

			$ancestors = $node->getAncestors(TRUE);
			array_pop($ancestors);
			$ancestor_array = array();

			foreach ($ancestors as $key => $item) {
				$ancestor_array[] = $item->toArray();
				if ($item->invite_id == 0) {
					$root_id = $item->Account_ID;
					break;
				}
			}

		}

		return $root_id;

	}
}
if (!function_exists('get_one_id')) {
	/**
	 * 获取一级合伙人ID
	 * @param string $UsersID  此店ID
	 * @param int $Owner_ID   此店拥有者ID
	 * @return int $root_id  此店代理商id,若无则返回0
	 */
	function get_one_id($UsersID, $Owner_ID) {
		$tree = create_distribute_tree($UsersID);
		$node = $tree->getNodeByID($Owner_ID);

		$root_id = array();
		if ($node != false) {

			$ancestors = $node->getAncestors(TRUE);
			array_pop($ancestors);
			$ancestor_array = array();

			foreach ($ancestors as $key => $item) {
				$ancestor_array[] = $item->toArray();
				if ($item->Enable_Agent == 3) {
					$root_id[] = $item->Account_ID;
				}
			}

		}

		return $root_id;

	}
}


if (!function_exists('get_channel_id')) {
	/**
	 * 获取一级合伙人ID
	 * @param string $UsersID  此店ID
	 * @param int $Owner_ID   此店拥有者ID
	 * @return int $root_id  此店代理商id,若无则返回0
	 */
	function get_channel_id($UsersID, $Owner_ID) {
		$tree = create_distribute_tree($UsersID);
		$node = $tree->getNodeByID($Owner_ID);

		$root_id = array();
		if ($node != false) {

			$ancestors = $node->getAncestors(TRUE);
			array_pop($ancestors);
			$ancestor_array = array();

			foreach ($ancestors as $key => $item) {
				$ancestor_array[] = $item->toArray();
				if ($item->Enable_Agent == 3) {
					$root_id[] = $item->Account_ID;
				}
			}

		}

		return $root_id;

	}
}

if (!function_exists('get_ancestor')) {

	/**
	 * 获取祖先id
	 *@param $self 是否包含自己 默认包含  true
	 *
	 */
	function get_ancestor($Users_ID,$Owner_ID,$User_ID, $Dis_Account_List = array(), $level=3) {

		if (!empty($Dis_Account_List)) {
			$tree = new BlueM\Tree($Dis_Account_List);
		}

		$tree = create_distribute_tree($Users_ID);//print_r($tree);
		$node = $tree->getNodeById($Owner_ID);
		
	
		$ancestors = false;
		if ($node != false) {
		
			$parent = $node->getParent();

			
			//获取自己和祖先
			$ancestors = $node->getAncestors(TRUE);
			
			$ancestors = collect($ancestors)->filter(function ($node) {
				if ($node->User_ID != 0) {
					return true;
				}
			})->map(function ($node) {
				return $node->toArray();
			})->all();
				//print_r($ancestors);exit;
			if (!empty($ancestors)) {
				//返回数组中前三个元素
				ksort($ancestors);
				//如果是自己在自己的店购买，自己不得佣金
				if ($User_ID == $Owner_ID) {
					array_shift($ancestors);
				}

				while (count($ancestors) > $level) {
					array_pop($ancestors);
				}
			}

			
		}

		return $ancestors;
	}
}


if (!function_exists('get_child')) {

	/**
	 * 获取子id
	 * @param $UsersID
	 * @param $User_ID
	 * @param $Level
	 * @return array
	 */
	function get_child($UsersID,$User_ID,$Level) {

		$tree = create_distribute_tree($UsersID);//print_r($tree);
		$node = $tree->getNodeById($User_ID);
		$cur_level=$node->getLevel();
		$limit_level=$cur_level+$Level;
		$all_child_nodes=array();
		$all_child_nodes=_get_child_loop($node,$tree,$all_child_nodes,$limit_level);

		return array_unique($all_child_nodes);
	}
}

if (!function_exists('_get_child_loop')) {

	/**
	 * 获取祖先id
	 *@param $self 是否包含自己 默认包含  true
	 *
	 */
	function _get_child_loop($node,$tree,$all_child_nodes,$limit_level) {

		$cur_level=$node->getLevel();
		$cur_id=$node->getId();
		array_push($all_child_nodes,$cur_id);
		if($cur_level<$limit_level) {
			unset($cur_level);
			unset($cur_id);
			$children = $node->getChildren();
			//var_dump($children);
			foreach ($children as $v) {

				$child_nodes = _get_child_loop($v, $tree, $all_child_nodes,$limit_level);
				
				foreach($child_nodes as $vv)
				{
					if(!in_array($vv,$all_child_nodes))
					array_push($all_child_nodes,$vv);
					
				}
				//$all_child_nodes = array_merge($all_child_nodes, $child_nodes);
				unset($child_nodes);

			}
		}
		
		return $all_child_nodes;
	}
}


if (!function_exists('change_dsaccount_record_status')) {
	/**
	 *更改分销账号明细状态
	 *
	 */
	function change_dsaccount_record_status($OrderID, $Status) {
		
		$order = Order::Find($OrderID);
		
		$disAccountRecord = $order->disAccountRecord();
		$flag = true;
		if($disAccountRecord->count() >0){
			 $flag = $disAccountRecord->rawUpdate(array('Record_Status'=>$Status)); 
		}	         

	
		return $flag;

	}
}

if (!function_exists('create_distribute_acccount')) {

	/**
	 *创建分销商
	 */
	function create_distribute_acccount($rsConfig, $UserID, $Real_Name, $owner, $Account_Mobile, $status = 0) {
		/*获取此店铺的配置信息*/
		
		
		$UsersID = $rsConfig['Users_ID'];
		$user = User::find($UserID)->toArray();

		//若不存在指定用户
		if (empty($user)) {
			return false;
		}

		$dis_account = Dis_Account::Multiwhere(array('Users_ID' => $UsersID, 'User_ID' => $UserID))
			->first();

		//若此分销账户已存在，只需将其通过审核 Is_Audit
		if (!empty($dis_account)) {
			if ($status == 1 && $dis_account->Is_Audit == 0) {
				$dis_account->Is_Audit = 1;
				$dis_account->save();
			}
			return false;
		}

		$data = array(
			"Users_ID" => $UsersID,
			"User_ID" => $UserID,
			"Real_Name" => $Real_Name,
			"Shop_Name" => $user['User_NickName'] . '的店',
			"Shop_Logo" => $user['User_HeadImg'],
			"balance" => 0,
			"status" => 1,
			"invite_id" => !empty($owner['id']) ? $owner['id'] : 0,
			"Is_Audit" => $status,
			"Account_Mobile" => $Account_Mobile,
			"Account_CreateTime" => time(),
			"Group_Num" => 1,
		);
		
		Dis_Account::observe(new DisAccountObserver());
		$Flag = Dis_Account::create($data);

		if ($Flag) {
			return true;
		} else {
			return false;
		}

	}
}

if (!function_exists('pre_add_distribute_account')) {
	function pre_add_distribute_account($shop_config, $UsersID) {
		$error_msg = '';
		if (!empty($_SESSION[$UsersID . 'User_ID'])) {
			$User_ID = $_SESSION[$UsersID . 'User_ID'];
			$user = User::Multiwhere(array('Users_ID' => $UsersID, 'User_ID' => $User_ID))
				->first()
				->toArray();

			$owner["id"] = $user["Owner_Id"];

			if ($user) {
				$truename = $user["User_Name"] ? $user["User_Name"] : ($user["User_NickName"] ? $user["User_NickName"] : '真实姓名');

				if ($user["Is_Distribute"] == 0) {

					switch ($shop_config["Distribute_Type"]) {
						case '0': //自动成为分销商
																				
							$flag = create_distribute_acccount( $shop_config, $_SESSION[$UsersID . 'User_ID'], $truename, $owner, '', 1);
							$error_msg = $flag ? 'OK' : '会员自动成为分销商失败';
							break;

						case '1': //积分限制
							if ($user["User_TotalIntegral"] >= $shop_config["Distribute_Limit"]) {
								$flag = create_distribute_acccount( $shop_config, $_SESSION[$UsersID . 'User_ID'], $truename, $owner, '', 1);
								$error_msg = $flag ? 'OK' : '会员自动成为分销商失败';
							} else {
								$error_msg = '1';
							}
							break;
						case '2': //消费金额
							if ($user["User_Cost"] >= $shop_config["Distribute_Limit"]) {
								$flag = create_distribute_acccount($shop_config,$_SESSION[$UsersID . 'User_ID'], $truename, $owner, '', 1);
								$error_msg = $flag ? 'OK' : '会员自动成为分销商失败';
							} else {
								$error_msg = '2';
							}
							break;
						case '3':
							$error_msg = '3';
							break;
						case '4':
							$error_msg = '4';
							break;
					}
				} else {
					$error_msg = 'OK';
				}
			} else {
				$error_msg = "会员不存在，请先清除缓存";
			}
		}
		return $error_msg;
	}
}

if(!function_exists('is_agent')){

	/**
	 * 判定某人是否是代理商
	 * @return boolean [description]
	 */
	function is_agent($shop_config,$ds_account){
		
	   $Dis_Agent_Type  = $shop_config['Dis_Agent_Type'];
		//非根店
		// if($ds_account['invite_id'] != 0 && $Dis_Agent_Type != 3){
			// return FALSE;
		// }	

	   if($Dis_Agent_Type != 0){
		 if($Dis_Agent_Type == 1){
			//普通代理	
		 	$result = ($ds_account['Enable_Agent'] == 1)?TRUE:FALSE;
		 }elseif($Dis_Agent_Type == 2){
			 //地区代理
		 	$where = array('Users_ID'=>$shop_config['Users_ID'],
		 		           'Account_ID'=>$ds_account['Account_ID']);
//print_r($where);
			$num = Dis_Agent_Area::Multiwhere($where)->count();
		 	$result = ($num >0)?TRUE:FALSE;
		 }else if($Dis_Agent_Type == 3){
			$result = ($ds_account['Enable_Agent'] == 3)?TRUE:FALSE;
		 }else if($Dis_Agent_Type == 4){
			$result = ($ds_account['Enable_Agent'] == 4 || $ds_account['Enable_Agent'] == 3)?TRUE:FALSE;
		 }
	   }else{
	   		$result = false;
	   }

	   return $result ;
	}
}


if (!function_exists('determine_dis_protitle_by_group_sales')) {

	
	/**
	 * 通过团队销售量确定用户称号
	 * @param  int $Pro_Title_Level 用户称号列表
	 * @param  float $user_Sales  团队销售额
	 * @param  int $cur_title      用户当前title级别
	 * @return int                计算后的title级别
	 */
	function determine_dis_protitle_by_group_sales($Pro_Title_Level,$Group_Sales,$cur_title) {


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
}


if (!function_exists('determine_dis_protitle_by_num')) {
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

}
