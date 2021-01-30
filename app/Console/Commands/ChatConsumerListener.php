<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ChatConsumerListener extends Command
{
    //amqp配置
    private $amqp;
    //判定堆积最小数量
    const PILE_UP = 1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listen:chat_consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '聊天记录消费者监听脚本(动态启动)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        //拆分项目后从config文件中获取
        $this->amqp = require app_path() . '/Swoole/demo/env/amqp.php';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        //获取amqp参数配置
        $amqp = $this->amqp;

        //rabbitMQ连接
        $connection = new AMQPStreamConnection(
            $amqp['chat']['connection']['host'],
            $amqp['chat']['connection']['port'],
            $amqp['chat']['connection']['user'],
            $amqp['chat']['connection']['password']
        );
        //接收者<=>rabbitMQ通道
        $channel = $connection->channel();
        //声明队列(此处能获取到队列堆积数据)
        $queue = $channel->queue_declare(
            $amqp['chat']['queue']['name'],
            $amqp['chat']['queue']['passive'],
            $amqp['chat']['queue']['durable'],
            $amqp['chat']['queue']['exclusive'],
            $amqp['chat']['queue']['auto_delete']
        );
        $pileUp = $queue[1];
        $this->info("consumer pile up:{$pileUp} messages");

        //若超过判定堆积最小数量,启动消费进程
        if ($pileUp >= self::PILE_UP) {
            $php = 'php';
            $artisan = 'artisan';
            $command = 'start:chat_consumer';
            //后台启动消费者进程
            $shell = "{$php} {$artisan} {$command} >/dev/null &";
            exec($shell);
        }
    }
}
