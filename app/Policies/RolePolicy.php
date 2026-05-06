<?php

declare(strict_types=1);

namespace App\Policies;

use App\Policies\Concerns\AuthorizesApplicationAccess;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use AuthorizesApplicationAccess, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function view(AuthUser $authUser, Role $role): bool
    {
        return $this->isAdmin($authUser);
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function update(AuthUser $authUser, Role $role): bool
    {
        return $this->isAdmin($authUser);
    }

    public function delete(AuthUser $authUser, Role $role): bool
    {
        return $this->isAdmin($authUser) && $role->name !== 'admin';
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function restore(AuthUser $authUser, Role $role): bool
    {
        return $this->isAdmin($authUser);
    }

    public function forceDelete(AuthUser $authUser, Role $role): bool
    {
        return $this->isAdmin($authUser) && $role->name !== 'admin';
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function replicate(AuthUser $authUser, Role $role): bool
    {
        return false;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return false;
    }
}
