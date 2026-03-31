<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('status');
        });

        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });

        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });
    }
};
