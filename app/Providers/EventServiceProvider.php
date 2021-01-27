<?php

namespace App\Providers;

use App\Events\TestEvent;
use App\Listeners\AddPaginationStatusToResponse;
use App\Listeners\TestListener;
use App\Subscribers\RecordEventsSubscriber;
use Dingo\Api\Event\ResponseWasMorphed;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * 注册监听者
     * 1个事件可以绑定多个监听者
     * 绑定事件和监听者(事件=>监听者)
     *
     * 生成在 EventServiceProvider 中列出的所有事件和监听器(不包括已存在的)
     * php artisan event:generate
     *
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
//        'App\Events\TestEvent' => [
//            'App\Listeners\TestListener',
//        ],
        //效果等同,以上代码用于event:generate之前生成文件用
        TestEvent::class => [
            TestListener::class
        ],

        /*
         * dingo api
         * ResponseIsMorphing（转化前触发）和 ResponseWasMorphed（转化后触发）事件
         */
        ResponseWasMorphed::class => [
            //dingo api响应转化数据(分页)后响应头添加状态
            AddPaginationStatusToResponse::class
        ]
    ];

    /**
     * 注册订阅者
     * 1个订阅者内部可订阅多个事件
     * @var array
     */
    protected $subscribe = [
        //获取到用户实例时记录日志,需要到模型类绑定自定义事件
        RecordEventsSubscriber::class
    ];

    /**
     * 手动注册事件
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
