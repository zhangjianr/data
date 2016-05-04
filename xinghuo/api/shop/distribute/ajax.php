<?php require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/url.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/helper/distribute.php');

$base_url = base_url();

if(isset($_GET["UsersID"])){
	$UsersID=$_GET["UsersID"];
}else{
	echo 'error';
	exit;
}


$action=empty($_REQUEST["action"])?"":$_REQUEST["action"];

//申请成为分销商
if($action == 'join'){

	$UserID = $_SESSION[$UsersID.'User_ID'];
	$Real_Name = $_POST['Real_Name'];
	$User_Mobile = $_POST['User_Mobile'];
	
	$user = $DB->GetRs("user","*","where Users_ID='".$UsersID."' and User_ID=".$UserID);
	$owner["id"] = $user["Owner_Id"];
	create_distribute_acccount($DB,$UsersID,$UserID,$Real_Name,$owner,$User_Mobile);

	$Flag = $DB->set('user',array('Is_Distribute'=>1),"where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."'");
	
	if($Flag){
		$response = array('status'=>1);
	}else{
		$response = array('status'=>0);
	}
	
	echo json_encode($response,JSON_UNESCAPED_UNICODE);
	
}elseif($action == 'edit_card'){
	//修改分销账户银行卡号
	
	$data = array("Bank_Card"=>$_POST['bank_card'],
				  "Bank_Name"=>$_POST['bank_name']);

	$condition = "where Users_ID='".$UsersID."' and User_ID= '".$_SESSION[$UsersID.'User_ID']."'";

	$Flag = $DB->Set('shop_distribute_account',$data,$condition);

	if($Flag){
		$response = array('status'=>1);
	}else{
		$response = array('status'=>0);
	}
	
	echo json_encode($response,JSON_UNESCAPED_UNICODE);
}elseif($action == 'withdraw_appy'){
	//查看是否有足够的余额用于提现
	$condition = "where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."'";
	$dsAccount = $DB->getRs('shop_distribute_account',"balance",$condition);
	$userInfo = $DB->getRs('user',"User_OpenID",$condition);
	
	if($_POST['money'] >$dsAccount['balance']){
		$response = array('status'=>0,'msg'=>'余额不足');
	}else{
		$money  = $_POST['money'];
	
		//获取用户提现方式
		$condition = "where User_Method_ID=".$_POST['User_Method_ID']." and Users_ID='".$UsersID."'";
		$UserMethod = $DB->getRs('shop_user_withdraw_methods',"*",$condition);		
		$Account_Info = $UserMethod['Method_Name'].' '.$UserMethod['Account_Name'].' '.$UserMethod['Account_Val'].' '.$UserMethod['Bank_Position'];
		
		mysql_query("BEGIN");//开始事务
	
		$data = array(
			"Users_ID"=>$UsersID,
			"User_ID"=>$_SESSION[$UsersID.'User_ID'],
			"Account_Info"=>$Account_Info,
			"realname"=>$UserMethod['Account_Name'],
			"openid"=>$userInfo['User_OpenID'],
			"Method_Type"=>$UserMethod['Method_Type'],
			"Record_Sn"=>build_withdraw_sn(),
			"Record_Money"=>$money,
			"Record_CreateTime"=>time(),
			"Record_Type"=>1,
			"Record_Status"=>0,
		);
	
		$condition = "where User_ID=".$_SESSION[$UsersID."User_ID"]." and Users_ID='".$UsersID."'";
	
		$flag1 = $DB->Set('shop_distribute_account',"balance=balance-$money",$condition);
		$Flag2= $DB->add('shop_distribute_account_record',$data);
		$RecordID = $DB->insert_id();
		if($flag1 === false){
			mysql_query("ROLLBACK");//判断执行失败回滚
			$response = array('msg'=>'提交失败，如发现账户余额已扣，请联系商家客服核对！','status'=>0);
			echo json_encode($response,JSON_UNESCAPED_UNICODE);
			exit;
		}
		if($Flag2 === false){
			mysql_query("ROLLBACK");//判断执行失败回滚
			$DB->Set('shop_distribute_account',"balance=balance+$money",$condition);
			$response = array('msg'=>'提交失败，请联系商家客服！','status'=>0);
			echo json_encode($response,JSON_UNESCAPED_UNICODE);
			exit;
		}
		
		//判断是否开启微信企业自动付款
		$isAutoPay = false;
		$methodCondition = "where Users_ID='".$UsersID."' and Method_Type= 'red'";
		$rsMethod = $DB->getRs('shop_withdraw_method','*',$methodCondition);
		if(!empty($rsMethod) && $rsMethod['isAuto'] == "1"){
			$isAutoPay = true;
		}else{
			mysql_query("COMMIT");//执行事务
			$response = array('status'=>1);
			echo json_encode($response,JSON_UNESCAPED_UNICODE);
			exit;
		}
		
		if($isAutoPay && !empty($data['realname']) && !empty($data['openid']) && $data['Method_Type'] == "red" && !empty($RecordID)){
			require_once($_SERVER["DOCUMENT_ROOT"] . '/include/library/weixin_pay.class.php');
			$pay = new weixin_pay($data);
			$payResult = false;
			$payResult = $pay->startPay();
			if($payResult === true) {
				$updata = array("Record_Status"=>1);
				$redCondition = "where Users_ID='".$UsersID."' and Record_ID='".$RecordID."'";
				$FlagUpdata = $DB->Set("shop_distribute_account_record", $updata, $redCondition);
				mysql_query("COMMIT");//执行事务
				$response = array('status'=>1);
			}else{
				mysql_query("ROLLBACK");//判断执行失败回滚
				$response = array('msg'=>'微信企业付款失败，如发现账户余额已扣，请联系商家客服核对【'.$payResult['return_msg'].'】！','status'=>0);
			}
		}else{
			mysql_query("ROLLBACK");//判断执行失败回滚
			$response = array('msg'=>'微信企业付款失败，缺少必要参数，如发现账户余额已扣，请联系商家客服核对！','status'=>0);
		}
	}
	echo json_encode($response,JSON_UNESCAPED_UNICODE);
	exit;
	
}elseif($action == 'check_exist'){
	$field = $_GET['field'];
	if($field == 'Real_Name'){
		$value = $_GET['real_name'];
	}elseif($field == 'ID_Card'){
		$value = $_GET['idcard'];
	}elseif($field == 'Email'){
		$value = $_GET['email'];
	}
	$condition = "where User_ID=".$_SESSION[$UsersID."User_ID"]." and ".$field."='".$value."'";
	
	$rsUser = $DB->getRs("shop_distribute_account","*",$condition);
	
	if(!empty($rsUser)){
		echo 'false';
	}else{
		echo 'true';
	}
}elseif($action == "add_user_withdraw_method"){

	
	$data = array();
	$data['Users_ID'] = $UsersID;
	$data['User_ID'] = $_SESSION[$UsersID.'User_ID'];
	$data['Method_Name'] = $_POST['Method_Name'];
	$data['Method_Type'] = $_POST['Method_Type'];
	$data['Account_Name'] = $_POST['Account_Name'];
    $data['Account_Val'] = !empty($_POST['Account_Val'])?$_POST['Account_Val']:'';
	$data['Bank_Position'] = !empty($_POST['Bank_Position'])?$_POST['Bank_Position']:'';
	$data['Method_CreateTime'] = time();
	$data['Method_Status'] = 1;		
	
	$Flag = $DB->add('shop_user_withdraw_methods',$data);
	
	if($Flag){
		$response = array('status'=>1,'msg'=>'添加新的提现方式成功');
	}else{
		$response = array('status'=>0,'msg'=>'添加新的提现方式失败');
	}
	
	echo json_encode($response,JSON_UNESCAPED_UNICODE);
	
}elseif($action == "delete_user_withdraw_method"){
	$method_id = $_POST['method_id'];
	
	$condition = "User_Method_ID=".$method_id." and Users_ID='".$UsersID."'";
	$Flag = $DB->Del('shop_user_withdraw_methods',$condition);
	
	if($Flag){
		$response = array('status'=>1);
	}else{
		$response = array('status'=>0);
	}

	echo json_encode($response,JSON_UNESCAPED_UNICODE);

}elseif($action == 'store_poster'){
	
 	$img = $_POST['dataUrl'];
 	$owner_id = $_POST['owner_id'];
	$file_path = '/data/poster/'.$UsersID.$owner_id.'.png';
	$web_path = '/data/poster/'.$UsersID.$owner_id.'.png';
	 
	$Flag = generate_postere($img,$UsersID,$owner_id);
	
	if($Flag){
		
		$condition = "Where Users_ID = '".$UsersID."' and User_ID=".$owner_id;	
		$DB->Set('shop_distribute_account',array('Is_Regeposter'=>0),$condition);
		$response = array('status'=>1,'poster_path'=>$web_path);
		
	}else{
		$response = array('status'=>0,'msg'=>'Unable to save the file.');
	}
	
	echo json_encode($response,JSON_UNESCAPED_UNICODE);
	
}elseif($action == 'get_ex_bonus'){
   //检测此账号是否有额外奖金
   $condition = "where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID.'User_ID'];
   $rsDistirbuteAccount = $DB->getRs('shop_distribute_account','Ex_Bonus,balance',$condition);
 
   if($rsDistirbuteAccount['Ex_Bonus'] == 0){
   		$response = array('status'=>0,'msg'=>'分销额外奖金为零');
   }else{
   		 		
   		$data = array('Ex_Bonus'=>0,
   					  'balance'=>$rsDistirbuteAccount['balance']+$rsDistirbuteAccount['Ex_Bonus']);
   					  
   		$Flag = $DB->Set('shop_distribute_account',$data,$condition);
   		$response = array('status'=>1,'msg'=>'获取奖金成功');
   }

   echo json_encode($response,JSON_UNESCAPED_UNICODE);

}





