<?php

/**
 *去除字符串中的emoji表情
 */
function removeEmoji($text) {

    $clean_text = "";

    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, '', $text);

    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);

    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, '', $clean_text);

    // Match Miscellaneous Symbols
    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
    $clean_text = preg_replace($regexMisc, '', $clean_text);

    // Match Dingbats
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
    $clean_text = preg_replace($regexDingbats, '', $clean_text);

    return $clean_text;
}

/**
 *获取商品的平均评分
 *
 */
function get_comment_aggregate($DB,$UsersID,$ProductID){
	
	$condition = "where Users_ID = '".$UsersID."' and Product_ID=".$ProductID;
	$rsCommit = $DB->getRs("user_order_commit","AVG(Score) as Points,COUNT(Item_ID) as NUM",$condition);
	$points = 0; 
    $aggerate = array('points'=>0,'num'=>0);
	
    if(!empty($rsCommit)){
		$aggerate['points'] = intval($rsCommit['Points']);
        $aggerate['num'] = $rsCommit['NUM'];
	}

	return $aggerate;

}




function get_property($usersid="",$typeid=0, $ProductsID=0){
	
	global $DB1;
	$html = "";
	$PROPERTY = array();
	if($ProductsID){
		$rsProducts=$DB1->GetRs("shop_products","*","where Users_ID='".$usersid."' and Products_ID=".$ProductsID);
		
		if($rsProducts){
			$JSON=json_decode($rsProducts['Products_JSON'],true);
			if(!empty($JSON["Property"])){
				$PROPERTY = $JSON["Property"];
			}
		}
	}
	
	$DB1->get("shop_property","*","where Users_ID='".$usersid."' and (Type_ID=".$typeid." or Type_ID=0) order by Property_Index asc,Property_ID asc");
	
	
	while($r=$DB->fetch_assoc()){
		if($r["Property_Type"]==0){//单行文本
			$html .='<div class="rows">
			  <label>'.$r["Property_Name"].'</label>
			  <span class="input"><input type="text" name="JSON[Property]['.$r["Property_Name"].']" value="'.(!empty($PROPERTY) && !empty($PROPERTY[$r["Property_Name"]]) ? $PROPERTY[$r["Property_Name"]] : "").'" class="form_input" size="35" /></span>
			  <div class="clear"></div>
			</div>';
		}elseif($r["Property_Type"]==1){//多行文本
			
			$html .='<div class="rows">
			  <label>'.$r["Property_Name"].'</label>
			  <span class="input"><textarea name="JSON[Property]['.$r["Property_Name"].']" class="briefdesc">'.(!empty($PROPERTY) && !empty($PROPERTY[$r["Property_Name"]]) ? $PROPERTY[$r["Property_Name"]] : "").'</textarea></span>
			  <div class="clear"></div>
			</div>';
			
		}elseif($r["Property_Type"]==2){//下拉框
			$html .='<div class="rows">
			  <label>'.$r["Property_Name"].'</label>
			  <span class="input"><select name="JSON[Property]['.$r["Property_Name"].']" style="width:180px">';
			  $List=json_decode($r["Property_Json"],true);
			  foreach($List as $key=>$value){
				  $html .='<option value="'.$value.'"'.(!empty($PROPERTY) && !empty($PROPERTY[$r["Property_Name"]]) && $value==$PROPERTY[$r["Property_Name"]] ? " selected" : "").'>'.$value.'</option>';
			  }
			  $html .='</select></span>
			  <div class="clear"></div>
			</div>';
		}elseif($r["Property_Type"]==3){//多选框
			
		
			$html .='<div class="rows">
			  <label>'.$r["Property_Name"].'</label>
			  <span class="input">';
			  $List=json_decode($r["Property_Json"],true);
			  
			 foreach($List as $key=>$value){
				  
				  $html .='<input type="checkbox" name="JSON[Property]['.$r["Property_Name"].'][]" value="'.$value.'"'.(!empty($PROPERTY) && !empty($PROPERTY[$r["Property_Name"]]) && in_array($value,$PROPERTY[$r["Property_Name"]]) ? " checked" : "").'>&nbsp;'.$value.'&nbsp;&nbsp;&nbsp;&nbsp;';
			  }
			  $html .='</span>
			  <div class="clear"></div>
			</div>';
		}else{//单选按钮
			$html .='<div class="rows">
			  <label>'.$r["Property_Name"].'</label>
			  <span class="input">';
			  $List=json_decode($r["Property_Json"],true);
			  foreach($List as $key=>$value){
				  $html .='<input type="radio" name="JSON[Property]['.$r["Property_Name"].']" value="'.$value.'"'.(!empty($PROPERTY) && !empty($PROPERTY[$r["Property_Name"]]) && $value==$PROPERTY[$r["Property_Name"]] ? " checked" : "").'/>&nbsp;'.$value.'&nbsp;&nbsp;&nbsp;&nbsp;';
			  }
			  $html .='</span>
			  <div class="clear"></div>
			</div>';
		}
	}
	return $html;
}


/**
 * 获取可用的支付方式
 * @return [type] [description]
 */
