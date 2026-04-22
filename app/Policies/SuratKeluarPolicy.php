<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SuratKeluar;
use Illuminate\Auth\Access\HandlesAuthorization;

class SuratKeluarPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SuratKeluar');
    }

    public function view(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return $authUser->can('View:SuratKeluar');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SuratKeluar');
    }

    public function update(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return $authUser->can('Update:SuratKeluar');
    }

    public function delete(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return $authUser->can('Delete:SuratKeluar');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SuratKeluar');
    }

    public function restore(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return $authUser->can('Restore:SuratKeluar');
    }

    public function forceDelete(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return $authUser->can('ForceDelete:SuratKeluar');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SuratKeluar');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SuratKeluar');
    }

    public function replicate(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return $authUser->can('Replicate:SuratKeluar');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SuratKeluar');
    }

}