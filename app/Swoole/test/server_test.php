<?php

/*
 * 服务器
 * https://wiki.swoole.com/#/server/methods
 */

//创建服务(tcp/udp/unixSocket) SWOOLE_BASE模式和nginx一致 若服务出现错误请求将直接结束
//$server = new \Swoole\Server('0.0.0.0', '9502', SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
$server = new \Swoole\WebSocket\Server('0.0.0.0', '9503');

//设置参数 https://wiki.swoole.com/#/server/setting
$server->set([
    'reactor_num ' => 2,//主进程内事件处理线程的数量,服务器cpu核数1-4倍

    'dispatch_mode' => 3,//数据包分发策略(reactor与worker交互)
//    'backlog' => 128,//决定最多同时有多少个等待 accept 的连接(没怎么看懂文档！应该指的是当服务阻塞的时候等待处理的请求数量,此处阻塞指的是worker阻塞)

    'worker_num' => 2,//worker进程数,服务器cpu核数1-1000倍。开的进程越多,占用的内存就会大大增加,而且进程间切换的开销就会越来越大。根据请求响应时间和系统负载来调整
//    'enable_coroutine' => true,//开启异步风格服务器的协程支持
    'max_coroutine' => 3000,//设置当前worker进程最大协程数量
//    'max_request' => 0,//单个worker最大任务数,完成后退出释放所有内存和资源,0表示不会退出。可利用Swoole Tracker发现泄漏的代码
//    'max_connection' => 1024,//最大允许的连接数,默认ulimit -n
    'buffer_output_size' => 2 * 1024 * 1024,//配置发送输出缓存区内存尺寸(即send发送数据指令时,单次最大发送的数据大小)。开启大量 Worker 进程时,将会占用 worker_num * buffer_output_size 字节的内存

    'task_worker_num' => 2,//task进程数,不配置则不启动task进程,若配置必须注册 onTask、onFinish 2 个事件回调函数。根据任务投递速度和任务处理耗时来调整
//    'task_ipc_mode' => 1,//设置 Task 进程与 Worker 进程之间通信的方式
//    'task_max_request' => 0,//设置 task 进程的最大任务数
    'task_tmpdir' => '/tmp',//设置 task 的数据临时目录。如果投递的数据超过 8180 字节,将启用临时文件来保存数据
    'task_enable_coroutine' => true,//开启 Task 协程支持。必须在 enable_coroutine 为 true 时才可以使用
//    'task_use_object' => false,//使用面向对象风格的 Task 回调格式

    'daemonize' => 1,//守护进程化。如果不启用守护进程,当 ssh 终端退出后,程序将被终止运行
    'log_file' => __DIR__ . '/../logs/server_test.log',//指定 Swoole 错误日志文件。开启守护进程模式后,标准输出将会被重定向到这里(包括echo/var_dump/print)。文件不会切分,需要定期清理
    'log_level' => SWOOLE_LOG_INFO,//设置错误日志打印的等级

//    'request_slowlog_file' => false,//开启请求慢日志。只能在同步阻塞的进程里面生效,不能在协程环境用
//    'request_slowlog_timeout' => 2,//设置请求超时时间(秒)
//    'trace_event_worker' => true,//跟踪 Task 和 Worker 进程

    'user' => 'www',//设置 Worker/TaskWorker 子进程的所属用户
    'pid_file' => __DIR__ . '/../logs/server_test.pid',//设置 pid 文件地址

    'reload_async' => true,//设置异步重启开关,Worker 进程会等待异步事件完成后再退出
    'max_wait_time' => 10,//设置 Worker 进程收到停止服务通知后最大等待时间,到达最大等待时间强制杀掉重启进程

    'open_http_protocol' => true,//启用 HTTP 协议处理
    'open_websocket_protocol' => true,//启用 WebSocket 协议处理

    'open_cpu_affinity' => false,//启用 CPU 亲和性设置。将 reactor线程 /worker进程绑定到固定的一个核上。可以避免进程 / 线程的运行时在多个核之间互相切换,提高 CPU Cache 的命中率(查看进程的 CPU 亲和设置:taskset -p 进程ID)

//    'open_tcp_keepalive' => 1,//启用 TCP keepalive,用于踢掉死链接
//    'tcp_keepidle' => 30,//单位秒,连接在 n 秒内没有数据请求,将开始对此连接进行探测。
//    'tcp_keepcount' => 3,//探测的次数,超过次数后将 close 此连接
//    'tcp_keepinterval' => 10,//探测的间隔时间,单位秒
//...
]);

