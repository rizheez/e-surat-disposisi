<?php

namespace Database\Seeders;

use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class UnitKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $rektorat = UnitKerja::create([
            'nama' => 'Rektorat',
            'kode' => 'REK',
        ]);

        $baa = UnitKerja::create([
            'nama' => 'Biro Administrasi Akademik',
            'kode' => 'BAA',
            'parent_id' => $rektorat->id,
        ]);

        $bau = UnitKerja::create([
            'nama' => 'Biro Administrasi Umum',
            'kode' => 'BAU',
            'parent_id' => $rektorat->id,
        ]);

        $fti = UnitKerja::create([
            'nama' => 'Fakultas Teknologi Informasi',
            'kode' => 'FTI',
            'parent_id' => $rektorat->id,
        ]);

        UnitKerja::create([
            'nama' => 'Program Studi Informatika',
            'kode' => 'IF',
            'parent_id' => $fti->id,
        ]);

        UnitKerja::create([
            'nama' => 'Program Studi Sistem Informasi',
            'kode' => 'SI',
            'parent_id' => $fti->id,
        ]);

        $feb = UnitKerja::create([
            'nama' => 'Fakultas Ekonomi dan Bisnis',
            'kode' => 'FEB',
            'parent_id' => $rektorat->id,
        ]);

        UnitKerja::create([
            'nama' => 'Program Studi Manajemen',
            'kode' => 'MAN',
            'parent_id' => $feb->id,
        ]);

        UnitKerja::create([
            'nama' => 'Program Studi Akuntansi',
            'kode' => 'AKT',
            'parent_id' => $feb->id,
        ]);
    }
}
