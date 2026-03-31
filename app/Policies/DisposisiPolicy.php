<?php

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
        return $user->hasAnyRole(['admin', 'pimpinan', 'sekretaris', 'staf']);
    }

    /**
     * Semua role bisa melihat detail disposisi.
     */
    public function view(User $user, Disposisi $disposisi): bool
    {
        return $user->hasAnyRole(['admin', 'pimpinan', 'sekretaris', 'staf']);
    }

    /**
     * Hanya admin dan pimpinan yang bisa membuat disposisi.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'pimpinan']);
    }

    /**
     * Admin bisa edit semua, pimpinan hanya disposisi yang dia buat.
     */
    public function update(User $user, Disposisi $disposisi): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Tembusan hanya untuk "mengetahui": tidak boleh mengubah status/proses.
        if ($disposisi->is_tembusan) {
            return false;
        }

        return $user->hasRole('pimpinan') && $disposisi->dari_user_id === $user->id;
    }

    /**
     * Hanya admin yang bisa menghapus disposisi.
     */
    public function delete(User $user, Disposisi $disposisi): bool
    {
        return $user->hasRole('admin');
    }
}
