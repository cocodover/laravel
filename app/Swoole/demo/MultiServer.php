<?php

use Lib\SwooleManager;
use Lib\SwooleWebsocketServer;
use Lib\SwooleWorker;
use Swoole\Atomic;

/**
 * chat_socket类似功能+优化
 * 1.nginx+swoole http服务路由转发&处理请求
 * 2.worker交互mysql&worker交互redis&task推送数据至rabbitmq
 * 3.动态控制消费:消费者&消费监听者&触发停止脚本
 * 4.swoole websocket分发数据
 * 5.swoole table数据共享&atomic 无锁计数器
 * 6.剔除死连接机制
 * 7.平滑重启&停止服务
 * 8.面向对象编程
 *
 * 9.考虑mysql&redis&rabbitmq服务断线重连问题
 * 10.从复现问题角度打印关键信息&压测
 */
class MultiServer
{
    /**
     * @var SwooleWebsocketServer $server swoole服务(master进程)
     */
    private $server;

    /**
     * @var SwooleManager $manager manager进程
     */
    private $manager;

    /**
     * @var SwooleWorker $worker worker进程
     */
    private $worker;

    public function __construct()
    {
        $this->server = new SwooleWebsocketServer('127.0.0.1', 9501);
        $this->server->set([
            'reactor_num' => 2 * 4,//cpu核数*4
//            'dispatch_mode' => 3,//worker争抢reactor分发的数据包 todo http服务设置为3,websocket设置为2(多端口监听)
            'worker_num' => 2 * 4 * 1,//根据响应时间调整,reactor_num倍数
            'task_worker_num' => 2 * 4 * 1 * 1,//根据任务投递&消耗速度调整,worker_num倍数
            'task_enable_coroutine' => true,//开启task进程协程
//            'daemonize' => 1,//守护进程化(调试的时候可以关闭)
//            'log_file' => dirname(__DIR__) . '/logs/swoole_server.log',//指定服务日志(调试的时候可以关闭)
            'log_level' => SWOOLE_LOG_INFO,//错误日志打印等级
//            'group' => 'www',//进程所属用户组
//            'user' => 'www',//进程所属用户
            'pid_file' => dirname(__DIR__) . '/logs/swoole_server.pid',//设置pid文件
            'reload_async' => true,//开启平滑重启
            'max_wait_time' => 10,//平滑重启时worker进程最大停止时间
            'open_cpu_affinity' => true,//将reactor线程&worker进程绑定到同一个核(生产环境不推荐) taskset -p 进程ID 查看是否绑定在同一个核
            'open_websocket_close_frame' => true,//开启后,可在 onMessage 回调中接收到客户端或服务端发送的关闭帧,开发者可自行对其进行处理
            'heartbeat_check_interval' => 10,//心跳检测检查周期,单位 秒(服务不会主动向客户端发送心跳包,仅是检测上次发送数据时间,若超过限制将切断连接)
            'heartbeat_idle_time' => 12,//连接最大允许空闲的时间,单位 秒
        ]);

        //master进程
        $this->server->on('Start', [$this, 'onStart']);
        $this->server->on('Shutdown', [$this, 'onShutdown']);

        //manager进程
        $this->server->on('ManagerStart', [$this, 'onManagerStart']);
        $this->server->on('WorkerError', [$this, 'onWorkerError']);

        //worker进程
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->server->on('WorkerStop', [$this, 'onWorkerStop']);

        $this->server->on('Request', [$this, 'onRequest']);
        $this->server->on('Task', [$this, 'onTask']);
        $this->server->on('Finish', [$this, 'onFinish']);

        $this->server->on('Open', [$this, 'onOpen']);
        $this->server->on('Message', [$this, 'onMessage']);
        $this->server->on('Close', [$this, 'onClose']);

        //设置共享内存大小
        $table = new swoole_table(1024);
        //设置共享内存列(字段)
        $table->column('user', swoole_table::TYPE_STRING, 16);//底层对内存长度做了对齐处理,字符串长度必须为 8 的整数倍
        //创建共享内存
        $tableFlag = $table->create();
        if ($tableFlag === false) {
            exit("create table fail\n");
        }
        $this->server->table = $table;

        //设置无锁计数器
        $atomic = new Atomic();
        $this->server->atomic = $atomic;
    }

