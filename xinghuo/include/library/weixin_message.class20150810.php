<?php
class weixin_message{
	var $db;
	var $usersid;
	var $userid;
	var $access_token;

	function __construct($DB,$usersid,$userid){
		$this->db = $DB;
		$this->usersid = $usersid;
		$this->userid = $userid;
		$this->access_token = "";
		require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/weixin_token.class.php');
		$weixin_token = new weixin_token($DB,$usersid);
		$this->access_token = $weixin_token->get_access_token();
	}
	
	private function sendmessage($openid,$contentStr){
		if($this->access_token){
			$postdata = array(
				"touser"=>$openid,
				"msgtype"=>"text",
				"text"=>array(
					"content"=>$contentStr
				)
			);
			$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this->access_token;
			$postdata = json_encode($postdata,JSON_UNESCAPED_UNICODE);
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			$res = curl_exec($ch);
			curl_close($ch);
			$message = $contentStr;
			$this->db->Add("weixin_log",array("message"=>$message.' _ '.$res.' _ '.$openid));
		}
    }
	
	private function getownerinfo($uid){
		if($uid>0){
			$u = $this->db->GetRs("user","Owner_Id,User_OpenID,User_NickName","where Users_ID='".$this->usersid."' and User_ID=".$uid);
			if($u){
				$data = array(
					"Owner_Id"=>$u["Owner_Id"],
					"User_OpenID"=>$u["User_OpenID"],
					"User_NickName"=>$u["User_NickName"]
				);
				$account = $this->db->GetRs("shop_distribute_account","Enable_Tixian","where Users_ID='".$this->usersid."' and User_ID=".$uid);
				if($account["Enable_Tixian"]==1){
					$data["boss"] = 1;
				}else{
					$data["boss"] = 0;
				}
				return $data;
			}else{
				return "";
			}
		}else{
			return "";
		}
	}
	
	private function get_bonus($orderid,$uid){
		$r = $this->db->GetRs("shop_distribute_account_record","sum(Record_Money) as bonus","where Users_ID='".$this->usersid."' and User_ID=".$uid." and Order_ID=".$orderid);
		return $r ? $r["bonus"] : 0;
	}
	
	public function sendmember(){
		$u0 = $this->db->GetRs("user","Owner_Id,User_NickName","where Users_ID='".$this->usersid."' and User_ID=".$this->userid);
		$name = $u0["User_NickName"];
		$u1 = $this->getownerinfo($u0["Owner_Id"]);
		if(is_array($u1)){
			$text = "您的一级会员".$name."关注了本公众号";			
			$this->sendmessage($u1["User_OpenID"],$text);
			$u2 = $this->getownerinfo($u1["Owner_Id"]);
			if(is_array($u2)){
				$text = "您的二级会员".$name."关注了本公众号";
				$this->sendmessage($u2["User_OpenID"],$text);
				$u3 = $this->getownerinfo($u2["Owner_Id"]);
				if(is_array($u3)){
					$text = "您的三级会员".$name."关注了本公众号";
					$this->sendmessage($u3["User_OpenID"],$text);
				}
			}
		}
	}
	
