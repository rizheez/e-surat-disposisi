<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_nomor_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->date('tanggal_surat');
            $table->foreignId('klasifikasi')->nullable()->constrained('klasifikasis')->nullOnDelete();
            $table->string('tujuan')->nullable();
            $table->text('perihal');
            $table->string('sifat_surat', 25)->default('biasa');
            $table->text('keterangan')->nullable();
            $table->string('status', 25)->default('reserved');
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('used_at')->nullable();
            $table->foreignId('used_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('surat_keluar_id')->nullable()->constrained('surat_keluars')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_nomor_surats');
    }
};
