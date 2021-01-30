<?php

namespace Lib;

use Controllers\TaskController;
use Controllers\WebsocketController;
use Error;
use Exception;
use PDO;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Redis;
use RuntimeException;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server\Task;
use Swoole\WebSocket\Frame;
use const Task\RABBITMQ_PUSH;
use const Task\UNDEFINED;
use const Task\WEBSOCKET_PUSH;

class SwooleWorker
{
    /**
     * @var SwooleWebsocketServer $server swoole服务
     */
    public $server;

    public function __construct(SwooleWebsocketServer $server)
    {
        $this->server = $server;
    }

    public function onWorkerStart(SwooleWebsocketServer $server, int $workerId)
    {
        //判断当前是Worker进程还是Task进程
        if ($server->taskworker === true) {
            swoole_set_process_name('swoole_task_' . $workerId);
        } else {
            swoole_set_process_name('swoole_worker_' . $workerId);
        }

        //加载常量
        require dirname(__DIR__) . '/env/const.php';
        //加载路由配置
        $server->route = require dirname(__DIR__) . '/env/route.php';
        //加载数据库配置
        $database = require dirname(__DIR__) . '/env/database.php';

        //mysql连接池(由于虚拟机上为了模拟公司php环境,装了php7.0+swoole4.3.4,不支持连接池)建议按照文档要求安装php7.1-swoole4.0+
        $dsn = $database['mysql']['connection'];
        $mysqlUsername = $database['mysql']['username'];
        $mysqlPassword = $database['mysql']['password'];
        //这里绑定到server上好处是:许多回调事件会有server这个参数,能直接获取到
        $server->pdo = new PDO($dsn, $mysqlUsername, $mysqlPassword);

        //redis连接池(问题同上)
        $redisConnection = $database['redis']['connection'];
        $redisPort = $database['redis']['port'];
        $redisPassword = $database['redis']['password'];
        $redis = new Redis();
        $redis->connect($redisConnection, $redisPort);
        $redis->auth($redisPassword);
        $server->redis = $redis;

        if ($server->taskworker === true) {//task进程需要加载
            //加载amqp配置
            $amqp = require dirname(__DIR__) . '/env/amqp.php';
            /*
             * amqp连接
             * 快速理解:https://blog.csdn.net/weixin_37641832/article/details/83270778
             * 使用扩展连接rabbitmq
             * 使用文档:https://github.com/rabbitmq/rabbitmq-tutorials/tree/master/php
             * 此处业务场景:多个task进程(生产者)推送消息给队列,多个消费者直接从队列中获取数据并加以处理。考虑到业务场景不再使用发布/订阅模式(不使用交换机)
             */
            //rabbitMQ连接
            $rabbitMQConnection = new AMQPStreamConnection(
                $amqp['chat']['connection']['host'],
                $amqp['chat']['connection']['port'],
                $amqp['chat']['connection']['user'],
                $amqp['chat']['connection']['password']
            );
            //发布者<=>rabbitMQ通道
            $rabbitMQChannel = $rabbitMQConnection->channel();
            //声明队列
            $rabbitMQChannel->queue_declare(
                $amqp['chat']['queue']['name'],
                $amqp['chat']['queue']['producer_passive'],//若不存在创建新队列
                $amqp['chat']['queue']['durable'],//重启时重建队列
                $amqp['chat']['queue']['exclusive'],//不排他队列
                $amqp['chat']['queue']['auto_delete']//不自动销毁
            );
            $rabbitMQChannel->config = $amqp;
            $server->rabbitMQChannel = $rabbitMQChannel;

            //异步任务对象(减少每次创建对象类开销)
            $server->taskController = new TaskController($server);
        } else {//worker进程需要加载
            //加载websocket控制器
            $server->websocketController = new WebsocketController();
        }
    }

    /*
     * 此处关闭websocket连接&清理table数据
     * mysql&redis&rabbitmq不回收,经测试未出现 重启前连接依旧存在情况
     * onWorkerExit事件按文档描述配置,经测试无法调起
     */
    public function onWorkerStop(SwooleWebsocketServer $server, int $workerId)
    {
        if ($server->taskworker !== true) {
            echo "worker stopped id:{$workerId}\n";
            $table = $server->table;
            $data = [
                'status' => 0,
                'message' => 'server reloading,please retry later'
            ];
            //实际情况是:第一个worker停止的时候关闭了所有worker的websocket连接
            foreach ($table as $fd => $row) {
                //此处fd应为int,但存入到table的时候被转化为了string
                $fd = (int)$fd;
                /*
                 * exist判断 WebSocket 客户端是否存在并且状态为 Active 状态
                 * isEstablished 检查连接是否为有效的 WebSocket 客户端连接
                 * 但实际情况是代码判断连接有效,但推送的时候连接已经无效了
                 */
                if ($server->exist($fd) && $server->isEstablished($fd)) {
                    $server->push($fd, json_encode($data));
                }
                $server->close($fd, true);
                $table->del($fd);
            }
        } else {
            echo "task stopped id:{$workerId}\n";
        }
    }

