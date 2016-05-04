<?php
/*导出表格处理文件*/

require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/outputExcel.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');

$UsersID = $_SESSION['Users_ID'];

$type = $_REQUEST['type'];
//var_dump($type);
if($type == 'product_gross_info'){

	$table = 'shop_products';
	$fields  = '*';
	$condition = "where Users_ID='".$UsersID."'";
	$resource = $DB->get($table,$fields,$condition);
	
	$data = $DB->toArray($resource);
	//处理数据,获取分类信息
	$category_list  = getCategoryList();

	
	foreach($data as $key=>$item){
		
		if($item['Products_Category'] == '0'){
			$item['Products_Category'] = '未指定';
		}else{
			if(isset($category_list[$item['Products_Category']])){	
			$item['Products_Category'] = $category_list[$item['Products_Category']];
			}else{
			$item['Products_Category'] = '已删除';	
			}
		}
		
		
		//处理产品属性
		$JSON = json_decode($item['Products_JSON'],TRUE);
		$property = '';
		if(isset($JSON['Property'])){

			foreach($JSON['Property'] as  $k=>$value){
				$property .= $k.':';

				if(is_array($value)){
					foreach($value as $v){
						$property .= $v;
					}

				}else{
					$property .= $value;
				}
			}
				
		}
		$item['Products_Property'] = $property;
		$data[$key] = $item;
	}

	
	$outputExcel = new OutputExcel();
	$outputExcel->product_gross_info($data);
	
}elseif($type == 'order_detail_list'){
	
	$condition = "where Users_ID='".$_SESSION["Users_ID"]."' and Order_Type='shop'";
	
	//判断是否地区管理
	if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'employee'){
		$employee_info = $DB->GetRs("users_employee","","where id = '{$_SESSION["employee_id"]}'");
		if(!empty($employee_info) && $employee_info['isAbleArea'] == "1"){
			if(!empty($employee_info['loc_province'])){
				$condition .= " AND Address_Province = '{$employee_info["loc_province"]}'";
			}
			if(!empty($employee_info['loc_city'])){
				$condition .= " AND Address_City = '{$employee_info["loc_city"]}'";
			}
			if(!empty($employee_info['loc_town'])){
				$condition .= " AND Address_Area = '{$employee_info["loc_town"]}'";
			}
			
		}
	}

	if(!empty($_GET["Keyword"])){
		$condition .= " and Order_CartList like '%".$_GET["Keyword"]."%'";
	}
	if(isset($_GET["Status"])){
		if($_GET["Status"]<>''){
			$condition .= " and Order_Status=".$_GET["Status"];
		}
	}

	if(!empty($_GET["AccTime_S"])){
		$condition .= " and Order_CreateTime>=".strtotime($_GET["AccTime_S"]);
	}
	if(!empty($_GET["AccTime_E"])){
		$condition .= " and Order_CreateTime<=".strtotime($_GET["AccTime_E"]);
	}
	
	$beginTime = !empty($_GET["AccTime_S"])?$_GET["AccTime_S"]:'开始时间未指定';
	$endTime  = !empty($_GET["AccTime_E"])?$_GET["AccTime_E"]:'结束时间未指定';

	$resource = $DB->get("user_order","*",$condition);
	$list = $DB->toArray($resource);
	
	foreach($list as $key=>$item){
		if(is_numeric($item['Address_Province'])){
			$area_json = read_file($_SERVER["DOCUMENT_ROOT"].'/data/area.js');
			$area_array = json_decode($area_json,TRUE);
			$province_list = $area_array[0];
			$Province = '';
			if(!empty($item['Address_Province'])){
				$Province = $province_list[$item['Address_Province']].',';
			}
			$City = '';
			if(!empty($item['Address_City'])){
				$City = $area_array['0,'.$item['Address_Province']][$item['Address_City']].',';
			}

			$Area = '';
			if(!empty($item['Address_Area'])){
				$Area = $area_array['0,'.$item['Address_Province'].','.$item['Address_City']][$item['Address_Area']];
			}
		}else{
			$Province = $item['Address_Province'];
			$City = $item['Address_City'];
			$Area = $item['Address_Area'];
		}
	
		$list[$key]['Order_CartList'] = json_decode($item['Order_CartList'],TRUE);
		$list[$key]['receiver_address'] = $Province.$City.$Area.$item['Address_Detailed'];
	}
	$outputExcel = new OutputExcel();
	$outputExcel->order_detail_list($beginTime,$endTime,$list);
	
}elseif($type =='spark_order_list'){
	$condition = "where Users_ID='".$_SESSION["Users_ID"]."'";
	
	//判断是否地区管理
	/* if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'employee'){
		$employee_info = $DB->GetRs("users_employee","","where id = '{$_SESSION["employee_id"]}'");
		if(!empty($employee_info) && $employee_info['isAbleArea'] == "1"){
			if(!empty($employee_info['loc_province'])){
				$condition .= " AND Address_Province = '{$employee_info["loc_province"]}'";
			}
			if(!empty($employee_info['loc_city'])){
				$condition .= " AND Address_City = '{$employee_info["loc_city"]}'";
			}
			if(!empty($employee_info['loc_town'])){
				$condition .= " AND Address_Area = '{$employee_info["loc_town"]}'";
			}
			
		}
	} */

	if(!empty($_GET["Keyword"])){
		$condition .= " and realName like '%".$_GET["Keyword"]."%'  OR  mobile like '%".$_GET["Keyword"]."%'";
	}
	if(!empty($_GET["AccTime_S"])){
		$condition .= " and createtime>=".strtotime($_GET["AccTime_S"]);
	}
	if(!empty($_GET["AccTime_E"])){
		$condition .= " and createtime<=".strtotime($_GET["AccTime_E"]);
	}
	
	$beginTime = !empty($_GET["AccTime_S"])?$_GET["AccTime_S"]:'开始时间未指定';
	$endTime  = !empty($_GET["AccTime_E"])?$_GET["AccTime_E"]:'结束时间未指定';

	$resource = $DB->get("spark_order","*",$condition);
	$list = $DB->toArray($resource);
	
	/* foreach($list as $key=>$item){
		if(is_numeric($item['Address_Province'])){
			$area_json = read_file($_SERVER["DOCUMENT_ROOT"].'/data/area.js');
			$area_array = json_decode($area_json,TRUE);
			$province_list = $area_array[0];
			$Province = '';
			if(!empty($item['Address_Province'])){
				$Province = $province_list[$item['Address_Province']].',';
			}
			$City = '';
			if(!empty($item['Address_City'])){
				$City = $area_array['0,'.$item['Address_Province']][$item['Address_City']].',';
			}

			$Area = '';
			if(!empty($item['Address_Area'])){
				$Area = $area_array['0,'.$item['Address_Province'].','.$item['Address_City']][$item['Address_Area']];
			}
		}else{
			$Province = $item['Address_Province'];
			$City = $item['Address_City'];
			$Area = $item['Address_Area'];
		}
	
		$list[$key]['Order_CartList'] = json_decode($item['Order_CartList'],TRUE);
		$list[$key]['receiver_address'] = $Province.$City.$Area.$item['Address_Detailed'];
	} */
	//var_dump($list);exit();
	$outputExcel = new OutputExcel();
	$outputExcel->spark_order_list($beginTime,$endTime,$list);  
	
	
}elseif($type =='spark_rebate_list'){
    $condition = "where a.Users_ID='".$_SESSION["Users_ID"]."'";
    if(!empty($_GET["Keyword"])){
            $condition .= " and b.realName like '%".$_GET["Keyword"]."%' or b.mobile like '%".$_GET["Keyword"]."%'";
    }
    if(!empty($_GET["AccTime_S"])){
            $condition .= " and a.createtime>=".strtotime($_GET["AccTime_S"]);
    }
    if(!empty($_GET["AccTime_E"])){
            $condition .= " and a.createtime<=".strtotime($_GET["AccTime_E"]);
    }

    $beginTime = !empty($_GET["AccTime_S"])?$_GET["AccTime_S"]:'开始时间未指定';
    $endTime  = !empty($_GET["AccTime_E"])?$_GET["AccTime_E"]:'结束时间未指定';
    $lists = $DB->GetS("spark_logs as a join spark_user as b on a.User_ID= b.User_ID", "*", $condition, 10);
    foreach($lists as $k=>$v){
         $lists[$k]['fo'] = $DB->GetRs("spark_user","*"," where  Users_ID='".$_SESSION['Users_ID']."' AND User_ID=".$v['buy_User_ID']);				                      
    }
    $outputExcel = new OutputExcel();
//    print_r($lists);exit;
    $outputExcel->spark_rebate_list($beginTime,$endTime,$lists);  
}

/*获取产品分类列表*/
function getCategoryList(){
	global $DB;
	$table  = 'shop_category';
	$fields  = 'Category_ID,Category_Name';
	$condition = '';
	$resource = $DB->get($table,$fields,$condition);
	$list = $DB->toArray();

	foreach($list as $key=>$item){
		$dropdown[$item['Category_ID']] = $item['Category_Name'];
	}

	return $dropdown;
}