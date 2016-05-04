<?php
include $_SERVER["DOCUMENT_ROOT"] . '/include/library/weixin_message.class.php';
include $_SERVER["DOCUMENT_ROOT"] . '/include/library/weixin_pay.class.php';
class spark {

	public $DB;
	private $orderId;
	private $orderPrice;
	public $level = 1;
	public $disData = array();
	public $UsersID;
	public $Owner_Id;
	public $payType = 0;
	public $isUpDis = 1;//升级是否返佣
	public $disInfo = array();//用户返佣信息 默认为空 升级使用
	function __construct($DB, $OrderID) {
		$this->orderId = $OrderID;
		$this->DB = $DB;
	}

	//开始
	public function start() {
		$rsOrder = $this->getOrderInfo();
		if($rsOrder === FALSE){
			$this->logs(__FUNCTION__, "订单不存在");
			return "订单不存在";
		}
		$rsUser = $this->getUserInfo($rsOrder['User_ID']);
		if($rsUser === FALSE){
			$this->logs(__FUNCTION__, "用户不存在");
			return "用户不存在";
		}
		$rsConfig = $this->getConfigInfo();
		
		while ($this->Owner_Id > 0) {
			if ($this->handleDis() == "0") {
				break;
			}
			$this->level++;
		}
		return $this->updateDis($this->disData);
	}

	//	处理分销金额和人员
	public function handleDis() {
		//获取父亲的信息
		$parent = $this->DB->GetRs("user", "User_ID, Owner_Id", "WHERE Users_ID='{$this->UsersID}' and User_ID='{$this->Owner_Id}'");
		if (empty($parent)) {
			return $this->Owner_Id = 0;
		}
		//获取父亲的产品包信息
		$parentSpark = $this->DB->GetR("spark_user AS a LEFT JOIN spark_package AS b ON a.packageId = b.id", "a.Users_ID, a.User_ID, a.disNum, b.rebate", "WHERE a.Users_ID='{$this->UsersID}' and a.User_ID='{$this->Owner_Id}'");
		if (empty($parentSpark)) {
			$this->logs(__FUNCTION__, "上级用户位尚未购买星火代理" . $this->Owner_Id);
			return $this->Owner_Id = $parent['Owner_Id'];
		}
		$parentSpark['rebate'] = json_decode($parentSpark['rebate'], TRUE);
		$parentSpark['disNum'] = json_decode($parentSpark['disNum'], TRUE);
		//判断等级完了没有
		if (!isset($parentSpark['rebate'][$this->level]['num']) || !isset($parentSpark['rebate'][$this->level]['money'])) {
			$this->logs(__FUNCTION__, "当前用户产品没有设置人数或金额" . $this->Owner_Id);
			return $this->Owner_Id = $parent['Owner_Id'];
		}
		if ($parentSpark['rebate'][$this->level]['num'] !== "0" &&  $parentSpark['disNum'][$this->level] >= $parentSpark['rebate'][$this->level]['num']){
			$this->logs(__FUNCTION__, "当前用户产品设置人数不足" . $this->Owner_Id);
			return $this->Owner_Id = $parent['Owner_Id'];
		}
		if ($parentSpark['rebate'][$this->level]['money'] >= $this->orderPrice) {
			$this->logs(__FUNCTION__, "当前用户产品设置金额不足返佣" . $this->Owner_Id);
			return $this->Owner_Id = $parent['Owner_Id'];
		}
		if(!$this->hasDis($parent['User_ID'])){
			$this->logs(__FUNCTION__, "当前用户已经被返佣，升级不再返佣" . $this->Owner_Id);
			return $this->Owner_Id = $parent['Owner_Id'];
		};
		$this->disData[$this->level] = array(
			"User_ID" => $parent['User_ID'],
			"Money" => $parentSpark['rebate'][$this->level]['money']
		);
		$this->updateSpark($parentSpark);
		$this->orderPrice = $this->orderPrice - $parentSpark['rebate'][$this->level]['money'];
		return $this->Owner_Id = $parent['Owner_Id'];
	}
	
	//查看用户是否被返佣
	private function hasDis($parentId) {
		if (!$this->isUpDis) {
			return TRUE;
		}
		foreach ($this->disInfo as $k => $v) {
			if (in_array($parentId, $v)) {
				return FALSE;
			}
		}
		return TRUE;
	}

