<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table): void {
            $table->index(['status', 'archived_at'], 'sm_status_archived_idx');
            $table->index(['penerima', 'status', 'archived_at'], 'sm_penerima_status_archived_idx');
            $table->index('tanggal_surat', 'sm_tanggal_surat_idx');
        });

        Schema::table('surat_keluars', function (Blueprint $table): void {
            $table->index(['status', 'archived_at'], 'sk_status_archived_idx');
            $table->index(['pembuat_id', 'status', 'archived_at'], 'sk_pembuat_status_archived_idx');
            $table->index(['penandatangan_id', 'status', 'archived_at'], 'sk_ttd_status_archived_idx');
            $table->index('tanggal_surat', 'sk_tanggal_surat_idx');
        });

        Schema::table('disposisis', function (Blueprint $table): void {
            $table->index(['ke_user_id', 'status', 'is_tembusan'], 'disp_user_status_tembusan_idx');
            $table->index(['ke_unit_id', 'status', 'is_tembusan'], 'disp_unit_status_tembusan_idx');
            $table->index(['dari_user_id', 'status'], 'disp_dari_status_idx');
            $table->index(['surat_masuk_id', 'status', 'is_tembusan'], 'disp_surat_status_tembusan_idx');
            $table->index(['batas_waktu', 'status'], 'disp_batas_status_idx');
        });

        Schema::table('generated_nomor_surats', function (Blueprint $table): void {
            $table->index(['status', 'created_at'], 'gns_status_created_idx');
            $table->index(['status', 'tanggal_surat'], 'gns_status_tanggal_idx');
        });
    }

    public function down(): void
    {
        Schema::table('generated_nomor_surats', function (Blueprint $table): void {
            $table->dropIndex('gns_status_created_idx');
            $table->dropIndex('gns_status_tanggal_idx');
        });

        Schema::table('disposisis', function (Blueprint $table): void {
            $table->dropIndex('disp_user_status_tembusan_idx');
            $table->dropIndex('disp_unit_status_tembusan_idx');
            $table->dropIndex('disp_dari_status_idx');
            $table->dropIndex('disp_surat_status_tembusan_idx');
            $table->dropIndex('disp_batas_status_idx');
        });

        Schema::table('surat_keluars', function (Blueprint $table): void {
            $table->dropIndex('sk_status_archived_idx');
            $table->dropIndex('sk_pembuat_status_archived_idx');
            $table->dropIndex('sk_ttd_status_archived_idx');
            $table->dropIndex('sk_tanggal_surat_idx');
        });

        Schema::table('surat_masuks', function (Blueprint $table): void {
            $table->dropIndex('sm_status_archived_idx');
            $table->dropIndex('sm_penerima_status_archived_idx');
            $table->dropIndex('sm_tanggal_surat_idx');
        });
    }
};

