<?php
/**
 * Name: Events.php.
 * Author: JiaMeng <666@majiameng.com>
 * Description: websocket callback
 */
namespace app\index\controller;

use tinymeng\worker\Server;
use GatewayWorker\Lib\Gateway;
use app\common\model\mysqldb\Chatlog;

class Events extends Server{
    /**
     * @var string socket connect address
     */
    protected $socket = 'websocket://0.0.0.0:1314';
    /**
     * @var string The current class of namespace
     */
    protected $eventHandler = 'app\index\controller\Events';

    /**
     * Description:  onConnect
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
        $message = json_decode($data, true);
        $message_type = $message['type'];
        switch($message_type) {
            case 'init':
                // 设置session
                $_SESSION = [
                    'username' => $message['username'],
                    'avatar'   => $message['avatar'],
                    'id'       => $message['id'],
                    'sign'     => $message['sign']
                ];

                // 将当前链接与uid绑定
                Gateway::bindUid($client_id, $message['id']);

                /** 修改为在线状态... */


                // 通知当前客户端初始化
                $init_message = array(
                    'message_type' => 'init',
                    'id'           => $message['id'],
                );
                Gateway::sendToClient($client_id, json_encode($init_message));

                $resMsg = [];//查询有没有需要推送的离线信息

                if( !empty( $resMsg ) ){
                    foreach( $resMsg as $key=>$vo ){

                        $log_message = [
                            'message_type' => 'logMessage',
                            'data' => [
                                'username' => $vo['fromname'],
                                'avatar'   => $vo['fromavatar'],
                                'id'       => $vo['fromid'],
                                'type'     => 'friend',
                                'content'  => htmlspecialchars( $vo['content'] ),
                                'timestamp'=> $vo['timeline'] * 1000,
                            ]
                        ];
                        Gateway::sendToUid( $message['id'], json_encode($log_message) );
                        /** 设置推送状态为已经推送... */

                    }
                }

                break;
            case 'addUser' :
                //添加用户
                $add_message = [
                    'message_type' => 'addUser',
                    'data' => [
                        'type' => 'friend',
                        'avatar'   => $message['data']['avatar'],
                        'username' => $message['data']['username'],
                        'groupid'  => $message['data']['groupid'],
                        'id'       => $message['data']['id'],
                        'sign'     => $message['data']['sign']
                    ]
                ];
                Gateway::sendToAll( json_encode($add_message), null, $client_id );
                break;
            case 'delUser' :
                //删除用户
                $del_message = [
                    'message_type' => 'delUser',
                    'data' => [
                        'type' => 'friend',
                        'id'       => $message['data']['id']
                    ]
                ];
                Gateway::sendToAll( json_encode($del_message), null, $client_id );
                break;
            case 'addGroup':
                //添加群组

                break;
            case 'joinGroup':
                //加入群组

                break;
            case 'addMember':
                //添加群组成员

                break;
            case 'removeMember':
                //将移除群组的成员的群信息移除，并从讨论组移除

                break;
            case 'delGroup':
                //删除群组

                break;
            case 'chatMessage':
                // 聊天消息
                $type = $message['data']['to']['type'];
                $to_id = $message['data']['to']['id'];
                $uid = $_SESSION['id'];

                $chat_message = [
                    'message_type' => 'chatMessage',
                    'data' => [
                        'username' => $_SESSION['username'],
                        'avatar'   => $_SESSION['avatar'],
                        'id'       => $type === 'friend' ? $uid : $to_id,
                        'type'     => $type,
                        'content'  => htmlspecialchars($message['data']['mine']['content']),
                        'timestamp'=> time()*1000,
                    ]
                ];
                //聊天记录数组
                $param = [
                    'fromid' => $uid,
                    'toid' => $to_id,
                    'fromname' => $_SESSION['username'],
                    'fromavatar' => $_SESSION['avatar'],
                    'content' => htmlspecialchars($message['data']['mine']['content']),
                    'timeline' => time(),
                    'needsend' => 0
                ];
                switch ($type) {
                    // 私聊
                    case 'friend':
                        // 插入
                        $param['type'] = 'friend';
                        Gateway::getClientIdByUid( $to_id ) ;
                        if( empty( Gateway::getClientIdByUid( $to_id ) ) ){
                            $param['needsend'] = 1;  //用户不在线,标记此消息推送
                        }
                        Chatlog::create($param);
                        Gateway::sendToUid($to_id, json_encode($chat_message));
                    // 群聊
                    case 'group':
                        $param['type'] = 'group';
                        Chatlog::create($param);
                        Gateway::sendToGroup($to_id, json_encode($chat_message), $client_id);
                }
                break;
            case 'hide':
                /** 修改为隐身通知好友 */
                $status_message = [
                    'message_type' => 'outline',
                    'id'           => $_SESSION['id'],
                ];
                Gateway::sendToAll(json_encode($status_message));
                break;
            case 'online':
                /** 修改为上线通知好友 */
                $status_message = [
                    'message_type' => 'online',
                    'id'           => $_SESSION['id'],
                ];
                Gateway::sendToAll(json_encode($status_message));
                break;
            case 'ping':
                /** 心跳处理 */
                break;
            default:
                echo "unknown message $data" . PHP_EOL;
                break;
        }
        return true;
    }

    /**
     * Description:  当用户断开连接时触发
     * Author: JiaMeng <666@majiameng.com>
     * Updater:
     * @param int $client_id 连接id
     */
    public static function onClose($client_id) {
        echo 'client_id : '.$client_id .' close '.PHP_EOL;

        /** 修改为离线状态... */

        /** 发送消息 */
        $logout_message = [
            'message_type' => 'logout',
            'id'           => $_SESSION['id']
        ];
        Gateway::sendToAll(json_encode($logout_message));
    }

}