	public function sendorder($money,$orderid){
		
		$rsConfig = $this->db->GetRs("shop_config","Withdraw_Type,Withdraw_Limit","where Users_ID='".$this->usersid."'");
		$msg = '';
		if($rsConfig["Withdraw_Type"]==1){
			$url = 'http://'.$_SERVER['HTTP_HOST'].'/api/'.$this->usersid.'/shop/';
			$msg = '请<a href="'.$url.'"> 立即购买 </a>成为老板，获取您应得的佣金';
		}elseif($rsConfig["Withdraw_Type"]==2){
			$ptemp = $this->db->GetRs("shop_products","Products_ID","where Users_ID='".$this->usersid."' and Products_ID=".$rsConfig["Withdraw_Limit"]);
			$url = $ptemp ? 'http://'.$_SERVER['HTTP_HOST'].'/api/'.$this->usersid.'/shop/products_virtual/'.$ptemp["Products_ID"].'/' : 'http://'.$_SERVER['HTTP_HOST'].'/api/'.$this->usersid.'/shop/';
			$msg = '请<a href="'.$url.'"> 立即购买 </a>成为老板，获取您应得的佣金';
		}else{
			$msg = '当您的佣金达到 '.$rsConfig["Withdraw_Limit"].' 元时才能成为老板，获取您应得的佣金';
		}
		
		$u0 = $this->db->GetRs("user","Owner_Id,User_NickName","where Users_ID='".$this->usersid."' and User_ID=".$this->userid);
		$name = $u0["User_NickName"];
		$u1 = $this->getownerinfo($u0["Owner_Id"]);
		$b1 = $b2 = 1;
		if(is_array($u1)){
			$bonus = $this->get_bonus($orderid, $u0["Owner_Id"]);
			if($u1["boss"]==0){
				$b1 = 0;
				$text = '您推荐的一级会员'.$name.'下单成功，支付了'.$money.'元，您将获取佣金'.$bonus.'元，由于您还不是老板，'.$msg;
			}else{
				$b1 = 1;
				$text = "您推荐的一级会员".$name."下单成功，支付了".$money."元，您将获取佣金".$bonus."元";
			}
			$this->sendmessage($u1["User_OpenID"],$text);
			$u2 = $this->getownerinfo($u1["Owner_Id"]);
			if(is_array($u2)){
				$bonus = $this->get_bonus($orderid, $u1["Owner_Id"]);
				if($u2["boss"]==0){
					$b2 = 0;
					$text = '您推荐的二级会员'.$name.'下单成功，支付了'.$money.'元，您将获取佣金'.$bonus.'元，由于您还不是老板，'.$msg;
				}else{
					$b2 = 1;
					$text = "您推荐的二级会员".$name."下单成功，支付了".$money."元，您将获取佣金".$bonus."元";
				}
				$this->sendmessage($u2["User_OpenID"],$text);
				if($b1==0){
					$text = "由于您的一级会员".$u1["User_NickName"]."还不是老板，请抓紧时间联系下家购买成为老板赚取佣金";
					$this->sendmessage($u2["User_OpenID"],$text);
				}
				
				$u3 = $this->getownerinfo($u2["Owner_Id"]);
				if(is_array($u3)){
					$bonus = $this->get_bonus($orderid, $u2["Owner_Id"]);
					if($u3["boss"]==0){
						$text = '您推荐的三级会员'.$name.'下单成功，支付了'.$money.'元，您将获取佣金'.$bonus.'元，由于您还不是老板，'.$msg;
					}else{
						$text = "您推荐的三级会员".$name."下单成功，支付了".$money."元，您将获取佣金".$bonus."元";
					}
					$this->sendmessage($u3["User_OpenID"],$text);
					
					if($b2==0){
						$text = "由于您的一级会员".$u2["User_NickName"]."还不是老板，请抓紧时间联系下家购买成为老板赚取佣金";
						$this->sendmessage($u3["User_OpenID"],$text);
					}
				}
			}
		}
	}
	
	public function sendordernotice(){
		$diff = time() - 1200;
		$diff1 = time() - 600;
		$this->db->Get("user_order","User_ID,Order_ID","where Order_Status=1 and Message_Notice=0 and Order_CreateTime>=".$diff." and Order_CreateTime<=".$diff1);
		$lists = array();
		$users = array();
		while($r=$this->db->fetch_assoc()){
			$lists[] = $r;		
		}
		foreach($lists as $v){
			if(!in_array($v["User_ID"],$users)){
				$users[] = $v["User_ID"];
			}
			$this->db->Set("user_order",array("Message_Notice"=>1),"where Order_ID=".$v["Order_ID"]);	
		}
		
		foreach($users as $u){
			$usertemp = $this->db->GetRs("user","User_OpenID,Users_ID","where User_ID=".$u);
			if(!empty($usertemp["User_OpenID"])){
				$text = '您购买的商品还未付款，如需付款请<a href="http://'.$_SERVER["HTTP_HOST"].'/api/'.$usertemp["Users_ID"].'/shop/member/status/1/">点击付款</a>';
				$this->sendmessage($usertemp["User_OpenID"],$text);
			}
		}
	}
	
	private function get_user_openid(){
		$r = $this->db->GetRs("user","User_OpenID","where Users_ID='".$this->usersid."' and User_ID=".$this->userid);
		return $r ? $r["User_OpenID"] : '';
	}
	
	public function sendscorenotice($content){
		$openid = $this->get_user_openid();
		if($openid){
			$this->sendmessage($openid,$content);
		}
	}
}
?>
