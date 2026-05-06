<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SuratKeluar;
use App\Policies\Concerns\AuthorizesApplicationAccess;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SuratKeluarPolicy
{
    use AuthorizesApplicationAccess, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'view_surat_keluar');
    }

    public function view(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return $this->viewAny($authUser)
            || $this->isCreator($authUser, $suratKeluar)
            || $this->isSigner($authUser, $suratKeluar);
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->canCreateSuratKeluar($authUser)
            || $this->hasPermission($authUser, 'create_surat_keluar');
    }

    public function update(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'edit_surat_keluar')
            || ($this->isCreator($authUser, $suratKeluar) && $suratKeluar->status === 'draft');
    }

    public function delete(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'delete_surat_keluar')
            || ($this->isCreator($authUser, $suratKeluar) && $suratKeluar->status === 'draft');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'delete_surat_keluar');
    }

    public function restore(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return $this->isAdmin($authUser);
    }

    public function forceDelete(AuthUser $authUser, SuratKeluar $suratKeluar): bool
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

    public function replicate(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return false;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return false;
    }

    public function submitReview(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return $suratKeluar->status === 'draft'
            && blank($suratKeluar->file_path)
            && ($this->isAdmin($authUser) || $this->isCreator($authUser, $suratKeluar));
    }

    public function kirim(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return $suratKeluar->status === 'approved'
            && ($this->isAdmin($authUser) || $this->isCreator($authUser, $suratKeluar));
    }

    private function isCreator(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return filled($suratKeluar->pembuat_id)
            && (int) $suratKeluar->pembuat_id === (int) $authUser->getAuthIdentifier();
    }

    private function isSigner(AuthUser $authUser, SuratKeluar $suratKeluar): bool
    {
        return filled($suratKeluar->penandatangan_id)
            && (int) $suratKeluar->penandatangan_id === (int) $authUser->getAuthIdentifier();
    }
}
