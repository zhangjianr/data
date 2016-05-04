<?php
if(empty($_SESSION[$UsersID."OpenID"])){
	if(!empty($_SESSION[$UsersID."User_ID"])){
		$s = $DB->GetRs("user","User_OpenID","where Users_ID='".$UsersID."' and User_ID=".$_SESSION[$UsersID."User_ID"]);
		if($s){
			if($s["User_OpenID"]){
				$_SESSION[$UsersID."OpenID"] = $s["User_OpenID"];
			}
		}
	}
}

if(!empty($_SESSION[$UsersID."OpenID"])){
	$_SESSION[$UsersID."substribe"] = get_substribe($DB,$_SESSION[$UsersID."OpenID"]);
}else{
	$_SESSION[$UsersID."substribe"] = 0;
}

if(empty($_SESSION[$UsersID."substribe"])){
	$ss = $DB->GetRs("shop_config","Substribe,SubstribeUrl","where Users_ID='".$UsersID."'");
	if(!empty($owner)){
		substribe_html($DB,$owner,$ss["Substribe"],$ss["SubstribeUrl"]);
	}
}else{
	if($_SESSION[$UsersID."substribe"]==0){
		if(!empty($owner)){
			substribe_html($DB,$owner,$ss["Substribe"],$ss["SubstribeUrl"]);
		}
	}
}

function substribe_html($DB,$owner,$enabled,$url){
	if($enabled==1){
		if($owner["id"]==0){
			echo '<div style="clear:both; position:fixed; top:0px; left:0px; height:42px; font-size:1px; width:100%; background:#000; filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5; z-index:500"></div><div style="width:100%; display:block; position:fixed; top:0px; height:42px; left:0px; z-index:999999; color:#FFF" /><p style="background:#4878C6; border-radius:8px; height:30px; line-height:28px; font-size:14px; text-align:center; width:80px; margin:6px 8px 0px 0px; padding:0px; float:right">'.($url ? '<a href="'.$url.'" style="display:block; width:100%; height:100%; color:#FFF">' : '').'关注我们'.($url ? '</a>' : '').'</p><img src="" style="float:left; width:42px; height:42px; display:block;" /><span style="display:block; font-size:14px; color:#FFF; height:42px; line-height:42px; float:left; margin-left:8px">您还未关注公众号</span></div>';
		}else{
			$owner = $DB->GetRs("user","User_NickName,User_HeadImg","where User_ID=".$owner["id"]);
			echo '<div style="clear:both; position:fixed; top:0px; left:0px; height:42px; font-size:1px; width:100%; background:#000; filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5; z-index:500"></div><div style="width:100%; display:block; position:fixed; top:0px; height:42px; left:0px; z-index:999999; color:#FFF" /><p style="background:#4878C6; border-radius:8px; height:30px; line-height:28px; font-size:14px; text-align:center; width:80px; margin:6px 8px 0px 0px; padding:0px; float:right">'.($url ? '<a href="'.$url.'" style="display:block; width:100%; height:100%; color:#FFF">' : '').'关注我们'.($url ? '</a>' : '').'</p><img src="'.$owner['User_HeadImg'].'" style="float:left; width:42px; height:42px; display:block;" /><span style="display:block; font-size:14px; color:#FFF; height:42px; line-height:18px; float:left; margin-left:8px">'.$owner['User_NickName'].'<br />推荐</span></div>';
		}
		
	}else{
		echo '';
	}
}

function substribe_curl_get($url){
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $res = curl_exec($ch);
	curl_close($ch);
	$data = json_decode($res,true);
	return $data;
}

function get_substribe($DB,$openid){
	$flag = 0;
	$r = $DB->GetRs("http_raw_post_data","HTTP_RAW_POST_DATA","where HTTP_RAW_POST_DATA like '%".$openid."%' ORDER BY ID desc");
	if(!$r){
		$flag = 0;
	}else{
		if(strpos($r['HTTP_RAW_POST_DATA'],"unsubscribe")>-1){
			$flag = 0;
		}else{
			$flag = 1;
		}
	}
	return $flag;
}
?>