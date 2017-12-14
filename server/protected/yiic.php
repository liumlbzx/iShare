<?php

// change the following paths if necessary
defined('YII_DEBUG') or define('YII_DEBUG',false);
$yiic= __DIR__ . '/../framework/yiic.php';
$config= __DIR__ . '/config/console.php';

require_once($yiic);
Yii::createWebApplication($config)->run();

