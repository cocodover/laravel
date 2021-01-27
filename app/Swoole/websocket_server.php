<?php

//websocket服务占用9503端口
$server = new swoole_websocket_server('localhost', 9503);

//建立连接时触发
$server->on('open', function (swoole_websocket_server $server, $request) {
    echo "server: handshake success with fd{$request->fd}\n";
});

//收到消息时触发推送
$server->on('message', function (swoole_websocket_server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    $server->push($frame->fd, 'this is server');
});

//关闭 WebSocket 连接时触发
$server->on('close', function ($server, $fd) {
    echo "client {$fd} closed\n";
});

//启动 WebSocket 服务器
$server->start();