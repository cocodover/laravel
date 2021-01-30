<?php

namespace App\Transformers;

use App\Role;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
{
    /**
     * 资源(数据)转换器
     * @param Role $role
     * @return array
     */
    public function transform(Role $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'permission' => $role->permission
        ];
    }
}