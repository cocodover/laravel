<?php

namespace App\Console\Commands;

use App\Http\Tools\Warning\DingTalk;
use Illuminate\Console\Command;

class ScheduleReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:schedule_reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '小组群排期统计提醒';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        DingTalk::warning('schedule_reminder', '麻烦各位组长大大下班前提供一下小组排期哦~');
    }
}
