<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            // Add new columns
            $table->text('alamat_pengirim')->nullable()->after('pengirim');
            $table->string('sifat_surat', 25)->default('biasa')->after('klasifikasi');
            $table->string('prioritas', 25)->default('sedang')->after('sifat_surat');
            $table->text('keterangan')->nullable()->after('file_path');
            $table->foreignId('penerima')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->softDeletes();

            // Change klasifikasi to nullable integer (FK ready)
            $table->unsignedBigInteger('klasifikasi')->nullable()->change();

            // Change perihal to text
            $table->text('perihal')->change();

            // Remove unit_tujuan_id
            $table->dropForeign(['unit_tujuan_id']);
            $table->dropColumn('unit_tujuan_id');
        });
    }

    public function down(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->dropForeign(['penerima']);
            $table->dropColumn([
                'alamat_pengirim',
                'sifat_surat',
                'prioritas',
                'keterangan',
                'penerima',
            ]);
            $table->dropSoftDeletes();

            $table->string('klasifikasi')->nullable()->change();
            $table->string('perihal')->change();

            $table->foreignId('unit_tujuan_id')->nullable()->constrained('unit_kerjas')->nullOnDelete();
        });
    }
};
