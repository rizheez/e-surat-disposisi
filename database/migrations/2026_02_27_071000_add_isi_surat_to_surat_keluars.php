<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->longText('isi_surat')->nullable()->after('perihal');
            $table->foreignId('template_surat_id')->nullable()->after('isi_surat')
                ->constrained('template_surats')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->dropForeign(['template_surat_id']);
            $table->dropColumn(['isi_surat', 'template_surat_id']);
        });
    }
};
