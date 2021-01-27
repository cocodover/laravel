<?php

namespace App\Console\Commands;

use App\Http\Tools\Warning\DingTalk;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EatingCensus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:eating_census';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '技术部恰饭群统计吃饭情况提醒';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $today = Carbon::now()->dayOfWeek;
        if ($today < 5) {
            //周一到周四提醒明天
            DingTalk::warning('eating_census', '明天不吃饭的小盆友请举个爪子~');
        } else {
            //周五提醒下周一
            DingTalk::warning('eating_census', '下周一不吃饭的小盆友请举个爪子~');
        }

    }
}
