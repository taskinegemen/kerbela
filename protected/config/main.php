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

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'12548442',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1','*.*.*.*'),
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
			'showScriptName'=>false,
			'urlFormat'=>'path',
			'rules'=>array(
				array('AuthenticationServer/authenticate', 'pattern'=>'api/authenticate/', 'verb'=>'POST'),
				array('AuthenticationServer/getip', 'pattern'=>'api/getip/'),
				array('TicketGrantingServer/ticketgrant','pattern'=>'api/ticketgrant/','verb'=>'POST'),
				array('HttpService/authenticate','pattern'=>'httpservice/authenticate/','verb'=>'POST'),
				array('HttpService/Service','pattern'=>'httpservice/service/','verb'=>'POST'),
				array('KerbelaClient/step1', 'pattern'=>'api/client/step1', 'verb'=>'GET'),
				array('Crypto/authenticate','pattern'=>'crypto/authenticate/','verb'=>'POST'),

				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		/*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),*/
		// uncomment the following to use a MySQL database
		
		'db'=>array(
			'connectionString' => 'mysql:host=lindneo.com;dbname=kerbela',
			'username' => 'db_kerbela',
			'password' => 'wN3vqns9VtmQJquB',
			'charset' => 'utf8',
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
		'Smtpmail'=>array(
            'class'=>'application.extension.smtpmail.PHPMailer',
            'Host'=>"tls://smtp.gmail.com",
            'Username'=>'noreply@okutus.com',
            'Password'=>'7m68FJ:J:JHoAeY',
            'Mailer'=>'smtp',
            'Port'=>465,
            'SMTPAuth'=>true, 
           	//'SMTPSecure' => 'tls',
        ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'pacific@linden-tech.com',
		'noreplyEmail'=>'noreply@okutus.com',
		'reader_host'=>'http://reader.lindneo.com/ekaratas',
	),
);
