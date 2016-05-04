<?php
if (empty($_SESSION["Users_Account"])) {
	header("location:/member/login.php");
}
$op = !empty($_GET['op']) ? $_GET['op'] : "display";

if($op == "display"){
	$condition = "where Users_ID='{$_SESSION["Users_ID"]}'  and payCode=1";
	if (isset($_GET['search'])) {
		if ($_GET['Keyword']) {
			$condition .= " and orderId like '%" . $_GET['Keyword'] . "%' or mobile like '%" . $_GET['Keyword'] . "%' AND Users_ID='{$_SESSION["Users_ID"]}'  and payCode=1";
		}
	}
	$condition .= " order by id desc";
	include 'html/traorder.php';
}
