<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 06.12.20 07:26:59
 */

/** @noinspection PhpUnhandledExceptionInspection, PhpUnused */
declare(strict_types = 1);

use dicr\cdek\CdekApi;
use yii\caching\FileCache;
use yii\console\Application;
use yii\log\FileTarget;

error_reporting(-1);
ini_set('display_errors', '1');

/** */
define('YII_ENABLE_ERROR_HANDLER', false);

/** */
define('YII_DEBUG', true);

/** */
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
            'debug' => true,
            'login' => CdekApi::LOGIN_TEST,
            'password' => CdekApi::PASSWORD_TEST,
        ],
        'log' => [
            'targets' => [
                'file' => [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning', 'info', 'trace']
                ]
            ]
        ],
    ],
    'bootstrap' => ['log']
]);
