<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * 启动广播需要实现ShouldBroadcast接口(同步推送用ShouldBroadcastNow接口)
 * 特别注意:使用异步广播时,假如event修改了也需要重启queue:work
 * 参考文档 https://learnku.com/docs/laravel/5.5/broadcasting/1315
 * Class NewsPushEvent
 * @package App\Events
 */
class NewsPushEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * 自定义队列名称
     * php artisan queue:work redis --queue=news --tries=1 --timeout=3
     *
     * @var string
     */
    public $broadcastQueue = 'news';

    /**
     * NewsPushEvent constructor.
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * 指定广播频道
     * Channel(public)公共频道 无需权限
     * PrivateChannel私有频道 需要权限
     * PresenceChannel存在频道 需要权限
     *
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('news');
    }

    /**
     * 自定义事件的广播名称
     *
     * @return string
     */
//    public function broadcastAs()
//    {
//        return 'NewsPushEvent';
//    }

    /**
     * 自定义广播数据
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return ['msg' => $this->message];
    }
}
