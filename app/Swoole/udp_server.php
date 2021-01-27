<?php

//udp服务占用9504端口
$server = new swoole_server('localhost', 9504, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

//监听数据接收事件
$server->on('Packet', function (swoole_server $server, $data, $clientInfo) {
    $server->sendto($clientInfo['address'], $clientInfo['port'], 'Server：' . $data);
});

//启动服务器
$server->start();

//也可以通过nc命令对服务进行访问
//nc -u 127.0.0.1 9504