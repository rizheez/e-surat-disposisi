<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Disposisi;
use Illuminate\Auth\Access\HandlesAuthorization;

class DisposisiPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Disposisi');
    }

    public function view(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isAdmin($authUser) || $this->isParticipant($authUser, $disposisi);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Disposisi');
    }

    public function update(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isAdmin($authUser) || $this->isActiveTarget($authUser, $disposisi);
    }

    public function delete(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isAdmin($authUser);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Disposisi');
    }

    public function restore(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $authUser->can('Restore:Disposisi');
    }

    public function forceDelete(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isAdmin($authUser);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Disposisi');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Disposisi');
    }

    public function replicate(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $authUser->can('Replicate:Disposisi');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Disposisi');
    }

    public function process(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return ($this->isAdmin($authUser) || $this->isActiveTarget($authUser, $disposisi))
            && $disposisi->status === 'belum_diproses';
    }

    public function complete(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return ($this->isAdmin($authUser) || $this->isActiveTarget($authUser, $disposisi))
            && $disposisi->status !== 'selesai';
    }

    public function forward(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isActionable($disposisi)
            && ($this->isAdmin($authUser) || $this->isTarget($authUser, $disposisi));
    }

    public function updateStatus(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isActionable($disposisi)
            && ($this->isAdmin($authUser) || $this->isTarget($authUser, $disposisi));
    }

    private function isAdmin(AuthUser $authUser): bool
    {
        return method_exists($authUser, 'isAdminRole') && $authUser->isAdminRole();
    }

    private function isParticipant(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $disposisi->dari_user_id === $authUser->getKey()
            || $this->isTarget($authUser, $disposisi);
    }

    private function isActiveTarget(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isActionable($disposisi)
            && $this->isTarget($authUser, $disposisi);
    }

    private function isActionable(Disposisi $disposisi): bool
    {
        return ! $disposisi->is_tembusan && $disposisi->status !== 'selesai';
    }

    private function isTarget(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $disposisi->ke_user_id === $authUser->getKey()
            || (
                filled($disposisi->ke_unit_id)
                && filled($authUser->unit_kerja_id)
                && $disposisi->ke_unit_id === $authUser->unit_kerja_id
            );
    }

}
