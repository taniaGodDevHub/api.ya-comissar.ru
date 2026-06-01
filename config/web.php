<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'school-avarcom-back',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'request' => [
            'cookieValidationKey' => 'your-secret-key-here',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'enableCsrfValidation' => false,
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $request = Yii::$app->request;
                $origin = $request->headers->get('Origin');

                
                // Добавляем CORS заголовки для API запросов
                if (strpos($request->url, '/api/') === 0 || strpos($request->pathInfo, 'api/') === 0) {
                    if (in_array($origin, $allowedOrigins)) {
                        $response->headers->set('Access-Control-Allow-Origin', '*');
                    }
                    $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS');
                    $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
                    $response->headers->set('Access-Control-Allow-Credentials', 'true');
                    $response->headers->set('Access-Control-Max-Age', '86400');
                    
                    // Логируем для отладки
                    Yii::info("CORS headers set for origin: " . $origin . " on URL: " . $request->url, 'cors');
                }
            },
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        /*'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // API routes
                'api/auth/login' => 'api/auth/login',
                'api/auth/logout' => 'api/auth/logout',
                'api/auth/me' => 'api/auth/me',
                'api/auth/register' => 'api/auth/register',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/user',
                    'prefix' => 'api',
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/school',
                    'prefix' => 'api',
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/student',
                    'prefix' => 'api',
                ],
                // Web routes
                '' => 'site/index',
                'login' => 'user/login',
                'logout' => 'user/logout',
                'users' => 'user/index',
                'users/<id:\d+>' => 'user/view',
                'users/<id:\d+>/edit' => 'user/update',
                'users/<id:\d+>/delete' => 'user/delete',
                'user/generate-registration-token' => 'user/generate-registration-token',
            ],
        ],*/
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;