<?php
if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) )
{
  $realIp = '';
  $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
  if(!$ips)
  {
    die('ip lost.');
  }
  $realIp = urlencode(trim(end($ips)));
  if($realIp && preg_match('/^\d+\.\d+\.\d+\.\d+$/', $realIp))
  {
    $_SERVER['REMOTE_ADDR'] = $realIp;
  }
  else
  {
    die('ip lost.');
  }
}
else
{
  die('ip lost.');
}

$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
if (preg_match('/^(127\.0\..+)/', $http_host))
{
  defined('YII_DEBUG') or define('YII_DEBUG', true);
}
else
{
  defined('YII_DEBUG') or define('YII_DEBUG', false);
}

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

$yii = __DIR__ . '/framework/yii.php';
$config = require(__DIR__ . '/protected/config/main.php');
require_once($yii);
Yii::createWebApplication($config)->run();
