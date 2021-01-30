<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //用于用户登录和退出
    use AuthenticatesUsers;

    //单位时间内最大登录尝试次数
    protected $maxAttempts = 5;
    //单位时间值(分钟)
    protected $decayMinutes = 30;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * 自定义登陆时使用的用户名字段
     * @return string
     */
//    public function username()
//    {
//        return 'name';
//    }

    /**
     * 自定义登录方法(token登录)
     * @param Request $request
     * @return JsonResponse|Response
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        //登录
        if ($this->attemptLogin($request)) {
            /**
             * @var $user User
             */
            $user = $this->guard()->user();
            //更新token
            $user->updateToken();

            return response()->json([
                'data' => $user->toArray(),
            ]);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * 自定义登出方法(token登录)
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        //登出
        $user = Auth::guard('api')->user();
        
        /**
         * @var $user User
         */
        if ($user) {
            //清理token
            $user->cleanToken();
        }

        return response()->json(['data' => '登出成功']);
    }
}
