<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Disposisi;
use App\Models\User;

class DisposisiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canCreateSuratKeluar();
    }

    public function view(User $user, Disposisi $disposisi): bool
    {
        return $user->isAdminRole()
            || $this->isCreatedByUser($user, $disposisi)
            || $this->isAssignedToUser($user, $disposisi);
    }

    /**
     * Hanya admin dan pejabat struktural yang bisa membuat disposisi.
     */
    public function create(User $user): bool
    {
        return $user->canManageDisposisi();
    }

    /**
     * Admin bisa edit semua, pejabat struktural hanya disposisi yang dia buat.
     */
    public function update(User $user, Disposisi $disposisi): bool
    {
        if ($user->isAdminRole()) {
            return true;
        }

        // Tembusan hanya untuk "mengetahui": tidak boleh mengubah status/proses.
        if ($disposisi->is_tembusan) {
            return false;
        }

        return $user->canManageDisposisi() && $disposisi->dari_user_id === $user->id;
    }

    /**
     * Hanya admin yang bisa menghapus disposisi.
     */
    public function delete(User $user, Disposisi $disposisi): bool
    {
        return $user->isAdminRole();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdminRole();
    }

    public function process(User $user, Disposisi $disposisi): bool
    {
        return $disposisi->status === 'belum_diproses'
            && ! $disposisi->is_tembusan
            && ($user->isAdminRole() || $this->isAssignedToUser($user, $disposisi));
    }

    public function complete(User $user, Disposisi $disposisi): bool
    {
        return $disposisi->status === 'sedang_diproses'
            && ! $disposisi->is_tembusan
            && ($user->isAdminRole() || $this->isAssignedToUser($user, $disposisi));
    }

    public function forward(User $user, Disposisi $disposisi): bool
    {
        return $disposisi->status !== 'selesai'
            && ! $disposisi->is_tembusan
            && ($user->isAdminRole() || $this->isAssignedToUser($user, $disposisi));
    }

    public function updateStatus(User $user, Disposisi $disposisi): bool
    {
        return ! $disposisi->is_tembusan
            && ($user->isAdminRole() || $this->isAssignedToUser($user, $disposisi));
    }

    private function isCreatedByUser(User $user, Disposisi $disposisi): bool
    {
        return filled($disposisi->dari_user_id)
            && (int) $disposisi->dari_user_id === (int) $user->id;
    }

    private function isAssignedToUser(User $user, Disposisi $disposisi): bool
    {
        if (filled($disposisi->ke_user_id) && (int) $disposisi->ke_user_id === (int) $user->id) {
            return true;
        }

        return filled($disposisi->ke_unit_id)
            && filled($user->unit_kerja_id)
            && (int) $disposisi->ke_unit_id === (int) $user->unit_kerja_id;
    }
}
