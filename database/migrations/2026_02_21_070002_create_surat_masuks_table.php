<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_agenda')->unique();
            $table->string('nomor_surat');
            $table->date('tanggal_surat');
            $table->date('tanggal_terima');
            $table->string('pengirim');
            $table->string('perihal');
            $table->string('klasifikasi')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['diterima', 'dibaca', 'didisposisi', 'selesai'])->default('diterima');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('unit_tujuan_id')->nullable()->constrained('unit_kerjas')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_masuks');
    }
};
