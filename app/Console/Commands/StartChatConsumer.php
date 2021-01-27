<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class StartChatConsumer extends Command
{
    //amqp配置
    private $amqp;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start:chat_consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '聊天记录消费者启动脚本';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        //拆分项目后从config文件中获取
        require app_path() . '/Swoole/demo/env/const.php';
        $this->amqp = require app_path() . '/Swoole/demo/env/amqp.php';

        parent::__construct();
    }


    /**
     * 绑定rabbitmq消费数据
     */
    public function handle()
    {
        $this->info('start consume');

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
        $channel->queue_declare(
            $amqp['chat']['queue']['name'],
            $amqp['chat']['queue']['consumer_passive'],//若不存在抛出异常
            $amqp['chat']['queue']['durable'],//重启时重建队列
            $amqp['chat']['queue']['exclusive'],//不排他队列
            $amqp['chat']['queue']['auto_delete']//不自动销毁
        );
        //收到信息后要做的事情
        $callback = function ($message) {
            //数据格式取决于生产者推送
            $data = json_decode($message->body, true);

            //消费数据主体逻辑
            $this->consume($data);

            //找到消息传递的消息通道,返回"确认收到消息"的信息
            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        };

        //告知rabbitmq只有消费者处理并确认了上一个message才分配新的message给他(第二个参数为未确认消息的数量)
        $channel->basic_qos(null, 1, null);

        //开始接收消息
        $channel->basic_consume(
            $amqp['chat']['consume']['queue'],
            $amqp['chat']['consume']['consumer_tag'],
            $amqp['chat']['consume']['no_local'],
            $amqp['chat']['consume']['no_ack'],//消息需要确认
            $amqp['chat']['consume']['exclusive'],//不排他消费
            $amqp['chat']['consume']['nowait'],//等待消费者执行结果确认
            $callback
        );
        //注册信号处理器,专门用来处理SIGUSR1(用户自定义信号):kill -10 进程id 触发
        pcntl_signal(SIGUSR1, function () use ($channel, $connection) {
            //关闭链接
            $channel->close();
            $connection->close();
            $this->info('consume finish');
            //收到该信号进程会终止
        });

        //当通道正常连接后死循环监听
        while ($channel->is_consuming()) {
            try {
                //等待通道推送下一条数据,这里只会消费一条数据
                $channel->wait();
            } catch (\Exception $exception) {
                Log::error(json_encode([
                    'notice' => 'chat consuming error',
                    'message' => $exception->getMessage()
                ]));
            }
        }
    }

    /**
     * 消费数据
     * @param array $data
     */
    private function consume(array $data)
    {
        $this->info('receive data: ' . json_encode($data));
        sleep(3);
        $this->info('data consume finish');
    }
}
