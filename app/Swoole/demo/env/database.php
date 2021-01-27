<?php

/*
 * 数据库连接配置
 */
return [
    'mysql' => [
        'connection' => 'mysql:host=127.0.0.1;dbname=laravel;charset=utf8',
        'username' => 'root',
        'password' => 'root'
    ],
    'redis' => [
        'connection' => '127.0.0.1',
        'port' => 6379,
        'password' => null
    ]
];