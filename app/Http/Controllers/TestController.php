<?php

namespace App\Http\Controllers;

use App\Events\NewsPushEvent;
use App\Events\TestEvent;
use App\Facades\WarningFacade as Warning;
use App\Http\Requests\FileUploadRequest;
use App\Http\Tools\Warning\WarningInterface;
use App\Jobs\TestJob;
use App\Mail\UserRegistered;
use App\User;
use Exception;
use Generator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Iterator;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use SMS;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Web控制器 的职责就是真实应用的传输层:仅负责收集用户请求数据,然后将其传递给处理方
 * Class TestController
 * @package App\Http\Controllers
 */
class TestController extends Controller
{
    /**
     * 测试控制器
     * @return RedirectResponse|Redirector
     */
    public function testController()
    {
        return redirect('api/test/redirect');
    }

    /**
     * 测试重定向
     * @return string
     */
    public function testRedirect(): string
    {
        return '测试重定向成功';
    }

    /**
     * 测试兜底函数
     * @param Request $request
     * @return string
     */
    public function testFallback(Request $request): string
    {
        return $request->getBaseUrl() . $request->getUri() . ' ' . '路径不存在';
    }

    /**
     * 测试服务提供者
     */
    public function testProvider()
    {
        /*
         * 从服务容器中获取服务实例
         * 若新增了provider需要到config/app.php里面进行注册
         * 契约在laravel中实际的意思就是interface
         */
        $service = app()->make(WarningInterface::class);
        //调用服务实例方法
        $service::warning('TEST', 'test provider');
    }

    /**
     * 测试门面类
     */
    public function testFacade()
    {
        /*
         * 门面自动解析实例
         * 门面类的使用大部分基于服务提供者——要看facade返回的是个实例还是字符串,若为字符串需要在容器中进行绑定
         */
        Warning::warning('default', 'test facade');
    }

