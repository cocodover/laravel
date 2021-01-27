<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

/**
 * 用于jwt权限管理
 * Class Authorize
 * @package App\Http\Middleware
 */
class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //options请求直接响应正常
        if ($request->getMethod() === 'OPTIONS') {
            return response('ok');
        }

        //判断用户是否具有权限
        $user = auth('jwt')->user();

        /**
         * @var $user User
         */
        if ($user === null) {
            return response()->json('用户尚未登录,请重新登录!');
        }

        if (!$user->isSuperAdmin()) {
            //获取当前路由名称
            $routeName = $request->route()->getName();

            //获取用户拥有的权限
            $permission = $user->role->permission;

            if (!in_array($routeName, $permission, true)) {
                return response()->json('用户权限受限!');
            }
        }

        return $next($request);
    }
}
