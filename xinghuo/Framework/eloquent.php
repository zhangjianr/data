<?php

define('BASEPATH',$_SERVER["DOCUMENT_ROOT"]);
defined('BASEPATH') OR exit('No direct script access allowed');
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Pagination\Paginator;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

// Autoload 自动载入
require $_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php';

// 载入数据库配置文件
require_once 'dbconfig.php';

$db['eloquent'] = [
  'driver'    => 'mysql',
  'host'      => $host,
  'database'  => $data,
  'username'  => $user,
  'password'  => $pass,
  'charset'   => 'utf8',
  'collation' => 'utf8_general_ci',
  'prefix'    => ''
  ];

  
// Eloquent ORM
//生成Illuminate管理器
$capsule = new Capsule;

$capsule->getContainer()->bind('paginator', 'Illuminate\Pagination\Paginator');

/*分页类路径解析器*/
Paginator::currentPathResolver(function(){
	
	return $_SERVER['REQUEST_URI'];		
});

/*分页类页码解析器*/
Paginator::currentPageResolver(function(){
	$page = !empty($_GET['page'])?$_GET['page']:1;
	return $page;
});


$capsule->addConnection($db['eloquent']);
$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
$capsule->bootEloquent();


//$capsule->getConnection()->enableQueryLog();
//$logs = $capsule->getConnection()->getQueryLog();	







