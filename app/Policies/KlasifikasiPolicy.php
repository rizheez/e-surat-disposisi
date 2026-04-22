<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Klasifikasi;
use Illuminate\Auth\Access\HandlesAuthorization;

class KlasifikasiPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Klasifikasi');
    }

    public function view(AuthUser $authUser, Klasifikasi $klasifikasi): bool
    {
        return $authUser->can('View:Klasifikasi');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Klasifikasi');
    }

    public function update(AuthUser $authUser, Klasifikasi $klasifikasi): bool
    {
        return $authUser->can('Update:Klasifikasi');
    }

    public function delete(AuthUser $authUser, Klasifikasi $klasifikasi): bool
    {
        return $authUser->can('Delete:Klasifikasi');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Klasifikasi');
    }

    public function restore(AuthUser $authUser, Klasifikasi $klasifikasi): bool
    {
        return $authUser->can('Restore:Klasifikasi');
    }

    public function forceDelete(AuthUser $authUser, Klasifikasi $klasifikasi): bool
    {
        return $authUser->can('ForceDelete:Klasifikasi');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Klasifikasi');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Klasifikasi');
    }

    public function replicate(AuthUser $authUser, Klasifikasi $klasifikasi): bool
    {
        return $authUser->can('Replicate:Klasifikasi');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Klasifikasi');
    }

}