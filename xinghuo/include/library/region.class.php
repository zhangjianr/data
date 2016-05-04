<?php
class region{
	var $db;
	var $usersid;
	var $module;
	var $table;

	function __construct($DB,$UsersID){
		$this->db = $DB;
		$this->usersid = $UsersID;
		$this->table = 'area_region';
	}
	
	public function get_region($condition){
		$region = array();
		$this->db->Get($this->table,'*',$condition);
		while($r = $this->db->fetch_assoc()){
			if($r["Region_ParentID"]==0){
				if(empty($region[$r["Area_ID"]][$r["Region_ID"]]["Region_ID"])){
					$region[$r["Area_ID"]][$r["Region_ID"]] = $r;
				}
			}else{
				$region[$r["Area_ID"]][$r["Region_ParentID"]]["child"][] = $r;
			}
		}
		return $region;
	}
	
	public function get_areainfo($field,$value){
		$r = $this->db->GetRs("area","*","where `".$field."`='".$value."'");
		return $r ? $r : false;
	}
	
	public function get_areaparent($areaid){
		$data = array(
			"province"=>0
		);
		$r = $this->get_areainfo('area_id',$areaid);
		if($r["area_deep"]==1){
			$data["province"] = $areaid;
		}elseif($r["area_deep"]==2){
			$data["city"] = $areaid;
			$data["province"] = $r["area_parent_id"];
		}elseif($r["area_deep"]==3){
			$parent = $this->get_areainfo('area_id',$r["area_parent_id"]);
			$data["area"] = $areaid;
			$data["city"] = $r["area_parent_id"];
			$data["province"] = $parent["area_parent_id"];
		}
		return $data;
	}
	
	public function get_regionids($regionid){
		$r = $this->db->GetRs("area_region","*","where Users_ID='".$this->usersid."' and Region_ID=".$regionid);
		$data = array(0,0);
		if($r){
			$data = array($r["Region_ID"],$r["Region_ParentID"]);
		}
		return $data;
	}
}
?>
