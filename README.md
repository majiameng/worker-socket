Workerman websocket 扩展
===============

## 1.安装
> composer require tinymeng/worker-socket:~1.0.0  -vvv

### 目录结构
> worker-socket 已集成thinkphp、larverl、yii框架使用

```
.
├── example                          实例代码源文件目录
│   ├── laravel
│   │   ├── EventsController.php   回调事件处理实例
│   │   └── socket.php             socket启动文件
│   ├── thinkphp
│   │   ├── Events.php             回调事件处理实例
│   │   └── socket.php             socket启动文件
│   └── yii
│        ├── EventsController.php   回调事件处理实例
│        └── socket.php             socket启动文件
├── src                              代码源文件目录
│   └── Server.php                  封装服务基础类
├── composer.json                    composer文件
├── LICENSE                          MIT License
└── README.md                        说明文件
```


## 2.业务逻辑(Events类)

#### 在项目<code> /application/index/controller </code>下创建文件Events类 `Events.php`

> 首先创建控制器类并继承 tinymeng\worker\Server，然后设置属性和添加回调方法

##### 2.1 ThinkPhp框架示例如下：

~~~
<?php
/**
 * Name: Events.php.
 * Author: JiaMeng <666@majiameng.com>
 * Description: websocket callback
 */
namespace app\index\controller;

use tinymeng\worker\Server;
use GatewayWorker\Lib\Gateway;

class Events extends Server{
    /**
     * @var string Socket connect address
     */
    protected $socket = 'websocket://0.0.0.0:1314';
    /**
     * @var string The current class of namespace
     */
    protected $eventHandler = 'app\index\controller\Events';

    /**
     * Description:  当客户端连接时时触发
     * @param $client_id
     */
    public static function onConnect($client_id){
        echo 'client_id : '.$client_id. ', connect ' .PHP_EOL;
    }

    /**
     * Description:  当客户端发来消息时触发
     * Author: JiaMeng <666@majiameng.com>
     * @param int $client_id 连接id
     * @param string $data 具体消息
     * @return bool
     */
    public static function onMessage($client_id, $data) {
        echo 'client : '.$client_id. ',message data :'.$data .PHP_EOL;
    }

    /**
     * Description:  当客户端断开连接时触发
     * Author: JiaMeng <666@majiameng.com>
     * Updater:
     * @param int $client_id 连接id
     */
    public static function onClose($client_id) {
        echo 'client_id : '.$client_id .' close '.PHP_EOL;
    }

}
~~~

> 支持workerman所有的回调方法定义（回调方法必须是public static类型）


## 3.启动项目

在应用根目录增加入口文件 socket.php

##### 3.1.1 ThinkPhp框架示例如下：
~~~
#!/usr/bin/env php
<?php
/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php socket.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

define('APP_PATH', __DIR__ . '/application/');

/** Events类，根据自己的模块和控制器填写 */
define('BIND_MODULE','index/Events');

// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
~~~

##### 3.1.2 Laravel框架示例如下：
~~~
#!/usr/bin/env php
<?php
/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php socket.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

/** Events类，根据自己的命名规范填写 */
$kernel = $app->make(App\Http\Controllers\EventsController::class);

~~~

##### 3.1.3 Yii框架示例如下：
~~~
#!/usr/bin/env php
<?php
/**
 * worker-socket command socket file.
 */
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = [
    /** Events类，根据自己的命名规范填写 */
    'class' => 'backend\controllers\EventController',
];
$modifyPassword = Yii::createObject($config);
~~~


##### 3.2 在命令行启动服务端 <code> php socket.php start </code>

```liunx

[root@izbp153yczpm4pp9pjs0u3z majiameng.com]# php socket.php start

Workerman[server.php] start in DEBUG mode
----------------------- WORKERMAN -----------------------------
Workerman version:3.5.13          PHP version:7.2.6
------------------------ WORKERS -------------------------------
user          worker              listen                    processes status
root          ChatBusinessWorker  none                       4         [OK] 
root          ChatGateway         websocket://0.0.0.0:1314   4         [OK] 
root          Register            text://0.0.0.0:1236        1         [OK] 
----------------------------------------------------------------
Press Ctrl+C to stop. Start success.

```

##### linux下面可以支持下面指令
~~~
php socket.php start|stop|status|restart|reload
~~~

##### 需要后台运行的话
```php
php socket.php start -d
```

## 4.测试

在浏览器中进行客户端测试

> http://www.blue-zero.com/WebSocket/

输入socket地址  ws://IP:1314 测试socket服务是否正常


> 网站事例：  [打开](https://www.majiameng.com/) (需要登录哦!)

> 大家如果有问题要交流，就发在这里吧：  [worke-socket 交流](https://github.com/majiameng/worker-socket/issues/1) 或发邮件 666@majiameng.com
