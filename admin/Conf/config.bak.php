<?php
return array(
	//'配置项'=>'配置值'
	'DB_TYPE'=>'mysql',
	'DB_USER'=>'root',
	'DB_PWD'=>'root',
	'DB_HOST'=>'localhost',
	'DB_PORT'=>'3306',
	'DB_PREFIX'=>'',
	'URL_MODEL' => '1',
	'DB_NAME'=>'telegram',
	'APP_DEBUG' => false,
	'SHOW_PAGE_TRACE'=>false,
	'DB_CHARSET'=> 'utf8mb4',
	'view_replace_str'       => [
        '__PUBLIC__'=> SCRIPT_DIR . '/public'              //定义首页
    ],
);
/*
return array(
	//'配置项'=>'配置值'
	'DB_TYPE'=>'mysql',
	'DB_USER'=>'root',
	'DB_PWD'=>'root',
	'DB_HOST'=>'localhost',
	'DB_PORT'=>'3306',
	'DB_PREFIX'=>'xqs_',
	'URL_MODEL' => '1',
	'DB_NAME'=>'hdm128467116_db',
	'APP_DEBUG' => true
);
?>
*/
