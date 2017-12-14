<?php
$script_name = $_SERVER['SCRIPT_NAME'];
if( strpos($script_name , 'yiic_dev') )
{
	$_SERVER['HTTP_HOST'] = '127.0.0.1_command';
}
elseif( strpos($script_name , 'yiic_pro') )
{
	$_SERVER['HTTP_HOST'] = 'pro_command';
}
$arr = require(__DIR__ . '/main.php');
unset($arr['defaultController']);
return $arr;