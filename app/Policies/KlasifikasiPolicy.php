<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Klasifikasi;
use App\Policies\Concerns\AuthorizesApplicationAccess;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class KlasifikasiPolicy
{
    use AuthorizesApplicationAccess, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function view(AuthUser $authUser, Klasifikasi $klasifikasi): bool
    {
        return $this->isAdmin($authUser);
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function update(AuthUser $authUser, Klasifikasi $klasifikasi): bool
    {
        return $this->isAdmin($authUser);
    }

    public function delete(AuthUser $authUser, Klasifikasi $klasifikasi): bool
    {
        return $this->isAdmin($authUser);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function restore(AuthUser $authUser, Klasifikasi $klasifikasi): bool
    {
        return $this->isAdmin($authUser);
    }

    public function forceDelete(AuthUser $authUser, Klasifikasi $klasifikasi): bool
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

    public function replicate(AuthUser $authUser, Klasifikasi $klasifikasi): bool
    {
        return false;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return false;
    }
}
