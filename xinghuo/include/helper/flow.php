<?php
/**
 *确定需要多少运费
 *
 */
function get_shipping_fee($UsersID,$Shipping_ID,$Business,$City_Code,$shopConfig,$Products_Info){
	
	
	$template =  get_cur_shipping_template($UsersID,$Shipping_ID,$shopConfig);

	$method = $template['By_Method'];
	
	//是否符合免运费条件	
	if(!empty($template['Free_Content'])){
	    //进行免运费处理
		$res = free_shipping_deal($template['Free_Content'],$Business,$City_Code,$Products_Info);
		//如果免运费
		if($res){
			return 0;
		}
	}
	
	//不符合免运费条件,计算具体运费
	$rule = array();
	$template_content = organize_template_content(json_decode($template['Template_Content'],TRUE));
	$business_content =  $template_content[$Business];
	$specify_areas = array();
    	
	//此运费模板存在特殊区域
	if(!empty($business_content['specify'])){	
		foreach($business_content['specify'] as $key=>$specify){
			$citys = explode(',',$specify['areas']);
			if(in_array($City_Code,$citys)){
				$rule = $specify;
				break;
			}
		}
	}
	
	//如果rule仍为空
	if(empty($rule)){
		$rule = $business_content['default'];
	}
	
	//根据规则计算具体运费
	$weight = $Products_Info['weight'];
	$qty = $Products_Info['qty'];
	$money = $Products_Info['money'];
    $fee = 0;
	if($template['By_Method'] == 'by_weight'){
		if($weight == 0){
			$fee = 0;
		}elseif($weight <= $rule['start']){
			$fee = $rule['postage'];	
		}else{
			$extra_weight = $weight - $rule['start'];
			$extra_fee = ceil($extra_weight/$rule['plus'])*$rule['postageplus'];
			$fee = $rule['postage']+ $extra_fee;
		}
		
	}elseif($template['By_Method'] == 'by_qty'){
		if($qty == 0 ){
			$fee = 0;
		}elseif($qty <= $rule['start']){
			$fee = $rule['postage'];	
		}else{
			$extra_qty = $qty - $rule['start'];	
			$extra_fee = ceil($extra_qty/$rule['plus'])*$rule['postageplus'];
			$fee = $rule['postage']+ $extra_fee;
		}
	
	}
	
	
	return $fee;
	
}



/**
 *确定此产品是否满足免运费条件
 */
function free_shipping_deal($free_content,$Business,$City_Code,$Products_Info){
  
   $free_content = json_decode($free_content,true);
   
   //整理免运费条件
   $list_by_business = array();
   foreach($free_content as $key=>$item){
	   $list_by_business[$item['trans_type']][] = $item;
   }
   
   //如果不存在此业务的免费条款
   if(!array_key_exists($Business,$list_by_business)){
	   return false;
   }else{
	   $regulartions = $list_by_business[$Business];
	   $result = FALSE;
	  
	   foreach($regulartions as $key=>$regulation){
		   //是否在免运费城市之内
		   if(!empty($regulation['areas'])){
				$citys = explode(',',$regulation['areas']);
				//如果不在免运费城市中，继续循环
				if(!in_array($City_Code,$citys)){
					continue;
				}
		   }
		  
		   //根据优惠类型，进行具体决定
		   //0 按件数,1 按金额,2 件数+金额
		   if($regulation['designated'] == 0){
	
			  if($Products_Info['qty'] >= $regulation['prefrrendial_qty']){
				$result = TRUE;
				break;
			  }
			  
		   }elseif($regulation['designated'] == 1){
			  
			  $money = $Products_Info['money']; 
			  if($money >= $regulation['prefrrendial_money']){			
				$result = TRUE;	
				 break;
			  }
		
		   }elseif($regulation['designated'] == 2){
			  $money = $Products_Info['money']; 
		
			  if($Products_Info['qty'] >= $regulation['prefrrendial_qty']&&$money >= $regulation['prefrrendial_money']){
				$result = TRUE;	
				break;
			  }
		   }
		   
	   }
	   
	   
   }
   
   return $result;
  
}

/**
 *确定使用的是哪一个物流模板
 *店铺配置参数
 *
 */
