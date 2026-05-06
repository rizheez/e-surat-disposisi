<?php

namespace App\Policies;

use App\Policies\Concerns\AuthorizesApplicationAccess;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class UserPolicy
{
    use AuthorizesApplicationAccess, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'view_user');
    }

    public function view(AuthUser $authUser): bool
    {
        return $this->viewAny($authUser);
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'create_user');
    }

    public function update(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'edit_user');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'delete_user');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'delete_user');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function replicate(AuthUser $authUser): bool
    {
        return false;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return false;
    }
}
