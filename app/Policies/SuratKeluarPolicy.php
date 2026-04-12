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

    public function view(User $user, SuratKeluar $suratKeluar): bool
    {
        return $user->isAdminRole()
            || $this->isOwnedByUser($user, $suratKeluar)
            || $this->isSignedByUser($user, $suratKeluar);
    }

    /**
     * Semua user panel bisa membuat surat keluar.
     */
    public function create(User $user): bool
    {
        return $user->canCreateSuratKeluar();
    }

    /**
     * Admin bisa edit semua, user lain hanya surat draft yang dia buat.
     */
    public function update(User $user, SuratKeluar $suratKeluar): bool
    {
        if ($user->isAdminRole()) {
            return true;
        }

        return $this->isDraftOwnedByUser($user, $suratKeluar);
    }

    /**
     * Admin bisa menghapus semua, user lain hanya surat draft yang dia buat.
     */
    public function delete(User $user, SuratKeluar $suratKeluar): bool
    {
        if ($user->isAdminRole()) {
            return true;
        }

        return $this->isDraftOwnedByUser($user, $suratKeluar);
    }

    public function deleteAny(User $user): bool
    {
        return $user->canCreateSuratKeluar();
    }

    /**
     * Admin bisa submit semua draft, user lain hanya draft yang dia buat.
     */
    public function submitReview(User $user, SuratKeluar $suratKeluar): bool
    {
        if ($suratKeluar->status !== 'draft') {
            return false;
        }

        return $user->isAdminRole() || $this->isOwnedByUser($user, $suratKeluar);
    }

    /**
     * Admin bisa kirim semua surat approved, user lain hanya surat yang dia buat.
     */
    public function kirim(User $user, SuratKeluar $suratKeluar): bool
    {
        if ($suratKeluar->status !== 'approved') {
            return false;
        }

        return $user->isAdminRole() || $this->isOwnedByUser($user, $suratKeluar);
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

    private function isDraftOwnedByUser(User $user, SuratKeluar $suratKeluar): bool
    {
        return $user->canCreateSuratKeluar()
            && $suratKeluar->status === 'draft'
            && $this->isOwnedByUser($user, $suratKeluar);
    }

    private function isOwnedByUser(User $user, SuratKeluar $suratKeluar): bool
    {
        return filled($suratKeluar->pembuat_id)
            && (int) $suratKeluar->pembuat_id === (int) $user->id;
    }

    private function isSignedByUser(User $user, SuratKeluar $suratKeluar): bool
    {
        return filled($suratKeluar->penandatangan_id)
            && (int) $suratKeluar->penandatangan_id === (int) $user->id;
    }
}
