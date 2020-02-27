<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 28.02.20 04:01:12
 */

/** @noinspection PhpUnhandledExceptionInspection, PhpUnused */
declare(strict_types = 1);

use dicr\cdek\CdekApi;
use yii\caching\FileCache;
use yii\console\Application;

error_reporting(- 1);
ini_set('display_errors', '1');

define('YII_ENABLE_ERROR_HANDLER', false);
define('YII_DEBUG', true);

define('VENDOR', dirname(__DIR__) . '/vendor');

require_once(VENDOR . '/autoload.php');
require_once(VENDOR . '/yiisoft/yii2/Yii.php');

new Application([
    'id' => 'test',
    'basePath' => dirname(__DIR__),
    'vendorPath' => VENDOR,
    'components' => [
        'cache' => FileCache::class,
        'api' => [
            'class' => CdekApi::class,
            'baseUrl' => CdekApi::URL_TEST,
            'login' => CdekApi::LOGIN_TEST,
            'password' => CdekApi::PASSWORD_TEST,
        ]
    ]
]);
