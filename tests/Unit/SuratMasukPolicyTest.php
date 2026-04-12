<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\SuratMasuk;
use App\Models\User;
use App\Policies\SuratMasukPolicy;
use PHPUnit\Framework\TestCase;

class SuratMasukPolicyTest extends TestCase
{
    public function test_admin_and_staf_administrasi_can_manage_surat_masuk(): void
    {
        $policy = new SuratMasukPolicy;
        $user = $this->user(id: 1, canManageSuratMasuk: true);
        $suratMasuk = $this->suratMasuk();

        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->view($user, $suratMasuk));
        $this->assertTrue($policy->create($user));
        $this->assertTrue($policy->update($user, $suratMasuk));
        $this->assertTrue($policy->delete($user, $suratMasuk));
    }

    public function test_recipient_can_view_but_not_edit_or_delete_surat_masuk(): void
    {
        $policy = new SuratMasukPolicy;
        $user = $this->user(id: 2);
        $suratMasuk = $this->suratMasuk(penerima: 2);

        $this->assertTrue($policy->view($user, $suratMasuk));
        $this->assertFalse($policy->update($user, $suratMasuk));
        $this->assertFalse($policy->delete($user, $suratMasuk));
    }

    public function test_only_admin_can_restore_and_force_delete_surat_masuk(): void
    {
        $policy = new SuratMasukPolicy;
        $admin = $this->user(id: 1, isAdmin: true);
        $user = $this->user(id: 2);
        $suratMasuk = $this->suratMasuk();

        $this->assertTrue($policy->restore($admin, $suratMasuk));
        $this->assertTrue($policy->forceDelete($admin, $suratMasuk));
        $this->assertFalse($policy->restore($user, $suratMasuk));
        $this->assertFalse($policy->forceDelete($user, $suratMasuk));
    }

    private function suratMasuk(?int $penerima = null): SuratMasuk
    {
        return new SuratMasuk([
            'penerima' => $penerima,
        ]);
    }

    private function user(int $id, bool $canManageSuratMasuk = false, bool $isAdmin = false): User
    {
        $user = new class extends User
        {
            public bool $canManageSuratMasukForTest = false;

            public bool $isAdminForTest = false;

            public function canManageSuratMasuk(): bool
            {
                return $this->canManageSuratMasukForTest;
            }

            public function isAdminRole(): bool
            {
                return $this->isAdminForTest;
            }
        };

        $user->forceFill(['id' => $id]);
        $user->canManageSuratMasukForTest = $canManageSuratMasuk;
        $user->isAdminForTest = $isAdmin;

        return $user;
    }
}
