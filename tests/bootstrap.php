<?php
error_reporting(-1);
ini_set('display_errors', 1);

define('YII_ENABLE_ERROR_HANDLER', false);
define('YII_DEBUG', true);

define('VENDOR', __DIR__ . '/../vendor');

require_once(VENDOR . '/autoload.php');
require_once(VENDOR . '/yiisoft/yii2/Yii.php');

\Yii::setAlias('@dicr/tests', __DIR__);
\Yii::setAlias('@dicr/cdek', dirname(__DIR__) . '/src');

