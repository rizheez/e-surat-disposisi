<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TemplateSurat;
use App\Policies\Concerns\AuthorizesApplicationAccess;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TemplateSuratPolicy
{
    use AuthorizesApplicationAccess, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'view_template_surat');
    }

    public function view(AuthUser $authUser, TemplateSurat $templateSurat): bool
    {
        return $this->viewAny($authUser)
            || $this->isCreator($authUser, $templateSurat);
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'create_template_surat');
    }

    public function update(AuthUser $authUser, TemplateSurat $templateSurat): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'edit_template_surat')
            || $this->isCreator($authUser, $templateSurat);
    }

    public function delete(AuthUser $authUser, TemplateSurat $templateSurat): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'delete_template_surat')
            || $this->isCreator($authUser, $templateSurat);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser)
            || $this->hasPermission($authUser, 'delete_template_surat');
    }

    public function restore(AuthUser $authUser, TemplateSurat $templateSurat): bool
    {
        return $this->isAdmin($authUser);
    }

    public function forceDelete(AuthUser $authUser, TemplateSurat $templateSurat): bool
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

    public function replicate(AuthUser $authUser, TemplateSurat $templateSurat): bool
    {
        return false;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return false;
    }

    private function isCreator(AuthUser $authUser, TemplateSurat $templateSurat): bool
    {
        return filled($templateSurat->created_by)
            && (int) $templateSurat->created_by === (int) $authUser->getAuthIdentifier();
    }
}