    /**
     * 启动后在主进程（master）的主线程回调此函数
     * @param $server
     */
    public function onStart($server)
    {
        $this->server->onStart($server);
    }

    /**
     * 此事件在 Server 正常结束时发生
     * @param $server
     */
    public function onShutdown($server)
    {
        $this->server->onShutdown($server);
    }

    /**
     * 当管理进程启动时触发此事件
     * @param $server
     */
    public function onManagerStart($server)
    {
        $this->manager = new SwooleManager();
        $this->manager->onManagerStart($server);
    }

    /**
     * 此事件在 Worker 进程 / Task 进程 启动时发生
     * 这里创建的对象可以在进程生命周期内使用
     * @param $server
     * @param $workerId
     */
    public function onWorkerStart($server, $workerId)
    {
        //此处重新加载文件(这样在平滑重启后能重新读取worker文件内容)
        require_once __DIR__ . '/Lib/SwooleWorker.php';
        $this->worker = new SwooleWorker($this->server);
        $this->worker->onWorkerStart($server, $workerId);
    }

    /**
     * 当 Worker/Task 进程发生异常后会在 Manager 进程内回调此函数
     * @param $server
     * @param $workerId
     * @param $workerPid
     * @param $exitCode
     * @param $signal
     */
    public function onWorkerError($server, $workerId, $workerPid, $exitCode, $signal)
    {
        $this->manager->onWorkerError($server, $workerId, $workerPid, $exitCode, $signal);
    }

    /**
     * 此事件在 Worker 进程终止时发生
     * 在此函数中可以回收 Worker 进程申请的各类资源
     * 服务异常关闭无法进入此回调
     * @param $server
     * @param $workerId
     */
    public function onWorkerStop($server, $workerId)
    {
        $this->worker->onWorkerStop($server, $workerId);
    }

    /**
     * HTTP服务特有
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response)
    {
        $this->worker->onRequest($request, $response);
    }

    /**
     * 在 task 进程内被调用
     *
     * worker 进程可以使用 task 函数向 task_worker 进程投递新的任务
     * 当前的 Task 进程在调用 onTask 回调函数时会将进程状态切换为忙碌,这时将不再接收新的 Task
     * 当 onTask 函数返回时会将进程状态切换为空闲然后继续接收新的 Task
     *
     * 设置了task_worker_num必须设置onTask回调
     *
     * 未开启 task进程协程:
     * function onTask(\Swoole\Server $server, int $taskId, int $srcWorkerId, mixed $data);
     * 开启 task进程协程 api如下(接收参数值以及触发onFinish的api都与不开启时不同)
     * @param $server
     * @param $task
     */
    public function onTask($server, $task)
    {
        $this->worker->onTask($server, $task);
    }

    /**
     * 此回调函数在 worker 进程被调用(与下发task任务的worker进程是同一个)
     * 当 worker 进程投递的任务在 task 进程中完成时,task 进程会通过 Swoole\Server->finish() 方法将任务处理的结果发送给 worker 进程
     * @param $server
     * @param $taskId
     * @param $data
     */
    public function onFinish($server, $taskId, $data)
    {
        $this->worker->onFinish($server, $taskId, $data);
    }

    /**
     * swoole服务(基于http服务),必须有 onMessage 回调设置
     * 当 WebSocket 客户端与服务器建立连接并完成握手后会回调此函数
     * 此处由worker进程与websocket进行交互,一旦通讯成功将由同一个进程处理同一个连接
     * @param $server
     * @param $request
     */
    public function onOpen($server, $request)
    {
        $this->worker->onOpen($server, $request);
    }

    /**
     * 当服务器收到来自客户端的数据帧时会回调此函数
     * @param $server
     * @param $frame
     */
    public function onMessage($server, $frame)
    {
        $this->worker->onMessage($server, $frame);
    }

    /**
     * TCP 客户端连接关闭后,在 worker 进程中回调此函数
     * @param $server
     * @param $fd
     * @param $reactorId
     */
    public function onClose($server, $fd, $reactorId)
    {
        $this->worker->onClose($server, $fd, $reactorId);
    }

    /**
     * swoole服务启动
     */
    public function start()
    {
        $this->server->start();
    }
}