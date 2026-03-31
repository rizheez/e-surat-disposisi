<?php

namespace Database\Seeders;

use App\Models\Klasifikasi;
use Illuminate\Database\Seeder;

class KlasifikasiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Surat Internal (01.x)
            ['kode' => '01.1',  'nama' => 'Urusan Umum',          'kategori' => 'internal'],
            ['kode' => '01.2',  'nama' => 'Urusan Administrasi',  'kategori' => 'internal'],
            ['kode' => '01.3',  'nama' => 'Urusan Akademik',      'kategori' => 'internal'],
            ['kode' => '01.4',  'nama' => 'Urusan Kepegawaian',   'kategori' => 'internal'],
            ['kode' => '01.5',  'nama' => 'Urusan Keuangan',      'kategori' => 'internal'],
            ['kode' => '01.6',  'nama' => 'Urusan Kehumasan',     'kategori' => 'internal'],
            ['kode' => '01.7',  'nama' => 'Urusan Penelitian',    'kategori' => 'internal'],
            ['kode' => '01.8',  'nama' => 'Urusan Keagamaan',     'kategori' => 'internal'],
            ['kode' => '01.9',  'nama' => 'Urusan Sosial',        'kategori' => 'internal'],
            ['kode' => '01.10', 'nama' => 'Urusan Kemahasiswaan', 'kategori' => 'internal'],

            // Surat Eksternal (02.x)
            ['kode' => '02.1',  'nama' => 'Urusan Umum',          'kategori' => 'eksternal'],
            ['kode' => '02.2',  'nama' => 'Urusan Administrasi',  'kategori' => 'eksternal'],
            ['kode' => '02.3',  'nama' => 'Urusan Akademik',      'kategori' => 'eksternal'],
            ['kode' => '02.4',  'nama' => 'Urusan Kepegawaian',   'kategori' => 'eksternal'],
            ['kode' => '02.5',  'nama' => 'Urusan Keuangan',      'kategori' => 'eksternal'],
            ['kode' => '02.6',  'nama' => 'Urusan Kehumasan',     'kategori' => 'eksternal'],
            ['kode' => '02.7',  'nama' => 'Urusan Penelitian',    'kategori' => 'eksternal'],
            ['kode' => '02.8',  'nama' => 'Urusan Keagamaan',     'kategori' => 'eksternal'],
            ['kode' => '02.9',  'nama' => 'Urusan Sosial',        'kategori' => 'eksternal'],
            ['kode' => '02.10', 'nama' => 'Urusan Kemahasiswaan', 'kategori' => 'eksternal'],

            // Jenis Surat Khusus
            ['kode' => 'ND',    'nama' => 'Nota Dinas',                         'kategori' => 'khusus', 'kode_surat' => 'ND'],
            ['kode' => 'SPTDD', 'nama' => 'Surat Perintah Perjalanan Dinas (Dalam Daerah)', 'kategori' => 'khusus', 'kode_surat' => 'SPTDD'],
            ['kode' => 'SPTLD', 'nama' => 'Surat Perintah Perjalanan Dinas (Luar Daerah)',  'kategori' => 'khusus', 'kode_surat' => 'SPTLD'],
            ['kode' => 'SPTLN', 'nama' => 'Surat Perintah Perjalanan Dinas (Luar Negeri)',  'kategori' => 'khusus', 'kode_surat' => 'SPTLN'],
            ['kode' => 'S.Kep', 'nama' => 'Surat Keputusan',                    'kategori' => 'khusus', 'kode_surat' => 'S.Kep'],
            ['kode' => 'KB',    'nama' => 'Kesepakatan Bersama',                 'kategori' => 'khusus', 'kode_surat' => 'KB'],
            ['kode' => 'PK',    'nama' => 'Perjanjian Kerjasama',                'kategori' => 'khusus', 'kode_surat' => 'PK'],
        ];

        foreach ($data as $item) {
            Klasifikasi::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }
    }
}
