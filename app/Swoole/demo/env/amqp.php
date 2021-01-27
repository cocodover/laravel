<?php

/*
 * amqp连接配置
 * 参数含义: https://www.cnblogs.com/brady-wang/p/11168152.html
 */
return [
    'chat' => [
        'connection' => [
            'host' => '127.0.0.1',
            'port' => 5672,
            'user' => 'root',
            'password' => 'root',
//            'vhost' => '/',//虚拟机(每个用户且只能访问被指派vhost内的队列&交换机)
        ],
//        'exchange' => [
//            'name' => 'chat',//交换机名称
//            'type' => 'direct',//交换机类型(direct&fanout&topic)
//            'passive' => false,//只查询不创建新交换机
//            'durable' => true,//重启时重建交换机(持久化)
//            'auto_delete' => false,//自动销毁(若绑定队列都不在使用了销毁)
//            'internal' => false,//内部交换机(只允许内部投递)
//            'nowait' => false,//等待执行结果(生产者=>交换机)
////            'arguments' => [],//交换机属性
////            'ticket' => null,
//        ],
        'queue' => [
            'name' => 'chat',//队列名称
            'producer_passive' => false,//只查询不创建新队列(若队列存在返回队列信息)
            'consumer_passive' => false,
            'durable' => true,//重启时重建队列(持久化)
            'exclusive' => false,//排他队列(true=>若进程断开连接,队列也会被销毁)
            'auto_delete' => false,//自动销毁(若消费者不再订阅了销毁)
//            'nowait' => false,//等待执行结果(生产者=>队列)
//            'arguments' => [],//队列属性(可设置队列内数据存活时间&队列存活时间&队列优先级等等)
//            'ticket' => null,
        ],
//        'queue_bind' => [
//            'queue' => 'chat',//队列名称
//            'exchange' => 'chat',//交换机名称
//            'routing_key' => '',//路由名(实际此处应该为binding_key,routing_key在basic_publish中设置)
//            'nowait' => false,//等待执行结果(交换机=>队列)
////            'arguments' => [],//额外参数
////            'ticket' => null,
//        ],
        'publish' => [
            'exchange' => '',//交换机名称
            'routing_key' => 'chat',//路由名
//            'mandatory' => false,//true=>若没有队列能接受该消息,将消息发送到失败投递记录中
//            'immediate' => false,//废弃
//            'ticket' => null,
        ],
        'consume' => [
            'queue' => 'chat',//队列名称
            'consumer_tag' => '',//消费者标签,用于退订消息
            'no_local' => false,//这个功能属于AMQP的标准,但是rabbitMQ并没有做实现
            'no_ack' => false,//消息确认机制(false=>消息需要确认)
            'exclusive' => false,//排他消费者
            'nowait' => false,//等待执行结果(队列=>消费者)
        ]
    ],
];