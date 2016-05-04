<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/Framework/Conn.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/include/library/weixin_message.class.php');
class traffic {

	public $appkey;
	public $account;
	public $apiUrl = 'http://m.zgliuliang.com:8080/api.aspx?v=1.1';
	public $Users_ID;

	public function __construct($Users_ID) {
		global $DB;
		$this->Users_ID = $Users_ID;
		$sparkConfig=$DB->GetRs("spark_config","*","where Users_ID='{$Users_ID}'");
		$this->account = $sparkConfig['traAccount'];
		$this->appkey = $sparkConfig['traAppkey'];
	}

	/**
	 * 获取手机号信息
	 * @param  手机号
	 * @return array
	 */
	public function getMobileType($mobile = 0) {
		$ch = curl_init();
		$url = 'http://apis.baidu.com/apistore/mobilenumber/mobilenumber?phone=' . $mobile;
		$header = array(
			'apikey: 8528c1d14c270dfa371f8f8e5d688054',
		);
		// 添加apikey到header
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// 执行HTTP请求
		curl_setopt($ch, CURLOPT_URL, $url);
		$res = curl_exec($ch);
		$res = json_decode($res, TRUE);
		if ($res['errNum'] == '0') {
			return $this->serveType($res['retData']['supplier']);
		} else {
			$this->message("暂时无法确定手机号信息，请更换号码重试");
		}
	}

	/**
	 * 获取手机号服务商
	 * @param  服务商名称
	 * @return string
	 */
	private function serveType($type) {
		if (strpos($type, "移动") !== FALSE) {
			return 1;
		} else if (strpos($type, "联通") !== FALSE) {
			return 2;
		} else if (strpos($type, "电信") !== FALSE) {
			return 3;
		}
		$this->message("暂时无法确定手机号服务商，请更换号码重试");
	}

	/**
	 * 获取流量卡包
	 * @param  流量服务商
	 * @return array
	 */
	public function getPackage($type = 0) {
		$params = array(
			"account" => $this->account,
			"type" => $type
		);
		$sign = $this->getSign($params);
		$params["sign"] = $sign;
		$content = $this->juhecurl($this->apiUrl . "&action=getPackage", $params);
		$result = json_decode($content, TRUE);
		if ($result['Code'] == '0') {
			return $this->sortPackage(json_encode($result["Packages"]));
		} else {
			$this->message("请求失败，请重试或联系客服");
		}
	}

	private function sortPackage($Packages) {
		$Packages = str_replace(array("M", "G"), array("", "000"), $Packages);
		$Packages = json_decode($Packages, TRUE);
		foreach ($Packages as $key => $value) {
			if (strpos($value['Name'], "广州") !== FALSE) {
				unset($value);
				continue;
			}
			$tmp[$value['Name']] = $value;
			if ($value['Name'] / 1000 >= 1) {
				$tmp[$value['Name']]['Name'] = $value['Name'] / 1000 . "G";
			} else {
				$tmp[$value['Name']]['Name'] = $value['Name'] . "M";
			}
		}
		ksort($tmp);
		return array_values($tmp);
	}

	private function getSign($params) {
		//签名步骤一：按字典序排序参数
		ksort($params);
		$buff = "";
		foreach ($params as $k => $v) {
			$buff .= strtolower($k) . "=" . $v . "&";
		}
		$buff = trim($buff, "&");
		//签名步骤二：在string后加入KEY
		$string = $buff . "&key=" . $this->appkey;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtolower($string);
		return $result;
	}