function get_cur_shipping_template($UsersID,$Shipping_ID,$shopConfig){
	
	global $DB1;
	$Shipping_Config = json_decode($shopConfig['Shipping'],TRUE);
	
	
	if(!empty($Shipping_Config[$Shipping_ID])){
		$Template_ID = $Shipping_Config[$Shipping_ID];
	}else{
		echo '没找到相应的物流模板，请联系管理员';
		exit();
	}
	
	$condition = "where Users_ID = '".$UsersID."' and Template_ID = ".$Template_ID;
	$rsTemplate = $DB1->getRs('shop_shipping_template','*',$condition);
	
	return $rsTemplate;
}

/**
 * 获取物流简述信息,包含可用快递公司信息，及其下属物流模板
 * @param  String $UsersID
 * @return Array  $brief  结构
 *                
 */
function get_shipping_brief($UsersID){
	
	global $DB1;
	
	//获得所有可用的快递公司
	$condition = "where Users_ID ='".$_SESSION["Users_ID"]."' and Shipping_Status = 1";
	$rsShippingCompanys = $DB1->get('shop_shipping_company','Shipping_ID,Shipping_Name,Cur_Template',$condition);
	
	if(empty($rsShippingCompanys)){
		return false;
	}
	
	$Shipping_List = $DB1->toArray($rsShippingCompanys);
	//获取这些快递公司下属的物流模板 
	$brief['Shipping_List'] = $Shipping_List;
    
    //检索出快递公司Shipping_ID
	$Shipping_ID_List = array();
	foreach($Shipping_List as $key=>$company){
		$Shipping_ID_List[] = $company['Shipping_ID'];
	}
	
	$Shipping_ID_Str = implode(',',$Shipping_ID_List);
	
	$condition = "where Users_ID ='".$_SESSION["Users_ID"]."' and Template_Status = 1".
				 " and  Shipping_ID in (".$Shipping_ID_Str.")";
 	
	$rsTemplates = $DB1->get('shop_shipping_template','Template_ID,Template_Name,Shipping_ID',$condition);
    
	$Template_List = array();
	if($rsTemplates){
		$Template_List = $DB1->toArray($rsTemplates);	
	}
	
	$Template_Dropdown = array();
	foreach($Template_List as $key=>$item){
		$Template_Dropdown[$item['Shipping_ID']][]		= $item;
	}
	
	$brief['Template_Dropdown'] = $Template_Dropdown;
	
	return $brief;
	
}


/**
 *获取购物车产品总价
 *以及总运费
 */
function get_order_total_info($UsersID,$CartList,$rsConfig,$Shipping_ID,$City_Code = 0  ){
	
	$qty = 0;
	$weight = 0;
	$shipping_money = 0;
	$total =0 ;
	$total_shipping_fee = 0;
	
	foreach($CartList as $key=>$value){
		
		foreach($value as $j=>$v){
			//计算运费
			if($City_Code != 0 ){
				
				/*不免运费*/
				$free_shipping_flag = false;
				if($v['IsShippingFree'] == 1){
					if($v['Shipping_Free_Company'] == $Shipping_ID||$v['Shipping_Free_Company'] == 0){
						$free_shipping_flag = true;
					}
				}
				
				if($free_shipping_flag){
					$qty += 0;
					$weight +=  0;
					$shipping_money += 0;
				}else{
					$qty += $v["Qty"];
					$weight +=  $v["Qty"]*$v["ProductsWeight"];
					$shipping_money += $v["Qty"]*$v["ProductsPriceX"];
					
				}
			}
			
		}
		$total += $v["Qty"]*$v["ProductsPriceX"];
	}
	
	
	
	$Products_Info = array('qty'=>$qty,'weight'=>$weight,'money'=>$shipping_money);

	$Business = 'express';
	
	if($Shipping_ID ==0 &&$City_Code == 0){
		$total_shipping_fee = 0;	
	}else{
		
		$total_shipping_fee = get_shipping_fee($UsersID,$Shipping_ID,$Business,$City_Code,$rsConfig,$Products_Info);
		
	}
	
	//是否符合满多少送多少活动
	$man_flag = false;
	$man_list = json_decode($rsConfig['Man'],true);
	if(count($man_list) >0 ){
		$man_sum = man_act($total,$man_list);
	
		if($man_sum < $total){
			$man_flag = true;
			$origin_total = $total;
			$total = $man_sum;	
			$reduce = $origin_total-$total;
		}
	}
	
	if(isset($reduce)){
		$res['reduce'] = $reduce;
		
	}
	
	$res['man_flag'] = $man_flag;
	$res['total'] = $total;
	$res['total_shipping_fee'] = $total_shipping_fee;

	return $res;
}

