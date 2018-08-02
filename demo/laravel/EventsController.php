<?php
/**
 * Name: IndexController.php.
 * Author: JiaMeng <666@majiameng.com>
 * Date: 2018/4/16 14:12
 * Description: IndexController.php.
 */
namespace App\Http\Controllers;
use tinymeng\worker\Server;
use GatewayWorker\Lib\Gateway;
class EventsController extends Server
{
    /**
     * @var string Socket connect address
     */
    protected $socket = 'websocket://0.0.0.0:1314';
    /**
     * @var string The current class of namespace
     */
    protected $eventHandler = 'App\Http\Controllers\EventsController';

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