<?php

namespace App\Listeners;

use App\Events\TestEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * 启用队列需要实现ShouldQueue接口
 * Class TestListener
 * @package App\Listeners
 */
class TestListener implements ShouldQueue
{
    //手动访问队列(提供队列方法)
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 指定队列连接名称
     * 若队列为异步,需要使用以下命令进行消费
     * php artisan queue:work redis --queue=listeners --tries=3 --timeout=10
     *
     * @var string|null
     */
    public $connection = 'redis';

    /**
     * 指定队列名称
     *
     * @var string|null
     */
    public $queue = 'listeners';

    /**
     * Handle the event.
     *
     * @param TestEvent $event
     * @return void
     */
    public function handle(TestEvent $event)
    {
        $attr = $event->attribute;

        //若参数不为空 且 第一次进入队列 延迟15s处理
        if (!blank($attr) && $this->attempts() === 1) {
            $this->release(15);
            //此处需要return否则会继续执行代码
            return;
        }

        //记录日志
        Log::info(json_encode([
            'notice' => 'test listener',
            'request' => $attr
        ]));
    }
}
