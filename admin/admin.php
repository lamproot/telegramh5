<?php
/*****
*  文件说明
*  运营后台管理
*  author:Suo
*
*
*/
	define('APP_NAME','admin');
	define('APP_PATH','./');
	define('APP_DEBUG',true);
	//定义常量
    define('SCRIPT_DIR', rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/\\'));     //新增常量，大家打印一下就知道是什么了

	define('__PUBLIC__', "../Public");
	require '../ThinkPHP/ThinkPHP.php';
?>
