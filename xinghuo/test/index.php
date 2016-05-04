<?php
ini_set("display_errors","On");
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/eloquent.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/Framework/Conn.php');
$userIDS = array(1,38);

$Users_ID = $_SESSION['Users_ID'];
$disAccoutn_list = Dis_Account::where('Users_ID',$Users_ID)->whereIn('User_ID',$userIDS )->get();
print_r($disAccoutn_list);