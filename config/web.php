<?php

$params = require(__DIR__ . '/params.php');
$url = require(__DIR__ . '/url.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'EFHbYxZzo1Lhxrz9vae61SPOFUc2J2I2',
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'assetsAutoCompress' => [
            'class' => '\skeeks\yii2\assetsAuto\AssetsAutoCompressComponent',
            'enabled' => true,
            'readFileTimeout' => 3,                     //Time in seconds for reading each asset file
            'jsCompress' => true,                       //Enable minification js in html code
            'jsCompressFlaggedComments' => true,        //Cut comments during processing js
            'cssCompress' => true,                      //Enable minification css in html code
            'cssFileCompile' => true,                   //Turning association css files
            'cssFileRemouteCompile' => false,           //Trying to get css files to which the specified path as the remote file, skchat him to her.
            'cssFileCompress' => true,                  //Enable compression and processing before being stored in the css file
            'cssFileBottom' => false,                   //Moving down the page css files
            'cssFileBottomLoadOnJs' => false,           //Transfer css file down the page and uploading them using js
            'jsFileCompile' => true,                    //Turning association js files
            'jsFileRemouteCompile' => false,            //Trying to get a js files to which the specified path as the remote file, skchat him to her.
            'jsFileCompress' => true,                   //Enable compression and processing js before saving a file
            'jsFileCompressFlaggedComments' => true,    //Cut comments during processing js
            'htmlCompress' => true,                     //Enable compression html
            'noIncludeJsFilesOnPjax' => true,           //Do not connect the js files when all pjax requests
            'htmlCompressOptions' => [                  //options for compressing output result
                'extra' => false,                       //use more compact algorithm
                'no-comments' => true                   //cut all the html comments
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
            /*'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'ssl://smtp.yandex.com',
                'username' => 'no-reply@yii2-sm.loc',
                'password' => '',
                'port' => '465',
            ],*/
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
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => $url,
        ], 'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'app' => 'app.php',
                    ],
                ],
            ],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                // https://console.developers.google.com/project
                'google' => [
                    'class' => 'yii\authclient\clients\GoogleOAuth',
                    'clientId' => 'google_client_id',
                    'clientSecret' => 'google_client_secret',
                ],
                // https://developers.facebook.com/apps
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    //'authUrl' => 'https://www.facebook.com/dialog/oauth?display=popup',
                    'clientId' => '573233759491564',
                    'clientSecret' => '324b3c43bb6572874d96d9f0676c8471',
                ],
                // http://vk.com/editapp?act=create
                'vkontakte' => [
                    'class' => 'yii\authclient\clients\VKontakte',
                    'clientId' => 'vkontakte_client_id',
                    'clientSecret' => 'vkontakte_client_secret',
                ],
                // https://dev.twitter.com/apps/new
                'twitter' => [
                    'class' => 'yii\authclient\clients\Twitter',
                    'consumerKey' => 'twitter_consumer_key',
                    'consumerSecret' => 'twitter_consumer_secret',
                ],
                // https://oauth.yandex.ru/client/new
                'yandex' => [
                    'class' => 'yii\authclient\clients\YandexOAuth',
                    'clientId' => 'yandex_client_id',
                    'clientSecret' => 'yandex_client_secret',
                ],
            ],
        ],
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug']['class'] = 'yii\debug\Module';
    $config['modules']['debug']['enableDebugLogs'] = false;
    $config['modules']['debug']['allowedIPs'] = ['*.*.*.*'];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii']['class'] = 'yii\gii\Module';
    $config['modules']['gii']['allowedIPs'] = ['*.*.*.*'];
}

return $config;
