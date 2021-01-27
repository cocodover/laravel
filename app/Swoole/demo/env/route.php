<?php

/*
 * url=>[controller@function,method]
 */
return [
    /*
     * 测试
     */
    'api/test/route' => ['ApiController@testRoute', 'get'],//测试路由
    'api/test/database' => ['ApiController@testDatabase', 'get'],//测试数据库连接
    'api/test/task' => ['ApiController@testTask', 'post'],//测试异步任务投递
    'api/test/websocket' => ['ApiController@testWebsocket', 'post'],//测试分发websocket数据
];
