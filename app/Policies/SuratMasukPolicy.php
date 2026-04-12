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
        if ($user->canManageSuratMasuk()) {
            return true;
        }

        return $this->isRelevantToUser($user, $suratMasuk);
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

    public function deleteAny(User $user): bool
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

    public function restoreAny(User $user): bool
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

    public function forceDeleteAny(User $user): bool
    {
        return $user->isAdminRole();
    }

    private function isRelevantToUser(User $user, SuratMasuk $suratMasuk): bool
    {
        if (filled($suratMasuk->penerima) && (int) $suratMasuk->penerima === (int) $user->id) {
            return true;
        }

        $unitId = $user->unit_kerja_id;

        return $suratMasuk->disposisis()
            ->where(function ($query) use ($user, $unitId): void {
                $query->where('ke_user_id', $user->id);

                if (filled($unitId)) {
                    $query->orWhere('ke_unit_id', $unitId);
                }
            })
            ->exists();
    }
}