//计算参加满多少减多少活动后的金额,无需叠加性
function  man_act($sum,$regulation){
	
	$man_sum = $sum;

	$regulation =  array_reverse($regulation);
	foreach($regulation as $key=>$item){
		
		if($sum >= $item['reach']){
		    $man_sum = $sum - $item['award'];
			break;
		}

	}
	
	return $man_sum;
	
}



/**
 *获取可用优惠券
 **/
function get_useful_coupons($User_ID,$UsersID,$total_price){
	
	global $DB1;
	$num = 0;
	$condition = "where User_ID=".$User_ID." and Users_ID='".$UsersID."' and Coupon_UseArea=1 and (Coupon_UsedTimes=-1 or Coupon_UsedTimes>0) and Coupon_StartTime<=".time()." and Coupon_EndTime>=".time();
	$DB1->Get("user_coupon_record","*",$condition);

	$lists = array();
	while($rsCoupon = $DB1->fetch_assoc()){
		if($rsCoupon["Coupon_Condition"]<=$total_price){
			if($rsCoupon['Coupon_UseType']==0 && $rsCoupon['Coupon_Discount']>0 && $rsCoupon['Coupon_Discount']<1){
				$lists[] = $rsCoupon;
				$num++;
			}
			if($rsCoupon['Coupon_UseType']==1 && $rsCoupon['Coupon_Cash']>0){
				$lists[] = $rsCoupon;
				$num++;
			}
		}
	}

	//完善优惠券信息
	if($num>0){
		foreach($lists as $k=>$v){
			   $r = $DB1->GetRs("user_coupon","Coupon_Subject","where Coupon_ID=".$v["Coupon_ID"]);
			   $v["Subject"] = $r["Coupon_Subject"];
			   if($v['Coupon_UseType']==0 && $v['Coupon_Discount']>0 && $v['Coupon_Discount']<1){
					$v["Subject"] .= '(可享受折扣'.($v['Coupon_Discount']*10).'折)';
				}
				if($v['Coupon_UseType']==1 && $v['Coupon_Cash']>0){
					$v["Subject"] .= '(可抵现金'.$v['Coupon_Cash'].'元)';
				}
		    $lists[$k] = $v;		
		}
	
	}
	
	$coupon_info = Array();
	$coupon_info['num'] = $num; 
    $coupon_info['lists']	= $lists;
	return $coupon_info;
}

/**
 *生成优惠券html
 */
function build_coupon_html($smarty,$coupon_info){
  
    $coupon_html = '';
    if($coupon_info['num'] > 0 ){
		$lists = $coupon_info['lists'];
		foreach($lists as $key=>$item){
			$lists[$key]['Price'] =  $item["Coupon_UseType"]==0?$item["Coupon_Discount"]:$item["Coupon_Cash"];
		}
		$smarty->assign('lists',$lists);
		$coupon_html = $smarty->fetch('order_coupon.html');
	}
   

	return $coupon_html;
}

function get_gift_shipping_fee($UsersID,$rsConfig,$Shipping_ID,$City_Code = 0  ){
	$total_shipping_fee = 0;
	$Products_Info = array('qty'=>1,'weight'=>1,'money'=>0);

	$Business = 'express';
	
	if($Shipping_ID ==0 &&$City_Code == 0){
		$total_shipping_fee = 0;	
	}else{
		$total_shipping_fee = get_shipping_fee($UsersID,$Shipping_ID,$Business,$City_Code,$rsConfig,$Products_Info);
	}
	
	$res['total_shipping_fee'] = $total_shipping_fee;
	return $res;
}