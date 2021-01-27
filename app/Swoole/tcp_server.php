<?php

//tcp服务占用9502端口
$server = new swoole_server('localhost', 9502);

//监听连接进入事件
$server->on('connect', function (swoole_server $server, $fd) {
    echo "Client:Connect.\n";
});

//监听数据接收事件
//$server->on('receive', function (swoole_server $server, $fd, $fromId, $data) {
//    $server->send($fd, 'Swoole: ' . $data);
//    $server->close($fd);
//});

//监听连接关闭事件
$server->on('close', function (swoole_server $server, $fd) {
    echo "Client:Close.\n";
});

/*
 * 异步任务(worker进程处理主逻辑,task进程处理异步逻辑)
 */
//此回调函数在worker进程中执行
$server->on('receive', function (swoole_server $server, $fd, $fromId, $data) {
    //投递异步任务
    $taskId = $server->task($data);
    echo "Dispatch AsyncTask: id=$taskId\n";
});

//处理异步任务(此回调函数在task进程中执行)
$server->on('task', function (swoole_server $server, $taskId, $fromId, $data) {
    echo "New AsyncTask[id=$taskId]" . PHP_EOL;
    //返回任务执行的结果(给worker进程)
    $server->finish("$data -> OK");
});

//处理异步任务的结果(此回调函数在worker进程中执行)
$server->on('finish', function (swoole_server $server, $taskId, $data) {
    echo "AsyncTask[$taskId] Finish: $data" . PHP_EOL;
});

//设置异步任务的工作进程数量
$server->set([
    'task_worker_num' => 4
]);

//启动 TCP 服务器
$server->start();
