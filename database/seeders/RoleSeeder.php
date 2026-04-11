<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    private const GUARD_NAME = 'web';

    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_surat_masuk',
            'create_surat_masuk',
            'edit_surat_masuk',
            'delete_surat_masuk',
            'view_surat_keluar',
            'create_surat_keluar',
            'edit_surat_keluar',
            'delete_surat_keluar',
            'approve_surat_keluar',
            'view_disposisi',
            'create_disposisi',
            'edit_disposisi',
            'delete_disposisi',
            'reply_disposisi',
            'view_unit_kerja',
            'create_unit_kerja',
            'edit_unit_kerja',
            'delete_unit_kerja',
            'view_user',
            'create_user',
            'edit_user',
            'delete_user',
            'ViewAny:Role',
            'View:Role',
            'Create:Role',
            'Update:Role',
            'Delete:Role',
            'DeleteAny:Role',
            'Restore:Role',
            'ForceDelete:Role',
            'ForceDeleteAny:Role',
            'RestoreAny:Role',
            'Replicate:Role',
            'Reorder:Role',
            'view_template_surat',
            'create_template_surat',
            'edit_template_surat',
            'delete_template_surat',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, self::GUARD_NAME);
        }

        $admin = Role::findOrCreate('admin', self::GUARD_NAME);
        $admin->syncPermissions(Permission::all());

        $leadershipPermissions = [
            'view_surat_masuk',
            'view_surat_keluar',
            'create_surat_keluar',
            'edit_surat_keluar',
            'delete_surat_keluar',
            'approve_surat_keluar',
            'view_disposisi',
            'create_disposisi',
            'edit_disposisi',
            'view_unit_kerja',
            'view_user',
            'view_template_surat',
        ];

        foreach (['rektor', 'wr', 'kabiro', 'dekan', 'kaprodi'] as $roleName) {
            Role::findOrCreate($roleName, self::GUARD_NAME)
                ->syncPermissions($leadershipPermissions);
        }

        Role::findOrCreate('staf_administrasi', self::GUARD_NAME)
            ->syncPermissions([
                'view_surat_masuk',
                'create_surat_masuk',
                'edit_surat_masuk',
                'delete_surat_masuk',
                'view_surat_keluar',
                'create_surat_keluar',
                'edit_surat_keluar',
                'delete_surat_keluar',
                'view_disposisi',
                'reply_disposisi',
                'view_unit_kerja',
                'view_template_surat',
                'create_template_surat',
                'edit_template_surat',
            ]);

        foreach (['staf', 'dosen'] as $roleName) {
            Role::findOrCreate($roleName, self::GUARD_NAME)
                ->syncPermissions([
                    'view_surat_keluar',
                    'create_surat_keluar',
                    'edit_surat_keluar',
                    'delete_surat_keluar',
                    'view_disposisi',
                    'reply_disposisi',
                    'view_unit_kerja',
                    'view_template_surat',
                ]);
        }

    }
}