function get_enabled_pays($DB,$UsersID){
	$rsPayConfig = $DB->GetRs("users_payconfig","*","where Users_ID='".$UsersID."'");

	$pays = array();
	if($rsPayConfig['PaymentWxpayEnabled'] == 1){
		$pays['Wxpay'] = '微信支付';
	}
	if($rsPayConfig['Payment_AlipayEnabled'] == 1){

		$pays['Alipay'] = '支付宝支付';
	}
	if($rsPayConfig['PaymentYeepayEnabled'] == 1){
	
		$pays['Yeepay'] = '易宝支付';
	}
	
	if($rsPayConfig['Payment_OfflineEnabled'] == 1){
		$pays['Offline'] = '线下支付(货到付款)';
	}
	
	
	return $pays;
}

/**
 * 截取UTF-8编码下字符串的函数
 *
 * @param   string      $str        被截取的字符串
 * @param   int         $length     截取的长度
 * @param   bool        $append     是否附加省略号
 *
 * @return  string
 */
function sub_str($str, $length = 0, $append = true)
{
	$ec_charset = 'utf-8';
    $str = trim($str);
    $strlength = strlen($str);

    if ($length == 0 || $length >= $strlength)
    {
        return $str;
    }
    elseif ($length < 0)
    {
        $length = $strlength + $length;
        if ($length < 0)
        {
            $length = $strlength;
        }
    }

    if (function_exists('mb_substr'))
    {
        $newstr = mb_substr($str, 0, $length, $ec_charset);
    }
    elseif (function_exists('iconv_substr'))
    {
        $newstr = iconv_substr($str, 0, $length,$ec_charset);
    }
    else
    {
        //$newstr = trim_right(substr($str, 0, $length));
        $newstr = substr($str, 0, $length);
    }

    if ($append && $str != $newstr)
    {
        $newstr .= '...';
    }

    return $newstr;
}



function filter($var)
{
	if($var == '')
	{
		return false;
	}
	return true;
}


/**
 *将积分移入不可用积分
 *@param $UsersID
 *@param $UserID  用户ID
 *@param $Integral 移动积分量 
 */
function  add_userless_integral($UsersID,$User_ID,$Integral){
	 
	 $Data = array(
	 			"User_Integral"=>'User_Integral-'.$Integral,
				"User_UseLessIntegral"=>'User_UseLessIntegral+'.$Integral,
	 		);
			
	  global $DB1;
	  $condition = "where Users_ID='".$UsersID."' and User_ID=".$User_ID;
	  $Flag = $DB1->Set('user',$Data,$condition,'User_Integral,User_UseLessIntegral');
	
	  return $Flag;
}
	
/**
 *将不可用积分返还到可用积分 
 *@param $UsersID
 *@param $UserID  用户ID
 *@param $Integral 移动积分量 
 */	
function  remove_userless_integral($UsersID,$User_ID,$Integral){
	 
	 $Data = array(
	 			"User_Integral"=>'User_Integral+'.$Integral,
				"User_UseLessIntegral"=>'User_UseLessIntegral-'.$Integral,
	 		);
			
	  global $DB1;
	  $condition = "where Users_ID='".$UsersID."' and User_ID=".$User_ID;
	  $Flag = $DB1->Set('user',$Data,$condition,'User_Integral,User_UseLessIntegral');
	
	  return $Flag;
}	

 
/**
 *根据昵称关键词获取用户id串
 */ 
function find_userids_by_nickname($Users_ID,$NickName){
	global $DB1;
	$condition = "where Users_ID ='".$Users_ID."' and User_NickName like '%".$NickName."%'";
	$rsIds = $DB1->get('user','User_ID',$condition);
	$rsIDList = $DB1->toArray($rsIds);
	
	if(!empty($rsIDList)){
		$id_array = array();
		foreach($rsIDList as $key=>$item){
			$id_array[] = $item['User_ID'];	   
		}
		
		$result  =implode(',',$id_array);
		
	}else{
		$result = false;
	}
	
	return $result;
	
}
 
/**
 *根据产品名获取产品id串
 */ 
function find_productids_by_Name($Users_ID,$Name){
	
	global $DB1;
	$condition = "where Users_ID ='".$Users_ID."' and Products_Name like '%".$Name."%'";
	
	$rsIds = $DB1->get('shop_products','Products_ID',$condition);
	$rsIDList = $DB1->toArray($rsIds);
	
	if(!empty($rsIDList)){
		$id_array = array();
		foreach($rsIDList as $key=>$item){
			$id_array[] = $item['Products_ID'];	   
		}
		
		$result  =implode(',',$id_array);
	}else{
		$result = false;
	}
	
	
	return $result;
	

}

/**
 * 创建以逗号分隔值字符串
 * @param  Array  $val_list 值列表 
 * @param  String $ids     以逗号分隔的id字符串
 * @return String $html    以逗号分隔的值字符串
 */
function build_comma_html($val_list,$ids){
	$html = '';
	if(strlen($ids) >0 ){
		
		$id_list = explode(',',$ids);
		$html_list = array();
		foreach($id_list as $k=>$id){
			$html_list [] = $val_list[$id];
		}
		
		$html = implode(',',$html_list);
	}
	
	return $html;
}


