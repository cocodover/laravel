<?php

namespace App\Models;

use App\Events\RecoredFindingEvent;
use App\Scopes\UserScope;
use Eloquent;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 用户请求记录表
 * App\Models\Record
 *
 * @property int $id 序列号
 * @property int $user_id 用户ID
 * @property int|null $ip IP地址
 * @property string $uri 请求URI
 * @property string $method 请求方式
 * @property string $route 路由名称
 * @property array|null $request 请求参数
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $ip_address
 * @method static Builder|Record createdTime($beginAt = null, $endAt = null)
 * @method static Builder|Record modify()
 * @method static Builder|Record newModelQuery()
 * @method static Builder|Record newQuery()
 * @method static Builder|Record query()
 * @method static Builder|Record whereCreatedAt($value)
 * @method static Builder|Record whereId($value)
 * @method static Builder|Record whereIp($value)
 * @method static Builder|Record whereMethod($value)
 * @method static Builder|Record whereRequest($value)
 * @method static Builder|Record whereRoute($value)
 * @method static Builder|Record whereUpdatedAt($value)
 * @method static Builder|Record whereUri($value)
 * @method static Builder|Record whereUserId($value)
 * @mixin Eloquent
 */
class Record extends Model
{
    //表名
    protected $table = 'record';

    //主键属性
//    protected $primaryKey = 'id';
//    public $incrementing = true;
//    protected $keyType = 'int';

    //自动维护时间戳
//    public $timestamps = true;
//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';
//    protected $dateFormat = 'Y-m-d H:i:s';

    //选择数据库连接
//    protected $connection = 'mysql';

    //数据类型转换
    protected $casts = [
        'id' => 'int',
        'user_id' => 'int',
        'ip' => 'int',
        'request' => 'array'
    ];

    //批量赋值字段
    protected $fillable = [
        'user_id',
        'ip',
        'uri',
        'method',
        'route',
        'request'
    ];

    /*
     * 模型事件
     * 1.创建事件
     * 2.将Eloquent 支持的模型事件与自定义的事件类建立映射关系
     * 3.将事件类注册到监听器类中
     * https://xueyuanjun.com/post/9713
     */
    protected $dispatchesEvents = [
        'retrieved' => RecoredFindingEvent::class
    ];

    /*
     * 访问器
     */
    //获取ip地址信息(访问器字段名称避免与数据库相同)
    public function getIpAddressAttribute(): string
    {
        return $this->ip ? long2ip($this->ip) : '未知IP地址';
    }

    /*
     * 修改器
     */
    //转换ip地址入库格式
    public function setIpAttribute($ip)
    {
        if (!is_numeric($ip)) {
            $ip = ip2long($ip);
        }
        $this->attributes['ip'] = $ip ?: null;
    }

    /**
     * boot方法会在模型类实例化的时候调用
     */
    protected static function boot()
    {
        parent::boot();
        /*
         * 全局作用域(过滤器)
         */
        //用于请求记录过滤
        static::addGlobalScope(new UserScope());
        /*
         * 监听模型事件
         */
        //获取到模型实例后触发
//        self::retrieved(function ($record) {
//            Log::info(json_encode([
//                'notice' => 'record retrieved',
//                'user_id' => $record->user_id,
//                'uri' => $record->uri
//            ]));
//        });
    }

    /*
     * 局部作用域(过滤器)
     */
    public function scopeModify(Builder $query)
    {
        return $query->where('method', 'POST')
            ->whereNotNull('request');
    }

    /*
     * 动态作用域(过滤器)
     */
    public function scopeCreatedTime(Builder $query, $beginAt = null, $endAt = null): BuildsQueries
    {
        return $query->when($beginAt, function () use ($query, $beginAt) {
            return $query->where('created_at', '>=', $beginAt);
        })->when($endAt, function () use ($query, $endAt) {
            return $query->where('created_at', '<=', $endAt);
        });
    }
}
