<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-m',
    'name' => '笑e购移动端',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'm\common\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'm\modules\v1\Module',
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'loginUrl' => null
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
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'format' => 'json'
        ]
    ],
    'params' => $params,
];