    /**
     * 测试表单验证
     * @param FileUploadRequest $request
     * @return ResponseFactory|Response
     */
    public function testValidate(FileUploadRequest $request)
    {
        return response('validate success');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function testDatabase(Request $request): JsonResponse
    {
        /*
         * 原生SQL
         */
        //运行原生SQL Statement(创建、删除、修改数据表操作)
//        $bool = DB::statement('ALTER TABLE `laravel`.`users` ADD INDEX `name`(`name`) USING BTREE');

        //原生查询语句
//        $id = $request->input('id') ?: 1;
//        $user = DB::select('select * from users where id = :id;', ['id' => $id]);

        //原生插入语句(不会自动维护时间字段)
//        $name = str_random(10);
//        $email = $name . '@gmail.com';
//        $password = bcrypt('secret');
//        $bool = DB::insert('insert into users (name,email,password) values (?,?,?)', [$name, $email, $password]);

        //原生更新语句
//        $affectedRows = DB::update('update users set name = ? where id = ?', ['djd', 1]);

        //原生删除语句
//        $affectedRows = DB::delete('delete from users where id = 6');

        /*
         * 查询构建器
         */
        //查询
//        $users = DB::table('users')->first();

        //增加(不会自动维护时间字段)
//        $name = str_random(10);
//        $email = $name . '@gmail.com';
//        $password = bcrypt('secret');
//        $id = DB::table('users')->insertGetId([
//            'name' => $name,
//            'email' => $email,
//            'password' => $password
//        ]);

        //修改
//        $affectedRows = DB::table('users')->where('id', 2)->update(['name' => 'Kolys']);

        //删除
//        $affectedRows = DB::table('users')->where('id', 7)->delete();

        /*
         * 复杂查询
         */
        //pluck
//        $name = DB::table('users')->orderBy('id')->pluck('name', 'id');

        //chunk(分批从mysql获取数据,而非取完数据分隔处理)
//        DB::table('users')->orderBy('id')->chunk(2, function ($users) {
//            foreach ($users as $user) {
//                echo $user->name . "\r\n";
//            }
//        });

        //is null
//        $emptyToken = DB::table('users')->whereNull('remember_token')->get();

        //对比字段值
//        $data = DB::table('users')->whereColumn('created_at', 'updated_at')->get();

        /*
         * Eloquent模型
         */
        //查询
//        $data = User::all();

        //增加
//        $user = new User;
//        $user->name = 'haswell';
//        $user->email = 'haswell@gmail.com';
//        $user->password = bcrypt('secret');
//        $bool = $user->save();

        //修改
//        $user = User::query()->where('name', 'haswell')->first();
//        if (!empty($user)) {
//            $user->name = 'Haswell';
//            $bool = $user->save();
//        }

        //删除
//        $affectedRows = User::query()->where('id', 8)->delete();

        /*
         * 批量赋值
         */
        //增加
//        $user = new User($request->all());
//        $bool = $user->save();

        //修改
//        $user = User::find(9);
//        if (!empty($user)) {
//            $user->fill($request->all());
//            $bool = $user->save();
//        }

        //删除（新增deleted字段用于软删除）软删除之后用正常查询无法查找到数据
//        $user = User::find(9);
//        if (!empty($user)) {
//            $affectedRows = $user->delete();
//            if ($user->trashed()) {
//                return response()->json('用户记录删除');
//            }
//        }

        //查询被软删除的数据
//        $user = User::withTrashed()->find(9);

        //恢复误删数据
//        $bool = User::onlyTrashed()->where('name', 'wiki')->restore();

        //物理删除记录（强制删除）
//        $user = User::find(9);
//        $bool = $user->forceDelete();

        //修改器
//        $record = new Record([
//            'user_id' => 1,
//            'ip' => $request->getClientIp(),
//            'uri' => $request->path(),
//            'method' => $request->method(),
//            'route' => $request->route()->getName(),
//            'request' => $request->all() ?: null
//        ]);
//        $bool = $record->save();

        //访问器
//        $record = Record::find(1);
//        $ip = $record->ip_address;

        //数据类型转化
//        $record = new Record([
//            'user_id' => 1,
//            'ip' => $request->getClientIp(),
//            'uri' => $request->path(),
//            'method' => $request->method(),
//            'route' => $request->route()->getName(),
//            'request' => $request->all() ?: null
//        ]);
//        $bool = $record->save();
//        $recordData = Record::find(2);
//        $dataRequest = $recordData->request;

        //全局作用域(过滤器)
//        $record = Record::find(1);

        //移除全局作用域
//        $record = Record::withoutGlobalScope(UserScope::class)->find(1);

        //局部作用域(过滤器)
//        $record = Record::modify()->get();

        //动态作用域(过滤器)
//        $record = Record::modify()->createdTime('2020-08-09')->get();

        //监听事件
//        Record::find(1);

        //一对多
//        $user = User::query()->findOrFail(1);
//        /**
//         * @var $user User
//         */
//        $role = $user->role;
//        $name = $role->name;

//        $role = Role::query()->findOrFail(0);
//        /**
//         * @var $role Role
//         */
//        $users = $role->user;
//        $names = $users->pluck('name');

        //渴求式加载(查询比实际+1次,性能高)
//        $users = User::query()
//            ->with('role')
//            ->whereNotNull('remember_token')
//            ->get();
//        $data = [];
//        foreach ($users as $user) {
//            $data[] = [
//                'user_name' => $user->name,
//                'role_name' => $user->role->name
//            ];
//        }

        //别名
//        $user = User::query()
//            ->select(['name as user_name'])
//            ->where('id', 1)
//            ->first();

        //分页展示
//        $users = User::query()->paginate(2);

        //临时测试
        $this->testDB($request);
        return response()->json('SQL执行完毕');
    }

    /**
     * 数据库写法测试
     * @param Request $request
     */
    private function testDB(Request $request)
    {
    }

    /**
     * redis api使用测试
     * @param Request $request
     */
    public function testRedis(Request $request)
    {

    }

    /**
     * elastic search使用测试
     * @param Request $request
     */
    public function testElasticSearch(Request $request)
    {

    }

    /**
     * rabbitMq使用测试
     * @param Request $request
     */
    public function testRabbitMq(Request $request)
    {

    }

    /**
     * 文件上传测试
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function testFileUploads(Request $request)
    {
        //判断是否有文件上传
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            //若文件上传成功
            if ($file->isValid()) {
                //获取文件名
//                $originalName = $file->getClientOriginalName();
                //获取扩展名
                $originalExtension = $file->getClientOriginalExtension();
                //获取文件大小
//            $size = $file->getClientSize();
                //获取文件mime类型
//            $mimeType = $file->getClientMimeType();
                //获取临时上传文件绝对路径
                $realpath = $file->getRealPath();

                //生成文件唯一名称(md5_file返回字符串长度32,uniqid返回字符串长度13,熵值长度10,预计数据库设置长度70)
                $fileName = uniqid(md5_file($file), true) . '.' . $originalExtension;
                //上传文件
                $flag = Storage::disk('public')->put($fileName, file_get_contents($realpath));
                if ($flag === true) {
                    //web server默认指向项目的public目录,需要 php artisan storage:link 对文件夹进行软链处理
                    $url = asset('/storage/' . $fileName);
                    return response('上传成功!访问文件地址' . $url);
                }
                return response('上传失败...');
            }
        }
        return response('上传文件为空,请重新上传文件!');
    }

    /**
     * 校验认证功能
     * @return JsonResponse
     */
    public function testAuth(): JsonResponse
    {
        //指定api为认证服务方(token验证)
        $id = Auth::guard('api')->id();
        $user = Auth::guard('api')->user();
        $isLogin = Auth::guard('api')->check();
        return response()->json([
            'id' => $id,
            'user' => $user,
            'isLogin' => $isLogin,
        ]);
    }

    /**
     * 检查路由访问权限
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function testPermission(Request $request)
    {
        //获取当前用户
        $user = Auth::guard('api')->user();
        //获取当前路由名称
        $routeName = $request->route()->getName();

        //该写法必须在路由层使用auth中间件
//        if (Gate::allows('permission', $routeName)) {
//            return response('授权测试通过!');
//        }

        //指定用户获取是否拥有权限
        if (Gate::forUser($user)->allows('permission', $routeName)) {
            return response('授权测试通过!');
        }

        return response('该用户无权限!');
    }

    /**
     * 检查用户数据更新权限
     * @return ResponseFactory|Response
     */
    public function testPolicy()
    {
        //获取当前用户
        $user = Auth::guard('api')->user();
        if ($user === null) {
            return response('该用户无修改权限!');
        }

        //获取其他用户
        $anonymous = User::find(3);

        //判断当前用户是否有更新数据权限
        /**
         * @var $user User
         */
        if ($user->can('update', $anonymous)) {
            return response('该用户可以更新数据!');
        }

        return response('该用户无修改权限!');
    }

    /**
     * 测试请求api
     * @param Request $request
     * @return JsonResponse
     */
    public function testRequest(Request $request): JsonResponse
    {
        $uri = $request->getPathInfo();
        $uri = trim($uri, '/');
        return response()->json($uri);
    }

    /**
     * 测试响应api
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function testResponse(Request $request): BinaryFileResponse
    {
        $path = storage_path('app/public') . '/' . $request->input('file_name');
        $fileName = 'data_' . date('Y-m-d') . '.' . pathinfo($path)['extension'];

        //测试下载功能
        return response()->download($path, $fileName);
    }

    /**
     * 测试触发artisan命令
     * @return JsonResponse
     */
    public function testArtisan(): JsonResponse
    {
        Artisan::call('test');
        return response()->json('success');
    }

    /**
     * 测试缓存api
     * @return JsonResponse
     */
    public function testCache(): JsonResponse
    {
        //这里注意用cache门面存储的键名是有前缀的
        Cache::put('cache_test', 'success', 1);
        $value = Cache::get('cache_test', 'fail');
        return response()->json($value);
    }

    /**
     * 测试集合
     * @return JsonResponse
     */
    public function testCollection(): JsonResponse
    {
        $collection = collect(['a' => 1, 'b' => 2, 'c' => 3]);
        $count = $collection->count();
        return response()->json($count);
    }

    /**
     * 测试队列
     * @return JsonResponse
     */
    public function testQueue(): JsonResponse
    {
        /*
         * php artisan queue:work redis --queue=test --tries=3 --timeout=10
         * 必须设置tries谨防部分没有设置最大尝试次数的队列(造成失败无限重试——死循环)
         * php artisan queue:restart
         * 这个命令将会告诉所有队列处理器在执行完当前任务后结束进程(并不会重新启动新的,只是平滑结束)
         */
        TestJob::dispatch()->onQueue('test');
        return response()->json('队列数据生产成功!');
    }

    /**
     * 测试辅助函数
     * @param Request $request
     * @return JsonResponse
     */
    public function testHelper(Request $request): JsonResponse
    {
        //获取项目路径
//        $path = base_path();

        //转化为驼峰命名
//        $var = camel_case('file_content');

        //转化为蛇形命名
//        $var = snake_case('fileContent');

        //判断给定的值是否为空
        /*
         * true
         */
//        $data = collect([]);
//        $data = '       ';
        /*
         * false
         */
//        $data = 0;
//        $data = true;
//        $data = false;

//        $bool = blank($data);

        //验证器
        $rules = ['content' => 'required'];
        $messages = ['content.required' => 'content字段不能为空'];
        $validator = validator(
            $request->all(),
            $rules,
            $messages
        );
        if ($validator->fails()) {
            return response()->json('校验失败');
        }

        return response()->json('校验成功');
    }

    /**
     * 测试事件
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function testEvent(Request $request)
    {
        //分发事件
        event(new TestEvent($request->all()));
        return response('触发事件成功');
    }

    /**
     * 测试广播
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function testBroadcast(Request $request)
    {
        //广播事件
        broadcast(new NewsPushEvent($request->input('msg')));
        return response('新闻推送成功');
    }

    /**
     * 测试获取通知
     * @param Request $request
     * @return JsonResponse
     */
    public function testNotifications(Request $request): JsonResponse
    {
        //获取用户信息
        $userId = $request->input('user_id');
        $user = User::findOrFail($userId);

        //拼装返回数据
        $result = [
            'total' => 0,
            'data' => []
        ];

        //此处模型需要使用 Notifiable trait,假如通知实现了 ShouldQueue,需要使用queue:work进行消费
        foreach ($user->unreadNotifications as $unreadNotification) {
            $result['total']++;
            $result['data'] = $unreadNotification->data;
            //此处注意通知表的id并不是唯一索引,生产环境需要考虑这个问题
            $result['data']['notification_id'] = $unreadNotification->id;
        }

        return response()->json($result);
    }

    /**
     * 测试标记通知已读
     * @param Request $request
     * @return JsonResponse
     */
    public function testMarkNotification(Request $request): JsonResponse
    {
        //获取用户信息
        $userId = $request->input('user_id');
        $user = User::findOrFail($userId);
        $notificationId = $request->input('notification_id');

        //标记成已读(实际生产过程中最好直接对通知模型的唯一索引进行更新,此处是线查出数据再用集合的where方法进行排查效率较低)
        $user->unreadNotifications
            ->where('id', $notificationId)
            ->markAsRead();

        return response()->json('标记通知已读状态成功');
    }

    /**
     * 邮件模板预览
     * @return UserRegistered
     */
    public function testMail(): UserRegistered
    {
        $user = User::find(1);
        return new UserRegistered($user);
    }

    /**
     * 测试短信发送
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function testSms(): JsonResponse
    {
        //指定短信接收者
        $phone = '18857876819';
        //随机数(验证码)
        try {
            //php7.0以上支持,需要系统环境支持(非windows&linux系统报错)
            $code = random_int(1000, 9999);
        } catch (Exception $exception) {
            Log::error(json_encode([
                'notice' => 'code generate fail',
                'message' => $exception->getMessage()
            ]));
        }

        //发送短信内容 详见 https://github.com/overtrue/easy-sms
        $message = [
            'template' => '222123',//模板id
            'data' => [
                'code' => $code
            ],
        ];

        //发送短信(寻找对应渠道gateway,若发送失败请检查gateway代码是否正确)
        try {
            $result = SMS::send($phone, $message);
            return response()->json($result);
        } catch (NoGatewayAvailableException $exception) {
            Log::error(json_encode([
                'notice' => 'send sms fail',
                'result' => $exception->getResults(),
                'exceptions' => $exception->getExceptions()
            ]));
            return response()->json('短信发送失败');
        }
    }

    /**
     * 测试导出
     *
     * @throws Exception
     */
    public function testDownload()
    {
        $header = [
            'workOrderNo' => '工单号',
            'item' => '服务内容',
            'settlement' => '结算价'
        ];
        $baseData = [
            [
                'workOrderNo' => 1,
                'item' => 'a-1',
                'settlement' => '0.01'
            ],
            [
                'workOrderNo' => 2,
                'item' => 'a-2',
                'settlement' => '100.01'
            ]
        ];
        $excel = $this->makeExcel('example', $header, iterator_to_array($this->transformData($baseData)));
        $this->downloadExcel($excel, 'test', 'xlsx');
    }

    /**
     * @param array $baseData
     * @return Generator
     */
    private function transformData(array $baseData)
    {
        foreach ($baseData as $baseDatum) {
            yield [
                'workOrderNo' => $baseDatum['workOrderNo'],
                'item' => $baseDatum['item'],
                'settlement' => $baseDatum['settlement']
            ];
        }
    }

    /**
     * 生成excel表格
     *
     * @param string $sheetName
     * @param array $header
     * @param mixed $data
     * @return Spreadsheet
     * @throws Exception
     */
    private function makeExcel(string $sheetName, array $header, $data): Spreadsheet
    {
        if (!is_array($data) && !$data instanceof Iterator) {
            throw new Exception('参数错误');
        }

        $excel = new Spreadsheet();
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle($sheetName);

        //处理列和头部
        $index = 1;
        $map = [];
        foreach ($header as $field => $title) {
            $columnName = Coordinate::stringFromColumnIndex($index);
            $sheet->getColumnDimension($columnName)->setAutoSize(true);//没啥用因为生成时算不精准具体宽度
            $sheet->getStyle("{$columnName}1")->getFont()->setBold(true);
            $sheet->setCellValue("{$columnName}1", $title);
            $map[$field] = $columnName;
            $index++;
        }

        //处理数据
        $row = 2;
        foreach ($data as $datum) {
            foreach ($map as $field => $columnName) {
                $sheet->setCellValue("{$columnName}{$row}", $datum[$field]);
            }
            $row++;
        }
        return $excel;
    }

    /**
     * 导出excel
     *
     * @param Spreadsheet $excel
     * @param string $fileName
     * @param string $format
     * @throws Exception
     */
    private function downloadExcel(Spreadsheet $excel, string $fileName, string $format)
    {
        $format = strtolower($format);
        if ($format === 'xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        } elseif ($format === 'xls') {
            header('Content-Type: application/vnd.ms-excel');
        } else {
            throw new Exception('暂不支持该格式导出');
        }

        header("Content-Disposition: attachment;filename={$fileName}.{$format}");
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($excel, ucwords($format));
        ob_end_clean();//php7.4问题
        $writer->save('php://output');
        exit();
    }
}
