<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * 角色表
 * App\Role
 *
 * @property int $id 序列号
 * @property string $name 角色名
 * @property array $permission 权限
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $user
 * @property-read int|null $user_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereUpdatedAt($value)
 * @mixin \Eloquent
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
    public function user()
    {
        //此处foreignkey为关联表关联字段;localkey为本表关联字段
        return $this->hasMany(User::class, 'role_id', 'id');
    }
}
