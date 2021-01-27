<?php

namespace App\Events;

use App\Models\Record;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RecoredFindingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    //record模型类
    public $record;

    /**
     * 创建一个事件实例
     * RecoredFindingEvent constructor.
     * @param Record $record
     */
    public function __construct(Record $record)
    {
        $this->record = $record;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
