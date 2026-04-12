<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Disposisi;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SuratAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The pdo_sqlite extension is not available.');
        }

        parent::setUp();
    }

    public function test_surat_keluar_pdf_route_rejects_user_who_is_not_creator_signer_or_admin(): void
    {
        $creator = User::factory()->create();
        $signer = User::factory()->create();
        $outsider = User::factory()->create();
        $suratKeluar = $this->suratKeluar($creator, $signer);

        $this
            ->actingAs($outsider)
            ->get(route('pdf.surat-keluar.preview', $suratKeluar))
            ->assertForbidden();
    }

    public function test_surat_keluar_file_route_rejects_user_who_is_not_creator_signer_or_admin(): void
    {
        $creator = User::factory()->create();
        $signer = User::factory()->create();
        $outsider = User::factory()->create();
        $suratKeluar = $this->suratKeluar($creator, $signer, [
            'file_path' => 'surat-keluar/test.pdf',
        ]);

        $this
            ->actingAs($outsider)
            ->get(route('surat-keluar.file.download', $suratKeluar))
            ->assertForbidden();
    }

    public function test_creator_signer_and_admin_can_view_surat_keluar(): void
    {
        $this->seedRoles();

        $creator = User::factory()->create();
        $signer = User::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $suratKeluar = $this->suratKeluar($creator, $signer);

        $this->assertTrue(Gate::forUser($creator)->allows('view', $suratKeluar));
        $this->assertTrue(Gate::forUser($signer)->allows('view', $suratKeluar));
        $this->assertTrue(Gate::forUser($admin)->allows('view', $suratKeluar));
    }

    public function test_surat_masuk_can_be_seen_by_recipient_and_disposition_target_only(): void
    {
        $creator = User::factory()->create();
        $recipient = User::factory()->create();
        $target = User::factory()->create();
        $outsider = User::factory()->create();
        $suratMasuk = $this->suratMasuk($creator, [
            'penerima' => $recipient->id,
        ]);

        Disposisi::query()->create([
            'surat_masuk_id' => $suratMasuk->id,
            'dari_user_id' => $recipient->id,
            'ke_user_id' => $target->id,
            'instruksi' => 'Tindak lanjuti',
            'status' => 'belum_diproses',
        ]);

        $this->assertTrue(Gate::forUser($recipient)->allows('view', $suratMasuk));
        $this->assertTrue(Gate::forUser($target)->allows('view', $suratMasuk));
        $this->assertFalse(Gate::forUser($outsider)->allows('view', $suratMasuk));
    }

    public function test_disposisi_can_be_seen_by_creator_user_target_and_unit_target_only(): void
    {
        $unit = UnitKerja::query()->create([
            'nama' => 'Akademik',
            'kode' => 'AKD',
        ]);
        $suratCreator = User::factory()->create();
        $disposisiCreator = User::factory()->create();
        $target = User::factory()->create();
        $unitMember = User::factory()->create(['unit_kerja_id' => $unit->id]);
        $outsider = User::factory()->create();
        $suratMasuk = $this->suratMasuk($suratCreator);

        $disposisi = Disposisi::query()->create([
            'surat_masuk_id' => $suratMasuk->id,
            'dari_user_id' => $disposisiCreator->id,
            'ke_user_id' => $target->id,
            'ke_unit_id' => $unit->id,
            'instruksi' => 'Tindak lanjuti',
            'status' => 'belum_diproses',
        ]);

        $this->assertTrue(Gate::forUser($disposisiCreator)->allows('view', $disposisi));
        $this->assertTrue(Gate::forUser($target)->allows('view', $disposisi));
        $this->assertTrue(Gate::forUser($unitMember)->allows('view', $disposisi));
        $this->assertFalse(Gate::forUser($outsider)->allows('view', $disposisi));
    }

    private function seedRoles(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::findOrCreate('admin', 'web');
    }

    private function suratKeluar(User $creator, User $signer, array $attributes = []): SuratKeluar
    {
        return SuratKeluar::query()->create([
            'nomor_surat' => 'SK/'.uniqid(),
            'tanggal_surat' => now(),
            'perihal' => 'Surat keluar',
            'tujuan' => 'Tujuan',
            'status' => 'draft',
            'pembuat_id' => $creator->id,
            'penandatangan_id' => $signer->id,
            ...$attributes,
        ]);
    }

    private function suratMasuk(User $creator, array $attributes = []): SuratMasuk
    {
        return SuratMasuk::query()->create([
            'nomor_agenda' => 'SM/'.uniqid(),
            'nomor_surat' => 'NS/'.uniqid(),
            'tanggal_surat' => now(),
            'tanggal_terima' => now(),
            'pengirim' => 'Pengirim',
            'perihal' => 'Surat masuk',
            'status' => 'diterima',
            'created_by' => $creator->id,
            ...$attributes,
        ]);
    }
}
