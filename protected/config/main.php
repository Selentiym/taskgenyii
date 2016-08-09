<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',

	// preloading 'log' component
	'preload'=>array('log'),
	'aliases'=> array(
			'Shingles' => dirname(__FILE__) . '/../components/shingles/',
	),
	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.models.users.*',
		'application.models.strings.*',
		'application.components.*',
		'application.extensions.tinymce.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool

		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'kicker_1995',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			//'ipFilters'=>array('127.0.0.1','::1'),
		),

	),

	// application components
	'components'=>array(
		'user'=>array(
			'class' => 'application.components.UWebUser',
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		'authManager'=>array(
				'class'=>'CDbAuthManager',
				'connectionID'=>'db',
		),
		// uncomment the following to enable URLs in path-format

		'urlManager'=>array(
			'class' => 'application.components.UUrlManager',
			'urlFormat'=>'path',
			'showScriptName' => false,
			'rules'=>array(
				'' => 'cabinet/index',
				'stem' => 'site/stem',
				'logout' => 'login/logout',
				'dialog/open/<arg:\w+>' => 'dialog/open',
				'<action:(TaskCreate|TextCreate)>/parent/<parentId:\d+>' => 'cabinet/<action>',
				'<action:(TaskCreate|TextCreate)>' => 'cabinet/<action>',
				'cabinet' => 'cabinet/index',
				'loadKeywords/<arg:\d+>' => 'cabinet/loadKeywords',
				'task/move/<arg:\d+>/to/<where:\d+>' => 'task/move',

				//'loadKeywords/parent/<parentId:\d+>' => 'cabinet/loadKeywords',
				//'cabinet/<arg:\w+>' => 'cabinet/index',
				/*array(
						'class' => 'application.components.UserTypeUrlRule',
						'connectionID' => 'db'
				),*/
				'<controller:\w+>/<arg:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<arg:\w+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>/<arg:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),

		'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=cq97848_taskgen',
            'tablePrefix' => 'tbl_',
            'emulatePrepare' => true,
            'username' => 'cq97848_taskgen',
            'password' => 'kicker',
            'charset' => 'utf8',
        ),
		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=testdrive',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		*/
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
		'UrlHelper' => array(
			'class' => 'UrlHelper'
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);