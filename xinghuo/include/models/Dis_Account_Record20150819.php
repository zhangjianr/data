<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 分销账户记录Model
 */
use Illuminate\Database\Eloquent\SoftDeletes;
class Dis_Account_Record extends Illuminate\Database\Eloquent\Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = ['Record_Status'];
	protected  $primaryKey = "Record_ID";
	protected  $table = "shop_distribute_account_record";
	public $timestamps = false;
	
    //一个佣金获得记录属于一个分销记录
	public  function DisRecord()
    {
       return $this->belongsTo('Dis_Record','Ds_Record_ID');
	}
	
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