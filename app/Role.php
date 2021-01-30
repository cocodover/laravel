<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * 角色表
 * App\Role
 *
 * @property int $id 序列号
 * @property string $name 角色名
 * @property array $permission 权限
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|User[] $user
 * @property-read int|null $user_count
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role query()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role wherePermission($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Role extends Model
{
    //表名
    protected $table = 'roles';

    //数据类型转换
    protected $casts = [
        'id' => 'int',
        'permission' => 'array'
    ];

    //批量赋值字段
    protected $fillable = [
        'name',
        'permission'
    ];

    //一个角色对应多个用户
    public function user(): HasMany
    {
        //此处foreignkey为关联表关联字段;localkey为本表关联字段
        return $this->hasMany(User::class, 'role_id', 'id');
    }
}
