<?php
$dbParams = require(__DIR__ . '/test_db.php');
$origConfig = require(__DIR__ . '/web.php');

return yii\helpers\ArrayHelper::merge($origConfig, [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'db' => $dbParams,
        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
        'assetsAutoCompress' => [
            'class' => '\skeeks\yii2\assetsAuto\AssetsAutoCompressComponent',
            'enabled' => false,
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => false,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
    ],
]);