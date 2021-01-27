<?php

use Illuminate\Http\Request;

//接口路由
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//使用auth中间件,选择api作为认证服务方
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api')->name('user');

/**
 * 测试页面api
 */
Route::group(['prefix' => 'test'], function () {
    //测试路由
    Route::get('/route', function () {
        return '测试路由成功';
    })->name('test.route');
    //测试控制器
    Route::get('/controller', 'TestController@testController')->name('test.controller');
    //测试重定向
    Route::get('/redirect', 'TestController@testRedirect')->name('test.redirect');
    //路由模型绑定 https://xueyuanjun.com/post/9615
    Route::get('/user/{id}', function ($id) {
        $user = \App\User::findOrFail($id);
        return $user->only(['name', 'email']);
    })->name('test.user');
    //兜底路由
    Route::fallback('TestController@testFallback')->name('test.fallback');
    //测试服务提供者
    Route::get('/provider', 'TestController@testProvider')->name('test.provider');
    //测试门面类
    Route::get('/facade', 'TestController@testFacade')->name('test.facade');
    //文件上传 https://learnku.com/docs/laravel/5.5/filesystem/1319
    Route::post('/fileUploads', 'TestController@testFileUploads')->name('test.fileUploads');
    //表单验证
    Route::post('/validate', 'TestController@testValidate')->name('test.validate');
    //数据库相关
    Route::any('/database', 'TestController@testDatabase')->name('test.database');
    //redis
    Route::post('/redis', 'TestController@testRedis')->name('test.redis');
    //elastic_search
    Route::post('/elasticSearch', 'TestController@testElasticSearch')->name('test.elasticSearch');
    //rabbit_mq
    Route::post('/rabbitMq', 'TestController@testRabbitMq')->name('test.rabbitMq');
    //小型社交网站 https://xueyuanjun.com/post/9488


    /*
     * 认证授权(token)
     */
    //注册
    Route::post('/register', 'Auth\RegisterController@register')->name('test.register');
    //登录
    Route::post('/login', 'Auth\LoginController@login')->name('test.login');
    //登出
    Route::post('/logout', 'Auth\LoginController@logout')->name('test.logout');
    //认证校验
    Route::post('/auth', 'TestController@testAuth')->name('test.auth');
    //授权校验
    Route::post('/permission', 'TestController@testPermission')->middleware('auth:api')->name('test.permission');
    //授权校验(策略类)
    Route::post('/policy', 'TestController@testPolicy')->middleware('auth:api')->name('test.policy');

    /*
     * 认证授权(jwt)
     * 原理文档:https://learnku.com/articles/17883
     */
    //登录
    Route::post('/jwt/login', 'JwtAuthController@login')->name('test.jwt.login');
    //刷新token
    Route::post('/jwt/refresh', 'JwtAuthController@refresh')->name('test.jwt.refresh');
    //登出
    Route::post('/jwt/logout', 'JwtAuthController@logout')->name('test.jwt.logout');
    //认证校验
    Route::post('/jwt/auth', 'JwtAuthController@auth')->name('test.jwt.auth');
    //权限校验
    Route::post('/jwt/permission', 'JwtAuthController@permission')->name('test.jwt.permission');

    //请求
    Route::get('/request', 'TestController@testRequest')->name('test.request');
    //响应
    Route::get('/response', 'TestController@testResponse')->name('test.response');
    //触发artisan命令
    Route::get('/artisan', 'TestController@testArtisan')->name('test.artisan');
    //缓存 https://learnku.com/docs/laravel/5.5/cache/1316
    Route::get('/cache', 'TestController@testCache')->name('test.cache');
    //集合 https://learnku.com/docs/laravel/5.5/collections/1317
    Route::get('/collection', 'TestController@testCollection')->name('test.collection');
    //队列(异步解耦)
    Route::get('/queue', 'TestController@testQueue')->name('test.queue');
    //辅助函数
    Route::get('/helper', 'TestController@testHelper')->name('test.helper');
    //事件
    Route::get('/event', 'TestController@testEvent')->name('test.event');
    //广播
    Route::get('/broadcast', 'TestController@testBroadcast')->name('test.broadcast');

    /*
     * 通知 https://learnku.com/docs/laravel/5.5/notifications/1322
     */
    //获取通知
    Route::get('/notifications', 'TestController@testNotifications')->name('test.notifications');
    //标记为已读通知
    Route::post('/markNotification', 'TestController@testMarkNotification')->name('test.markNotification');

    //邮件
    Route::get('/mail', 'TestController@testMail')->name('test.mail');
    //短信
    Route::get('/sms', 'TestController@testSms')->name('test.sms');
});

/**
 * 测试资源控制器（RESTFUL风格:通过 GET 方法获取资源,通过 POST 方法创建资源,通过 PUT/PATCH 方法更新资源,通过 DELETE 方法删除资源）
 */
//Route::resource('resource','ResourceController');
//以下等同
Route::group(['prefix' => 'resource'], function () {
    //展示全部
    Route::get('/', 'ResourceController@index')->name('resource.all');
    //创建
    Route::get('/create', 'ResourceController@create')->name('resource.create');
    //更新
    Route::post('/store', 'ResourceController@store')->name('resource.store');
    //展示单个(只读)
    Route::get('/{id}', 'ResourceController@show')->name('resource.single');
    //展示单个(可编辑)
    Route::get('/{id}/edit', 'ResourceController@edit')->name('resource.singleEdit');
    //更新单个
    Route::put('/{id}', 'ResourceController@update')->name('resource.singleUpdate');
    //删除单个
    Route::delete('/{id}', 'ResourceController@destroy')->name('resource.singleDelete');
});

/**
 * dingo api路由
 * 每次请求需要添加请求头 Accept: application/Accept: application/x.laravel.v1+json
 */
$api = app(\Dingo\Api\Routing\Router::class);

$api->version('v1', function (\Dingo\Api\Routing\Router $api) {
    //测试配置环境
    $api->get('/test', function () {
        return response()->json('dingo api 测试成功');
    })->name('dingo_api.test');
    //在 Dingo API 中,如果基于控制器方法定义 API 路由,需要指定完整的控制器命名空间
    $api->group([
        'prefix' => 'test',
        'namespace' => '\App\Http\Controllers\DingoApi'
    ], function (\Dingo\Api\Routing\Router $api) {
        //响应构建器
        $api->get('/response', 'ApiTestController@testResponse')->name('dingo_api.test.response');
        /*
         * Fractal(dingo内置league/fractal)用于数据转化
         */
        //转化器
        $api->get('/fractal', 'ApiTestController@testFractal')->name('dingo_api.test.fractal');
        //在响应构建器中使用转化器
        $api->get('/responseFractal', 'ApiTestController@testResponseFractal')->name('dingo_api.test.responseFractal');

        //认证&权限(dingo api) basic auth&jwt
        $api->get('/authentication', 'ApiTestController@testAuthentication')->name('dingo_api.test.authentication')->middleware('api.auth');
        //节流(dingo api)默认 1 个小时内针对应用该中间件的路由发起 60 次请求
        $api->get('/throttleReal', 'ApiTestController@testThrottle')->name('dingo_api.test.throttleReal');
        $api->get('throttle', ['middleware' => 'api.throttle', 'limit' => 3, 'expires' => 1, function () {
            //内部请求 https://xueyuanjun.com/post/19683.html
            $dispatcher = app(\Dingo\Api\Dispatcher::class);
            return $dispatcher->version('v1')->get('test/throttleReal');
        }]);
    });
});