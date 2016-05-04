<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 一级合伙人Modeld
 */

class Dis_Agent_One extends Illuminate\Database\Eloquent\Model
{	
	protected  $primaryKey = "id";
	protected  $table = "shop_dis_agent_one";
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
	
	//无需日期转换
	public function getDates()
	{
		return array();
	}
	
	
}