<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Disposisi;
use App\Models\User;

class DisposisiPolicy
{
    /**
     * Semua role bisa melihat daftar disposisi.
     */
    public function viewAny(User $user): bool
    {
        return $user->canCreateSuratKeluar();
    }

    /**
     * Semua role bisa melihat detail disposisi.
     */
    public function view(User $user, Disposisi $disposisi): bool
    {
        return $user->canCreateSuratKeluar();
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
}
