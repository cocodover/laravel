<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class TestJob implements ShouldQueue
{
    /*
     * SerializesModels使得 Eloquent 模型在处理任务时可以被优雅地序列化和反序列化
     */
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //任务最大尝试次数
    public $tries = 3;

    //任务运行的超时时间
    public $timeout = 10;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->fail(new Exception('手动抛出异常'));
        //fail调用之后不会主动结束任务
        return;
        Log::info('异步队列消费成功!');
    }

    /**
     * 处理失败任务
     * 需要通过handle方法中$this->fail()手动触发
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        Log::info(json_encode([
            'notice' => 'job failed',
            'message' => $exception->getMessage()
        ]));
    }
}
