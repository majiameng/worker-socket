<?php
/**
 * Name: Server.php.
 * Author: JiaMeng <666@majiameng.com>
 * Date: 2018/07/20 14:21
 * Description: Worker.php.
 */
namespace tinymeng\worker;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Workerman\Worker;

/**
 * This is the base class.
 *
 * @author JiaMeng <666@majiameng.com>
 */
abstract class Server
{
    /**
     * @var array config
     */
    protected $config;
    /**
     * @var string socket
     */
    protected $socket    = '';
    /**
     * @var string
     */
    protected $protocol  = 'websocket';
    /**
     * @var string
     */
    protected $host      = '0.0.0.0';
    /**
     * @var string
     */
    protected $port      = '1314';
    /**
     * @var int
     * Defaults to 4.
     */
    protected $processes = 4;
    /**
     * @var string 处理回调事件类
     */
    protected $eventHandler = 'app\index\controller\Event';

    /**
     * Server constructor.
     */
    public function __construct()
    {
        /**
         * BusinessWorker 进程
         */
        $worker = new BusinessWorker();
        // worker名称
        $worker->name = 'ChatBusinessWorker';
        // bussinessWorker进程数量
        $worker->count = 4;
        // 服务注册地址
        $worker->registerAddress = '127.0.0.1:1236';
        // 处理回调事件类
        $worker->eventHandler = $this->eventHandler;

        /**
         * Gateway 进程
         */
        $gateway = new Gateway($this->socket ?: $this->protocol . '://' . $this->host . ':' . $this->port);
        // 设置名称，方便status时查看
        $gateway->name = 'ChatGateway';
        // 设置进程数，gateway进程数建议与cpu核数相同
        $gateway->count = $this->processes;
        // 分布式部署时请设置成内网ip（非127.0.0.1）
        $gateway->lanIp = '127.0.0.1';
        // 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
        // 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口
        $gateway->startPort = 2300;
        // 心跳间隔
        $gateway->pingInterval = 10;
        // 心跳数据
        $gateway->pingData = '{"type":"ping"}';
        // 服务注册地址
        $gateway->registerAddress = '127.0.0.1:1236';

        /**
         * register 服务必须是text协议
         */
        $register = new Register('text://0.0.0.0:1236');

        /** init server */
        $this->init();
    }

    /**
     * Description:  init server
     */
    protected function init(){
        // Run all worker
        Worker::runAll();
    }

}
