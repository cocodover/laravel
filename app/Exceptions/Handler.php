<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * 日志记录错误
     * @param Exception $exception
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * 渲染各种异常的浏览器输出
     * @param Request $request
     * @param Exception $exception
     * @return JsonResponse|Response
     */
    public function render($request, Exception $exception)
    {
        //没有通过校验的请求
        if ($exception instanceof ValidationException) {
            //errors获取验证错误提示
            return response()->json($exception->errors());
        }

        //获取不存在的资源
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'error' => '未找到相关资源'
            ], 404);
        }

        return parent::render($request, $exception);
    }

    /**
     * 自定义未通过认证(中间件)的响应
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|RedirectResponse|\Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        //认证服务方为jwt时自定义报错内容
        if (in_array('jwt', $exception->guards(), true)) {
            return response()->json(['error' => '用户未登录,请重新登录!'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
