<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Disposisi;
use App\Policies\Concerns\AuthorizesApplicationAccess;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class DisposisiPolicy
{
    use AuthorizesApplicationAccess, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->canManageDisposisi($authUser)
            || $this->hasPermission($authUser, 'view_disposisi');
    }

    public function view(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'view_disposisi')
            || $this->isParticipant($authUser, $disposisi);
    }

    public function create(AuthUser $authUser): bool
    {
        return false;
    }

    public function update(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'edit_disposisi')
            || $this->isActiveTarget($authUser, $disposisi);
    }

    public function delete(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isAdmin($authUser);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function restore(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isAdmin($authUser);
    }

    public function forceDelete(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return $this->isAdmin($authUser);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser);
    }

    public function replicate(AuthUser $authUser, Disposisi $disposisi): bool
    {
        return false;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return false;
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
        return filled($disposisi->ke_user_id)
            && (int) $disposisi->ke_user_id === (int) $authUser->getAuthIdentifier();
    }
}
