<?php

use Illuminate\Foundation\Inspiring;

//命令路由
/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

//laravel自带示例:一些激励的言语
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

//命令闭包 php artisan test
Artisan::command('test', function () {
    $this->info('闭包命令测试成功!');
    \Illuminate\Support\Facades\Log::info('闭包命令测试成功!');
})->describe('闭包命令测试');