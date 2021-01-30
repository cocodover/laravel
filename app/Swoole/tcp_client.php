<?php

//Swoole4以后通过协程来实现异步通信
use Swoole\Coroutine\Client;

go(static function () {
    $client = new Client(SWOOLE_SOCK_TCP);

    //尝试与指定 TCP 服务端建立连接（IP和端口号需要与服务端保持一致，超时时间为0.5秒）
    if ($client->connect('localhost', 9502, 0.5)) {
        //建立连接后发送内容
        $client->send("hello world\n");

        //打印接收到的消息
        echo $client->recv();

        //关闭连接
        $client->close();
    } else {
        echo 'connect failed.';
    }
});

//也可以通过telnet命令对服务进行访问
//telnet 127.0.0.1 9502