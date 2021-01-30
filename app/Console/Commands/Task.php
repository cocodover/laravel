<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Task extends Command
{
    /**
     * 运行命令：
     * php artisan task:handle abc -r 5 -t7
     * php artisan task:handle abc --retry 5 --timeout=7
     * @var string
     */
    protected $signature = 'task:handle
    {name : 任务名称}
    {--r|retry : 是否重试}
    {retry_times=3 : 重试次数}
    {--t|timeout=5 : 超时时间}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试任务处理(参数获取与传递)';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        //参数取值
        $this->info('input name is ' . $this->argument('name'));
        $this->info('input retry_times is ' . $this->argument('retry_times'));

        //选项取值
        if ($this->option('retry')) {
            $this->info('input retry is ' . $this->option('retry'));
        }
        if ($this->option('timeout')) {
            $this->info('input timeout is ' . $this->option('timeout'));
        }

        $this->info('========================');
        //获取用户输入数据
        $userName = $this->ask("What's your name ?");
        $this->info("I knew, I knew you're the great {$userName}.");

        //隐藏用户输入
        $userSecret = $this->secret('Tell me your secret,I will keep that~');
        $this->info("NO!NO!NO! '{$userSecret}' is not a secret.");

        //让用户确认信息
        if ($this->confirm('Want to hear my secret?')) {
            $this->info('I am a robot.');
        } else {
            $this->info('Ok, I will keep that.');
        }

        //自动完成提示(windows下无法展示)
        $this->anticipate('Are you listening?', [
            'yes',
            'no'
        ]);

        //提供用户选择
        $userCountry = $this->choice('So, where do you come from?', [
            'China',
            'Other'
        ]);

        //展示表格
        $headers = ['name', 'country'];
        $data = [
            ['artisan', 'Other'],
            [$userName, $userCountry],
        ];
        $this->table($headers, $data);

        //展示进度条
        $this->info('Writing your record...');
        $totalUnits = 10;
        $this->output->progressStart($totalUnits);

        $i = 0;
        while ($i++ < $totalUnits) {
            sleep(2);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info('Finish!');
    }
}
