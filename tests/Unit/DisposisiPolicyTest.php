<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Disposisi;
use App\Models\User;
use App\Policies\DisposisiPolicy;
use PHPUnit\Framework\TestCase;

class DisposisiPolicyTest extends TestCase
{
    public function test_creator_target_and_unit_member_can_view_disposisi(): void
    {
        $policy = new DisposisiPolicy;
        $disposisi = $this->disposisi(dariUserId: 1, keUserId: 2, keUnitId: 10);

        $this->assertTrue($policy->view($this->user(id: 1), $disposisi));
        $this->assertTrue($policy->view($this->user(id: 2), $disposisi));
        $this->assertTrue($policy->view($this->user(id: 3, unitKerjaId: 10), $disposisi));
    }

    public function test_unrelated_user_cannot_view_disposisi(): void
    {
        $policy = new DisposisiPolicy;
        $disposisi = $this->disposisi(dariUserId: 1, keUserId: 2, keUnitId: 10);

        $this->assertFalse($policy->view($this->user(id: 4, unitKerjaId: 11), $disposisi));
    }

    public function test_global_create_disposisi_is_disabled(): void
    {
        $policy = new DisposisiPolicy;

        $this->assertFalse($policy->create($this->user(id: 1, isAdmin: true)));
        $this->assertFalse($policy->create($this->user(id: 2)));
    }

    public function test_only_admin_or_assigned_user_can_process_complete_forward_and_update_status(): void
    {
        $policy = new DisposisiPolicy;
        $disposisi = $this->disposisi(dariUserId: 1, keUserId: 2, status: 'belum_diproses');

        $this->assertTrue($policy->process($this->user(id: 2), $disposisi));
        $this->assertTrue($policy->forward($this->user(id: 2), $disposisi));
        $this->assertTrue($policy->updateStatus($this->user(id: 2), $disposisi));
        $this->assertFalse($policy->complete($this->user(id: 2), $disposisi));
        $this->assertTrue($policy->process($this->user(id: 4, isAdmin: true), $disposisi));
        $this->assertFalse($policy->process($this->user(id: 3), $disposisi));

        $disposisi->status = 'sedang_diproses';

        $this->assertTrue($policy->complete($this->user(id: 2), $disposisi));
        $this->assertFalse($policy->complete($this->user(id: 3), $disposisi));
    }

    public function test_forward_button_is_hidden_after_disposisi_has_child(): void
    {
        $policy = new DisposisiPolicy;
        $disposisi = $this->disposisi(dariUserId: 1, keUserId: 2, status: 'sedang_diproses');
        $disposisi->setRelation('children', collect([new Disposisi]));

        $this->assertFalse($policy->complete($this->user(id: 2), $disposisi));
        $this->assertFalse($policy->forward($this->user(id: 2), $disposisi));
        $this->assertFalse($policy->updateStatus($this->user(id: 2), $disposisi));
    }

    public function test_tembusan_cannot_be_processed_forwarded_or_updated_by_target(): void
    {
        $policy = new DisposisiPolicy;
        $disposisi = $this->disposisi(dariUserId: 1, keUserId: 2, isTembusan: true);

        $this->assertFalse($policy->process($this->user(id: 2), $disposisi));
        $this->assertFalse($policy->forward($this->user(id: 2), $disposisi));
        $this->assertFalse($policy->updateStatus($this->user(id: 2), $disposisi));
    }

    public function test_only_admin_can_delete_disposisi(): void
    {
        $policy = new DisposisiPolicy;
        $disposisi = $this->disposisi(dariUserId: 1, keUserId: 2);

        $this->assertTrue($policy->delete($this->user(id: 3, isAdmin: true), $disposisi));
        $this->assertFalse($policy->delete($this->user(id: 1), $disposisi));
    }

    private function disposisi(
        int $dariUserId,
        ?int $keUserId = null,
        ?int $keUnitId = null,
        string $status = 'belum_diproses',
        bool $isTembusan = false,
    ): Disposisi {
        return new Disposisi([
            'dari_user_id' => $dariUserId,
            'ke_user_id' => $keUserId,
            'ke_unit_id' => $keUnitId,
            'status' => $status,
            'is_tembusan' => $isTembusan,
        ]);
    }

    private function user(int $id, ?int $unitKerjaId = null, bool $isAdmin = false): User
    {
        $user = new class extends User
        {
            public bool $isAdminForTest = false;

            public function isAdminRole(): bool
            {
                return $this->isAdminForTest;
            }
        };

        $user->forceFill([
            'id' => $id,
            'unit_kerja_id' => $unitKerjaId,
        ]);
        $user->isAdminForTest = $isAdmin;

        return $user;
    }
}
