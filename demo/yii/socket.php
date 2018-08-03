#!/usr/bin/env php
<?php
/**
 * worker-socket command start file.
 */
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = [
    'class' => 'backend\controllers\EventController',
];
$modifyPassword = Yii::createObject($config);