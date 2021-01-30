<?php

/**
 * chat_socket类似功能+优化
 * 1.nginx+swoole http服务路由转发&处理请求
 * 2.worker交互mysql&worker交互redis&task推送数据至rabbitmq
 * 3.动态控制消费:消费者&消费监听者&触发停止脚本
 * 4.swoole websocket分发数据
 * 5.swoole table数据共享&atomic 无锁计数器
 * 6.剔除死连接机制
 * 7.平滑重启&停止服务
 *
 * 8.考虑mysql&redis&rabbitmq服务断线重连问题
 * 9.该文件是面向过程的,但实际上涉及到进程与进程间交互,后续优化成面向对象编程
 *
 * 10.从复现问题角度打印关键信息&压测
 * 11.被动缓存改为主动缓存&优化缓存数据结构
 * 12.变更配置文件重启不需要重启即可加载&学习laravel配置模式
 */

//此处配置成127.0.0.1之后无法通过外网访问服务,在nginx上做了反向代理
use Controllers\TaskController;
use Controllers\WebsocketController;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Swoole\Atomic;
use Swoole\Server;
use Swoole\Server\Task;
use Swoole\WebSocket\Frame;
use const Task\RABBITMQ_PUSH;
use const Task\UNDEFINED;

$server = new swoole_websocket_server('127.0.0.1', 9501);

$server->set([
    'reactor_num' => 2 * 4,//cpu核数*4
//    'dispatch_mode' => 3,//worker争抢reactor分发的数据包 todo http服务设置为3,websocket设置为2(多端口监听)
    'worker_num' => 2 * 4 * 1,//根据响应时间调整,reactor_num倍数
    'task_worker_num' => 2 * 4 * 1 * 1,//根据任务投递&消耗速度调整,worker_num倍数
    'task_enable_coroutine' => true,//开启task进程协程
//    'daemonize' => 1,//守护进程化(调试的时候可以关闭)
//    'log_file' => __DIR__ . '/../logs/swoole_server.log',//指定服务日志(调试的时候可以关闭)
    'log_level' => SWOOLE_LOG_INFO,//错误日志打印等级
//    'group' => 'www',//进程所属用户组
//    'user' => 'www',//进程所属用户
    'pid_file' => __DIR__ . '/../logs/swoole_server.pid',//设置pid文件
    'reload_async' => true,//开启平滑重启
    'max_wait_time' => 10,//平滑重启时worker进程最大停止时间
    'open_cpu_affinity' => true,//将reactor线程&worker进程绑定到同一个核(生产环境不推荐) taskset -p 进程ID 查看是否绑定在同一个核
    'open_websocket_close_frame' => true,//开启后,可在 onMessage 回调中接收到客户端或服务端发送的关闭帧,开发者可自行对其进行处理
    'heartbeat_check_interval' => 10,//心跳检测检查周期,单位 秒(服务不会主动向客户端发送心跳包,仅是检测上次发送数据时间,若超过限制将切断连接)
    'heartbeat_idle_time' => 12,//连接最大允许空闲的时间,单位 秒
]);

$server->on('Start', function (Server $server) {
    echo(date('Y-m-d H:i:s') . ":swoole server start! master pid:{$server->master_pid},manager pid:{$server->manager_pid}\n");
    //设置进程名称
    swoole_set_process_name('swoole_server');
});

$server->on('Shutdown', function (Server $server) {
    echo(date('Y-m-d H:i:s') . ":swoole server shutdown\n");
});

$server->on('ManagerStart', function (Server $server) {
    swoole_set_process_name('swoole_manager');
});

/*
 * 加载内容尽量放在这里处理,因为此处平滑重启后会重新调用
 */
