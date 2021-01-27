<?php

namespace App\Providers;

use App\Policies\UserPolicy;
use App\User;
use Dingo\Api\Auth\Auth;
use Dingo\Api\Auth\Provider\Basic;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * 应用的策略映射(模型=>策略类)
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * 注册任意认证/授权服务.
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //若为超级管理员不再检查权限
        Gate::before(function ($user) {
            /**
             * @var $user User
             */
            if ($user->isSuperAdmin()) {
                return true;
            }
            return null;
        });

        //自定义权限(每次请求都要会加载)
        Gate::define('permission', function ($user, $routeName) {
            /**
             * @var $user User
             */
            $permissions = $user->role->permission;

            return in_array($routeName, $permissions, true);
        });

        /*
         * dingo api http基本认证
         */
        // Dingo 认证驱动注册(basic auth)
        $this->app->make(Auth::class)->extend('basic', function ($app) {
            //identifier为认证唯一标识(用户名)
            return new Basic($app['auth'], 'name');
        });
    }
}
