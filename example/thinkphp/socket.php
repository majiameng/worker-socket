#!/usr/bin/env php
<?php
/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php server.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

define('APP_PATH', __DIR__ . '/application/');

define('BIND_MODULE','index/Events');

// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';