	//	更新sperk 购买数量
	function updateSpark($parentSpark) {
		$disNum = $parentSpark['disNum'];
		if(empty($disNum)){
			$disNum = array();
		}
		$disNum[$this->level] = $disNum[$this->level] + 1;
		$Flag = $this->DB->Set("spark_user", array("disNum" => json_encode($disNum)), " WHERE Users_ID='{$parentSpark["Users_ID"]}' AND User_ID='{$parentSpark["User_ID"]}'");
	}

	//添加分销记录 增加余额
	function updateDis($data) {
		if(empty($data)){
			$this->logs(__FUNCTION__, "没有分销数据可以使用");
			return true;
		}
		$disInfo = $data + $this->disInfo;
		$FlagA = $this->DB->Set("spark_user", array("disInfo" => json_encode($disInfo),"status"=>"1"), " WHERE Users_ID='{$this->UsersID}' AND User_ID='{$this->User_ID}'");
		foreach ($data as $key => $value) {
			$logsData = array(
				"Users_ID" => $this->UsersID,
				"User_ID" => $value['User_ID'],
				"orderId" => $this->orderId,
				"buy_User_ID" => $this->User_ID,
				"money" => $value['Money'],
				"level" => $key,
				"createtime" => time()
			);
			$FlagB = $this->DB->Add("spark_logs",$logsData);
			$this->pay($value['User_ID'],$value['Money'],$key);
		}
		if($FlagA === FALSE){
			return FALSE;
		}else{
			return TRUE;
		}
	}
	
	//开始通知和支付
	private function pay($User_ID,$Money,$key){
		$contentStr = '您的'.$key.'级会员成功开通了产品包，恭喜您获得￥'.$Money.'元的收益<a href="http://' . $_SERVER["HTTP_HOST"] . '/api/' . $this->UsersID . '/shop/spark/my/">查看详情</a>';
		$weixin_message = new weixin_message($this->DB, $this->UsersID, $User_ID);
		$weixin_message->sendscorenotice($contentStr);
		return TRUE;
	}
	
	private function getConfigInfo(){
		$sparkConfig = $this->DB->GetRs("spark_config","*","WHERE Users_ID='{$this->UsersID}'");
		if(!empty($sparkConfig)){
			$this->payType = $sparkConfig['payType'];
			$this->isUpDis = $sparkConfig['isUpDis'];
		}else{
			$this->payType = 0;
			$this->isUpDis = 1;
		}
	}

	private function getOrderInfo() {
		$rsOrder = $this->DB->GetR("spark_order AS a LEFT JOIN spark_user AS b ON a.User_ID = b.User_ID", "*", "where a.orderId='{$this->orderId}'");
		if (empty($rsOrder)) {
			$this->logs(__FUNCTION__, "订单不存在");
			return FALSE;
		} else if ($rsOrder['payCode'] == "0") {
			$this->logs(__FUNCTION__, "订单尚未支付");
			return FALSE;
		} else {
			$this->orderPrice = $rsOrder['price'];
			$this->UsersID = $rsOrder['Users_ID'];
			$this->User_ID = $rsOrder['User_ID'];
			return $rsOrder;
		}
	}

	private function getUserInfo($UserID = "0") {
		$sparkUser = $this->DB->GetRs("spark_user", "*", "where Users_ID='" . $this->UsersID . "' and User_ID=" . $UserID);
		$rsUser = $this->DB->GetRs("user", "*", "where Users_ID='" . $this->UsersID . "' and User_ID=" . $UserID);
		if (empty($sparkUser) || empty($rsUser)) {
			$this->logs(__FUNCTION__, "用户不存在" . $UserID);
			return FALSE;
		} else {
			$this->Owner_Id = $rsUser['Owner_Id'];
			if(empty($sparkUser['disInfo'])){
				$this->disInfo = array();
			}else{
				if (!$this->isUpDis) {
					$this->logs(__FUNCTION__, "当前设置升级不返佣，谁也拿不到钱" . $UserID);
					return FALSE;
				}
				$this->disInfo = json_decode($sparkUser['disInfo'], TRUE);
			}
			return TRUE;
		}
	}

	private function logs($name, $msg) {
		$fp = fopen("spark.txt", "a+");
		fwrite($fp, $name . "：" . $this->orderId . $msg . "\r\n");
		fclose($fp);
	}

}
