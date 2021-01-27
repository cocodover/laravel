<?php

namespace Controllers;

use Lib\SwooleWebsocketServer;
use Swoole\WebSocket\Frame;

/**
 * websocket处理类
 * Class WebsocketController
 * @package Controllers
 */
class WebsocketController
{
    /**
     * 测试websocket路由转发
     * @param  $server
     * @param  $frame
     */
    public function test(SwooleWebsocketServer $server, Frame $frame)
    {
        $atomic = $server->atomic;
        $count = $atomic->add();
        echo "message count:{$count}\n";
        $server->push($frame->fd, 'websocket push success');
    }
}