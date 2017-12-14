<?php
header('Content-type:text/html;charset=utf-8;');
if( defined('YII_DEBUG') && YII_DEBUG )
{
	error_reporting(E_ALL);
}
else
{
	error_reporting(E_ALL ^ E_NOTICE);
}

$configDiy = require_once(__DIR__.'/../../config.php');

$arr = array(
	'timeZone' => 'Asia/Shanghai',
	'basePath'=>__DIR__.DIRECTORY_SEPARATOR.'..',
	'name'=>'iShare',
	'defaultController' => 'default',
	'language' => 'zh_cn',
	'charset' => 'utf-8' ,

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123456',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
    'app' => [],
    'admin' => [],
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=> false,
		),
		'request'=>array(
			'enableCookieValidation'=>true, //开启cookie验证
		),
		'session'=>array(
			'autoStart'=>false,
			'sessionName'=>'PHPSESSID',
			'cookieMode'=>'only',
			'timeout' => 3600,
			//'savePath'=>__DIR__ . '/../runtime/sessions/',
		),
		// uncomment the following to enable URLs in path-format

		'urlManager' => array(
			'showScriptName' => false,
			'caseSensitive' => false,
			'urlFormat' => 'get',
			'rules' => array(
			),
		),

		// uncomment the following to use a MySQL database

		'db'=>array(
			'connectionString' => 'mysql:host=127.0.0.1;dbname=qianhao_ishare',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'KeYpZrZx',
			'charset' => 'utf8',
			'tablePrefix' => '',
			'enableParamLogging' => false , //display sql log end of page
			'schemaCachingDuration' => 86400,
		),

		'cache'=>array(
			'class'=>'system.caching.CFileCache',
			'cachePath' => __DIR__ . '/../runtime/fileCache',
			'directoryLevel' => 5,
			'keyPrefix' => 'ishare_'
		),

		'errorHandler'=>array(
			//'errorAction'=>'default/error',
		),

		'log' => array(
			'class' => 'CLogRouter',
			'routes'=>array(
				'filelog_error' => array(
					'class'=>'CFileLogRoute',
					'logFile' => 'error.log',
					'levels' => 'error' ,
					'logPath' => __DIR__ . '/../../log/app',
				),
				'filelog_warning' => array(
					'class'=>'CFileLogRoute',
					'logFile' => 'warning.log',
					'levels' => 'warning' ,
					'logPath' => __DIR__ . '/../../log/app',
				),
				'filelog_profile' => array(
					'class'=>'CFileLogRoute',
					'logFile' => 'profile.log',
					'levels' => 'profile',
					'logPath' => __DIR__ . '/../../log/app',
					'enabled' => YII_DEBUG
				),
				'filelog_info' => array(
					'class'=>'CFileLogRoute',
					'logFile' => 'info.log',
					'levels' => 'info' ,
					'logPath' => __DIR__ . '/../../log/app',
				),
				'CWebLogRoute' => array(
					'class' => 'CWebLogRoute',
					'ignoreAjaxInFireBug' => true,
					'categories' => 'system.db.*',
					'enabled' => YII_DEBUG,
				),
				'CProfileLogRoute' => array(
					'class' => 'CProfileLogRoute',
					'enabled' => YII_DEBUG,
				)
			),
		),

	),

	'params'=> $configDiy,
);

if( preg_match('/^(127\.0\..+)/', $_SERVER['HTTP_HOST']) || preg_match('/^localhost/', $_SERVER['HTTP_HOST']) )
{
	$arr['components']['db'] = array_merge( $arr['components']['db'] , array(
		'connectionString' => 'mysql:host=127.0.0.1;dbname=qianhao_ishare',
		'username' => 'root',
		'password' => '123456',
		'enableParamLogging' => true , //display sql log end of page
		'schemaCachingDuration' => 1,
		'enableProfiling' => true
	));
}

return $arr;
