<?php
//平台工具类
if ( ! function_exists('base_url'))
{
	/**
	 * 获取某用户下属分销账号
	 * @param  String $uri        uri参数
	 * @return String $base_url  本站基地址
	 */
	function base_url($uri = ''){
		$base_url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$uri;
		return $base_url;
	}
	
}

if ( ! function_exists('shop_url'))
{
	/**
	 * 获取某用户下属分销账号
	 * @param  String $uri        uri参数
	 * @return String $base_url  本站基地址
	 */
	function shop_url($uri = ''){
		
		$UsersID = $_GET['UsersID'];
		return base_url().'api/'.$UsersID.'/shop/'.$uri;
	}
	
}


if ( ! function_exists('shop_config'))
{
	/**
	 * 获取本店配置
	 * @param  String $UsersID   本店唯一标示
	 * @param  Array $fields    指定的字段
	 * @return Array $shop_config   返回结果
	 */
	function shop_config($UsersID = '',$fields = array()){
		
		$builder = Shop_Config::where('Users_ID',$UsersID);
		if(count($fields) >0 ){
			$shop_config = $builder->first($fields)
			                       ->toArray();	
		}else{
			$shop_config = $builder->first()
			                       ->toArray();	
		}			
		
		return !empty($shop_config)?$shop_config:false;
	}
}

if ( ! function_exists('shop_user_config'))
{
	/**
	 * 获取本店针对用户的配置
	 * @param  String $UsersID   本店唯一标示
	 * @param  Array $fields    指定的字段
	 * @return Array $shop_user_config   返回结果
	 */
	function shop_user_config($UsersID = '',$fields = array()){
		
		$builder = User_Config::where('Users_ID',$UsersID);
		if(count($fields) >0 ){
			$shop_user_config = $builder->first($fields)
			                       ->toArray();	
		}else{
			$shop_user_config = $builder->first()
			                       ->toArray();	
		}			
		
		return !empty($shop_user_config)?$shop_user_config:false;
	}
}

if ( ! function_exists('round_pad_zero'))
{
	/**
	* 浮点数四舍五入补零函数
	* 
	* @param float $num
	*        	待处理的浮点数
	* @param int $precision
	*        	小数点后需要保留的位数
	* @return float $result 处理后的浮点数
	*/
	function round_pad_zero($num, $precision) {
		if ($precision < 1) {  
				return round($num, $precision);  
			}  
		
			$r_num = round($num, $precision);  
			$num_arr = explode('.', "$r_num");  
			if (count($num_arr) == 1) {  
				return "$r_num" . '.' . str_repeat('0', $precision);  
			}  
			$point_str = "$num_arr[1]";  
			if (strlen($point_str) < $precision) {  
				$point_str = str_pad($point_str, $precision, '0');  
			}  
			return $num_arr[0] . '.' . $point_str;  
	}  
}


if(!function_exists('write_file')){
	
	 /**
	 * Write File
	 *
	 * Writes data to the file specified in the path.
	 * Creates a new file if non-existent.
	 *
	 * @param	string	$path	File path
	 * @param	string	$data	Data to write
	 * @param	string	$mode	fopen() mode (default: 'wb')
	 * @return	bool
	 */
	function write_file($path, $data, $mode = 'wb')
	{
		if ( ! $fp = @fopen($path, $mode))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);

		for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, substr($data, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return is_int($result);
	}
}


