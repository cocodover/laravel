<?php

//easysms配置
return [
    //HTTP请求的超时时间(秒)
    'timeout' => 5.0,

    //默认发送配置
    'default' => [
        //调用策略,默认:顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        //默认使用的短信服务
        'gateways' => [
            'juhe'
        ],
    ],

    //短信服务配置
    'gateways' => [
        //错误日志
        'errorlog' => [
            'file' => storage_path('logs/sms'),
        ],

        //短信服务配置
        'juhe' => [
            'app_key' => 'd0771738e85d7827b64a2c9ca02f4277',
        ],
    ],

];