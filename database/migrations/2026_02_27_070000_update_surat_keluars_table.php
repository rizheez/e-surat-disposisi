<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_keluars', function (Blueprint $table) {
            // Add new columns
            $table->string('nomor_agenda', 50)->nullable()->after('nomor_surat');
            $table->text('alamat_tujuan')->nullable()->after('tujuan');
            $table->unsignedBigInteger('klasifikasi')->nullable()->after('perihal');
            $table->string('sifat_surat', 25)->default('biasa')->after('klasifikasi');
            $table->foreignId('surat_masuk_id')->nullable()->after('file_path')
                ->constrained('surat_masuks')->nullOnDelete();
            $table->date('tanggal_kirim')->nullable()->after('status');
            $table->text('keterangan')->nullable()->after('tanggal_kirim');
            $table->softDeletes();

            // Rename columns
            $table->renameColumn('tanggal', 'tanggal_surat');
            $table->renameColumn('created_by', 'pembuat_id');
            $table->renameColumn('approved_by', 'penandatangan_id');

            // Change perihal to text
            $table->text('perihal')->change();

            // Change status from enum to varchar
            $table->string('status', 25)->default('draft')->change();
        });

        // Drop unused columns (separate call after rename to avoid conflicts)
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->dropForeign(['unit_kerja_id']);
            $table->dropColumn('unit_kerja_id');

            $table->dropForeign(['template_surat_id']);
            $table->dropColumn('template_surat_id');

            $table->dropColumn('isi_surat');
        });
    }

    public function down(): void
    {
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->renameColumn('tanggal_surat', 'tanggal');
            $table->renameColumn('pembuat_id', 'created_by');
            $table->renameColumn('penandatangan_id', 'approved_by');

            $table->dropForeign(['surat_masuk_id']);
            $table->dropColumn([
                'nomor_agenda',
                'alamat_tujuan',
                'klasifikasi',
                'sifat_surat',
                'surat_masuk_id',
                'tanggal_kirim',
                'keterangan',
            ]);
            $table->dropSoftDeletes();

            $table->longText('isi_surat')->nullable();
            $table->foreignId('unit_kerja_id')->nullable()->constrained('unit_kerjas')->nullOnDelete();
            $table->foreignId('template_surat_id')->nullable()->constrained('template_surats')->nullOnDelete();
        });
    }
};