if ( ! function_exists('read_file'))
{
	/**
	* Read File
	*
	* Opens the file specfied in the path and returns it as a string.
	*
	* @access	public
	* @param	string	path to file
	* @return	string
	*/	
	function read_file($file)
	{
		if ( ! file_exists($file))
		{
			return FALSE;
		}

		if (function_exists('file_get_contents'))
		{
			return file_get_contents($file);
		}

		if ( ! $fp = @fopen($file, FOPEN_READ))
		{
			return FALSE;
		}

		flock($fp, LOCK_SH);

		$data = '';
		if (filesize($file) > 0)
		{
			$data =& fread($fp, filesize($file));
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return $data;
	}
}


if ( ! function_exists('build_withdraw_sn'))
{	
	/**
	* 得到提现流水号
	* @return  string
	*/
	function build_withdraw_sn() {
		/* 选择一个随机的方案 */
		mt_srand((double) microtime() * 1000000);

		return 'WD' . date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
	}

}
	
if ( ! function_exists('generateUpdateBatchQuery'))
{	
	/**
	 *生成mysql批量更新语句
	 */
	 function generateUpdateBatchQuery($tableName = "", $multipleData = array()){

		if( $tableName && !empty($multipleData) ) {

			// column or fields to update
			$updateColumn = array_keys($multipleData[0]);
			$referenceColumn = $updateColumn[0]; //e.g id
			unset($updateColumn[0]);
			$whereIn = "";

			$q = "UPDATE ".$tableName." SET "; 
			foreach ( $updateColumn as $uColumn ) {
				$q .=  $uColumn." = CASE ";

				foreach( $multipleData as $data ) {
					$q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN '".$data[$uColumn]."' ";
				}
				$q .= "ELSE ".$uColumn." END, ";
			}
			foreach( $multipleData as $data ) {
				$whereIn .= "'".$data[$referenceColumn]."', ";
			}
			$q = rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";

			// Update  
			return $q;

		} else {
			return false;
		}
	}

}	
	
if ( ! function_exists('handle_product_list'))
{	
	/*处理产品列表*/
	function handle_product_list($list){
	
		$result = array();

		foreach($list as $key=>$item){
			$JSON = json_decode($item['Products_JSON'],TRUE);
			$product = $item;
			if(isset($JSON["ImgPath"])){
				$product['ImgPath'] = $JSON["ImgPath"][0];
			}else{
				$product['ImgPath'] =  'static/api/shop/skin/default/nopic.jpg';
			}
		
			$result[$product['Products_ID']] = $product;
		}
		return $result;
	}
}


if( !function_exists('FetchRepeatMemberInArray')){
	
	/*获取数组中的重复元素*/
	function FetchRepeatMemberInArray($array) {
		// 获取去掉重复数据的数组
		$unique_arr = array_unique ( $array );
		// 获取重复数据的数组
		$repeat_arr = array_diff_assoc ( $array, $unique_arr );
		return $repeat_arr;
	} 
}


if(!function_exists('sql_diff')){
	/**
	 *新老数据比对，确定哪个需要新增，哪个需要删除，哪个需要更新
	 *@param $new 新数据
	 *@param $old 老数据
	 *
	 */
	 function sql_diff($new,$old){
	 		$need_update = array_intersect($new,$old);
			$need_add =  array_diff($new,$old);
			$need_del =   array_diff($old,$new);
		
		    $res = array(
			    'need_update'=>$need_update,
				'need_add'=>$need_add,
				'need_del'=>$need_del
				);
				
			return $res;	

	 }
}


if(!function_exists('get_dropdown_list')){
	//生成dropdown数组
	function get_dropdown_list($data,$id_field,$value_field = ''){
		$drop_down = array();
	
		foreach($data as $key=>$item){
			if(strlen($value_field) > 0 ){
				$drop_down[$item[$id_field]] = $item[$value_field];
			}else{
				$drop_down[$item[$id_field]] = $item;
			}
		}
	
		return $drop_down;
	}
	
}

if(!function_exists('sdate')){
	/*
	 *return short format date,not incluing hour,minutes,seconds
	 *
	 */
	function sdate($time = '')
	{
		if (strlen($time) == 0) {
			$time = time();
		}
		if (is_string($time)) {
			$time = intval($time);
		}
		return date('Y/m/d', $time);
	}
}

if(!function_exists('ldate')){
	/*
	 *return short format date,not incluing hour,minutes,seconds
	 *
	 */
	function ldate($time = '')
	{
		if (strlen($time) == 0) {
			$time = time();
		}
		if (is_string($time)) {
			$time = intval($time);
		}
		return date('Y/m/d H:i:s', $time);
	}
}