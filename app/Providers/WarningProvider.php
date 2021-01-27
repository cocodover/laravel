<?php

namespace App\Providers;

use App\Http\Tools\Warning\DingTalk;
use App\Http\Tools\Warning\WarningInterface;
use Illuminate\Support\ServiceProvider;

class WarningProvider extends ServiceProvider
{
    /**
     * 服务提供者加是否延迟加载.
     * @var bool
     */
    protected $defer = true;

    /**
     * 该方法在所有服务提供者被注册以后才会被调用
     * Bootstrap the application services.
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * 注册服务提供者
     * Register the application services.
     * @return void
     */
    public function register()
    {
        //在定义绑定关系的时候使用的是匿名函数,这样做的好处是用到该依赖时才会实例化,从而提升了应用的性能。
        $this->app->singleton(WarningInterface::class, function () {
            switch (config('params.warning_type')) {
                case 'dingtalk':
                    return new DingTalk();
                    break;
                default:
                    return new DingTalk();
                    break;
            }
        });
    }
}
