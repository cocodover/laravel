<?php

namespace Controllers;

use Lib\SwooleWebsocketServer;
use Swoole\Http\Request;
use Swoole\Http\Response;
use const Task\RABBITMQ_PUSH;
use const Task\WEBSOCKET_PUSH;

/**
 * 接收请求入口
 * Class ApiController
 * @package Controllers
 */
class ApiController
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
     * 测试路由转发
     * @param Request $request
     * @param Response $response
     */
    public function testRoute(Request $request, Response $response)
    {
        echo date('Y-m-d H:i:s') . "\n";
        $response->end('success!');
    }

    /**
     * 测试数据库连接
     * @param Request $request
     * @param Response $response
     */
    public function testDatabase(Request $request, Response $response)
    {
        //查询数据库(同步阻塞方式)
        $cacheKey = __METHOD__;
        //查询redis缓存
        $redis = $this->server->redis;
        if ($redis->exists($cacheKey)) {
            $users = $redis->get($cacheKey);
            //标识查询数据库类型
            $database = 'redis';
        } else {
            //查询mysql数据库
            $pdo = $this->server->pdo;
            $statement = $pdo->query('SELECT `id` , `name` FROM `users` ORDER BY `id` ASC LIMIT 3');
            $users = [];
            foreach ($statement as $data) {
                $users[] = [
                    'id' => $data['id'],
                    'name' => $data['name']
                ];
            }
            $users = json_encode($users);
            $redis->set($cacheKey, $users, 60);
            $database = 'mysql';
        }

        //设置响应头
        $response->header('content-type', 'application/json');
        $response->header('database', $database);
        $response->end($users);
    }

    /**
     * 测试异步任务投递
     * @param Request $request
     * @param Response $response
     */
    public function testTask(Request $request, Response $response)
    {
        //获取原始POST包体(string)
        $chatData = $request->rawContent();
        $chatData = json_decode($chatData, true);

        //投递异步任务到task_worker池(不指定task_id底层根据task进程忙碌状态进行任务投递,若所有task均不空闲,底层轮询投递任务到各个进程)
        //若投递容量超过处理能力,task数据塞满缓存区,导致worker进程发生阻塞(无法接收新的请求,间接导致499问题——客户端等待时间过长断开连接)
        $data = [
            'type' => RABBITMQ_PUSH,//指定任务类型
            'data' => $chatData//投递数据
        ];
        $taskId = $this->server->task($data);
        if ($taskId === false) {
            $response->end('任务投递失败');
        } else {
            $response->end('任务投递成功,task_id:' . $taskId);
        }
    }

    /**
     * 测试分发websocket数据
     * @param Request $request
     * @param Response $response
     */
    public function testWebsocket(Request $request, Response $response)
    {
        //获取POST请求参数
        $message = $request->post['message'];

        //推送至异步任务
        $data = [
            'type' => WEBSOCKET_PUSH,
            'data' => $message
        ];
        $taskId = $this->server->task($data);

        if ($taskId === false) {
            $response->end('推送websocket失败');
        } else {
            $response->end('推送websocket成功,task_id:' . $taskId);
        }
    }
}