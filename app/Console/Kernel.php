<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * 手动注册命令
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * 自定义任务执行计划
     * 需要在linux crontab上部署 "* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1"(每分钟执行1次)
     * 学习文档 https://learnku.com/docs/laravel/5.5/scheduling/1325
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*
         * 技术打卡
         */
        $schedule->command('command:clock')->weekdays()->dailyAt('9:28');//上班提醒
        $schedule->command('command:clock')->weekdays()->dailyAt('21:00')->skip(function () {
            //周一到周四下班提醒
            return Carbon::now()->dayOfWeek === 5;
        });
        $schedule->command('command:clock')->fridays()->dailyAt('20:00');//周五下班提醒

        /*
         * 运营打卡
         */
        $schedule->command('command:clock_for_yunying')->mondays()->dailyAt('9:28');//周一上班提醒
        $schedule->command('command:clock_for_yunying')->weekdays()->dailyAt('10:28')->skip(function () {
            //周二到周五上班提醒
            return Carbon::now()->dayOfWeek === 1;
        });
        $schedule->command('command:clock_for_yunying')->weekdays()->dailyAt('22:00')->skip(function () {
            //周一到周四下班提醒
            return Carbon::now()->dayOfWeek === 5;
        });
        $schedule->command('command:clock_for_yunying')->fridays()->dailyAt('18:00');//周五下班提醒

        /*
         * 排期统计提醒
         */
        $schedule->command('command:schedule_reminder')->mondays()->dailyAt('9:30');

        /*
         * 吃饭统计提醒
         */
//        $schedule->command('command:eating_census')->weekdays()->dailyAt('10:30');
    }

    /**
     * 注册命令
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        //扫描app/Console/Commands目录下所有文件进行注册
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
