<?php

namespace App\Transformers;

use App\User;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    //引入关联模型
    protected $availableIncludes = ['role'];

    /**
     * 资源(数据)转换器
     * @param User $user
     * @return array
     */
    public function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
//            'role_name' => $user->role->name
        ];
    }

    /**
     * 引入关联模型
     * @param User $user
     * @return Item
     */
    public function includeRole(User $user): Item
    {
        $role = $user->role;
        return $this->item($role, new RoleTransformer());
    }
}