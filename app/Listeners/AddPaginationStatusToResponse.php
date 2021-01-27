<?php

namespace App\Listeners;

use Dingo\Api\Event\ResponseWasMorphed;

class AddPaginationStatusToResponse
{
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
     * ResponseWasMorphed（dingo-api数据转化后触发）事件
     * @param ResponseWasMorphed $event
     */
    public function handle(ResponseWasMorphed $event)
    {
        //判断是分页数据结构,设置响应头
        if (isset($event->content['meta']['pagination'])) {
            $event->response->headers->set('status', true);
        }
    }
}
