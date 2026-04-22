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
        return $this->hasPermission($authUser, 'ViewAny:Disposisi');
    }

    public function view(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'View:Disposisi')
            || $this->isParticipant($authUser, $disposisi);
    }

    public function create(AuthUser $authUser): bool
    {
        return false;
    }

    public function update(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'Update:Disposisi')
            || $this->isActiveTarget($authUser, $disposisi);
    }

    public function delete(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isAdmin($authUser) || $this->hasPermission($authUser, 'Delete:Disposisi');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'DeleteAny:Disposisi');
    }

    public function restore(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->hasPermission($authUser, 'Restore:Disposisi');
    }

    public function forceDelete(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isAdmin($authUser) || $this->hasPermission($authUser, 'ForceDelete:Disposisi');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'ForceDeleteAny:Disposisi');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'RestoreAny:Disposisi');
    }

    public function replicate(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->hasPermission($authUser, 'Replicate:Disposisi');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $this->hasPermission($authUser, 'Reorder:Disposisi');
    }

    public function process(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isActionable($disposisi)
            && $disposisi->status === 'belum_diproses'
            && ($this->isAdmin($authUser) || $this->isActiveTarget($authUser, $disposisi));
    }

    public function complete(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isActionable($disposisi)
            && $disposisi->status === 'sedang_diproses'
            && ! $this->hasBeenForwarded($disposisi)
            && ($this->isAdmin($authUser) || $this->isActiveTarget($authUser, $disposisi));
    }

    public function forward(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isActionable($disposisi)
            && ! $this->hasBeenForwarded($disposisi)
            && ($this->isAdmin($authUser) || $this->isActiveTarget($authUser, $disposisi));
    }

    public function updateStatus(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->forward($authUser, $disposisi);
    }

    private function isAdmin(AuthUser $authUser): bool
    {
        return method_exists($authUser, 'isAdminRole') && $authUser->isAdminRole();
    }

    private function hasPermission(AuthUser $authUser, string $permission): bool
    {
        try {
            return $authUser->can($permission);
        } catch (\Throwable) {
            return false;
        }
    }

    private function isParticipant(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return (int) $disposisi->dari_user_id === (int) $authUser->getAuthIdentifier()
            || $this->isTarget($authUser, $disposisi);
    }

    private function isActiveTarget(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return ! $disposisi->is_tembusan && $this->isTarget($authUser, $disposisi);
    }

    private function isActionable(Disposisi $disposisi): bool
    {
        return ! $disposisi->is_tembusan && $disposisi->status !== 'selesai';
    }

    private function hasBeenForwarded(Disposisi $disposisi): bool
    {
        if ($disposisi->relationLoaded('children')) {
            return $disposisi->children->isNotEmpty();
        }

        if (! $disposisi->exists) {
            return false;
        }

        return $disposisi->children()->exists();
    }

    private function isTarget(AuthUser $authUser, Disposisi $disposisi): bool
    {
        if (filled($disposisi->ke_user_id) && (int) $disposisi->ke_user_id === (int) $authUser->getAuthIdentifier()) {
            return true;
        }

        return filled($disposisi->ke_unit_id)
            && filled($authUser->unit_kerja_id)
            && (int) $disposisi->ke_unit_id === (int) $authUser->unit_kerja_id;
    }

}
