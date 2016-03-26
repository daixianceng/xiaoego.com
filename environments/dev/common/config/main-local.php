<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=xiaoego',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => 't_'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'smser' => [
            // changes if you using another smser.
            'class' => 'daixianceng\smser\CloudSmser',
            'username' => '',
            'password' => '',
            'fileMode' => true
        ]
    ],
];
