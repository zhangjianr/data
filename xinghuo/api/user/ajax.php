<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/Framework/Conn.php');

if(isset($_GET['UsersID'])){
	$UsersID=$_GET['UsersID'];
}else{
	echo json_encode(empty($Data)?array('status'=>0,'msg'=>'请勿非法操作！'):$Data,JSON_UNESCAPED_UNICODE);
}

if(isset($_SESSION[$UsersID."User_ID"])){
	$UserID=$_SESSION[$UsersID."User_ID"];
	$action=empty($_REQUEST['action'])?'':$_REQUEST['action'];
	$rsConfig=$DB->GetRs("user_config","*","where Users_ID='".$UsersID."'");
	$UserLevel=json_decode($rsConfig['UserLevel'],true);
	$rsUser=$DB->GetRs("user","*","where Users_ID='".$UsersID."' and User_ID=".$UserID);
	$RecordType=array("获得积分"=>2,"使用积分"=>3);
	
	//开始事务定义
	$Flag=true;
	$msg="";
	mysql_query("begin");
	if($action=='sign'){
		$rsSign=$DB->GetRs("user_Integral_record","*","where Record_Type=0 and Record_CreateTime>".strtotime(date("Y-m-d 00:00:00"))." and Users_ID='".$UsersID."' and User_ID=".$UserID);
		if($rsSign){
			$Data=array(
				'status'=>0
			);
		}else{
			//增加
			$Data=array(
				'Record_Integral'=>$rsConfig['SignIntegral'],
				'Record_SurplusIntegral'=>$rsUser['User_Integral']+$rsConfig['SignIntegral'],
				'Operator_UserName'=>'',
				'Record_Type'=>0,
				'Record_Description'=>'每日签到领取积分',
				'Record_CreateTime'=>time(),
				'Users_ID'=>$UsersID,
				'User_ID'=>$UserID
			);
			$Flag=$Flag&&$DB->Add('user_Integral_record',$Data);
			
			$Flag=$Flag&&$DB->Set("user","User_TotalIntegral=User_TotalIntegral+".$rsConfig['SignIntegral'].",User_Integral=User_Integral+".$rsConfig['SignIntegral'],"where Users_ID='".$UsersID."' and User_ID=".$UserID);
			if($Flag){
				require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/weixin_message.class.php');
				$weixin_message = new weixin_message($DB,$UsersID,$UserID);
				$contentStr = "每日签到领取".$rsConfig['SignIntegral']."积分";
				$weixin_message->sendscorenotice($contentStr);
				$Data=array(
					'status'=>1,
					'integral'=>$rsUser['User_Integral']+$rsConfig['SignIntegral']
				);
			}else{
				$Data=array(
					'status'=>0
				);
			}
		}
	}elseif($action=='record'){
		if(is_numeric($_POST['Integral'])){
			$rsPassword=$DB->GetRs("user_operator","*","where Users_ID='".$UsersID."' and Operator_Password='".$_POST["Password"]."'");
			if($rsPassword){
				//增加
				$inregral_chan = $_POST['RecordType']=='获得积分'?$_POST['Integral']:'-'.$_POST['Integral'];
				$Data=array(
					'Record_Integral'=>$_POST['RecordType']=='获得积分'?$_POST['Integral']:'-'.$_POST['Integral'],
					'Record_SurplusIntegral'=>$_POST['RecordType']=='获得积分'?$rsUser['User_Integral']+$_POST['Integral']:$rsUser['User_Integral']-$_POST['Integral'],
					'Operator_UserName'=>$rsPassword['Operator_UserName'],
					'Record_Type'=>$RecordType[$_POST['RecordType']],
					'Record_Description'=>$_POST['RecordType'],
					'Record_CreateTime'=>time(),
					'Users_ID'=>$UsersID,
					'User_ID'=>$UserID
				);
				$Flag=$Flag&&$DB->Add('user_Integral_record',$Data);
				if($_POST['RecordType']=='获得积分'){
					foreach($UserLevel as $k=>$v){
						 if($rsUser['User_TotalIntegral']+$_POST['Integral']>=$v['UpIntegral']){
							 $levelID=$k;
						 }
					}
					$Data=array(
						"User_Level"=>$levelID,
						"User_Integral"=>$rsUser['User_Integral']+$_POST['Integral'],
						"User_TotalIntegral"=>$rsUser['User_TotalIntegral']+$_POST['Integral']
					);
					$integral_change = $_POST['Integral'];
				}else{
					$Data=array(
						"User_Integral"=>$rsUser['User_Integral']-$_POST['Integral']
					);
					$integral_change = -$_POST['Integral'];
				}
				$Flag=$Flag&&$DB->Set("user",$Data,"where Users_ID='".$UsersID."' and User_ID=".$UserID);
				if($Flag){
					require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/weixin_message.class.php');
					$weixin_message = new weixin_message($DB,$UsersID,$UserID);
					$contentStr = $_POST['RecordType']=='获得积分' ? "获得".$_POST['Integral']."积分" : "使用".$_POST['Integral']."积分";
					$weixin_message->sendscorenotice($contentStr);
					$Data=array(
						'status'=>1,
						'msg'=>$_POST['RecordType'].'成功'
					);
				}else{
					$Data=array(
						'status'=>0,
						'msg'=>$_POST['RecordType'].'失败，请重新操作'
					);
				}
			}else{
				$Data=array(
					'status'=>0,
					'msg'=>'商家密码错误！'
				);
			}
		}else{
			$Data=array(
				'status'=>0,
				'msg'=>'请正确填写所需信息！'
			);
		}
	}elseif($action=='get_coupon'){
		$rsCoupon=$DB->GetRs("user_coupon","*","where Users_ID='".$UsersID."' and Coupon_ID=".$_POST['CouponID']);
		if($rsCoupon){
			if($rsCoupon["Coupon_UserLevel"] <= $rsUser["User_Level"]){
				$Data=array(
					'Coupon_ID'=>$rsCoupon['Coupon_ID'],
					'Coupon_UsedTimes'=>$rsCoupon['Coupon_UsedTimes'],
					'Coupon_UseArea'=>$rsCoupon['Coupon_UseArea'],
					'Coupon_UseType'=>$rsCoupon['Coupon_UseType'],
					'Coupon_Condition'=>$rsCoupon['Coupon_Condition'],
					'Coupon_Discount'=>$rsCoupon['Coupon_Discount'],
					'Coupon_Cash'=>$rsCoupon['Coupon_Cash'],
					'Coupon_StartTime'=>$rsCoupon['Coupon_StartTime'],
					'Coupon_EndTime'=>$rsCoupon['Coupon_EndTime'],
					'Record_CreateTime'=>time(),
					'Users_ID'=>$UsersID,
					'User_ID'=>$UserID
				);
				$Flag=$Flag&&$DB->Add('user_coupon_record',$Data);
				if($Flag){
					$Data=array(
						'status'=>1,
						'msg'=>'操作成功！'
					);
				}else{
					$Data=array(
						'status'=>0,
						'msg'=>'网络拥堵，请稍后再试！'
					);
				}
			}else{
				$Data=array(
					'status'=>0,
					'msg'=>'你没有权限领取该优惠券'
				);
			}
		}else{
			$Data=array(
				'status'=>0,
				'msg'=>'请勿非法操作！'
			);
		}
	}elseif($action=='payword'){
		$rsUser=$DB->GetRs("user","*","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID']);
		if($rsUser){
			if(md5($_POST["YPayPassword"]) != $rsUser["User_PayPassword"]){
				$Data=array(
					'status'=>0,
					'msg'=>'原支付密码输入不正确！'
				);
			}else{
				$Data=array(
					'User_PayPassword'=>md5($_POST["PayPassword"])
				);
				$Flag=$DB->Set('user',$Data,"where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID']);
				if($Flag){
					$Data=array(
						'status'=>1,
						'msg'=>'操作成功！'
					);
				}else{
					$Data=array(
						'status'=>0,
						'msg'=>'网络拥堵，请稍后再试！'
					);
				}
			}
		}else{
			$Data=array(
				'status'=>0,
				'msg'=>'请勿非法操作！'
			);
		}
	}elseif($action=='paymoney'){
		$rsUser=$DB->GetRs("user","*","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID']);
		if($rsUser && is_numeric($_POST['Amount']) && $_POST['Amount']>0){
			if(md5($_POST["PayPassword"]) != $rsUser["User_PayPassword"]){
				$Data=array(
					'status'=>0,
					'msg'=>'支付密码输入不正确！'
				);
			}else{
				if($rsUser['User_Money']>=$_POST['Amount']){
					//增加资金流水
					$Data=array(
						'Users_ID'=>$UsersID,
						'User_ID'=>$_SESSION[$UsersID.'User_ID'],				
						'Type'=>0,
						'Amount'=>$_POST['Amount'],
						'Total'=>$rsUser['User_Money']-$_POST['Amount'],
						'Note'=>"实体店消费 -".$_POST['Amount'],
						'CreateTime'=>time()			
					);
					$Flag=$DB->Add('user_money_record',$Data);
					//更新用户余额
					$Data=array(				
						'User_Money'=>$rsUser['User_Money']-$_POST['Amount']					
					);
					$Flag=$DB->Set('user',$Data,"where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID']);
					if($Flag){
						$Data=array(
							'status'=>1,
							'msg'=>'操作成功！'
						);
					}else{
						$Data=array(
							'status'=>0,
							'msg'=>'网络拥堵，请稍后再试！'
						);
					}
				}else{
					$Data=array(
						'status'=>0,
						'msg'=>'余额不足，请充值'
					);
				}
			}
		}else{
			$Data=array(
				'status'=>0,
				'msg'=>'消费金额输入格式错误'
			);
		}
	}elseif($action=='charge'){
		$PaymentMethod = array(
			"1"=>"微支付",
			"2"=>"支付宝"
		);
		$rsUser=$DB->GetRs("user","*","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID']);
		if($rsUser && is_numeric($_POST['Amount']) && $_POST['Amount']>0){
			$Data=array(
				'Users_ID'=>$UsersID,
				'User_ID'=>$_SESSION[$UsersID.'User_ID'],
				'Amount'=>$_POST['Amount'],
				'Total'=>$rsUser['User_Money']+$_POST['Amount'],
				'Operator'=>$PaymentMethod[$_POST["Operator"]]."充值 +".$_POST['Amount'],
				'CreateTime'=>time()			
			);
			$Flag=$DB->Add('user_charge',$Data);
			$itemid = $DB->insert_id();
			if($Flag){
				$Data=array(
					'status'=>1,
					'url'=>'/api/'.$UsersID.'/user/chargepay/'.$itemid.'/'.$_POST["Operator"].'/',
					'msg'=>'操作成功！'
				);
			}else{
				$Data=array(
					'status'=>0,
					'msg'=>'网络拥堵，请稍后再试！'
				);
			}
		}else{
			$Data=array(
				'status'=>0,
				'msg'=>'消费金额输入格式错误'
			);
		}
	}elseif($action=='use_coupon'){
		//判断是否拥有优惠券
		$rsCoupon=$DB->GetRs("user_coupon`,`user_coupon_record","user_coupon.Coupon_Subject,user_coupon_record.Coupon_UsedTimes","where user_coupon.Coupon_ID=user_coupon_record.Coupon_ID and user_coupon.Users_ID='".$UsersID."' and user_coupon_record.User_ID='".$_SESSION[$UsersID.'User_ID']."' and user_coupon.Coupon_ID=".$_POST['CouponID']);
		if($rsCoupon){
			//使用次数减一
			if($rsCoupon['Coupon_UsedTimes']==0){
				$Data=array(
					'status'=>0,
					'msg'=>'优惠券已经使用完！'
				);
			}else{
				if($rsCoupon['Coupon_UsedTimes']>=1){
					$Flag=$Flag&&$DB->Set("user_coupon_record","Coupon_UsedTimes=Coupon_UsedTimes-1","where Users_ID='".$UsersID."' and User_ID='".$_SESSION[$UsersID.'User_ID']."' and Coupon_ID=".$_POST['CouponID']);
				}
				$rsOperator=$DB->GetRs("user_operator","Operator_UserName","where Users_ID='".$UsersID."' and Operator_Password='".$_POST["Password"]."'");
				if($rsOperator){
					//增加积分
					$Data=array(
						'Record_Integral'=>$_POST['Integral'],
						'Record_SurplusIntegral'=>$rsUser['User_Integral']+$_POST['Integral'],
						'Operator_UserName'=>$rsOperator['Operator_UserName'],
						'Record_Type'=>4,
						'Record_Description'=>'使用优惠券获取积分',
						'Record_CreateTime'=>time(),
						'Users_ID'=>$UsersID,
						'User_ID'=>$UserID
					);
					$Flag=$Flag&&$DB->Add('user_Integral_record',$Data);
					//增加使用日志
					$Data=array(
						'Users_ID'=>$UsersID,
						'User_ID'=>$UserID,
						'User_Name'=>$_SESSION[$UsersID."User_Name"],
						'Coupon_Subject'=>$rsCoupon['Coupon_Subject'],
						'Logs_Price'=>$_POST['Price'],
						'Coupon_UsedTimes'=>$rsCoupon['Coupon_UsedTimes']>=1?$rsCoupon['Coupon_UsedTimes']-1:$rsCoupon['Coupon_UsedTimes'],
						'Logs_CreateTime'=>time(),
						'Operator_UserName'=>$rsOperator['Operator_UserName']
					);
					$Flag=$Flag&&$DB->Add('user_coupon_logs',$Data);
					
					foreach($UserLevel as $k=>$v){
						 if($rsUser['User_TotalIntegral']+$_POST['Integral']>=$v['UpIntegral']){
							 $levelID=$k;
						 }
					}
					$Flag=$Flag&&$DB->Set("user","User_Level=".$levelID.",User_TotalIntegral=User_TotalIntegral+".$_POST['Integral'].",User_Integral=User_Integral+".$_POST['Integral'],"where Users_ID='".$UsersID."' and User_ID=".$UserID);
					if($Flag){
						$Data=array(
							'status'=>1,
							'msg'=>'操作成功'
						);
					}else{
						$Data=array(
							'status'=>0,
							'msg'=>'网络拥堵，请稍后再试！'
						);
					}
				}else{
					$Data=array(
						'status'=>0,
						'msg'=>'商家密码错误！'
					);
				}
			}
		}else{
			$Data=array(
				'status'=>0,
				'msg'=>'请勿非法操作！'
			);
		}
	}elseif($action=='gift_change'){
		$rsGift=$DB->GetRs("user_gift","*","where Users_ID='".$UsersID."' and Gift_ID=".$_POST['GiftID']);
		if($rsGift){
			if($rsUser['User_Integral']-$rsGift['Gift_Integral']>=0){
				if($rsGift['Gift_Qty']>=1){
					$Flag=$Flag&&$DB->Set("user_gift","Gift_Qty=Gift_Qty-1","where Users_ID='".$UsersID."' and Gift_ID=".$_POST['GiftID']);
					//增加积分记录
					$Data=array(
						'Record_Integral'=>-$rsGift['Gift_Integral'],
						'Record_SurplusIntegral'=>$rsUser['User_Integral']-$rsGift['Gift_Integral'],
						'Operator_UserName'=>'',
						'Record_Type'=>5,
						'Record_Description'=>'使用积分兑换礼品',
						'Record_CreateTime'=>time(),
						'Users_ID'=>$UsersID,
						'User_ID'=>$UserID
					);
					$Flag=$Flag&&$DB->Add('user_Integral_record',$Data);
					//减掉用户表用户积分
					$Flag=$Flag&&$DB->Set("user","User_Integral=User_Integral-".$rsGift['Gift_Integral'],"where Users_ID='".$UsersID."' and User_ID=".$UserID);
					if(empty($rsGift['Gift_Shipping'])){
						$Data=array(
							"Users_ID"=>$UsersID,
							"User_ID"=>$UserID
						);
					}else{
						$AddressID=empty($_POST['AddressID'])?0:$_POST['AddressID'];
						if(empty($_POST['AddressID'])){
							//增加
							$Data=array(
								"Address_Name"=>$_POST['Name'],
								"Address_Mobile"=>$_POST["Mobile"],
								"Address_Province"=>empty($_POST['Province'])?"":$_POST['Province'],
								"Address_City"=>empty($_POST['City'])?"":$_POST['City'],
								"Address_Area"=>empty($_POST['Area'])?"":$_POST['Area'],
								"Address_Detailed"=>$_POST["Detailed"],
								"Users_ID"=>$UsersID,
								"User_ID"=>$UserID
							);
							$Flag=$Flag&&$DB->Add("user_address",$Data);
						}else{
							$rsAddress=$DB->GetRs("user_address","*","where Users_ID='".$UsersID."' and User_ID='".$UserID."' and Address_ID='".$AddressID."'");
							$Data=array(
								"Address_Name"=>$rsAddress['Address_Name'],
								"Address_Mobile"=>$rsAddress["Address_Mobile"],
								"Address_Province"=>$rsAddress["Address_Province"],
								"Address_City"=>$rsAddress["Address_City"],
								"Address_Area"=>$rsAddress["Address_Area"],
								"Address_Detailed"=>$rsAddress["Address_Detailed"],
								"Users_ID"=>$UsersID,
								"User_ID"=>$UserID
							);
						}
					}
					$Data["Orders_Status"]=0;
					$Data["Gift_ID"]=$_POST['GiftID'];
					$Data["Orders_FinishTime"]=time();
					$Data["Orders_CreateTime"]=time();
					$Flag=$Flag&&$DB->Add('user_gift_orders',$Data);
					if($Flag){
						$Data=array(
							'status'=>1,
							'msg'=>'操作成功！'
						);
					}else{
						$Data=array(
							'status'=>0,
							'msg'=>'网络拥堵，请稍后再试！'
						);
					}
				}else{
						$Data=array(
							'status'=>0,
							'msg'=>'礼品已经兑换完了！'
						);
				}
			}else{
				$Data=array(
					'status'=>0,
					'msg'=>'对不起，兑换此礼品需'.$rsGift['Gift_Integral'].'积分，您的帐户积分不足！'
				);
			}
		}else{
			$Data=array(
				'status'=>0,
				'msg'=>'请勿非法操作！'
			);
		}
	}elseif($action=='modify_mobile'){
		$rsPassword=$DB->GetRs("user","User_Password","where Users_ID='".$UsersID."' and User_ID=".$UserID." and User_Password='".md5($_POST["Password"])."'");
		if($rsPassword){
			if($_POST['Mobile']==$_POST['MobileCheck']){
				$Data=array(
					'status'=>0,
					'msg'=>'修改失败，原手机号与新手机号相同！'
				);
			}else{
				$rsMobile=$DB->GetRs("user","User_Mobile","where Users_ID='".$UsersID."' and User_Mobile=".$_POST['Mobile']);
				if($rsMobile){
					$Data=array(
						'status'=>0,
						'msg'=>'修改失败，手机号'.$_POST['Mobile'].'已经存在！'
					);
				}else{
					$Flag=$Flag&&$DB->Set("user","User_Mobile=".$_POST['Mobile'],"where Users_ID='".$UsersID."' and User_ID=".$UserID);
					if($Flag){
						$Data=array(
							'status'=>1,
							'msg'=>'操作成功'
						);
					}else{
						$Data=array(
							'status'=>0,
							'msg'=>'网络拥堵，请稍后再试！'
						);
					}
				}
			}
		}else{
			$Data=array(
				'status'=>0,
				'msg'=>'登录密码错误！'
			);
		}
	}elseif($action=='modify_password'){
		$rsPassword=$DB->GetRs("user","User_Password","where Users_ID='".$UsersID."' and User_ID=".$UserID." and User_Password='".md5($_POST["YPassword"])."'");
		if($rsPassword){
			if($_POST['Password']==$_POST['ConfirmPassword']){
				$Flag=$Flag&&$DB->Set("user","User_Password='".md5($_POST["Password"])."'","where Users_ID='".$UsersID."' and User_ID=".$UserID);
				if($Flag){
					$Data=array(
						'status'=>1,
						'msg'=>'操作成功'
					);
				}else{
					$Data=array(
						'status'=>0,
						'msg'=>'网络拥堵，请稍后再试！'
					);
				}
			}else{
				$Data=array(
					'status'=>0,
					'msg'=>'登录密码与确认密码不匹配，请重新输入！'
				);
			}
		}else{
			$Data=array(
				'status'=>0,
				'msg'=>'原登录密码错误！'
			);
		}
	}elseif($action=='profile'){
		$Data=array(
			'User_Address'=>empty($_POST['Address'])?'':$_POST['Address'],
			'User_Age'=>empty($_POST['Age'])?'':$_POST['Age'],
			'User_Area'=>empty($_POST['Area'])?'':$_POST['Area'],
			'User_Birthday'=>empty($_POST['BirthdayY'])||empty($_POST['BirthdayM'])||empty($_POST['BirthdayD'])?'':strtotime($_POST['BirthdayY'].'-'.$_POST['BirthdayM'].'-'.($_POST['BirthdayD']>date("t",strtotime($_POST['BirthdayY'].'-'.$_POST['BirthdayM'].'-01'))?date("t",strtotime($_POST['BirthdayY'].'-'.$_POST['BirthdayM'].'-01')):$_POST['BirthdayD'])),
			'User_City'=>empty($_POST['City'])?'':$_POST['City'],
			'User_Company'=>empty($_POST['Company'])?'':$_POST['Company'],
			'User_Email'=>empty($_POST['Email'])?'':$_POST['Email'],
			'User_Fax'=>empty($_POST['Fax'])?'':$_POST['Fax'],
			'User_Gender'=>empty($_POST['Gender'])?'':$_POST['Gender'],
			'User_IDNum'=>empty($_POST['IDNum'])?'':$_POST['IDNum'],
			'User_Name'=>empty($_POST['Name'])?'':$_POST['Name'],
			'User_NickName'=>empty($_POST['NickName'])?'':$_POST['NickName'],
			'User_Province'=>empty($_POST['Province'])?'':$_POST['Province'],
			'User_QQ'=>empty($_POST['QQ'])?'':$_POST['QQ'],
			'User_Telephone'=>empty($_POST['Telephone'])?'':$_POST['Telephone']
		);
		$Flag=$Flag&&$DB->Set("user",$Data,"where Users_ID='".$UsersID."' and User_ID=".$UserID);
		if($Flag){
			$Data=array(
				'status'=>1,
				'msg'=>'操作成功'
			);
		}else{
			$Data=array(
				'status'=>0,
				'msg'=>'网络拥堵，请稍后再试！'
			);
		}
	}elseif($action=='complete'){
		$Data=array(
			'User_Mobile'=>$_POST['Mobile'],
			'User_Name'=>$_POST['Name'],
			'User_Profile'=>1
		);
		$Flag=$Flag&&$DB->Set("user",$Data,"where Users_ID='".$UsersID."' and User_ID=".$UserID);
		if($Flag){
		    $_SESSION[$UsersID."User_Mobile"] =  $_POST['Mobile'];
			$_SESSION[$UsersID."User_Name"] =  $_POST['Name'];
			$Data=array(
				'status'=>1,
				'msg'=>'操作成功'
			);
		}else{
			$Data=array(
				'status'=>0,
				'msg'=>'网络拥堵，请稍后再试！'
			);
		}
	}elseif($action=='address_edit_save'){
		
		$AddressID=empty($_POST['AddressID'])?0:$_POST['AddressID'];
	
		$Data=array(
			"Address_Name"=>$_POST['Name'],
			"Address_Mobile"=>$_POST["Mobile"],
			"Address_Province"=>empty($_POST['Province'])?"":$_POST['Province'],
			"Address_City"=>empty($_POST['City'])?"":$_POST['City'],
			"Address_Area"=>empty($_POST['Area'])?"":$_POST['Area'],
			"Address_Detailed"=>$_POST["Detailed"],
			"Address_Is_Default"=>!empty($_POST["default"])?$_POST["default"]:0,
		);
		
		if(empty($_POST['AddressID'])){
			if($Data['Address_Is_Default'] == 1){
				$condition = "where Users_ID='".$UsersID."' and User_ID='".$UserID."'";
				$DB->set("user_address",array('Address_Is_Default'=>0),$condition);
			}
			
			//增加
			$Data["Users_ID"]=$UsersID;
			$Data["User_ID"]=$UserID;
			$Flag=$Flag&&$DB->Add("user_address",$Data);
		}else{
			
			if($Data['Address_Is_Default'] == 1){
				$condition = "where Users_ID='".$UsersID."' and User_ID='".$UserID."' and Address_ID != '".$AddressID."'";
				$DB->set("user_address",array('Address_Is_Default'=>0),$condition);
			}
			
			//修改
			$Flag=$Flag&&$DB->Set("user_address",$Data,"where Users_ID='".$UsersID."' and User_ID='".$UserID."' and Address_ID='".$AddressID."'");
		}
		
		
		if($Flag){
			$url = '/api/'.$UsersID.'/user/my/address/';
			
			if(!empty($_SESSION[$UsersID."From_Checkout"])){
				$url = $_SESSION[$UsersID."HTTP_REFERER"];
				unset($_SESSION[$UsersID."From_Checkout"]);
			}
			
			if(!empty($_SESSION[$UsersID."Select_Model"])&&!empty($_POST['AddressID'])){
				$url = '/api/'.$UsersID.'/user/my/address/'.$_POST['AddressID'].'/';
				unset($_SESSION[$UsersID."Select_Model"]);
			}
			
			
			$Data=array(
				'status'=>1,
				'msg'=>'操作成功',
				'url'=>$url
			);
			
		}else{
			$Data=array(
				'status'=>0,
				'msg'=>'网络拥堵，请稍后再试！'
			);
		}
	}elseif($action=='get_message_contents'){
		$rsMessage=$DB->GetRs("user_message","Message_Description","where Users_ID='".$UsersID."' and Message_ID=".$_POST["MessageID"]);
		if($rsMessage){
			$rsRecord=$DB->GetRs("user_message_record","User_ID","where Users_ID='".$UsersID."' and User_ID=".$UserID." and Message_ID=".$_POST["MessageID"]);
			if(empty($rsRecord)){
				$Data=array(
					'Users_ID'=>$UsersID,
					'User_ID'=>$UserID,
					'Message_ID'=>$_POST["MessageID"],
					'Record_CreateTime'=>time()
				);
				$Flag=$Flag&&$DB->Add("user_message_record",$Data);
			}
			if($Flag){
				$Data=array(
					'status'=>1,
					'msg'=>$rsMessage['Message_Description']
				);
			}else{
				$Data=array(
					'status'=>0,
					'msg'=>'网络拥堵，请稍后再试！'
				);
			}
		}else{
			$Data=array(
				'status'=>0,
				'msg'=>'该信息不存在！'
			);
		}
	}
	
	if($Flag){
		mysql_query("commit");
	}else{
		mysql_query("roolback");
	}
}
echo json_encode(empty($Data)?array('status'=>0,'msg'=>'请勿非法操作！'):$Data,JSON_UNESCAPED_UNICODE);
?>