<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klasifikasis', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10);           // 01.1, 02.3, ND, SPTDD, etc.
            $table->string('nama');                // Urusan Umum, Nota Dinas, etc.
            $table->enum('kategori', ['internal', 'eksternal', 'khusus'])->default('internal');
            $table->string('kode_surat', 10)->nullable(); // For special types: ND, SPTDD, S.Kep, etc.
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add FK constraints to surat tables
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->foreign('klasifikasi')->references('id')->on('klasifikasis')->nullOnDelete();
        });

        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->foreign('klasifikasi')->references('id')->on('klasifikasis')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->dropForeign(['klasifikasi']);
        });

        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->dropForeign(['klasifikasi']);
        });

        Schema::dropIfExists('klasifikasis');
    }
};
