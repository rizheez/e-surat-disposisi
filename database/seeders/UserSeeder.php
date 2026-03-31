<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $rektorat = UnitKerja::where('kode', 'REK')->first();
        $baa = UnitKerja::where('kode', 'BAA')->first();
        $fti = UnitKerja::where('kode', 'FTI')->first();

        // Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@esurat.test',
            'password' => bcrypt('password'),
            'jabatan' => 'Administrator Sistem',
            'unit_kerja_id' => $rektorat?->id,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Pimpinan
        $pimpinan = User::create([
            'name' => 'Dr. Ahmad Sutanto',
            'email' => 'pimpinan@esurat.test',
            'password' => bcrypt('password'),
            'jabatan' => 'Rektor',
            'unit_kerja_id' => $rektorat?->id,
            'email_verified_at' => now(),
        ]);
        $pimpinan->assignRole('pimpinan');

        // Sekretaris
        $sekretaris = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'sekretaris@esurat.test',
            'password' => bcrypt('password'),
            'jabatan' => 'Sekretaris Rektorat',
            'unit_kerja_id' => $rektorat?->id,
            'email_verified_at' => now(),
        ]);
        $sekretaris->assignRole('sekretaris');

        // Staf
        $staf = User::create([
            'name' => 'Budi Santoso',
            'email' => 'staf@esurat.test',
            'password' => bcrypt('password'),
            'jabatan' => 'Staf Administrasi',
            'unit_kerja_id' => $baa?->id,
            'email_verified_at' => now(),
        ]);
        $staf->assignRole('staf');

        // Wakil Rektor
        $wakilRektor = User::create([
            'name' => 'Prof. Dr. H. Wakil Rektor',
            'email' => 'wr@esurat.test',
            'password' => bcrypt('password'),
            'jabatan' => 'Wakil Rektor',
            'unit_kerja_id' => $rektorat?->id,
            'email_verified_at' => now(),
        ]);
        $wakilRektor->assignRole('pimpinan');

        // Additional pimpinan for FTI
        $dekanFti = User::create([
            'name' => 'Prof. Dewi Kumala',
            'email' => 'dekan.fti@esurat.test',
            'password' => bcrypt('password'),
            'jabatan' => 'Dekan FTI',
            'unit_kerja_id' => $fti?->id,
            'email_verified_at' => now(),
        ]);
        $dekanFti->assignRole('pimpinan');
    }
}
