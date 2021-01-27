<?php

namespace Controllers;

use Lib\SwooleWebsocketServer;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * 异步任务类
 * Class TaskWorker
 * @package Controllers
 */
class TaskController
{
    /**
     * @var SwooleWebsocketServer $server swoole服务
     */
    private $server;

    public function __construct(SwooleWebsocketServer $server)
    {
        $this->server = $server;
    }

    /**
     * 数据推送至rabbitMQ
     * @param array $data
     */
    public function rabbitMQPush(array $data)
    {
        $body = json_encode($data);
        $properties = [
            'content_type' => 'application/json',//内容格式
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,//消息持久化
//            'priority' => 1,//优先级
//            'expiration' => 60 * 1000,//存活时间(毫秒)
//            'timestamp' => time()//时间戳
        ];
        //生成消息
        $message = new AMQPMessage($body, $properties);
        $rabbitMQChannel = $this->server->rabbitMQChannel;
        $amqp = $rabbitMQChannel->config;
        //推送消息至默认交换机(直连)
        $rabbitMQChannel->basic_publish($message, $amqp['chat']['publish']['exchange'], $amqp['chat']['publish']['routing_key']);
        //此处数据由app/Console/Commands/StartChatConsumer进行消费
        echo "data push success\n";
    }

    /**
     * 数据推送至websocket
     * @param string $message
     */
    public function websocketPush(string $message)
    {
        $table = $this->server->table;

        foreach ($table as $fd => $row) {
            $data = "尊敬的{$row['user']}:{$message}";
            $this->server->push($fd, $data);
        }
    }
}