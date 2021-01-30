<?php

namespace App\Http\Controllers\DingoApi;

use App\Transformers\UserTransformer;
use App\User;
use Dingo\Api\Http\Response;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiTestController extends ApiController
{
    /**
     * 响应构建器
     * @return mixed
     */
    public function testResponse()
    {
//        $user = User::findOrFail(1);
        //效果等同
//        return response()->json(['id' => 1, 'name' => 'djd']);
//        return $this->response->array($user->toArray());

        //设置响应头
//        $user = User::findOrFail(1);
//        return $this->response->item($user, new UserTransformer())->withHeaders([
//            'Foo' => 'Bar',
//            'Hello' => 'World'
//        ]);

        //设置响应状态码
//        $user = User::findOrFail(1);
//        return $this->response->item($user, new UserTransformer())->setStatusCode(222);

        //错误响应
//        return $this->response->error('not found', 404);

        /*
         * 异常处理
         * https://xueyuanjun.com/post/19673.html
         */
//        throw new DingoApiException(400, '自定义抛出异常');
        //dingo api自带异常
        throw new BadRequestHttpException();
    }

    /**
     * 资源转化器
     * @return string
     */
    public function testFractal(): string
    {
        //获取资源
//        $user = User::findOrFail(1);

        //第一个参数是资源(数据对象),第二个参数是转化器(对数据进行的处理)
//        $resource = new Item($user, function (User $user) {
//            return [
//                'id' => $user->id,
//                'name' => $user->name,
//                'email' => $user->email
//            ];
//        });

//        $fractal = new Manager();
        //序列化器
        //ArraySerializer
//        $fractal->setSerializer(new ArraySerializer());
        //DataArraySerializer(默认)
//        $fractal->setSerializer(new DataArraySerializer());
        //JsonApiSerializer(这种模式转换器必须设置id字段)
//        $fractal->setSerializer(new JsonApiSerializer());

        //自定义转化器
//        $resource = new Item($user, new UserTransformer());

//        return $fractal->parseIncludes('role')->createData($resource)->toJson();

        /*
         * 分页器(若数据总量很大,建议使用游标)
         * 参考文档 https://xueyuanjun.com/post/19665.html
         */
        $paginator = User::paginate(2);
        $tasks = $paginator->getCollection();

        //此处collention是 League\Fractal\Resource\Collection
        $resource = new Collection($tasks, new UserTransformer());
        //适配 Laravel 框架的分页器
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        $fractal = new Manager();
        //获取关联模型数据
        return $fractal->parseIncludes('role')->createData($resource)->toJson();
    }

    /**
     * 在响应构建器中使用转化器
     * @return Response
     */
    public function testResponseFractal(): Response
    {
        //单个资源
//        $user = User::findOrFail(1);

        //请求参数输入 key为 include,value为 role(对应关联模型include名称)能获取到关联数据
//        return $this->response->item($user, new UserTransformer());

        //资源集合
//        $users = User::all();
//        return $this->response->collection($users, new UserTransformer());

        //分页响应
        $users = User::paginate(2);
        return $this->response->paginator($users, new UserTransformer());
    }

    /**
     * 测试认证 basic auth&jwt
     * 两个都可以使用
     * basic auth注册在AuthServiceProvider中
     * jwt注册在config/api的auth中,jwt的登录方法与laravel-jwt共用,路由为api/test/jwt/login
     * @return mixed
     */
    public function testAuthentication()
    {
//        return $this->response->array('basic auth认证验证通过!');

        //获取认证用户信息
        $user = $this->auth()->user();
        return $this->response->array($user ? $user->toArray() : []);
    }

    /**
     * 节流
     * 在响应头中通过 X-RateLimit-* 字段获取频率限制相关数值
     * Limit 标识总次数
     * Remaining 表示剩余次数
     * Reset 表示有效期
     * @return mixed
     */
    public function testThrottle()
    {
        return $this->response->array('限流测试');
    }
}
