<?php

//http服务占用9501端口
$server = new swoole_http_server('localhost', 9501);

//服务启动时返回响应
$server->on('start', function (swoole_http_server $server) {
    echo "Swoole http server is started at http://127.0.0.1:9501\n";
});

//收到请求时返回响应
$server->on('request', function (swoole_http_request $request, swoole_http_response $response) {
    //处理谷歌浏览器额外请求 favicon.ico 问题
    if ($request->server['path_info'] === '/favicon.ico' || $request->server['request_uri'] === '/favicon.ico') {
        $response->end();
        return;
    }

    //根据路由分发请求
//    list($controller, $action) = explode('/', trim($request->server['request_uri'], '/'));
    //根据 $controller, $action 映射到不同的控制器类和方法
//    (new $controller)->$action($request, $response);

    //可以获取请求参数，也可以设置响应头和响应内容
    $response->header('Content-Type', 'text/plain');
    $response->end("Hello World\n");
});

//启动 HTTP 服务器
$server->start();