/**
 *若值为空，则输出默认值
 *@param $val 需要输出的值
 *@pram  $default 默认值
 *@return $result 需要输出的最终值
 */
 
function empty_default($val,$default){
	$result = !empty($val)?$val:$default;
	return $result;
}


/**
 *获取地区列表
 */
function get_regison_list(){
	
	global $DB1;
	$rsAreas = $DB1->get('area','*','where area_deep = 1');
	$province_all_array = $DB1->toArray($rsAreas);
	foreach ($province_all_array as $a) {
     
            if ($a['area_deep'] == 1 && $a['area_region'])
                $region[$a['area_region']][] = $a['area_id'];
     
	}
	
	return $region;
}

/**
 * 获取所有区域信息
 * @param  int     $deep 区域深度
 * @return Array   $arr 区域数组
 */
function get_all_area($deep){
	
    global $DB1;
	$rsAreas = $DB1->get('area','*','where area_deep <='.$deep);
	
	$area_all_array = $DB1->toArray($rsAreas);
	
	
	foreach ($area_all_array as $a) {
            $data['name'][$a['area_id']] = $a['area_name'];
            $data['parent'][$a['area_id']] = $a['area_parent_id'];
            $data['children'][$a['area_parent_id']][] = $a['area_id'];

            if ($a['area_deep'] == 1 && $a['area_region'])
                $data['region'][$a['area_region']][] = $a['area_id'];
     }
	 
	$arr = array();
	
    foreach ($data['children'] as $k => $v) {
          foreach ($v as $vv) {
               $arr[$k][] = array($vv, $data['name'][$vv]);
          }
		  
	}
	
	return $arr;
			
}


/**
 *确定需要多少运费
 *
 */
function get_shipping_fee($UsersID,$Shipping_ID,$Business,$City_Code,$shopConfig,$Product_Info){
	
	$template =  get_cur_shipping_template($UsersID,$Shipping_ID,$shopConfig);
	$method = $template['By_Method'];
	
	//是否符合免运费条件	
	if(!empty($template['Free_Content'])){
	    //进行免运费处理
		$res = free_shipping_deal($template['Free_Content'],$Business,$City_Code,$Product_Info);
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
    $fee = 0;
	if($template['By_Method'] == 'by_weight'){
		$weight = $Product_Info['Weight']*$Product_Info['Qty'];
		
		if($weight <= $rule['start']){
			$fee = $rule['postage'];	
		}else{
			$extra_weight = $weight - $rule['start'];
			$extra_fee = ceil($extra_weight/$rule['plus'])*$rule['postageplus'];
			$fee = $rule['postage']+ $extra_fee;
		}
	}elseif($template['By_Method'] == 'by_qty'){
	
		$qty = $Product_Info['Qty'];
		
		if($qty <= $rule['start']){
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
function free_shipping_deal($free_content,$Business,$City_Code,$Product_Info){
  
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
			  
			  if($Product_Info['Qty'] >= $regulation['prefrrendial_qty']){
				
				$result = TRUE;
				break;
			  }
			  
		   }elseif($regulation['designated'] == 1){
			  
			  $money = $Product_Info['Price']*$Product_Info['Qty']; 
	
			  if($money >= $regulation['prefrrendial_money']){
				
				$result = TRUE;	
				break;
			  }
		
		   }elseif($regulation['designated'] == 2){
			  $money = $Product_Info['Price']*$Product_Info['Qty']; 
		
			  if($Product_Info['Qty'] >= $regulation['prefrrendial_qty']&&$money >= $regulation['prefrrendial_money']){
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
function get_order_total_info($UsersID,$CartList,$rsConfig,$City_Code = 0  ){
	
	
	$Default_Business = $rsConfig['Default_Business'];	
	$Default_Shipping = $rsConfig['Default_Shipping'];

	$total=0;
	$total_shipping_fee = 0;
	foreach($CartList as $key=>$value){
		
		foreach($value as $j=>$v){
			//计算运费
			if($City_Code != 0 ){
				
				if($v['IsShippingFree'] == 1){
					$shipping_fee = 0;
				}else{
					if(!empty($v['Products_Business'])){
						$Business = $v['Products_Business'];
					}else{
						$Business = $Default_Business;
					}
  
					if(!empty($v['Products_Shipping'])){
						$Shipping_ID = $v['Products_Shipping'];
					}else{
						$Shipping_ID = $Default_Shipping;
					}
					$Product_Info = array('Qty'=>$v["Qty"],'Weight'=>$v['ProductsWeight'],'Price'=>$v["ProductsPriceX"]);
					$shipping_fee =  get_shipping_fee($UsersID,$Shipping_ID,$Business,$City_Code,$rsConfig,$Product_Info);
				}
			
				$total_shipping_fee += $shipping_fee;
			}
			
			$total+=$v["Qty"]*$v["ProductsPriceX"];
			
		}
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
	foreach($regulation as $key=>$item){
		if($sum >= $item['reach']){
		    $man_sum = $sum - $item['award'];
			break;
		}

	}
	
	return $man_sum;
	
}