/*
 * 注册事件回调 https://wiki.swoole.com/#/server/events
 * a)onStart/onManagerStart/onWorkerStart 并发执行
 * b)onReceive/onConnect/onClose Worker进程中触发
 * c)onWorkerStart/onWorkerStop Worker/Task进程启动/停止时触发
 * d)onTask Task进程执行;onFinish Worker进程执行
 */

//启动后在master进程的主线程调用(SWOOLE_BASE模式没有master进程)
$server->on('Start', function (\Swoole\Server $server) {
    echo('swoole server start');
    //设置进程名称
    swoole_set_process_name('swoole_server');
    $masterPid = $server->master_pid;
    $managerPid = $server->manager_pid;
});

//在Server正常结束时发生(kill -9 不触发,CTRL+C不触发)
$server->on('Shutdown', function (\Swoole\Server $server) {
    echo('swoole server shutdown');
});

//在Worker进程/Task进程启动时发生,这里创建的对象可以在进程生命周期内使用
$server->on('WorkerStart', function (\Swoole\Server $server, int $workerId) {
    //判断当前是Worker进程还是Task进程
    if ($server->taskworker === true) {
        swoole_set_process_name('swoole_task_' . $workerId);
    } else {
        swoole_set_process_name('swoole_worker_' . $workerId);
    }
});

//Worker进程终止时发生,可以回收Worker进程申请的各类资源
$server->on('WorkerStop', function (\Swoole\Server $server, int $workerId) {
    //todo
});

//仅在开启reload_async特性后有效,等待Worker进程退出后才会执行onWorkerStop事件回调
$server->on('WorkerExit', function (\Swoole\Server $server, int $workerId) {
    //todo
});

//有新的连接进入时,在Worker进程中回调(不适用于dispatch_mode为轮循模式/抢占模式)
//$server->on('Connect', function (\Swoole\Server $server, int $fd, int $reactorId) {
//});

//接收到数据时回调此函数,发生在worker进程中(不适用于dispatch_mode为轮循模式/抢占模式)
//$server->on('Receive', function (\Swoole\Server $server, int $fd, int $reactorId, string $data) {
//});

//TCP客户端连接关闭后，在Worker进程中回调此函数
$server->on('Close', function (\Swoole\Server $server, int $fd, int $reactorId) {
    //服务端主动关闭$reactorId为-1,客户端关闭$reactorId>=0
    if ($reactorId < 0) {
        $server->close($fd);
    }
});

//Task进程被调用时触发
//未开启task_enable_coroutine
//$server->on('Task', function (\Swoole\Server $server, int $taskId, int $srcWorkerId, $data) {
//});
//开启task_enable_coroutine
$server->on('Task', function (Swoole\Server $server, Swoole\Server\Task $task) {
    $start = microtime(true);
    //任务数据
    $data = $task->data;
    //任务执行完毕返回,效果等同于return
    $task->finish(microtime(true) - $start);
});

//在Worker进程被调用,Task进程完成时触发
$server->on('Finish', function (\Swoole\Server $server, int $taskId, string $data) {
    echo("task_{$taskId} spend {$data} s");
});

//当Worker/Task进程发生异常后会在Manager进程内回调此函数
$server->on('WorkerError', function (\Swoole\Server $server, int $workerId, int $workerPid, int $exitCode, int $signal) {
    $msg = json_encode([
        'worker_id' => $workerId,
        'worker_pid' => $workerPid,
        'exit_code' => $exitCode,
        'signal' => $signal
    ]);
    echo($msg);
});

//当Manager进程启动时触发此事件
$server->on('ManagerStart', function (\Swoole\Server $server) {
    swoole_set_process_name('swoole_manager');
});

//当Manager进程结束时触发(此时Task&Worker进程已经被Manager进程回收)
$server->on('ManagerStop', function (\Swoole\Server $server) {
    //todo
});

//Worker进程Reload之前触发此事件,在Manager进程中回调
//$server->on('BeforeReload', function (\Swoole\Server $server) {
//});

//Worker进程Reload之后触发此事件,在Manager进程中回调
//$server->on('AfterReload', function (\Swoole\Server $server) {
//});

//当服务器收到来自客户端的数据帧时会回调此函数(websocket特有)
$server->on('Message', function (\Swoole\WebSocket\Server $server, Swoole\WebSocket\Frame $frame) {
    //todo
});

//启动服务
$bool = $server->start();

if ($bool === false) {
    echo 'server start fail';
}
