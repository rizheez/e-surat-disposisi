<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $rektorat = UnitKerja::where('kode', 'REK')->first();
        $baa = UnitKerja::where('kode', 'BAA')->first();
        $bau = UnitKerja::where('kode', 'BAU')->first();
        $fti = UnitKerja::where('kode', 'FTI')->first();
        $informatika = UnitKerja::where('kode', 'IF')->first();

        $this->createUser('Administrator', 'admin@esurat.test', 'Administrator Sistem', 'admin', $rektorat?->id);
        $this->createUser('Dr. Ahmad Sutanto', 'rektor@esurat.test', 'Rektor', 'rektor', $rektorat?->id);
        $this->createUser('Prof. Dr. H. Wakil Rektor I', 'wr1@esurat.test', 'Wakil Rektor I', 'wr', $rektorat?->id);
        $this->createUser('Prof. Dr. Hj. Wakil Rektor II', 'wr2@esurat.test', 'Wakil Rektor II', 'wr', $rektorat?->id);
        $this->createUser('Dr. Kepala Biro Akademik', 'kabiro.akademik@esurat.test', 'Kepala Biro Akademik', 'kabiro', $baa?->id);
        $this->createUser('Dr. Kepala Biro Umum', 'kabiro.umum@esurat.test', 'Kepala Biro Umum', 'kabiro', $bau?->id);
        $this->createUser('Prof. Dewi Kumala', 'dekan.fti@esurat.test', 'Dekan FTI', 'dekan', $fti?->id);
        $this->createUser('Rudi Hartono, M.Kom.', 'kaprodi.if@esurat.test', 'Kaprodi Informatika', 'kaprodi', $informatika?->id);
        $this->createUser('Siti Nurhaliza', 'administrasi@esurat.test', 'Staf Administrasi', 'staf_administrasi', $baa?->id);
        $this->createUser('Budi Santoso', 'staf@esurat.test', 'Staf', 'staf', $bau?->id);
        $this->createUser('Dosen Pengajar', 'dosen@esurat.test', 'Dosen', 'dosen', $informatika?->id);
    }

    private function createUser(string $name, string $email, string $jabatan, string $role, ?int $unitKerjaId): void
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt('password'),
                'jabatan' => $jabatan,
                'unit_kerja_id' => $unitKerjaId,
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles([$role]);
    }
}
