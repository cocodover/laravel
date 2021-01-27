<?php

namespace App\Scopes;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserScope implements Scope
{
    /**
     * 全局作用域(过滤器)
     * @param Builder $builder
     * @param Model $model
     * @return Builder|void
     */
    public function apply(Builder $builder, Model $model)
    {
        /**
         * @var User $user
         */
        $user = auth()->user();
        if ($user) {
            return $builder->where('user_id', $user->id);
        }
    }
}