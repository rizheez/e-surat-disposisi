<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SuratMasuk;
use App\Models\User;

class SuratMasukPolicy
{
    /**
     * Admin, pejabat struktural, dan staf administrasi bisa melihat daftar surat masuk.
     */
    public function viewAny(User $user): bool
    {
        return $user->canManageSuratMasuk() || $user->canManageDisposisi();
    }

    /**
     * Admin, pejabat struktural, dan staf administrasi bisa melihat detail surat masuk.
     */
    public function view(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->canManageSuratMasuk() || $user->canManageDisposisi();
    }

    /**
     * Admin dan staf administrasi bisa membuat surat masuk.
     */
    public function create(User $user): bool
    {
        return $user->canManageSuratMasuk();
    }

    /**
     * Admin dan staf administrasi bisa mengedit surat masuk.
     */
    public function update(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->canManageSuratMasuk();
    }

    /**
     * Admin dan staf administrasi bisa menghapus surat masuk.
     */
    public function delete(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->canManageSuratMasuk();
    }

    /**
     * Hanya admin yang bisa restore surat masuk yang dihapus.
     */
    public function restore(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->isAdminRole();
    }

    /**
     * Hanya admin yang bisa force delete.
     */
    public function forceDelete(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->isAdminRole();
    }
}
