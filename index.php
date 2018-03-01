<?php
ini_set('display_errors',1);            //错误信息
ini_set('display_startup_errors',1);    //php启动错误信息
error_reporting(-1);                    //打印出所有的 错误信息

define('APP_NAME','index');
	define('APP_PATH','./index/');
	define('APP_DEBUG',false);
	echo "1212";
	require './ThinkPHP/ThinkPHP.php';
	echo "3434";
?>
