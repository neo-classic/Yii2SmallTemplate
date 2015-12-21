<?php
if (strrpos($_SERVER['HTTP_HOST'], 'aumgn.org') || strrpos($_SERVER['HTTP_HOST'], '.loc')) {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
} else {
    error_reporting(0);
    ini_set('display_errors', false);
    ini_set('display_startup_errors', false);
    define('YII_DEBUG', false);
}


require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../global.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
