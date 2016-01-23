<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'name' => '笑e购·最好用的校园即时购物平台',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
    'bootstrap' => ['devicedetect', 'log'],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'urlManager' => [
            'rules' => [
                'page/<slug:[\d\w_-]+>' => 'site/page',
                'school/<id:\d+>' => 'school/index',
                'store/<id:\d+>' => 'store/index',
                'official/<id:\d+>' => 'store/official',
                'fruit/<id:\d+>' => 'store/fruit'
            ]
        ],
        'view' => [
            'theme' => [
                'basePath' => '@app/themes/default',
                'baseUrl' => '@web/themes/default',
                'pathMap' => [
                    '@app/views' => '@app/themes/default',
                ],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['application', 'yii\web\HttpException', 'yii\base\ErrorException'],
                    'logFile' => '@runtime/logs/app.' . date('Ymd') . '.log',
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'devicedetect' => [
            'class' => 'alexandernst\devicedetect\DeviceDetect'
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'bundles' => [
                'frontend\assets\AppAsset' => [
                    'css' => [
                        YII_ENV_DEV ? 'css/site.css' : 'css/site.min.css',
                        YII_ENV_DEV ? 'css/site-less.css' : 'css/site-less.min.css'
                    ],
                    'js' => [
                        YII_ENV_DEV ? 'js/site.js' : 'js/site.min.js'
                    ]
                ],
            ],
        ],
    ],
    'params' => $params,
];
