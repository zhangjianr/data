<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 分销账号爵位model
 */

class Channel_Config extends Illuminate\Database\Eloquent\Model
{
	
	protected  $primaryKey = "id";
	protected  $table = "shop_channel_config";
	public $timestamps = false;

	// 多where
	public function scopeMultiwhere($query, $arr)
	{
		if (!is_array($arr)) {
			return $query;
		}
	
		foreach ($arr as $key => $value) {
			$query = $query->where($key, $value);
		}
		return $query;
	}

	/**
	 * @param $UsersID
	 * @param string $type
	 * @return bool|mixed
     */
	public static function get_dis_channel_type($UsersID, $type = 'front'){
		$dis_config = static::where('Users_ID',$UsersID)->first(array('Channel_Type'));
		
		$channel_types = false;
		if (!empty($dis_config)) {
			
			$channel_types = json_decode($dis_config->Channel_Type, TRUE);
			if(!empty($channel_types)){
			if($type == 'front'){
					foreach($channel_types as $key=>$item){
						if(strlen($item['Name']) == 0){
							unset($channel_types[$key]);
						}
					}
					ksort($channel_types);
				}
		
			}
		
		}


			return $channel_types;

	}


	/**
	 * @param $UsersID
	 * @return mixed
     */
	public static function get_dis_channel_Depth($UsersID){
		$dis_config = static::where('Users_ID',$UsersID)->first(array('Depth'));

		return $dis_config->Depth;

	}
	
	//无需日期转换
	public function getDates()
	{
		return array();
	}
	
	
}