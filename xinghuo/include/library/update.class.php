<?php
class update {

	public $curVer; //当前版本号
	public $updateUrl = "http://spark.weiyuntop.com/update/version.php?ver="; //当前更新链接
	public $saveDir = ""; //更新文件保存目录
	public $fileName = ""; //更新文件名称
	public $newVer; //升级的版本号

	public function __construct($curVer) {
		if (!empty($curVer)) {
			$this->curVer = $curVer;
		} else {
			$this->curVer = "0";
		}
	}

//主函数开始
	public function start() {
		$this->checkVer();
		$resDown = $this->download($this->updatefile, $this->saveDir);
		if ($resDown !== FALSE) {
			$zip = new ZipArchive;
			$res = $zip->open($this->saveDir . $this->fileName);
			if ($res === TRUE) {
				$zip->extractTo($this->saveDir . "file");
				$zip->close();
				$file = $this->searchDir($this->saveDir . "file");
				$this->backUp($file);
				$this->update($file);
			} else {
				$this->message("解压失败，请联系鼎汉科技客服，获取人工帮助");
			}
		} else {
			$this->message("更新失败");
		}
		$this->message("系统更新成功，请退出重新登录","","successs");
	}

//检查版本号
	private function checkVer() {
		$api = file_get_contents($this->updateUrl . $this->curVer);
		$serve = json_decode($api, TRUE);
		if (!empty($serve) && version_compare($this->curVer, $serve['version'], ">=")) {
			$this->message("已经是最新版本");
		} else {
			$this->newVer = $serve['version'];
			$this->saveDir = $_SERVER["DOCUMENT_ROOT"] . "/update/" . $serve['version'] . "/";
			$this->fileName = pathinfo($serve['fileurl'], PATHINFO_BASENAME);
			$this->updatefile = $serve['fileurl'];
		}
	}

	private function backUp($file) {
		$zip = new ZipArchive;
		if ($zip->open($this->saveDir . 'backup.zip', ZIPARCHIVE::CREATE) === TRUE) {
			foreach ($file as $key => $val) {
				$val = str_replace($this->saveDir . "file/", "", $val);
				if(file_exists($val)){
					$zip->addFile($val, $val);
				}
			}
			$zip->close();
		} else {
			$this->message("无法压缩备份文件");
		}
	}

	private function update($file) {
		$zip = new ZipArchive;
		$res = $zip->open($this->saveDir . $this->fileName);
		if ($res === TRUE) {
			$zip->extractTo($_SERVER["DOCUMENT_ROOT"]);
			$zip->close();
			$fp = fopen($_SERVER["DOCUMENT_ROOT"] . "/version.txt", "w");
			fwrite($fp, $this->newVer);
			fclose($fp);
			return TRUE;
		} else {
			$this->message("解压失败，请联系鼎汉科技客服，获取人工帮助");
		}
	}

//下载文件函数
	private function download($url, $dir = "./", $timeout = 60) {
		if (empty(trim($url))) {
			$this->message("更新文件不存在，请稍后再试" . $this->updatefile);
		}

		if (!file_exists($dir)) {
			@mkdir($dir, 0755, true);
		}

		$url = str_replace(" ", "%20", $url);

		$file = $dir . pathinfo($url, PATHINFO_BASENAME);

		if (function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$temp = curl_exec($ch);
			if (@file_put_contents($file, $temp) && !curl_error($ch)) {
				return $file;
			} else {
				$this->message("下载更新文件失败");
			}
		} else {
			$opts = array(
				"http" => array(
					"method" => "GET",
					"header" => "",
					"timeout" => $timeout
				)
			);
			$context = stream_context_create($opts);
			if (@copy($url, $file, $context)) {
				return $file;
			} else {
				$this->message("下载更新文件失败");
			}
		}
	}

	private function searchDir($dir) {
		static $files = array();
		$dir_list = scandir($dir);
		foreach ($dir_list as $file) {
			if ($file != ".." && $file != ".") {
				if (is_dir($dir . "/" . $file)) {
					$this->searchDir($dir . "/" . $file);
				} else {
					$files[] = $dir . "/" . $file;
				}
			}
		}
		return $files;
	}

//消息管理函数
	private function message($msg = "", $redirectUrl = "", $type = "error") {
		echo json_encode(array("msg" => $msg, "redirectUrl" => $redirectUrl, "type" => $type));
		exit;
	}

}
