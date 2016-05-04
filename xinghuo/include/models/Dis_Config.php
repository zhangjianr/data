<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 分销账号爵位model
 */

class Dis_Config extends Illuminate\Database\Eloquent\Model
{
	
	protected  $primaryKey = "id";
	protected  $table = "shop_distribute_config";
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
	
	
	public static function get_dis_pro_title($UsersID,$type = 'front'){
		$dis_config = static::where('Users_ID',$UsersID)->first(array('Pro_Title_Level'));
		
		$pro_titles = false;
		if (!empty($dis_config)) {
			
			$pro_titles = json_decode($dis_config->Pro_Title_Level, TRUE);
			if(!empty($pro_titles)){
			if($type == 'front'){
					foreach($pro_titles as $key=>$item){
						if(strlen($item['Name']) == 0){
							unset($pro_titles[$key]);
						}
					}
					ksort($pro_titles);
				}
		
			}
		
		}
	

			return $pro_titles;
		
	}
	
	//无需日期转换
	public function getDates()
	{
		return array();
	}
	
	
}