<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SuratKeluar;
use App\Models\User;

class SuratKeluarPolicy
{
    /**
     * Semua user panel bisa melihat daftar surat keluar.
     */
    public function viewAny(User $user): bool
    {
        return $user->canCreateSuratKeluar();
    }

    /**
     * Semua user panel bisa melihat detail surat keluar.
     */
    public function view(User $user, SuratKeluar $suratKeluar): bool
    {
        return $user->canCreateSuratKeluar();
    }

    /**
     * Semua user panel bisa membuat surat keluar.
     */
    public function create(User $user): bool
    {
        return $user->canCreateSuratKeluar();
    }

    /**
     * Semua user panel bisa edit, tapi hanya jika surat masih draft.
     */
    public function update(User $user, SuratKeluar $suratKeluar): bool
    {
        if (! $user->canCreateSuratKeluar()) {
            return false;
        }

        return $suratKeluar->status === 'draft';
    }

    /**
     * Semua user panel bisa hapus, tapi hanya jika surat masih draft.
     */
    public function delete(User $user, SuratKeluar $suratKeluar): bool
    {
        if (! $user->canCreateSuratKeluar()) {
            return false;
        }

        return $suratKeluar->status === 'draft';
    }

    /**
     * Hanya admin yang bisa restore surat keluar yang dihapus.
     */
    public function restore(User $user, SuratKeluar $suratKeluar): bool
    {
        return $user->isAdminRole();
    }

    /**
     * Hanya admin yang bisa force delete.
     */
    public function forceDelete(User $user, SuratKeluar $suratKeluar): bool
    {
        return $user->isAdminRole();
    }

    /**
     * Hanya penandatangan surat yang bisa menyetujui.
     */
    public function approve(User $user, SuratKeluar $suratKeluar): bool
    {
        return $suratKeluar->penandatangan_id === $user->id
            && $suratKeluar->status === 'review';
    }

    /**
     * Hanya penandatangan surat yang bisa menolak.
     */
    public function reject(User $user, SuratKeluar $suratKeluar): bool
    {
        return $suratKeluar->penandatangan_id === $user->id
            && $suratKeluar->status === 'review';
    }
}