$server->on('WorkerStart', function (Server $server, int $workerId) {
    //判断当前是Worker进程还是Task进程
    if ($server->taskworker === true) {
        swoole_set_process_name('swoole_task_' . $workerId);
    } else {
        swoole_set_process_name('swoole_worker_' . $workerId);
    }

    /*
     * 此处加载的文件只能通过重启worker&task重新加载
     */
    //自动加载文件(onRequest事件new对象且文件未引用时会调用到这里)
    spl_autoload_register(static function ($class) {
        //将\替换为/
        $baseClassPath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        $classPath = __DIR__ . '/' . $baseClassPath;
        if (is_file($classPath)) {
            require $classPath;
        }
    });

    //todo 事件内容抽出到新的文件中,不然平滑重启不会重新加载本文件中的代码
    //加载常量
    require 'env/const.php';
    //加载扩展服务(代码抽离后该位置需要改动)
    require __DIR__ . '/../../../vendor/autoload.php';

    //加载路由配置
    $server->route = require 'env/route.php';
    //加载数据库配置
    $database = require 'env/database.php';

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

    //task进程需要加载
    if ($server->taskworker === true) {
        //加载amqp配置
        $amqp = require 'env/amqp.php';
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
    //todo 后续做一个ioc容器,这样就不要每次都注入到server对象里面了(ps:需要考虑协程共用连接问题,此处每开启一个进程会新建一个连接,但是连接却绑定的是server对象？)
});

$server->on('WorkerError', function (Server $server, int $workerId, int $workerPid, int $exitCode, int $signal) {
    $msg = json_encode([
        'worker_id' => $workerId,
        'worker_pid' => $workerPid,
        'exit_code' => $exitCode,
        'signal' => $signal
    ]);
    echo(date('Y-m-d H:i:s') . ':' . $msg . "\n");
});

/*
 * 此处关闭websocket连接&清理table数据
 * Server本应是\Swoole\Server,此处因为业务逻辑强制转化成\Swoole\WebSocket\Server
 * mysql&redis&rabbitmq不回收,经测试未出现 重启前连接依旧存在情况
 * onWorkerExit事件按文档描述配置,经测试无法调起
 * 服务异常关闭无法进入此回调
 */
$server->on('WorkerStop', function (\Swoole\WebSocket\Server $server, int $workerId) {
    if ($server->taskworker !== true) {
        echo "worker stopped id:{$workerId}\n";
        /**
         * @var swoole_table $table
         */
        $table = $server->table;
        $data = [
            'status' => 0,
            'message' => 'server reloading,please retry later'
        ];
        //实际情况是:第一个worker停止的时候关闭了所有worker的websocket连接
        foreach ($table as $key => $value) {
            //exist 方法仅判断是否为 TCP 连接,无法判断是否为已完成握手的 WebSocket 客户端
            if ($server->isEstablished($key)) {
                $server->push($key, json_encode($data));
            }
            $server->close($key, true);
            $table->del($key);
        }
    } else {
        echo "task stopped id:{$workerId}\n";
    }
});

/*
 * http服务
 * 不接受 onConnect/onReceive 回调设置
 * 额外接受 1 种新的事件类型 onRequest
 */
$server->on('Request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) use ($server) {
    try {
        $route = $server->route;
        //检查路由是否存在
        $requestUri = trim($request->server['request_uri'], '/');
        if (!isset($route[$requestUri])) {
            throw new Exception('route not found', 404);
        }

        //解析路由配置
        list($action, $method) = $route[$requestUri];
        $method = strtoupper($method);
        $requestMethod = $request->server['request_method'];

        //检查路由请求方法是否允许
        if ($method !== 'ANY' && $method !== $requestMethod) {
            throw new Exception('method not allowed', 405);
        }

        //获取文件名&方法名
        $position = strpos($action, '@');
        //此处class的命名空间必须与实际控制器的命名空间一致
        $class = 'Controllers\\' . substr($action, 0, $position);
        $function = substr($action, $position + 1);

        //检查方法是否存在
        $controller = new $class($server);
        if (!method_exists($class, $function)) {
            throw new Exception('action not found', 500);
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
});

/*
 * 设置了task_worker_num必须设置onTask回调
 * 在task进程中调用
 * 未开启 task进程协程
 * function onTask(\Swoole\Server $server, int $taskId, int $srcWorkerId, mixed $data);
 * 开启 task进程协程 api如下(接收参数值以及触发onFinish的api都与不开启时不同)
 */
$server->on('Task', function (Server $server, Task $task) {
    /**
     * @var Controllers\TaskController $taskController
     */
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
            case UNDEFINED:
            default:
                echo("warning:task type not defined\n");
                break;
        }
    }

    //返回执行结果到worker进程,在onFinish事件里接收值
    $task->finish($taskStart);
});

/*
 * 与文档有出入:设置了task_worker_num并非要设置onFinish
 * 在worker进程中被调用(与下发task任务的worker进程是同一个)
 */
$server->on('Finish', function (Server $server, int $taskId, string $data) {
    //计算异步任务总共耗时
    $taskSpend = microtime(true) - $data;
    echo("worker {$server->worker_id} task {$taskId} spend {$taskSpend} s\n");
});

/*
 * websocket,客户端与服务器建立连接并完成握手后执行回调
 * 127.0.0.1:9501/ws/ 通过nginx9500端口完成转发
 */
$server->on('Open', function (\Swoole\WebSocket\Server $server, \Swoole\Http\Request $request) {
    /**
     * @var swoole_table $table
     */
    $table = $server->table;

    $user = $request->get['user'];
    if (empty($user)) {
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
});

/*
 * swoole服务(基于http服务)
 * 必须有 onMessage 回调设置
 * 此处代码不会随平滑重启更新
 * 此处由worker进程与websocket进行交互,一旦通讯成功将由同一个进程处理同一个连接
 */
$server->on('Message', function (\Swoole\WebSocket\Server $server, Frame $frame) {
    //收到客户端或服务端发送的关闭帧自定义处理内容(此处dispatch_mode不能设置为1/3,底层会屏蔽 onConnect/onClose 事件)
    if ($frame->opcode === 0x08) {
        /**
         * @var swoole_table $table
         */
        $table = $server->table;

        //清理table数据
        $table->del($frame->fd);
        echo "client close websocket: fd {$frame->fd},code {$frame->code},reason {$frame->reason}\n";
    } else {
        /**
         * @var Controllers\WebsocketController $websocketController
         */
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
});

$server->on('Close', function (Server $server, int $fd, int $reactorId) {
    /**
     * @var swoole_table $table
     */
    $table = $server->table;

    //清理table数据
    $table->del($fd);

    //服务端主动关闭连接
    if ($reactorId === -1) {
        echo "server close tcp:fd {$fd}\n";
    } else {//此处无法辨认出客户端主动断开连接还是因为死连接导致的断开
        echo "tcp closed:fd {$fd}\n";
    }
});

//设置共享内存大小
$table = new swoole_table(1024);
//设置共享内存列(字段)
$table->column('user', swoole_table::TYPE_INT, 4);
//创建共享内存
$tableFlag = $table->create();
if ($tableFlag === false) {
    exit("create table fail\n");
}
$server->table = $table;

//设置无锁计数器
$atomic = new Atomic();
$server->atomic = $atomic;

//设置默认时区
date_default_timezone_set('PRC');

$server->start();