    public function onRequest(Request $request, Response $response)
    {
        $server = $this->server;
        try {
            $route = $server->route;
            //检查路由是否存在
            $requestUri = trim($request->server['request_uri'], '/');
            if (!isset($route[$requestUri])) {
                throw new RuntimeException('route not found', 404);
            }

            //解析路由配置
            list($action, $method) = $route[$requestUri];
            $method = strtoupper($method);
            $requestMethod = $request->server['request_method'];

            //检查路由请求方法是否允许
            if ($method !== 'ANY' && $method !== $requestMethod) {
                throw new RuntimeException('method not allowed', 405);
            }

            //获取文件名&方法名
            $position = strpos($action, '@');
            //此处class的命名空间必须与实际控制器的命名空间一致
            $class = 'Controllers\\' . substr($action, 0, $position);
            $function = substr($action, $position + 1);

            //检查方法是否存在
            $controller = new $class($server);
            if (!method_exists($class, $function)) {
                throw new RuntimeException('action not found', 500);
            }

            //分发请求至controller
            return $controller->$function($request, $response);
        } catch (Error $error) {
            /*
             * 捕获异常和错误
             * 参考文档:https://wiki.swoole.com/#/getting_started/notice?id=%e6%8d%95%e8%8e%b7%e5%bc%82%e5%b8%b8%e5%92%8c%e9%94%99%e8%af%af
             */
            echo "ERROR\n";
            $response->status(500);
            $response->end($error->getMessage());
        } catch (Exception $exception) {
            echo "EXCEPTION\n";
            $response->status($exception->getCode());
            $response->end($exception->getMessage());
        }
    }

    /*
     * 本类中唯一一个在task进程中调用的方法
     */
    public function onTask(SwooleWebsocketServer $server, Task $task)
    {
        $taskController = $server->taskController;

        //计时
        $taskStart = microtime(true);
        if (isset($task->data['data'])) {
            //根据任务类型进行对应处理
            $data['type'] = $task->data['type'] ?? UNDEFINED;
            switch ($data['type']) {
                case RABBITMQ_PUSH:
                    $taskController->rabbitMQPush($task->data['data']);
                    break;
                case WEBSOCKET_PUSH:
                    $taskController->websocketPush($task->data['data']);
                    break;
                case UNDEFINED:
                default:
                    echo("warning:task type not defined\n");
                    break;
            }
        }

        //返回执行结果到worker进程,在onFinish事件里接收值
        $task->finish($taskStart);
    }

    /*
     * 与文档有出入:设置了task_worker_num并非要设置onFinish
     */
    public function onFinish(SwooleWebsocketServer $server, int $taskId, string $data)
    {
        //计算异步任务总共耗时
        $taskSpend = microtime(true) - $data;
        echo("worker {$server->worker_id} task {$taskId} spend {$taskSpend} s\n");
    }

    /*
     * 127.0.0.1:9501/ws/ 通过nginx9500端口完成转发
     * 请求示例:ws://172.16.58.164:9500/ws/?user=1
     * 请求参数示例:{"type":"ping"}
     */
    public function onOpen(SwooleWebsocketServer $server, Request $request)
    {
        $table = $server->table;

        $user = $request->get['user'];
        if (empty($user) || strlen($user) > 16) {
            $data = [
                'status' => 0,
                'message' => 'request params error'
            ];
            $server->push($request->fd, json_encode($data));
            //强制关闭链接,丢弃发送队列中的数据
            $server->close($request->fd, true);
            return;
        }

        //绑定本次长连接的用户id
        $table->set($request->fd, ['user' => $user]);
        $data = [
            'status' => 1,
            'message' => 'connect success'
        ];
        $server->push($request->fd, json_encode($data));
    }

    public function onMessage(SwooleWebsocketServer $server, Frame $frame)
    {
        //收到客户端或服务端发送的关闭帧自定义处理内容(此处dispatch_mode不能设置为1/3,底层会屏蔽 onConnect/onClose 事件)
        if ($frame->opcode === 0x08) {
            $table = $server->table;

            //清理table数据
            $table->del($frame->fd);
            echo "client close websocket: fd {$frame->fd},code {$frame->code},reason {$frame->reason}\n";
        } else {
            $websocketController = $server->websocketController;

            //websocket服务路由
            $data = json_decode($frame->data, true);
            try {
                $method = $data['type'];
                switch ($method) {
                    //心跳检测机制(若长时间无数据传输,连接会自动断开,默认为1分钟)
                    case 'ping':
                        $server->push($frame->fd, 'pong');
                        break;
                    default:
                        return $websocketController->$method($server, $frame);
                }
            } catch (Error $error) {
                $data = [
                    'status' => 0,
                    'message' => $error->getMessage()
                ];
                $server->push($frame->fd, json_encode($data));
            } catch (Exception $exception) {
                $data = [
                    'status' => 0,
                    'message' => $exception->getMessage()
                ];
                $server->push($frame->fd, json_encode($data));
            }
        }
    }

    public function onClose(SwooleWebsocketServer $server, int $fd, int $reactorId)
    {
        if ($server->isEstablished($fd)) {
            //清理table数据
            $server->table->del($fd);

            if ($reactorId === -1) {//服务端主动关闭连接
                echo "server close websocket:fd {$fd}\n";
            } else {
                echo "websocket closed:fd {$fd}\n";
            }
        } else if ($reactorId === -1) {//服务端主动关闭连接
            echo "server close tcp:fd {$fd}\n";
        } else {
            echo "tcp closed:fd {$fd}\n";
        }
    }
}