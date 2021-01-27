<?php

namespace Task;
/*
 * 任务名称=》任务id
 */
const UNDEFINED = 0;//未定义任务类型
const RABBITMQ_PUSH = 1;//数据推送至rabbitmq
const WEBSOCKET_PUSH = 2;//数据推送至websocket

namespace Amqp;
/*
 * amqp队列名称
 */
//const CHAT = 'chat';//聊天监控数据推送