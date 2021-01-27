<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * 全局中间件
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Fruitcake\Cors\HandleCors::class,//跨域问题处理(cors) 需要放在最上面
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,//去除请求参数两侧空格
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,//转换空字符串为null
        \App\Http\Middleware\TrustProxies::class,
        /*
         * 三方扩展中间件
         */
    ];

    /**
     * 中间件组
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
//            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,//跨站请求伪造（CSRF）保护
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            /*
             * 频率限制：60次/1分钟,超过限制返回429状态码
             * 对于未登录用户而言，会基于应用域名 + 客户端 IP 地址为标识限制特定客户端
             * 对于已认证用户而言，会基于用户的唯一标识为维度限制特定用户
             * 故存在问题:对于api组来说全局共用1个throttle,并非1个路由1个throttle。可通过自定义节流器实现
             */
//            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * 路由中间件
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,//认证相关(将未登录用户重定向到登录页面)
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,//HTTP的简单认证
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,//认证相关(将已登录用户重定向到认证后页面，未登录则继续原来的请求)
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,//用户多次登录失败时使用(单位登录失败超过指定次数不允许继续发起登录请求，提高系统安全性)
        /*
         * 自定义中间件
         */
        'rbac' => \App\Http\Middleware\Authorize::class,//jwt权限管理
    ];
}
