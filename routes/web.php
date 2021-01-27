<?php

//页面路由
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * 测试页面
 */
Route::get('/test', function () {
    return view('test');
})->name('web.test');

/**
 * 认证相关
 * php artisan make:auth 注册认证路由、发布认证视图
 */
//包含login&logout&register&password/reset&password/email等多个路由
Auth::routes();
//登录成功后首页视图
Route::get('/home', 'HomeController@index')->name('web.home');

/**
 * 广播相关
 */
Route::view('newsRoom', 'newsRoom')->name('web.newsRoom');

/**
 * 日志查看
 */
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

/**
 * 全局兜底路由
 */
Route::fallback(function () {
    return '您访问的路由不存在!';
})->name('fallback');