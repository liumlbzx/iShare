<?php
//微信网页支付通知页,切勿修改
if( preg_match('/^(127\.0\..+)/', $_SERVER['HTTP_HOST']) || preg_match('/^(192\.168\..+)/', $_SERVER['HTTP_HOST']) )
{
  defined('YII_DEBUG') or define('YII_DEBUG',true);
}
else
{
  defined('YII_DEBUG') or define('YII_DEBUG',false);
}

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

$yii		= __DIR__ . '/framework/yii.php';
$config = require(__DIR__.'/protected/config/main.php');
require_once($yii);
$config['defaultController'] = 'mobile/wechat/notify';
Yii::createWebApplication($config)->run();
