<?php

/* 导出表格处理文件 */
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/PHPExcel.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/PHPExcel/Reader/Excel2007.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/PHPExcel/Reader/Excel5.php');
$UsersID = $_SESSION['Users_ID'];

if (!empty($_FILES['importExcel']['name'])) {
	$tmp_file = $_FILES ['importExcel']['tmp_name'];
	$file_types = explode(".", $_FILES['importExcel']['name']);
	$file_type = $file_types[count($file_types) - 1];
	/* 判别是不是.xls文件，判别是不是excel文件 */
	if (strtolower($file_type) != "xls" && strtolower($file_type) != "xlsx") {
		echo '<script language="javascript">alert("文件格式不对，请重新上传！");history.back();</script>';
		exit;
	}
}else{
	echo '<script language="javascript">alert("文件不能为空！");history.back();</script>';
	exit;
}
/*设置上传路径*/
$savePath = $_SERVER["DOCUMENT_ROOT"] . '/data/importExcel/';
$file_name = $UsersID . "." . $file_type;
if (!copy($tmp_file, $savePath . $file_name)){
	echo '<script language="javascript">alert("文件上传失败，请重试！");history.back();</script>';
	exit;
}

$filePath = $_SERVER["DOCUMENT_ROOT"] . '/data/importExcel/' . $file_name;

$PHPExcel = new PHPExcel();
/* * 默认用excel2007读取excel，若格式不对，则用之前的版本进行读取 */
$PHPReader = new PHPExcel_Reader_Excel2007();
if (!$PHPReader->canRead($filePath)) {
	$PHPReader = new PHPExcel_Reader_Excel5();
	if (!$PHPReader->canRead($filePath)) {
		echo '<script language="javascript">alert("文件无法读取！");history.back();</script>';
		exit;
	}
}

$PHPExcel = $PHPReader->load($filePath);
/* * 读取excel文件中的第一个工作表 */
$currentSheet = $PHPExcel->getSheet(0);
/* * 取得最大的列号 */
$allColumn = $currentSheet->getHighestColumn();
/* * 取得一共有多少行 */
$allRow = $currentSheet->getHighestRow();
/* * 从第二行开始输出，因为excel表中第一行为列名 */
$dataExcel = array();
for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
	/*	 * 从第A列开始输出 */
	for ($currentColumn = 'A'; $currentColumn <= 'B'; $currentColumn++) {
		$val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65, $currentRow)->getValue(); //ord()将字符转为十进制数
		//如果输出汉字有乱码，则需将输出内容用iconv函数进行编码转换，如下将gb2312编码转为utf-8编码输出 */
		if(empty($val)){
			echo '<script language="javascript">alert("表格内存在空值！");history.back();</script>';
			exit;
		}
		$dataExcel[$currentRow][$currentColumn] = trim(iconv('gb2312', 'utf-8', $val));
	}
}

mysql_query('START TRANSACTION');
$isBad = 0;
require_once($_SERVER["DOCUMENT_ROOT"].'/include/library/weixin_message.class.php');
foreach($dataExcel as $key=>$val){
	$tmp_data = array(
		"Order_Status"=>3,
		"Order_ShippingID"=>$val['B']
	);
	$Flag=$DB->Set("user_order",$tmp_data,"where Users_ID='".$_SESSION["Users_ID"]."' and Order_ID=".substr($val['A'], 8));
	if($Flag === false){
		mysql_query('ROLLBACK ');
		echo '<script language="javascript">alert("数据更新有误已经回滚，请重试！");history.back();</script>';
		exit;
	}else{
		$rsOrder=$DB->GetRs("user_order","Users_ID,Order_ID,User_ID","where Users_ID='".$_SESSION["Users_ID"]."' and Order_ID='".substr($val['A'], 8)."'");
		$url='http://'.$_SERVER["HTTP_HOST"]."/api/".$rsOrder['Users_ID']."/shop/member/detail/".$rsOrder['Order_ID']."/";
		$weixin_message = new weixin_message($DB,$_SESSION["Users_ID"],$rsOrder["User_ID"]);
		$contentStr = '您购买的商品已发货，<a href="'.$url.'">查看详情</a>';
		$weixin_message->sendscorenotice($contentStr);
	}
}
mysql_query('COMMIT');
echo '<script language="javascript">alert("订单导入成功！");history.back();</script>';
exit;