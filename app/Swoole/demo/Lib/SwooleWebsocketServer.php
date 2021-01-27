<?php

namespace Lib;

use Controllers\TaskController;
use Controllers\WebsocketController;
use PDO;
use PhpAmqpLib\Channel\AMQPChannel;
use Redis;
use Swoole\Atomic;
use Swoole\Server;
use swoole_table;
use swoole_websocket_server;

/**
 * 无法通过reload重新读取此文件内容
 * Class SwooleWebsocketServer
 * @package Lib
 */
class SwooleWebsocketServer extends swoole_websocket_server
{
    /**
     * @var mixed $route 路由配置
     */
    public $route;

    /**
     * @var PDO $pdo mysql连接
     */
    public $pdo;

    /**
     * @var Redis $redis redis连接
     */
    public $redis;

    /**
     * @var AMQPChannel $rabbitMQChannel amqp通道
     */
    public $rabbitMQChannel;

    /**
     * @var swoole_table $table 共享内存
     */
    public $table;

    /**
     * @var Atomic $atomic 无锁计数器
     */
    public $atomic;

    /**
     * @var TaskController $taskController 异步任务对象
     */
    public $taskController;

    /**
     * @var WebsocketController $websocketController websocket控制器
     */
    public $websocketController;

    public function onStart(Server $server)
    {
        echo(date('Y-m-d H:i:s') . ":swoole server start! master pid:{$server->master_pid},manager pid:{$server->manager_pid}\n");
        //设置进程名称
        swoole_set_process_name('swoole_server');
    }

    public function onShutdown(Server $server)
    {
        echo(date('Y-m-d H:i:s') . ":swoole server shutdown\n");
    }
}