	/**
	 * 请求接口返回内容
	 * @param  string $url [请求的URL地址]
	 * @param  string $params [请求的参数]
	 * @param  int $ipost [是否采用POST形式]
	 * @return  string
	 */
	private function juhecurl($url, $params = false, $ispost = 1) {
		$httpInfo = array();
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'dinghan');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if ($ispost) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_URL, $url);
		} else {
			if ($params) {
				curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
			} else {
				curl_setopt($ch, CURLOPT_URL, $url);
			}
		}
		$response = curl_exec($ch);
		if ($response === FALSE) {
			$this->message("请求失败，请重试或联系客服");
		}
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$httpInfo = array_merge($httpInfo, curl_getinfo($ch));
		curl_close($ch);
		return $response;
	}

	private function message($msg = "", $redirectUrl = "", $type = "error") {
		echo json_encode(array("msg" => $msg, "redirectUrl" => $redirectUrl, "type" => $type));
		exit;
	}

	/**
	 * 订单情况
	 * @param  订单号
	 * @return array 订单信息
	 */
	function getOrder($orderId) {
		global $DB;
		if (!empty($orderId)) {
			$order = $DB->GetR("spark_traffic_order", "*", " where orderId = '{$orderId}' AND payCode=1");
			$this->Users_ID = $order['Users_ID'];
			if (!empty($order)) {
				return $order;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * 用户三级情况
	 * @param  用户id
	 * @return array
	 */
	function getUserTree($userId) {
		global $DB;
		if (!empty($userId)) {
			$user = $DB->GetR("user", "*", " WHERE Users_ID = '{$this->Users_ID}' and User_ID ='{$userId}' ");
			if (!empty($user) && $user['Owner_Id']) {
				$user['one'] = $DB->GetR("user", "*", " WHERE Users_ID = '{$this->Users_ID}' AND User_ID = '{$user['Owner_Id']}'");
				if (!empty($user['one']) && $user['one']['Owner_Id']) {
					$user['two'] = $DB->GetR("user", "*", " WHERE Users_ID = '{$this->Users_ID}' AND User_ID = '{$user['one']['Owner_Id']}'");
					if (!empty($user['two']) && $user['two']['Owner_Id']) {
						$user['three'] = $DB->GetR("user", "*", " WHERE Users_ID = '{$this->Users_ID}' AND User_ID = '{$user['two']['Owner_Id']}'");
						return $user;
					} else {
						return $user;
					}
				} else {
					return $user;
				}
			} else {
				return $user;
			}
		} else {
			return false;
		}
	}

	/*
	 * 用户订单关联
	 * @param  订单号
	 * @return array
	 * 
	 *  */

	function getOrderList($orderId) {
		global $DB;
		$order = $this->getOrder($orderId);
		if (!empty($order)) {
			$tree = $this->getUserTree($order['User_ID']);
			if (!empty($tree)) {
				return $this->edit($tree, $order);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/*
	 * 数据
	 * 
	 * 
	 */

	function edit($tree, $order) {
		$arr = array();
		$users_id = $tree['Users_ID'];
		$traffic = $this->getTraffic($order);
		if (!empty($traffic)) {
			if (!empty($tree['one'])) {
				$tree['one']['money'] = round($traffic["priceX"] * $traffic['profit'] / 100 * $traffic['one'] / 100, 2);
				$this->renewData($tree['one']);
				$arr[1] = array(
					"User_ID" => $tree['one']['User_ID'],
					"money" => $tree['one']['money'],
				);
				$this->payInfo($arr[1]);
			}
			if (!empty($tree['two'])) {
				$tree['two']['money'] = round($traffic["priceX"] * $traffic['profit'] / 100 * $traffic['two'] / 100, 2);
				$this->renewData($tree['two']);
				$arr[2] = array(
					"User_ID" => $tree['two']['User_ID'],
					"money" => $tree['two']['money'],
				);
				$this->payInfo($arr[2]);
			}
			if (!empty($tree['three'])) {
				$tree['three']['money'] = round($traffic["priceX"] * $traffic['profit'] / 100 * $traffic['three'] / 100, 2);
				$this->renewData($tree['three']);
				$arr[3] = array(
					"User_ID" => $tree['three']['User_ID'],
					"money" => $tree['three']['money'],
				);
				$this->payInfo($arr[3]);
			}
			$this->updateTrafficDisinfo($order, $arr);
			return true;
		}
	}

	function payInfo($data) {
		global $DB;
		$contentStr = '您的下级会员成功购买了流量包，恭喜您获得￥' . $data['money'] . '元的收益<a href="http://' . $_SERVER["HTTP_HOST"] . '/api/' . $this->Users_ID . '/shop/distribute/">查看详情</a>';
		$weixin_message = new weixin_message($DB, $this->Users_ID, $data['User_ID']);
		$weixin_message->sendscorenotice($contentStr);
		return TRUE;
	}

	/*
	 * 获取流量包信息
	 */

	function getTraffic($order) {
		global $DB;
		return $DB->GetR("spark_traffic", "*", " where Users_ID =  '{$this->Users_ID}' and id='{$order['trafficId']}'");
	}

	/*
	 * 更新流量包订单
	 */

	function updateTrafficDisinfo($order, $arr) {
		global $DB;
		return $DB->Set("spark_traffic_order", array("disinfo" => json_encode($arr)), " where orderId = '{$order['orderId']}'");
	}

	/*
	 * 更新数据
	 * @param  data
	 * @return boolen
	 *  */

	function renewData($data) {
		global $DB;
		if (!empty($data) && !empty($data['User_ID']) && !empty($data['money'])) {
			$user = $DB->GetR("shop_distribute_account", "*", " where User_ID = '{$data['User_ID']}'");
			$re = $DB->Set("shop_distribute_account", array("balance" => $user["balance"] + $data['money']), " where User_ID = '{$data['User_ID']}' ");
			if ($re !== false) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function sendTraffic($orderId){
		global $DB;
		$rsOrder=$DB->GetRs("spark_traffic_order","*","WHERE orderId='{$orderId}'");
		$params = array(
			"Account" => $this->account,
			"Mobile"=>$rsOrder['mobile'],
			"Package"=>$rsOrder['package']
		);
		$sign = $this->getSign($params);
		$params["sign"] = $sign;
		$params['Range'] = 0;
		$params['OutTradeNo'] = $orderId;
		$content = $this->juhecurl($this->apiUrl . "&action=charge", $params);
		$result = json_decode($content, TRUE);
		if ($result['Code'] == '0') {
			$flag = $DB->Set("spark_traffic_order",array("traCode"=>1,"traTaskID"=>$result['TaskID']),"WHERE orderId='{$orderId}'");
			$contentStr = '您的流量包已经购买成功请耐心等待2到5分钟，如果为到账请联系客服核对</a>';
			$weixin_message = new weixin_message($DB, $this->Users_ID, $rsOrder['User_ID']);
			$weixin_message->sendscorenotice($contentStr);
			return TRUE;
		} else {
			$this->message("请求失败，请重试或联系客服");
		}
	}

}
