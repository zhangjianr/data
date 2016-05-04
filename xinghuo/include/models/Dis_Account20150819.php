<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 分销账户Model
 */
use Illuminate\Database\Eloquent\SoftDeletes;
class Dis_Account extends Illuminate\Database\Eloquent\Model {
	
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = ['Users_ID','User_Name','Account_ID','invite_id','balance','Total_Income','Enable_Agent','User_ID','Account_CreateTime','Shop_Name','Shop_Logo',
							'status','Ex_Bonus','Is_Audit','Up_Group_Num','Group_Num','Professional_Title','last_award_income'];
	                    
	protected $primaryKey = "Account_ID";
	protected $table = "shop_distribute_account";
	public $timestamps = false;

	//一个分销账号属于一个用户
	public function user() {
		return $this->belongsTo('User', 'User_ID', 'User_ID');
	}

	//获取此分销商的邀请人
	public function inviter(){
		return $this->belongsTo('User', 'invite_id', 'User_ID');
	}

	//一个分销账户拥有多个代理地区
	public function disAreaAgent(){
		return $this->hasMany('Dis_Agent_Area','Account_ID','Account_ID');
	}
	
	/*一个分销账号拥有多个分销记录*/
	public function disRecord() {
		return $this->hasMany('Dis_Record', 'Owner_ID','User_ID');
	}

	
	/*一个分销账号拥有多个发钱记录*/
	public function disAccountRecord() {
		return $this->hasManyThrough( 'Dis_Account_Record','Dis_Record','Owner_ID','Record_ID');
	}
	
	/*一个分销账号拥有多个得钱记录*/
	public function disAccountPayRecord(){
		return $this->hasMany( 'Dis_Account_Record','User_ID','User_ID');
	}


	//无需日期转换
	public function getDates() {
		return array();
	}

	// 多where
	public function scopeMultiwhere($query, $arr) {
		if (!is_array($arr)) {
			return $query;
		}
	
		foreach ($arr as $key => $value) {
			$query = $query->where($key, $value);
		}
		return $query;
	}
}