<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TemplateSurat;
use Illuminate\Auth\Access\HandlesAuthorization;

class TemplateSuratPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TemplateSurat');
    }

    public function view(AuthUser $authUser, TemplateSurat $templateSurat): bool
    {
        return $authUser->can('View:TemplateSurat');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TemplateSurat');
    }

    public function update(AuthUser $authUser, TemplateSurat $templateSurat): bool
    {
        return $authUser->can('Update:TemplateSurat');
    }

    public function delete(AuthUser $authUser, TemplateSurat $templateSurat): bool
    {
        return $authUser->can('Delete:TemplateSurat');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TemplateSurat');
    }

    public function restore(AuthUser $authUser, TemplateSurat $templateSurat): bool
    {
        return $authUser->can('Restore:TemplateSurat');
    }

    public function forceDelete(AuthUser $authUser, TemplateSurat $templateSurat): bool
    {
        return $authUser->can('ForceDelete:TemplateSurat');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TemplateSurat');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TemplateSurat');
    }

    public function replicate(AuthUser $authUser, TemplateSurat $templateSurat): bool
    {
        return $authUser->can('Replicate:TemplateSurat');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TemplateSurat');
    }

}