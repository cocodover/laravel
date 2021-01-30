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
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->isSuperAdmin();
    }

    /**
     * 创建权限
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return void
     */
    public function create(User $user)
    {
        //
    }

    /**
     * 更新权限
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->isSuperAdmin();
    }

    /**
     * 删除权限
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return false;
    }
}
