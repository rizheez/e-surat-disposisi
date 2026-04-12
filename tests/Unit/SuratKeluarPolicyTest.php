<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\SuratKeluar;
use App\Models\User;
use App\Policies\SuratKeluarPolicy;
use PHPUnit\Framework\TestCase;

class SuratKeluarPolicyTest extends TestCase
{
    public function test_creator_can_manage_and_submit_own_draft_surat_keluar(): void
    {
        $policy = new SuratKeluarPolicy;
        $user = $this->user(id: 1);
        $suratKeluar = $this->suratKeluar(pembuatId: 1, status: 'draft');

        $this->assertTrue($policy->view($user, $suratKeluar));
        $this->assertTrue($policy->update($user, $suratKeluar));
        $this->assertTrue($policy->delete($user, $suratKeluar));
        $this->assertTrue($policy->submitReview($user, $suratKeluar));
    }

    public function test_penandatangan_can_view_surat_keluar(): void
    {
        $policy = new SuratKeluarPolicy;
        $user = $this->user(id: 3);
        $suratKeluar = $this->suratKeluar(pembuatId: 1, status: 'review', penandatanganId: 3);

        $this->assertTrue($policy->view($user, $suratKeluar));
    }

    public function test_creator_can_send_own_approved_surat_keluar(): void
    {
        $policy = new SuratKeluarPolicy;
        $user = $this->user(id: 1);
        $suratKeluar = $this->suratKeluar(pembuatId: 1, status: 'approved');

        $this->assertTrue($policy->kirim($user, $suratKeluar));
    }

    public function test_non_creator_cannot_manage_or_submit_draft_surat_keluar(): void
    {
        $policy = new SuratKeluarPolicy;
        $user = $this->user(id: 2);
        $suratKeluar = $this->suratKeluar(pembuatId: 1, status: 'draft');

        $this->assertFalse($policy->view($user, $suratKeluar));
        $this->assertFalse($policy->update($user, $suratKeluar));
        $this->assertFalse($policy->delete($user, $suratKeluar));
        $this->assertFalse($policy->submitReview($user, $suratKeluar));
    }

    public function test_non_creator_cannot_send_approved_surat_keluar(): void
    {
        $policy = new SuratKeluarPolicy;
        $user = $this->user(id: 2);
        $suratKeluar = $this->suratKeluar(pembuatId: 1, status: 'approved');

        $this->assertFalse($policy->kirim($user, $suratKeluar));
    }

    public function test_creator_cannot_manage_surat_keluar_after_draft(): void
    {
        $policy = new SuratKeluarPolicy;
        $user = $this->user(id: 1);
        $suratKeluar = $this->suratKeluar(pembuatId: 1, status: 'review');

        $this->assertFalse($policy->update($user, $suratKeluar));
        $this->assertFalse($policy->delete($user, $suratKeluar));
        $this->assertFalse($policy->submitReview($user, $suratKeluar));
    }

    public function test_admin_can_manage_other_users_surat_keluar(): void
    {
        $policy = new SuratKeluarPolicy;
        $admin = $this->user(id: 2, isAdmin: true);
        $suratKeluar = $this->suratKeluar(pembuatId: 1, status: 'approved');

        $this->assertTrue($policy->update($admin, $suratKeluar));
        $this->assertTrue($policy->delete($admin, $suratKeluar));
    }

    public function test_admin_can_submit_and_send_other_users_surat_keluar(): void
    {
        $policy = new SuratKeluarPolicy;
        $admin = $this->user(id: 2, isAdmin: true);

        $this->assertTrue($policy->submitReview($admin, $this->suratKeluar(pembuatId: 1, status: 'draft')));
        $this->assertTrue($policy->kirim($admin, $this->suratKeluar(pembuatId: 1, status: 'approved')));
    }

    private function suratKeluar(int $pembuatId, string $status, ?int $penandatanganId = null): SuratKeluar
    {
        return new SuratKeluar([
            'penandatangan_id' => $penandatanganId,
            'pembuat_id' => $pembuatId,
            'status' => $status,
        ]);
    }

    private function user(int $id, bool $isAdmin = false, bool $canCreateSuratKeluar = true): User
    {
        $user = new class extends User
        {
            public bool $isAdminForTest = false;

            public bool $canCreateSuratKeluarForTest = true;

            public function isAdminRole(): bool
            {
                return $this->isAdminForTest;
            }

            public function canCreateSuratKeluar(): bool
            {
                return $this->canCreateSuratKeluarForTest;
            }
        };

        $user->forceFill(['id' => $id]);
        $user->isAdminForTest = $isAdmin;
        $user->canCreateSuratKeluarForTest = $canCreateSuratKeluar;

        return $user;
    }
}
