<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class TestRelease implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //延迟重试时间,尝试次数必须大于该值(此处至少为--tries=5)
    const delaySeconds = [
        1 => 5,
        2 => 10,
        3 => 15,
        4 => 20,
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle()
    {
        $attempts = $this->attempts();
        Log::info(json_encode([
            'notice' => 'entry',
            'attempts' => $attempts
        ]));

        return $this->retry();
    }

    /**
     * 延迟重试
     */
    private function retry()
    {
        $attempts = $this->attempts();
        if (isset(self::delaySeconds[$attempts])) {
            Log::info(json_encode([
                'notice' => 'debug',
                'attempts' => $attempts
            ]));
            $this->release(self::delaySeconds[$attempts]);
        }
    }
}
