<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\UnitKerja;
use App\Policies\Concerns\AuthorizesApplicationAccess;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class UnitKerjaPolicy
{
    use AuthorizesApplicationAccess, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'view_unit_kerja');
    }

    public function view(AuthUser $authUser, UnitKerja $unitKerja): bool
    {
        return $this->viewAny($authUser);
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'create_unit_kerja');
    }

    public function update(AuthUser $authUser, UnitKerja $unitKerja): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'edit_unit_kerja');
    }

    public function delete(AuthUser $authUser, UnitKerja $unitKerja): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'delete_unit_kerja');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'delete_unit_kerja');
    }

    public function restore(AuthUser $authUser, UnitKerja $unitKerja): bool
    {
        return $this->isAdmin($authUser);
    }

    public function forceDelete(AuthUser $authUser, UnitKerja $unitKerja): bool
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

    public function replicate(AuthUser $authUser, UnitKerja $unitKerja): bool
    {
        return false;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return false;
    }
}
