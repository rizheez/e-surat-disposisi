<?php

declare(strict_types=1);

namespace App\Policies\Concerns;

use Illuminate\Foundation\Auth\User as AuthUser;

trait AuthorizesApplicationAccess
{
    private function isAdmin(AuthUser $authUser): bool
    {
        if (method_exists($authUser, 'isAdminRole')) {
            return (bool) $authUser->isAdminRole();
        }

        return $this->hasRole($authUser, 'admin');
    }

    private function canManageSuratMasuk(AuthUser $authUser): bool
    {
        return method_exists($authUser, 'canManageSuratMasuk')
            && (bool) $authUser->canManageSuratMasuk();
    }

    private function canCreateSuratKeluar(AuthUser $authUser): bool
    {
        return method_exists($authUser, 'canCreateSuratKeluar')
            && (bool) $authUser->canCreateSuratKeluar();
    }

    private function canManageDisposisi(AuthUser $authUser): bool
    {
        return method_exists($authUser, 'canManageDisposisi')
            && (bool) $authUser->canManageDisposisi();
    }

    private function hasPermission(AuthUser $authUser, string $permission): bool
    {
        try {
            return $authUser->can($permission);
        } catch (\Throwable) {
            return false;
        }
    }

    private function hasRole(AuthUser $authUser, string $role): bool
    {
        try {
            return method_exists($authUser, 'hasRole') && $authUser->hasRole($role);
        } catch (\Throwable) {
            return false;
        }
    }
}
