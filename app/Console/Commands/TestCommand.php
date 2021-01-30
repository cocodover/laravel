<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;

/**
 * 学习文档 https://learnku.com/docs/laravel/5.5/artisan/1314
 * Class TestCommand
 * @package App\Console\Commands
 */
class TestCommand extends Command
{
    /**
     * 命令指令
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test';

    /**
     * 命令描述
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试脚本';

    /**
     * @throws Exception
     */
    public function handle()
    {
        /*
         * 测试异步任务重试机制(结论：release之后必须return，不然当前脚本会继续执行)
         */
//        TestRelease::dispatch();

        /*
         * 测试修改config
         */
//        $this->info(config('params.warning_type'));
//        config(['params.warning_type' => 'message']);
//        $this->info(config('params.warning_type'));
        /*
         * 测试异步队列中生产新任务放到新队列
         */
//        TestDispatch::dispatch()
//            ->onQueue('queue_a')
//            ->delay(5);
//        Log::info('test command finish');
    }
}
