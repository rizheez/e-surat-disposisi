<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Surat Masuk
            'view_surat_masuk',
            'create_surat_masuk',
            'edit_surat_masuk',
            'delete_surat_masuk',

            // Surat Keluar
            'view_surat_keluar',
            'create_surat_keluar',
            'edit_surat_keluar',
            'delete_surat_keluar',
            'approve_surat_keluar',

            // Disposisi
            'view_disposisi',
            'create_disposisi',
            'edit_disposisi',
            'delete_disposisi',
            'reply_disposisi',

            // Unit Kerja
            'view_unit_kerja',
            'create_unit_kerja',
            'edit_unit_kerja',
            'delete_unit_kerja',

            // User Management
            'view_user',
            'create_user',
            'edit_user',
            'delete_user',

            // Template
            'view_template_surat',
            'create_template_surat',
            'edit_template_surat',
            'delete_template_surat',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $pimpinan = Role::create(['name' => 'pimpinan']);
        $pimpinan->givePermissionTo([
            'view_surat_masuk',
            'view_surat_keluar',
            'approve_surat_keluar',
            'view_disposisi',
            'create_disposisi',
            'edit_disposisi',
            'view_unit_kerja',
            'view_user',
            'view_template_surat',
        ]);

        $sekretaris = Role::create(['name' => 'sekretaris']);
        $sekretaris->givePermissionTo([
            'view_surat_masuk',
            'create_surat_masuk',
            'edit_surat_masuk',
            'view_surat_keluar',
            'create_surat_keluar',
            'edit_surat_keluar',
            'view_disposisi',
            'reply_disposisi',
            'view_unit_kerja',
            'view_template_surat',
            'create_template_surat',
            'edit_template_surat',
        ]);

        $staf = Role::create(['name' => 'staf']);
        $staf->givePermissionTo([
            'view_surat_masuk',
            'view_surat_keluar',
            'view_disposisi',
            'reply_disposisi',
            'view_unit_kerja',
        ]);
    }
}
