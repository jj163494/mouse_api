<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

// 下面是分离前后台需要增加的  
$admin = dirname(dirname(__FILE__));  
$frontend = dirname($admin);  
Yii::setPathOfAlias('admin', $admin);
$params = dirname ( __FILE__ ) .'/params.php';
require_once ($params);
$const = dirname ( __FILE__ ) .'/const.php';
require_once ($const);

return array(
	'basePath'=>$frontend,
	'controllerPath' => $admin.'/controllers',
	'viewPath' => $admin.'/views',
	'runtimePath' => $admin.'/runtime',
	'name'=>'管理后台',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'webroot.lib.*',
		'admin.models.*',  
		'admin.components.*',
		'application.extensions.yii-mail.*'
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'111111',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		'db'=>array(
			'enableParamLogging' => true,//增加这行
		),
		
		// uncomment the following to use a MySQL database
		'db'=>array(
			'connectionString' => 'mysql:host='.$dbhost.';dbname='.$dbname.'',
			'emulatePrepare' => true,
			'username' => $username,
			'password' => $password,
			'charset' => $charset,
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'trace,info,error, warning',
					'categories'=> 'jeff.*',
					'logFile'=> 'api.log',
				),
				// uncomment the following to show log messages on web pages

//				array(
//					'class'=>'CWebLogRoute',
//					'levels'=>'trace, info, error, warning, xdebug',
//					'categories' =>'system.db.*'
//				),

			),
		),
		'mail'=>array(
			'class' => 'application.extensions.yii-mail.YiiMail',
			'viewPath' => 'application.views.mail',
			'logging' => true,
			'dryRun' => false,
			'transportType'=>'smtp',     // case sensitive!
			'transportOptions'=>array(
				'host'=>'smtp.qiye.163.com',   // smtp服务器
				'username'=>'',    // 验证用户
				'password'=>'',   // 验证密码
				'port'=>'25',           // 端口号
				//'encryption'=>'ssl',
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
		'logtype'=>'admin',
	),
);
