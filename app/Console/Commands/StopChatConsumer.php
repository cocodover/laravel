<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StopChatConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stop:chat_consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '聊天记录消费者停止脚本';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //获取配置
        $php = 'php';
        $artisan = 'artisan';
        $command = 'start:chat_consumer';
        //执行shell命令,将输出结果存入到变量中
        $shell = "ps -ef | grep {$php} | grep {$artisan} | grep {$command} | grep -v grep | awk '{print $2}'";
        //获取指定进程pid
        exec($shell, $pids);
        foreach ($pids as $pid) {
            //发送自定义信号给进程
            posix_kill($pid, SIGUSR1);
            //上述代码等同以下,但是上面代码需要安装posix(yum install --enablerepo=remi --enablerepo=remi-php70 php-process)
//            exec("kill -10 {$pid}");
        }
    }
}
