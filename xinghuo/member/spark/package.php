<?php
define("SYS_ACCESS", TRUE);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
if(empty($_SESSION["Users_Account"])){
	header("location:/member/login.php");
}

$op = !empty($_GET['op']) ? $_GET['op'] : "display";

if($op == "display"){
	$condition = "where Users_ID='{$_SESSION["Users_ID"]}'";
	if (isset($_GET['search'])) {
		if ($_GET['Keyword']) {
			$condition .= " and title like '%" . $_GET['Keyword'] . "%'";
		}
	}
	$condition .= " order by id desc";
	include 'html/package.php';
}else if($op == "post"){
	if(!empty($_GPC['id'])){
		$id = intval($_GPC['id']);
		$item = $DB->GetRs("spark_package", "*", " WHERE Users_ID='{$_SESSION["Users_ID"]}' AND id = '{$id}'");
	}
	if(isset($_POST['submit'])){
		$rebate = array();
		$rebate = json_encode($_GPC['rebate']);

		$data = array(
			"Users_ID"	=> $_SESSION['Users_ID'],
			"price"		=> $_GPC["price"],
			"levelName" => $_GPC["levelName"],
			"rebate"	=> $rebate,
			"status"	=> $_GPC["status"],
			"createtime"=> time()
		);
		if (!empty($id)) {
			$Flag = $DB->Set("spark_package", $data, " WHERE Users_ID='{$_SESSION['Users_ID']}' AND id='{$id}'");
		} else {
			$Flag = $DB->Add("spark_package", $data);
		}
		if ($Flag) {
			echo '<script language="javascript">alert("添加成功");window.location="package.php";</script>';
		} else {
			echo '<script language="javascript">alert("保存失败");history.back();</script>';
		}
		exit;
	}
	include 'html/packageAdd.php';
}else if($op == "delete"){
	if(!empty($_GET['id'])){
		$id = intval($_GET['id']);
		$item = $DB->GetRs("spark_package", "*", " WHERE Users_ID='{$_SESSION["Users_ID"]}' AND id = '{$id}'");
	}else{
		exit('<script language="javascript">alert("信息不存在");window.location="package.php";</script>');
	}
	$Flag=$DB->Del("spark_package", "Users_ID='{$_SESSION['Users_ID']}' AND id='{$id}'");
	if ($Flag) {
		exit('<script language="javascript">alert("删除成功");window.location="package.php";</script>');
	} else {
		exit('<script language="javascript">alert("删除失败");history.back();</script>');
	}
}
?>