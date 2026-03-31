<?php

namespace App\Policies;

use App\Models\SuratMasuk;
use App\Models\User;

class SuratMasukPolicy
{
    /**
     * Admin, pimpinan, sekretaris bisa melihat daftar surat masuk.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'pimpinan', 'sekretaris']);
    }

    /**
     * Admin, pimpinan, sekretaris bisa melihat detail surat masuk.
     */
    public function view(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->hasAnyRole(['admin', 'pimpinan', 'sekretaris']);
    }

    /**
     * Hanya admin dan sekretaris yang bisa membuat surat masuk.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'sekretaris']);
    }

    /**
     * Hanya admin dan sekretaris yang bisa mengedit surat masuk.
     */
    public function update(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->hasAnyRole(['admin', 'sekretaris']);
    }

    /**
     * Hanya admin dan sekretaris yang bisa menghapus surat masuk.
     */
    public function delete(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->hasAnyRole(['admin', 'sekretaris']);
    }

    /**
     * Hanya admin yang bisa restore surat masuk yang dihapus.
     */
    public function restore(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Hanya admin yang bisa force delete.
     */
    public function forceDelete(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->hasRole('admin');
    }
}
