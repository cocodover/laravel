<?php

namespace App\Subscribers;

use App\Events\RecoredFindingEvent;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

class RecordEventsSubscriber
{
    /**
     * 为订阅者注册监听器
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            RecoredFindingEvent::class,
            __CLASS__ . '@onRecordFinding'
        );
    }

    /**
     * 处理用户删除前事件
     * @param RecoredFindingEvent $event
     */
    public function onRecordFinding(RecoredFindingEvent $event)
    {
        Log::info(json_encode([
            'notice' => 'record retrieved',
            'user_id' => $event->record->user_id,
            'uri' => $event->record->uri
        ]));
    }
}