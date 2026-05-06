<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SuratMasuk;
use App\Policies\Concerns\AuthorizesApplicationAccess;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SuratMasukPolicy
{
    use AuthorizesApplicationAccess, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->canManageSuratMasuk($authUser)
            || $this->hasPermission($authUser, 'view_surat_masuk');
    }

    public function view(AuthUser $authUser, SuratMasuk $suratMasuk): bool
    {
        return $this->viewAny($authUser)
            || $this->isRecipient($authUser, $suratMasuk)
            || $this->isCreator($authUser, $suratMasuk);
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->canManageSuratMasuk($authUser)
            || $this->hasPermission($authUser, 'create_surat_masuk');
    }

    public function update(AuthUser $authUser, SuratMasuk $suratMasuk): bool
    {
        return $this->isAdmin($authUser)
            || $this->canManageSuratMasuk($authUser)
            || $this->hasPermission($authUser, 'edit_surat_masuk');
    }

    public function delete(AuthUser $authUser, SuratMasuk $suratMasuk): bool
    {
        return $this->isAdmin($authUser)
            || $this->canManageSuratMasuk($authUser)
            || $this->hasPermission($authUser, 'delete_surat_masuk');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->canManageSuratMasuk($authUser)
            || $this->hasPermission($authUser, 'delete_surat_masuk');
    }

    public function restore(AuthUser $authUser, SuratMasuk $suratMasuk): bool
    {
        return $this->isAdmin($authUser);
    }

    public function forceDelete(AuthUser $authUser, SuratMasuk $suratMasuk): bool
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

    public function replicate(AuthUser $authUser, SuratMasuk $suratMasuk): bool
    {
        return false;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return false;
    }

    private function isRecipient(AuthUser $authUser, SuratMasuk $suratMasuk): bool
    {
        return filled($suratMasuk->penerima)
            && (int) $suratMasuk->penerima === (int) $authUser->getAuthIdentifier();
    }

    private function isCreator(AuthUser $authUser, SuratMasuk $suratMasuk): bool
    {
        return filled($suratMasuk->created_by)
            && (int) $suratMasuk->created_by === (int) $authUser->getAuthIdentifier();
    }
}
