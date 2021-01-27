<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Overtrue\EasySms\EasySms;

class EasySmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //如果一个类没有基于任何接口那么就没有必要将其绑定到容器(以下情况仅仅是让容器中取出的实例默认使用配置参数)
        $this->app->singleton(EasySms::class, function ($app) {
            return new EasySms(config('easysms'));
        });
    }
}
