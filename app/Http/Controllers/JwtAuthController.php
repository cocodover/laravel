<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * 认证逻辑：
 * 1.登录时前端调用登录接口获取token
 * 2.登录成功后每次请求头带Authorization(Bearer token),并且成功通过auth:jwt中间件完成认证
 * 3.若token有效期过期,调用refresh接口对过期token进行token刷新,刷新后旧token将无法使用,若刷新失败要求用户重新登录
 * 4.登出调用登出接口,服务端将token纳入黑名单
 * Class JwtAuthController
 * @package App\Http\Controllers
 */
class JwtAuthController extends Controller
{
    /**
     * 构造函数:可以在这里定义中间件,但是通常在路由层进行定义
     * JwtAuthController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:jwt', ['except' => ['login', 'refresh']]);
        $this->middleware('rbac', ['only' => ['permission']]);
    }

    /**
     * JWT登录
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $credentials = request(['name', 'password']);

        //获取token
        if (!$token = auth('jwt')->attempt($credentials)) {
            return response()->json(['error' => '登录认证失败'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * 刷新token(刷新后旧token无法使用)
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        try {
            //两种写法效果等同
//        $token=auth('jwt')->refresh();
            $token = JWTAuth::refresh(JWTAuth::getToken());
        } catch (Exception $exception) {
            return response()->json(['error' => '刷新时间过期,请重新登录'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * JWT登出
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth('jwt')->logout();

        return response()->json(['message' => '成功登出']);
    }

    /**
     * 检查认证是否有效
     * @return JsonResponse
     */
    public function auth(): JsonResponse
    {
        return response()->json('用户身份认证通过');
    }

    /**
     * 检查授权是否有效
     * @return JsonResponse
     */
    public function permission(): JsonResponse
    {
        return response()->json('用户权限认证通过');
    }

    /**
     * 成功响应token格式
     * @param $token
     * @return JsonResponse
     */
    private function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}
