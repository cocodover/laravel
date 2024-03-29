<?php

namespace App\Console\Commands;

use App\Http\Tools\Warning\DingTalk;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClockInAndOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:clock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '上下班打卡提醒';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $time = Carbon::now()->toTimeString();
        $time = substr($time, 0, 2);
        if ($time < 12) {
            //上班打卡提醒
            DingTalk::warning('clock_in_and_out', '亲爱的小伙伴们，上班打卡啦~');
        } else {
            //下班打卡提醒
            DingTalk::warning('clock_in_and_out', '亲爱的小伙伴们，下班打卡啦~');
        }
    }
}
