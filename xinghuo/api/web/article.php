<?php require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
if(isset($_GET["UsersID"]))
{
	$UsersID=$_GET["UsersID"];
}else
{
	echo '缺少必要的参数';
	exit;
}
require_once('../share.php');
if(isset($_GET["ArticleID"]))
{
	$ArticleID=$_GET["ArticleID"];
}else
{
	echo '缺少必要的参数';
	exit;
}
if(!strpos($_SERVER['REQUEST_URI'],"mp.weixin.qq.com")){
	header("location:?wxref=mp.weixin.qq.com");
}
$rsConfig=$DB->GetRs("web_config","*","where Users_ID='".$UsersID."'");
$rsArticle=$DB->GetRs("web_article","*","where Users_ID='".$UsersID."' and Article_ID=".$ArticleID);
$rsArticle["Article_Description"] = str_replace('&quot;','"',$rsArticle["Article_Description"]);
$rsArticle["Article_Description"] = str_replace("&quot;","'",$rsArticle["Article_Description"]);
$rsArticle["Article_Description"] = str_replace('&gt;','>',$rsArticle["Article_Description"]);
$rsArticle["Article_Description"] = str_replace('&lt;','<',$rsArticle["Article_Description"]);
if($rsArticle["Article_Link"]==1 && !empty($rsArticle["Article_LinkUrl"])){
	header("location:".$rsArticle["Article_LinkUrl"]);
}
//加入访问记录
$Data=array(
	"Users_ID"=>$UsersID,
	"S_Module"=>"web",
	"S_CreateTime"=>time()
);
$DB->Add("statistics",$Data);
//客服
$KfIco = '';
$kfConfig=$DB->GetRs("kf_config","*","where Users_ID='".$UsersID."' and KF_IsWeb=1");
$KfIco = empty($kfConfig["KF_Icon"]) ? '' : $kfConfig["KF_Icon"];
$header_title = $share_title = $rsArticle["Article_Title"];
$share_desc = $rsArticle["Article_BriefDescription"] ? str_replace(array("\r\n", "\r", "\n"), "", $rsArticle["Article_BriefDescription"]) : $rsArticle["Article_Title"];
//调用模版
include($rsConfig['Skin_ID']."/article.php");
?>