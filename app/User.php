<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;//需要用于认证的模型必须继承该类

/**
 * 用户表(框架自带)
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $api_token 登录token
 * @property int $role_id 角色ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Role $role
 * @method static bool|null forceDelete()
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static Builder|User query()
 * @method static bool|null restore()
 * @method static Builder|User whereApiToken($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereRoleId($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 * @mixin Eloquent
 */
class User extends Authenticatable implements JWTSubject
{
    //消息通知,软删除
    use Notifiable, SoftDeletes;

    /**
     * 批量赋值字段（白名单）
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    /**
     * 不允许批量赋值字段（黑名单）
     * 白名单&黑名单属性只要有一个设置即可,不要两个都设置
     */
//    protected $guarded = ['id'];

    /**
     * 隐藏属性(不展示结果)
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    //一个用户对应一个角色
    public function role(): BelongsTo
    {
        //此处foreignkey为本表关联字段;ownerkey为主表关联字段
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * 更新token
     * @return string|null
     */
    public function updateToken(): string
    {
        //api_token字段存在唯一性,用时间戳加以区分
        $this->api_token = str_random(50) . time();
        $this->save();

        return $this->api_token;
    }

    /**
     * 清理token
     */
    public function cleanToken()
    {
        $this->api_token = null;
        $this->save();
    }

    /**
     * 判断该用户是否为 超级管理员
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->role_id === 0;
    }

    /*
     * JWT配置声明(载荷)
     */
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
