<?php
ini_set('display_errors',1);            //错误信息
ini_set('display_startup_errors',1);    //php启动错误信息
error_reporting(-1);                    //打印出所有的 错误信息

define('APP_NAME','index');
	define('APP_PATH','./index/');
	define('APP_DEBUG',true);
	echo "1212";
	print_r($_SERVER) ;
	require './ThinkPHP/ThinkPHP.php';
	// define('ROOT',$_SERVER['DOCUMENT_ROOT']);
	// define('THINK_PATH',ROOT.'/ThinkPHP/');
	// require THINK_PATH.'ThinkPHP.php';
?>
