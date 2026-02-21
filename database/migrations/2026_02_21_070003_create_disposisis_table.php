<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disposisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_masuk_id')->constrained('surat_masuks')->cascadeOnDelete();
            $table->foreignId('dari_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ke_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ke_unit_id')->nullable()->constrained('unit_kerjas')->nullOnDelete();
            $table->text('instruksi');
            $table->text('catatan')->nullable();
            $table->date('batas_waktu')->nullable();
            $table->enum('status', ['belum_diproses', 'sedang_diproses', 'selesai'])->default('belum_diproses');
            $table->foreignId('parent_id')->nullable()->constrained('disposisis')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposisis');
    }
};
