<?php
/*$wzwlinkurl = 'app.km129.com';
if ($_SERVER['HTTP_HOST'] != $wzwlinkurl) {
    echo '此软件仅授权于域名' . $wzwlinkurl;
    die;
}*/

basename($_SERVER['PHP_SELF']) == 'mysql.inc.php' && header('Location:http://' . $_SERVER['HTTP_HOST']);
@error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
class mysql
{
    private $host;
    private $user;
    private $pass;
    private $data;
    private $conn;
    private $sql;
    private $code;
    private $result;
    private $errLog = false;
    private $showErr = true;
    private $pageNo = 1;
    private $pageAll = 1;
    private $rsAll = 0;
    private $pageSize = 10;
    public function __construct($host, $user, $pass, $data, $code = 'utf8', $conn = 'conn')
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->data = $data;
        $this->conn = $conn;
        $this->code = $code;
        $this->connect();
    }
    public function __get($name)
    {
        return $this->{$name};
    }
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }
    private function connect()
    {
        if ($this->conn == 'pconn') {
            $this->conn = mysql_pconnect($this->host, $this->user, $this->pass);
        } else {
            $this->conn = mysql_connect($this->host, $this->user, $this->pass);
        }
        if (!$this->conn) {
            $this->show_error('无法连接服务器');
        }
        $this->select_db($this->data);
        $this->query('SET NAMES ' . $this->code);
        $this->query("SET CHARACTER_SET_CLIENT='{$this->code}'");
        $this->query("SET CHARACTER_SET_RESULTS='{$this->code}'");
    }
    public function select_db($data)
    {
        $result = mysql_select_db($data, $this->conn);
        if (!$result) {
            $this->show_error('无法连接数据库' . $data);
        }
        return $result;
    }
    public function get_info($num)
    {
        switch ($num) {
            case 1:
                return mysql_get_server_info();
                break;
            case 2:
                return mysql_get_host_info();
                break;
            case 3:
                return mysql_get_proto_info();
                break;
            default:
                return mysql_get_client_info();
        }
    }
    public function query($sql)
    {
        if (empty($sql)) {
            $this->show_error('SQL语句为空');
        }
        $this->sql = preg_replace('/ {2,}/', ' ', trim($sql));
        $this->result = mysql_query($this->sql, $this->conn);
        if (!$this->result) {
            $this->show_error('SQL语句有误', true);
        }
        return $this->result;
    }
    public function create_database($data = '')
    {
        $this->query("CREATE DATABASE {$data}");
    }
    public function show_database()
    {
        $this->query('SHOW DATABASES');
        $db = array();
        while ($row = $this->fetch_array()) {
            $db[] = $row['Database'];
        }
        return $db;
    }
    public function show_tables($data = '')
    {
        if (!empty($data)) {
            $db = ' FROM ' . $data;
        }
        $this->query('SHOW TABLES' . $data);
        $tables = array();
        while ($row = $this->fetch_row()) {
            $tables[] = $row[0];
        }
        return $tables;
    }
    public function copy_tables($tb1, $tb2, $Condition = '')
    {
        $this->query("SELECT * INTO `{$tb1}` FROM `{$tb2}` {$Condition}");
    }
    public function Get($Table, $Fileds = '*', $Condition = '', $Rows = 0)
    {
        if (!$Fileds) {
            $Fileds = '*';
        }
        if ($Rows > 0) {
            $Condition .= " LIMIT 0,{$Rows}";
        }
        $sql = "SELECT {$Fileds} FROM `{$Table}` {$Condition}";
        return $this->query($sql);
    }
    public function GetS($Table, $Fileds = '*', $Condition = '', $Rows = 0)
    {
        if (!$Fileds) {
            $Fileds = '*';
        }
        if ($Rows > 0) {
            $Condition .= " LIMIT 0,{$Rows}";
        }
        $sql = "SELECT {$Fileds} FROM {$Table} {$Condition}";
        $res = $this->query($sql);
        return $this->toArray($res);
    }
    public function GetRs($Table, $Fileds = '*', $Condition = '')
    {
        if (!$Fileds) {
            $Fileds = '*';
        }
        $this->query("SELECT {$Fileds} FROM `{$Table}` {$Condition} LIMIT 0,1");
        return $this->fetch_assoc();
    }
    public function GetR($Table, $Fileds = '*', $Condition = '')
    {
        if (!$Fileds) {
            $Fileds = '*';
        }
        $this->query("SELECT {$Fileds} FROM {$Table} {$Condition} LIMIT 0,1");
        return $this->fetch_assoc();
    }
    public function Add($Table, $Data)
    {
        $flag = false;
        if (!is_array($Data)) {
            $arr = explode(',', $Data);
            $Data = array();
            foreach ($arr as $val) {
                list($key, $val) = explode('=', $val);
                if (!$val) {
                    $val = '';
                }
                $Data[$key] = $val;
            }
        }
        $Fileds = '`' . implode('`,`', array_keys($Data)) . '`';
        $Value = '\'' . implode('\',\'', array_values($Data)) . '\'';
        if (!$flag) {
            $flag = $this->query("INSERT INTO `{$Table}` ({$Fileds}) VALUES ({$Value})");
        }
        return $flag;
    }
    public function Set($Table, $Data, $Condition = '', $unQuot = '')
    {
        $flag = false;
        if (is_array($Data)) {
            if (!is_array($unQuot)) {
                $unQuot = explode(',', $unQuot);
            }
            foreach ($Data as $key => $val) {
                $arr[] = $key . '=' . (in_array($key, $unQuot) ? $val : "'{$val}'");
            }
            $Value = implode(',', $arr);
        } else {
            $Value = $Data;
        }
        if (!$flag) {
            $flag = $this->query("UPDATE `{$Table}` SET {$Value} {$Condition}");
        }
        return $flag;
    }
    public function Del($Table, $Condition = '')
    {
        return $this->query("DELETE FROM `{$Table}`" . ($Condition ? " WHERE {$Condition}" : ''));
    }
    public function result($result = '')
    {
        if (empty($result)) {
            $result = $this->result;
        }
        if ($result == null) {
            $this->show_error('未获取到查询结果', true);
        }
        return mysql_result($result);
    }
    public function fetch_array($result = '', $type = MYSQL_BOTH)
    {
        if (empty($result)) {
            $result = $this->result;
        }
        if (!$result) {
            $this->show_error('未获取到查询结果', true);
        }
        return mysql_fetch_array($result, $type);
    }
    public function fetch_assoc($result = '')
    {
        if (empty($result)) {
            $result = $this->result;
        }
        if (!$result) {
            $this->show_error('未获取到查询结果', true);
        }
        return mysql_fetch_assoc($result);
    }
    public function fetch_row($result = '')
    {
        if (empty($result)) {
            $result = $this->result;
        }
        if (!$result) {
            $this->show_error('未获取到查询结果', true);
        }
        return mysql_fetch_row($result);
    }
    public function fetch_obj($result = '')
    {
        if (empty($result)) {
            $result = $this->result;
        }
        if (!$result) {
            $this->show_error('未获取到查询结果', true);
        }
        return mysql_fetch_object($result);
    }
    public function insert_id()
    {
        return mysql_insert_id();
    }
    public function data_seek($id)
    {
        if ($id > 0) {
            $id = $id - 1;
        }
        if (!mysql_data_seek($this->result, $id)) {
            $this->show_error('指定的数据为空');
        }
        return $this->result;
    }
    public function num_fields($result = '')
    {
        if (empty($result)) {
            $result = $this->result;
        }
        if (!$result) {
            $this->show_error('未获取到查询结果', true);
        }
        return mysql_num_fields($result);
    }
    public function num_rows($result = '')
    {
        if (empty($result)) {
            $result = $this->result;
        }
        $rows = mysql_num_rows($result);
        if ($result == null) {
            $rows = 0;
            $this->show_error('未获取到查询结果', true);
        }
        return $rows > 0 ? $rows : 0;
    }
    public function affected_rows()
    {
        return mysql_affected_rows();
    }
    public function getQuery($unset = '')
    {
        if (!empty($unset)) {
            $arr = explode(',', $unset);
            foreach ($arr as $val) {
                unset($_GET[$val]);
            }
        }
        $list = '';
        foreach ($_GET as $key => $val) {
            $list[] = $key . '=' . urlencode($val);
        }
        return is_array($list) ? implode('&', $list) : '';
    }
    public function getPage($Table, $Fileds = '*', $Condition = '', $pageSize = 10)
    {
        if (intval($pageSize) > 0) {
            $this->pageSize = intval($pageSize);
        }
        if (isset($_GET['page']) && intval($_GET['page'])) {
            $this->pageNo = intval($_GET['page']);
        }
        if (empty($Fileds)) {
            $Fileds = '*';
        }
        $sql = "SELECT * FROM `{$Table}` {$Condition}";
        $this->query($sql);
        $this->rsAll = $this->num_rows();
        if ($this->rsAll > 0) {
            $this->pageAll = ceil($this->rsAll / $this->pageSize);
            if ($this->pageNo < 1) {
                $this->pageNo = 1;
            }
            if ($this->pageNo > $this->pageAll) {
                $this->pageNo = $this->pageAll;
            }
            $sql = "SELECT {$Fileds} FROM `{$Table}` {$Condition}" . $this->limit(true);
            $this->query($sql);
        }
        return $this->rsAll;
    }
    public function limit($str = false)
    {
        $n = ($this->pageNo - 1) * $this->pageSize;
        return $str ? ' LIMIT ' . $n . ',' . $this->pageSize : $n;
    }
    public function showPage($number = true)
    {
        $pageBar = '';
        if ($this->pageAll > 1) {
            $pageBar .= '<div class="page">' . chr(10);
            $url = $this->getQuery('page');
            $url = empty($url) ? '?page=' : '?' . $url . '&page=';
            if ($this->pageNo > 1) {
                $pageBar .= '<a class="pre" href="' . $url . '1">首页</a>' . chr(10);
                $pageBar .= '<a class="pre" href="' . $url . ($this->pageNo - 1) . '">上页</a>' . chr(10);
            } else {
                $pageBar .= '<a class="nopre">首页</a>' . chr(10);
                $pageBar .= '<a class="nopre">上页</a>' . chr(10);
            }
            if ($number) {
                $arr = array();
                if ($this->pageAll < 6) {
                    for ($i = 0; $i < $this->pageAll; $i++) {
                        $arr[] = $i + 1;
                    }
                } else {
                    if ($this->pageNo < 3) {
                        $arr = array(1, 2, 3, 4, 5);
                    } elseif ($this->pageNo <= $this->pageAll && $this->pageNo > $this->pageAll - 3) {
                        for ($i = 1; $i < 6; $i++) {
                            $arr[] = $this->pageAll - 5 + $i;
                        }
                    } else {
                        for ($i = 1; $i < 6; $i++) {
                            $arr[] = $this->pageNo - 3 + $i;
                        }
                    }
                }
                foreach ($arr as $val) {
                    if ($val == $this->pageNo) {
                        $pageBar .= '<a class="cur">' . $val . '</a>' . chr(10);
                    } else {
                        $pageBar .= '<a href="' . $url . $val . '">' . $val . '</a>' . chr(10);
                    }
                }
            }
            if ($this->pageNo < $this->pageAll) {
                $pageBar .= '<a class="next" href="' . $url . ($this->pageNo + 1) . '">下页</a>' . chr(10);
                $pageBar .= '<a class="next" href="' . $url . $this->pageAll . '">尾页</a>' . chr(10);
            } else {
                $pageBar .= '<a class="nonext">下页</a>' . chr(10);
                $pageBar .= '<a class="nonext">尾页</a>' . chr(10);
            }
            $pageBar .= '</div>' . chr(10);
        }
        echo '<style>
.page{width:auto;text-align:center;height:30px;margin-top:5px;}
.page a,
.page span{display:inline-block;}
.page a{width:26px;height:24px;line-height:24px;color:#36c;border:1px solid #ccc;}
.page a:hover,
.page a.cur{background:#ffede1;border-color:#fd6d01;color:#fd6d24;text-decoration:none;}
.page .pre,
.page .next,
.page .nopre,
.page .nonext{width:41px;height:24px;}
.page .pre,
.page .nopre{padding-left:16px;text-align:left;}
.page .next,
.page .nonext{padding-right:16px;text-align:right;}
.page .nopre,
.page .nonext{border:1px solid #ccc;color:#000;line-height:24px;}
.page .nopre{background:url(/Framework/Static/images/page/bg_pre_g.png) no-repeat 6px 8px;}
.page .pre,
.page .pre:hover{background:url(/Framework/Static/images/page/bg_pre.png) no-repeat 6px 8px;}
.page .nonext{background:url(/Framework/Static/images/page/bg_next_g.png) no-repeat 46px 8px;}
.page .next,
.page .next:hover{background:url(/Framework/Static/images/page/bg_next.png) no-repeat 46px 8px;}
</style>';
        echo $pageBar;
    }
    public function showPage2($number = true)
    {
        $pageBar = '';
        if ($this->pageAll > 1) {
            $pageBar .= '<div class="page">' . chr(10);
            $url = $this->getQuery('page');
            $url = empty($url) ? '?page=' : '?' . $url . '&page=';
            if ($this->pageNo > 1) {
                $pageBar .= '<a class="pre" href="' . $url . '1">首页</a>' . chr(10);
                $pageBar .= '<a class="pre" href="' . $url . ($this->pageNo - 1) . '">上页</a>' . chr(10);
            } else {
                $pageBar .= '<span class="nopre">首页</span>' . chr(10);
                $pageBar .= '<span class="nopre">上页</span>' . chr(10);
            }
            if ($number) {
                $arr = array();
                if ($this->pageAll < 6) {
                    for ($i = 0; $i < $this->pageAll; $i++) {
                        $arr[] = $i + 1;
                    }
                } else {
                    if ($this->pageNo < 3) {
                        $arr = array(1, 2, 3, 4, 5);
                    } elseif ($this->pageNo <= $this->pageAll && $this->pageNo > $this->pageAll - 3) {
                        for ($i = 1; $i < 6; $i++) {
                            $arr[] = $this->pageAll - 5 + $i;
                        }
                    } else {
                        for ($i = 1; $i < 6; $i++) {
                            $arr[] = $this->pageNo - 3 + $i;
                        }
                    }
                }
                foreach ($arr as $val) {
                    if ($val == $this->pageNo) {
                        $pageBar .= '<a class="cur">' . $val . '</a>' . chr(10);
                    } else {
                        $pageBar .= '<a href="' . $url . $val . '">' . $val . '</a>' . chr(10);
                    }
                }
            }
            if ($this->pageNo < $this->pageAll) {
                $pageBar .= '<a class="next" href="' . $url . ($this->pageNo + 1) . '">下页</a>' . chr(10);
                $pageBar .= '<a class="next" href="' . $url . $this->pageAll . '">尾页</a>' . chr(10);
            } else {
                $pageBar .= '<span class="nonext">下页</span>' . chr(10);
                $pageBar .= '<span class="nonext">尾页</span>' . chr(10);
            }
            $pageBar .= '<span>';
            $pageBar .= "转到第 <input class=\"pagetext\" id=\"page\" value=\"{$this->pageNo}\" type=\"text\" onblur=\"goPage('{$url}',{$this->pageAll});\" />";
            $pageBar .= ' 页 <input class="pagebtn" name="" type="button" value="确定" /></span></div>' . chr(10);
        }
        echo $pageBar;
    }
    public function showWechatPage1($url = '', $number = true)
    {
        $pageBar = '';
        if ($this->pageAll > 1) {
            $pageBar .= '<div id="turn_page">';
            if ($this->pageNo > 1) {
                $pageBar .= '<a href="' . $url . ($this->pageNo - 1) . '" class="page_button"><<上一页</a>';
            } else {
                $pageBar .= '<font class="page_noclick"><<上一页</font>';
            }
            $pageBar .= '&nbsp;&nbsp;<span class="fc_red">' . $this->pageNo . '</span> / ' . $this->pageAll . '&nbsp;&nbsp;';
            if ($this->pageNo < $this->pageAll) {
                $pageBar .= '<a href="' . $url . ($this->pageNo + 1) . '" class="page_button">下一页>></a>';
            } else {
                $pageBar .= '<font class="page_noclick">下一页>></font>';
            }
            $pageBar .= '</div>';
        }
        echo $pageBar;
    }
    public function showWechatPage($url = '', $number = true)
    {
        $pageBar = '';
        if ($this->pageAll > 1) {
            $pageBar .= '<div id="turn_page">';
            if ($this->pageNo > 1) {
                $pageBar .= '<a href="' . $url . ($this->pageNo - 1) . '/" class="page_button"><<上一页</a>';
            } else {
                $pageBar .= '<font class="page_noclick"><<上一页</font>';
            }
            $pageBar .= '&nbsp;&nbsp;<span class="fc_red">' . $this->pageNo . '</span> / ' . $this->pageAll . '&nbsp;&nbsp;';
            if ($this->pageNo < $this->pageAll) {
                $pageBar .= '<a href="' . $url . ($this->pageNo + 1) . '/" class="page_button">下一页>></a>';
            } else {
                $pageBar .= '<font class="page_noclick">下一页>></font>';
            }
            $pageBar .= '</div>';
        }
        echo $pageBar;
    }
    public function showStaticPage($url = '', $number = true)
    {
        $pageBar = '';
        if ($this->pageAll > 1) {
            $pageBar .= '<div class="page">' . chr(10);
            if ($this->pageNo > 1) {
                $pageBar .= '<a href="' . $url . '1/">|<<</a>' . chr(10);
                $pageBar .= '<a href="' . $url . ($this->pageNo - 1) . '/">|<</a>' . chr(10);
            } else {
                $pageBar .= '<a>|<<</a>' . chr(10);
                $pageBar .= '<a>|<</a>' . chr(10);
            }
            if ($number) {
                $arr = array();
                if ($this->pageAll < 10) {
                    for ($i = 0; $i < $this->pageAll; $i++) {
                        $arr[] = $i + 1;
                    }
                } else {
                    if ($this->pageNo < 5) {
                        $arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
                    } elseif ($this->pageNo <= $this->pageAll && $this->pageNo > $this->pageAll - 5) {
                        for ($i = 1; $i < 10; $i++) {
                            $arr[] = $this->pageAll - 9 + $i;
                        }
                    } else {
                        for ($i = 1; $i < 10; $i++) {
                            $arr[] = $this->pageNo - 5 + $i;
                        }
                    }
                }
                foreach ($arr as $val) {
                    if ($val == $this->pageNo) {
                        $pageBar .= '<a class="cur">' . $val . '</a>' . chr(10);
                    } else {
                        $pageBar .= '<a href="' . $url . $val . '/">' . $val . '</a>' . chr(10);
                    }
                }
            }
            if ($this->pageNo < $this->pageAll) {
                $pageBar .= '<a href="' . $url . ($this->pageNo + 1) . '/">>|</a>' . chr(10);
                $pageBar .= '<a href="' . $url . $this->pageAll . '/">>>|</a>' . chr(10);
            } else {
                $pageBar .= '<a>>|</a>' . chr(10);
                $pageBar .= '<a>>>|</a>' . chr(10);
            }
            $pageBar .= '</div>' . chr(10);
        }
        echo '<style>
			.page{width:auto;text-align:center;height:30px;margin-top:5px;}
			.page a,
			.page span{display:inline-block;}
			.page a{width:26px;height:24px;line-height:24px;color:#36c;border:1px solid #ccc;}
			.page a:hover,
			.page a.cur{background:#ffede1;border-color:#fd6d01;color:#fd6d24;text-decoration:none;}
			.page .pre,
			.page .next,
			.page .nopre,
			.page .nonext{width:41px;height:24px;}
			.page .pre,
			.page .nopre{padding-left:16px;text-align:left;}
			.page .next,
			.page .nonext{padding-right:16px;text-align:right;}
			.page .nopre,
			.page .nonext{border:1px solid #ccc;color:#000;line-height:24px;}
			.page .nopre{background:url(/Framework/Static/images/page/bg_pre_g.png) no-repeat 6px 8px;}
			.page .pre,
			.page .pre:hover{background:url(/Framework/Static/images/page/bg_pre.png) no-repeat 6px 8px;}
			.page .nonext{background:url(/Framework/Static/images/page/bg_next_g.png) no-repeat 46px 8px;}
			.page .next,
			.page .next:hover{background:url(/Framework/Static/images/page/bg_next.png) no-repeat 46px 8px;}
			</style>';
        echo $pageBar;
    }
    public function getip()
    {
        if ($_SERVER['REMOTE_ADDR']) {
            return $_SERVER['REMOTE_ADDR'];
        } elseif (getenv('REMOTE_ADDR')) {
            return getenv('REMOTE_ADDR');
        } else {
            return '';
        }
    }
    public function show_error($msg = '', $sql = false)
    {
        $err = '[' . mysql_errno() . ']' . mysql_error();
        if ($sql) {
            $sql = 'SQL语句：' . $this->sql;
        }
        if ($this->errLog) {
            $dirs = 'error/';
            $fileName = date('Y-m-d') . '.log';
            $filePath = $dirs . $fileName;
            if (!is_dir($dirs)) {
                $dirs = explode('/', $dirs);
                $temp = '';
                foreach ($dirs as $dir) {
                    $temp .= $dir . '/';
                    if (!is_dir($temp)) {
                        mkdir($temp) or die('__无法建立目录' . $temp . '，自动取消记录错误信息');
                    }
                }
                $filePath = $temp . $fileName;
            }
            $text = '错误事件：' . $msg . '错误原因：' . $err . '' . ($sql ? $sql . '' : '') . '客户端IP：' . $this->getip() . '记录时间：' . date('Y-m-d H:i:s') . '';
            $log = '错误日志：__' . (error_log($text, 3, $filePath) ? '此错误信息已被自动记录到日志' . $fileName : '写入错误信息到日志失败');
        }
        if ($this->showErr) {
            echo '
      <fieldset class="errlog">
        <legend>错误信息提示</legend>
        <label class="tip">错误事件：' . $err . '</label><br>
        <label class="msg">错误原因：' . $msg . '</label><br>
        <label class="sql">' . $sql . '</label><br>
      </fieldset>';
            die;
        }
    }
    public function drop($table)
    {
        if ($table) {
            $this->query("DROP TABLE IF EXISTS `{$table}`");
        } else {
            $rst = $this->query('SHOW TABLES');
            while ($row = $this->fetch_array()) {
                $this->query("DROP TABLE IF EXISTS `{$row[0]}`");
            }
        }
    }
    public function makeSql($table)
    {
        $result = $this->query("SHOW CREATE TABLE `{$table}`");
        $row = $this->fetch_row($result);
        $sqlStr = '';
        if ($row) {
            $sqlStr .= '-- ---------------------------------------------------------------';
            $sqlStr .= "-- Table structure for `{$table}`\r\n";
            $sqlStr .= '-- ---------------------------------------------------------------';
            $sqlStr .= "DROP TABLE IF EXISTS `{$table}`;\r\n{$row[1]};\r\n";
            $this->Get($table);
            $fields = $this->num_fields();
            if ($this->num_rows() > 0) {
                $sqlStr .= '';
                $sqlStr .= '-- ---------------------------------------------------------------';
                $sqlStr .= "-- Records of `{$table}`\r\n";
                $sqlStr .= '-- ---------------------------------------------------------------';
                while ($row = $this->fetch_row()) {
                    $comma = '';
                    $sqlStr .= "INSERT INTO `{$table}` VALUES (";
                    for ($i = 0; $i < $fields; $i++) {
                        $sqlStr .= $comma . '\'' . mysql_escape_string($row[$i]) . '\'';
                        $comma = ',';
                    }
                    $sqlStr .= ');';
                }
            }
            $sqlStr .= '';
        }
        return $sqlStr;
    }
    public function readSql($filePath)
    {
        if (!file_exists($filePath)) {
            return false;
        }
        $sql = file_get_contents($filePath);
        if (empty($sql)) {
            return '';
        }
        $sql = preg_replace('/(\\/\\*(.*)\\*\\/)/s', '', $sql);
        $sql = preg_replace('/(--.*)|[\\f\\n\\r\\t\\v]*/', '', $sql);
        $sql = preg_replace('/ {2,}/', ' ', $sql);
        $arr = explode(';', $sql);
        $sql = array();
        foreach ($arr as $str) {
            $str = trim($str);
            if (!empty($str)) {
                $sql[] = $str;
            }
        }
        return $sql;
    }
    public function saveSql($sqlPath = '', $table = '')
    {
        if (empty($table)) {
            $result = $this->query('SHOW TABLES');
            while ($arr = $this->fetch_row($result)) {
                $str = $this->makeSql($arr[0]);
                if (!empty($str)) {
                    $sql .= $str;
                }
            }
            $text = '/***************************************************************';
            $text .= "-- Database: {$this->data}\r\n";
            $text .= '-- Date Created: ' . date('Y-m-d H:i:s') . '';
            $text .= '***************************************************************/';
        } else {
            $text = '';
            $sql = $this->makeSql($table);
        }
        if (empty($sql)) {
            return false;
        }
        $text .= $sql;
        $dir = dirname(__FILE__);
        $file = basename($sqlPath);
        if (empty($file)) {
            $file = date('YmdHis') . '.sql';
        }
        $sqlPath = $dir . '/' . $file;
        if (!empty($dir) && !is_dir($dir)) {
            $path = explode('/', $dir);
            $temp = '';
            foreach ($path as $dir) {
                $temp .= $dir . '/';
                if (!is_dir($temp)) {
                    if (!mkdir($temp)) {
                        return false;
                    }
                }
            }
            $sqlPath = $temp . $file;
        }
        $link = fopen($sqlPath, 'w+');
        if (!is_writable($sqlPath)) {
            return false;
        }
        return fwrite($link, $text);
        fclose($link);
    }
    public function loadSql($filePath)
    {
        $val = $this->readSql($filePath);
        if ($val == false) {
            $this->show_error($filePath . '不存在');
        } elseif (empty($val)) {
            $this->show_error($filePath . '中无有效数据');
        } else {
            $errList = '';
            foreach ($val as $sql) {
                $result = mysql_query($sql);
                if (!$result) {
                    $errList .= '执行语句' . $sql . '失败<br />';
                }
            }
            return $errList;
        }
        return false;
    }
    public function free()
    {
        if (is_resource($this->result)) {
            mysql_free_result($this->result);
        }
    }
    public function close()
    {
        mysql_close($this->conn);
    }
    public function toArray($resource = '')
    {
        if (!empty($this->result)) {
            $resource = $this->result;
        }
        $result = array();
        while ($item = $this->fetch_assoc($resource, MYSQL_ASSOC)) {
            $result[] = $item;
        }
        return $result;
    }
    public function __destruct()
    {
        $this->close();
    }
}