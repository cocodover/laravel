<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * 查看权限
     * Determine whether the user can view the model.
     *
     * @param \App\User $user
     * @param \App\User $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        if ($user->id === $model->id || $user->isSuperAdmin()) {
            return true;
        }
        return false;
    }

    /**
     * 创建权限
     * Determine whether the user can create models.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * 更新权限
     * Determine whether the user can update the model.
     *
     * @param \App\User $user
     * @param \App\User $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        if ($user->id === $model->id || $user->isSuperAdmin()) {
            return true;
        }
        return false;
    }

    /**
     * 删除权限
     * Determine whether the user can delete the model.
     *
     * @param \App\User $user
     * @param \App\User $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return false;
    }
}
