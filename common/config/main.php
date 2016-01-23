<?php
return [
    'name' => '笑e购',
    'timeZone' => 'Asia/Hong_Kong',
    'language' => 'zh-CN',
    'sourceLanguage' => 'zh-CN',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false
        ],
        'formatter' => [
            'dateFormat' => 'yyyy年MM月dd日',
            'datetimeFormat' => 'yyyy年MM月dd日 H:mm:ss',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CNY'
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager'
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,
                    'js' => [
                        '//cdn.bootcss.com/jquery/2.1.4/jquery.min.js',
                    ]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'sourcePath' => null,
                    'css' => [
                        '//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css',
                    ]
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'sourcePath' => null,
                    'js' => [
                        '//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js',
                    ]
                ],
                'yii\bootstrap\BootstrapThemeAsset' => [
                    'sourcePath' => null,
                    'css' => [
                        '//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css',
                    ]
                ],
                'rmrevin\yii\fontawesome\cdn\AssetBundle' => [
                    'css' => [
                        '//cdn.bootcss.com/font-awesome/4.4.0/css/font-awesome.min.css',
                    ]
                ],
            ],
        ],
    ]
];
